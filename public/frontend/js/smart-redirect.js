/**
 * Redirect Configuration
 * Konfigurasi untuk menentukan kemana user akan diarahkan setelah login
 */

const REDIRECT_CONFIG = {
    // Redirect berdasarkan role user
    roleBasedRedirects: {
        'admin': {
            type: 'backend',      // 'backend' atau 'frontend'
            path: '/admin',       // Path tujuan
            description: 'Admin Panel'
        },
        'instructor': {
            type: 'backend',
            path: '/instructor/dashboard',
            description: 'Instructor Dashboard'
        },
        'student': {
            type: 'frontend',
            path: '/student-dashboard.html',
            description: 'Student Dashboard'
        },
        'user': {
            type: 'frontend',
            path: '/dashboard.html',
            description: 'User Dashboard'
        }
    },

    // Default redirect jika role tidak ditemukan
    defaultRedirect: {
        type: 'frontend',
        path: '/dashboard.html',
        description: 'Default Dashboard'
    },

    // Redirect berdasarkan aplikasi/context
    contextBasedRedirects: {
        'lms': '/courses',           // Learning Management System
        'ecommerce': '/shop',        // E-commerce
        'blog': '/posts',           // Blog
        'portfolio': '/projects'     // Portfolio
    },

    // Protected pages yang memerlukan login
    protectedPages: [
        '/dashboard',
        '/profile', 
        '/courses',
        '/admin',
        '/instructor',
        '/settings'
    ],

    // Public pages yang tidak memerlukan login
    publicPages: [
        '/',
        '/home',
        '/about',
        '/contact',
        '/login',
        '/register'
    ]
};

/**
 * Smart Redirect Class
 * Menangani logika redirect yang cerdas berdasarkan berbagai faktor
 */
class SmartRedirect {
    constructor(config = REDIRECT_CONFIG) {
        this.config = config;
    }

    /**
     * Tentukan redirect destination setelah login sukses
     */
    getLoginSuccessRedirect(user, options = {}) {
        // Priority 1: Intended URL (halaman yang user coba akses sebelum login)
        const intendedUrl = this.getIntendedUrl();
        if (intendedUrl && this.isValidRedirectUrl(intendedUrl)) {
            this.clearIntendedUrl();
            return {
                type: this.getUrlType(intendedUrl),
                path: intendedUrl,
                reason: 'intended_url'
            };
        }

        // Priority 2: URL parameter 'redirect'
        const urlRedirect = this.getUrlRedirectParam();
        if (urlRedirect && this.isValidRedirectUrl(urlRedirect)) {
            return {
                type: this.getUrlType(urlRedirect),
                path: urlRedirect,
                reason: 'url_parameter'
            };
        }

        // Priority 3: Context-based redirect
        const context = options.context || this.detectContext();
        if (context && this.config.contextBasedRedirects[context]) {
            return {
                type: 'backend',
                path: this.config.contextBasedRedirects[context],
                reason: 'context_based'
            };
        }

        // Priority 4: Role-based redirect
        const roleRedirect = this.getRoleBasedRedirect(user.role);
        if (roleRedirect) {
            return {
                ...roleRedirect,
                reason: 'role_based'
            };
        }

        // Priority 5: Default redirect
        return {
            ...this.config.defaultRedirect,
            reason: 'default'
        };
    }

    /**
     * Get role-based redirect configuration
     */
    getRoleBasedRedirect(role) {
        return this.config.roleBasedRedirects[role] || null;
    }

    /**
     * Store intended URL untuk redirect setelah login
     */
    storeIntendedUrl(url = window.location.href) {
        // Jangan store jika sudah di halaman login
        if (url.includes('/login')) {
            return;
        }

        // Hanya store protected pages
        const path = new URL(url).pathname;
        if (this.isProtectedPage(path)) {
            localStorage.setItem('intended_url', url);
        }
    }

    /**
     * Get stored intended URL
     */
    getIntendedUrl() {
        return localStorage.getItem('intended_url');
    }

    /**
     * Clear stored intended URL
     */
    clearIntendedUrl() {
        localStorage.removeItem('intended_url');
    }

