/**
 * Login Handler dengan berbagai opsi redirect
 */

class LoginHandler {
    constructor() {
        this.redirectConfig = {
            // Konfigurasi redirect berdasarkan role
            admin: '/admin',                    // Laravel backend admin
            instructor: '/instructor/dashboard', // Laravel backend instructor
            student: '/student-dashboard.html', // Frontend student dashboard
            user: '/dashboard.html',            // Frontend user dashboard
            default: '/dashboard.html'          // Default fallback
        };
        
        this.init();
    }
    
    init() {
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', this.handleLogin.bind(this));
        }
        
        // Store intended URL jika user mencoba akses halaman protected
        this.storeIntendedUrl();
    }
    
    storeIntendedUrl() {
        const currentPath = window.location.pathname;
        const protectedPaths = ['/dashboard', '/profile', '/courses', '/admin'];
        
        if (protectedPaths.some(path => currentPath.includes(path))) {
            localStorage.setItem('intended_url', window.location.href);
        }
    }
    
    async handleLogin(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const email = formData.get('email');
        const password = formData.get('password');
        const rememberMe = formData.get('remember_me') === 'on';
        
        this.showLoading(true);
        
        try {
            const result = await userContext.login(email, password, rememberMe);
            
            if (result.success) {
                // Delay untuk UX yang smooth
                setTimeout(() => {
                    this.handleSuccessRedirect(result.data);
                }, 500);
            }
            
        } catch (error) {
            this.showError(error.message);
            this.showLoading(false);
        }
    }
    
    handleSuccessRedirect(data) {
        const user = data.user;
        
        // Show success message
        this.showSuccess(`Welcome back, ${user.name}!`);
        
        // Get redirect destination
        const redirectUrl = this.getRedirectUrl(user);
        
        console.log(`Redirecting ${user.role} to: ${redirectUrl}`);
        
        // Execute redirect
        setTimeout(() => {
            this.executeRedirect(redirectUrl, user);
        }, 1000);
    }
    
    getRedirectUrl(user) {
        // 1. Check intended URL first (halaman yang user mau akses sebelum login)
        const intendedUrl = localStorage.getItem('intended_url');
        if (intendedUrl) {
            localStorage.removeItem('intended_url');
            return intendedUrl;
        }
        
        // 2. Check URL parameter 'redirect'
        const urlParams = new URLSearchParams(window.location.search);
        const redirectParam = urlParams.get('redirect');
        if (redirectParam) {
            return decodeURIComponent(redirectParam);
        }
        
        // 3. Role-based redirect
        const userRole = user.role || 'user';
        return this.redirectConfig[userRole] || this.redirectConfig.default;
    }
    
    executeRedirect(redirectUrl, user) {
        // Check if it's a backend Laravel route (starts with /)
        if (redirectUrl.startsWith('/') && !redirectUrl.endsWith('.html')) {
            // Redirect to Laravel backend with session
            userContext.redirectToBackend(redirectUrl);
        } else {
            // Redirect to frontend page
            window.location.href = redirectUrl;
        }
    }
    
    showLoading(show) {
        const button = document.querySelector('#loginForm button[type="submit"]');
        const spinner = document.getElementById('loginSpinner');
        
        if (show) {
            button.disabled = true;
            button.textContent = 'Logging in...';
            if (spinner) spinner.style.display = 'inline-block';
        } else {
            button.disabled = false;
            button.textContent = 'Login';
            if (spinner) spinner.style.display = 'none';
        }
    }
    
    showError(message) {
        this.showMessage(message, 'error');
    }
    
    showSuccess(message) {
        this.showMessage(message, 'success');
    }
    
    showMessage(message, type) {
        const messageDiv = document.getElementById('loginMessage') || this.createMessageDiv();
        
        messageDiv.textContent = message;
        messageDiv.className = `alert alert-${type}`;
        messageDiv.style.display = 'block';
        
        // Auto hide error after 5 seconds, success after 2 seconds
        const hideAfter = type === 'error' ? 5000 : 2000;
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, hideAfter);
    }
    
    createMessageDiv() {
        const div = document.createElement('div');
        div.id = 'loginMessage';
        div.className = 'alert';
        
        const form = document.getElementById('loginForm');
        form.insertBefore(div, form.firstChild);
        
        return div;
    }
}

// Initialize login handler
document.addEventListener('DOMContentLoaded', () => {
    new LoginHandler();
});

// =====================================================
// Alternative: Simple redirect functions
// =====================================================

// Function untuk redirect sederhana
function redirectAfterLogin(user) {
    switch (user.role) {
        case 'admin':
            return userContext.redirectToBackend('/admin');
            
        case 'instructor':
            return userContext.redirectToBackend('/instructor/dashboard');
            
        case 'student':
            return window.location.href = '/student-dashboard.html';
            
        default:
            return window.location.href = '/dashboard.html';
    }
}

// Function untuk custom redirect
function customRedirect(path, isBackend = false) {
    if (isBackend) {
        userContext.redirectToBackend(path);
    } else {
        window.location.href = path;
    }
}

// =====================================================
// URL-based redirect examples
// =====================================================

// Example: Login dengan parameter redirect
// URL: /login.html?redirect=%2Fcourses%2F123
function handleUrlRedirect() {
    const urlParams = new URLSearchParams(window.location.search);
    const redirectUrl = urlParams.get('redirect');
    
    if (redirectUrl) {
        const decodedUrl = decodeURIComponent(redirectUrl);
        
        // Check if it's safe URL (same origin)
        if (decodedUrl.startsWith('/') || decodedUrl.startsWith(window.location.origin)) {
            return decodedUrl;
        }
    }
    
    return null;
}

// =====================================================
// Modal/Toast notifications
// =====================================================

function showLoginSuccessModal(user) {
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'login-success-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <h3>🎉 Welcome back, ${user.name}!</h3>
            <p>You will be redirected in <span id="countdown">3</span> seconds...</p>
            <div class="modal-actions">
                <button onclick="redirectNow()">Go Now</button>
                <button onclick="stayHere()">Stay Here</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Countdown
    let countdown = 3;
    const countdownEl = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        countdown--;
        countdownEl.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            redirectAfterLogin(user);
        }
    }, 1000);
    
    // Global functions for modal buttons
    window.redirectNow = () => {
        clearInterval(timer);
        document.body.removeChild(modal);
        redirectAfterLogin(user);
    };
    
    window.stayHere = () => {
        clearInterval(timer);
        document.body.removeChild(modal);
    };
}

// =====================================================
// Progressive Web App (PWA) redirect
// =====================================================

function handlePWARedirect(user) {
    // Check if it's PWA mode
    if (window.matchMedia('(display-mode: standalone)').matches) {
        // In PWA mode, prefer frontend routes
        switch (user.role) {
            case 'admin':
                return window.location.href = '/admin-dashboard.html';
            case 'instructor':
                return window.location.href = '/instructor-dashboard.html';
            default:
                return window.location.href = '/dashboard.html';
        }
    } else {
        // In browser mode, can use backend routes
        return redirectAfterLogin(user);
    }
}