    /**
     * Get redirect parameter dari URL
     */
    getUrlRedirectParam() {
        const urlParams = new URLSearchParams(window.location.search);
        const redirect = urlParams.get('redirect');
        return redirect ? decodeURIComponent(redirect) : null;
    }

    /**
     * Detect context aplikasi
     */
    detectContext() {
        const hostname = window.location.hostname;
        const path = window.location.pathname;

        // Detect dari subdomain
        if (hostname.includes('lms.')) return 'lms';
        if (hostname.includes('shop.')) return 'ecommerce';
        if (hostname.includes('blog.')) return 'blog';

        // Detect dari path
        if (path.includes('/courses')) return 'lms';
        if (path.includes('/shop')) return 'ecommerce';
        if (path.includes('/blog')) return 'blog';

        return null;
    }

    /**
     * Check apakah URL valid untuk redirect
     */
    isValidRedirectUrl(url) {
        try {
            const urlObj = new URL(url, window.location.origin);
            
            // Harus same origin untuk security
            if (urlObj.origin !== window.location.origin) {
                return false;
            }

            // Tidak boleh redirect ke login page
            if (urlObj.pathname.includes('/login')) {
                return false;
            }

            return true;
        } catch {
            return false;
        }
    }

    /**
     * Tentukan tipe URL (backend atau frontend)
     */
    getUrlType(url) {
        // Frontend URLs biasanya punya extension .html atau di folder frontend
        if (url.includes('.html') || url.includes('/frontend/')) {
            return 'frontend';
        }
        
        // Backend URLs biasanya pure path tanpa extension
        return 'backend';
    }

    /**
     * Check apakah halaman ini protected
     */
    isProtectedPage(path) {
        return this.config.protectedPages.some(protectedPath => 
            path.startsWith(protectedPath)
        );
    }

    /**
     * Check apakah halaman ini public
     */
    isPublicPage(path) {
        return this.config.publicPages.some(publicPath => 
            path === publicPath || path.startsWith(publicPath + '/')
        );
    }

    /**
     * Execute redirect berdasarkan konfigurasi
     */
    executeRedirect(redirectConfig, user) {
        const { type, path, reason } = redirectConfig;

        console.log(`Redirecting user ${user.name} (${user.role}) to ${path} (${reason})`);

        // Show loading/transition
        this.showRedirectNotification(redirectConfig, user);

        // Execute redirect setelah delay kecil untuk UX
        setTimeout(() => {
            if (type === 'backend') {
                // Redirect ke Laravel backend dengan session
                userContext.redirectToBackend(path);
            } else {
                // Redirect ke frontend page
                window.location.href = path;
            }
        }, 1500);
    }

    /**
     * Show notification saat redirect
     */
    showRedirectNotification(redirectConfig, user) {
        const { path, description, reason } = redirectConfig;
        
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'redirect-toast';
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">🚀</div>
                <div class="toast-message">
                    <strong>Welcome back, ${user.name}!</strong>
                    <p>Redirecting to ${description || path}...</p>
                </div>
                <div class="toast-spinner">⟳</div>
            </div>
        `;

        // Add CSS
        const style = document.createElement('style');
        style.textContent = `
            .redirect-toast {
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 9999;
                min-width: 300px;
                animation: slideInRight 0.3s ease-out;
            }
            
            .toast-content {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            
            .toast-icon {
                font-size: 24px;
            }
            
            .toast-message strong {
                display: block;
                margin-bottom: 4px;
            }
            
            .toast-message p {
                margin: 0;
                opacity: 0.9;
                font-size: 14px;
            }
            
            .toast-spinner {
                font-size: 20px;
                animation: spin 1s linear infinite;
            }
            
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(toast);

        // Remove after redirect
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 2000);
    }
}

// Create global instance
const smartRedirect = new SmartRedirect();

// Auto-store intended URL on protected page access
document.addEventListener('DOMContentLoaded', () => {
    if (!userContext.getState().isAuthenticated) {
        smartRedirect.storeIntendedUrl();
    }
});

// Export untuk digunakan di login handler
window.smartRedirect = smartRedirect;
window.REDIRECT_CONFIG = REDIRECT_CONFIG;
