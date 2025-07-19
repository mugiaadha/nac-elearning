/**
 * Cross-Platform Authentication Helper
 * Untuk mengintegrasikan frontend dengan backend Laravel
 */
class AuthManager {
    constructor() {
        this.apiBaseUrl = '/api';
        this.token = localStorage.getItem('api_token');
        this.sessionKey = localStorage.getItem('session_key');
    }

    /**
     * Login user dan simpan token
     */
    async login(email, password) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (data.success) {
                this.token = data.data.token;
                this.sessionKey = data.data.session_key;
                
                localStorage.setItem('api_token', this.token);
                localStorage.setItem('session_key', this.sessionKey);
                localStorage.setItem('user', JSON.stringify(data.data.user));

                return data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    }

    /**
     * Logout user
     */
    async logout() {
        try {
            if (this.token) {
                await fetch(`${this.apiBaseUrl}/auth/logout`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
            }

            this.clearAuth();
            return true;
        } catch (error) {
            console.error('Logout error:', error);
            this.clearAuth();
            return false;
        }
    }

    /**
     * Clear authentication data
     */
    clearAuth() {
        this.token = null;
        this.sessionKey = null;
        localStorage.removeItem('api_token');
        localStorage.removeItem('session_key');
        localStorage.removeItem('user');
    }

    /**
     * Get current user info
     */
    async getCurrentUser() {
        if (!this.token) {
            return null;
        }

        try {
            const response = await fetch(`${this.apiBaseUrl}/auth/me`, {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                localStorage.setItem('user', JSON.stringify(data.data));
                return data.data;
            } else {
                this.clearAuth();
                return null;
            }
        } catch (error) {
            console.error('Get user error:', error);
            this.clearAuth();
            return null;
        }
    }

    /**
     * Check if user is logged in
     */
    isLoggedIn() {
        return this.token !== null && this.token !== '';
    }

    /**
     * Get stored user data
     */
    getUser() {
        const userData = localStorage.getItem('user');
        return userData ? JSON.parse(userData) : null;
    }

    /**
     * Make authenticated API request
     */
    async apiRequest(url, options = {}) {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(options.headers || {})
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        if (this.sessionKey) {
            headers['X-Session-Key'] = this.sessionKey;
        }

        try {
            const response = await fetch(url, {
                ...options,
                headers
            });

            const data = await response.json();

            // If unauthorized, clear auth
            if (response.status === 401) {
                this.clearAuth();
                throw new Error('Unauthorized');
            }

            return data;
        } catch (error) {
            console.error('API request error:', error);
            throw error;
        }
    }

    /**
     * Validate current token
     */
    async validateToken() {
        if (!this.token) {
            return false;
        }

        try {
            const response = await fetch(`${this.apiBaseUrl}/auth/validate-token`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            
            if (!data.success) {
                this.clearAuth();
                return false;
            }

            return true;
        } catch (error) {
            console.error('Token validation error:', error);
            this.clearAuth();
            return false;
        }
    }

    /**
     * Redirect to backend dashboard with session
     */
    redirectToBackend(path = '/dashboard') {
        if (this.sessionKey) {
            const backendUrl = window.location.origin + path;
            const separator = path.includes('?') ? '&' : '?';
            window.location.href = `${backendUrl}${separator}session_key=${this.sessionKey}`;
        } else {
            window.location.href = '/login';
        }
    }
}

// Initialize auth manager
const authManager = new AuthManager();

// Auto-validate token on page load
document.addEventListener('DOMContentLoaded', async function() {
    if (authManager.isLoggedIn()) {
        const isValid = await authManager.validateToken();
        if (!isValid) {
            console.log('Token expired, redirecting to login');
            // window.location.href = '/login';
        }
    }
});

// Export for use in other scripts
window.AuthManager = AuthManager;
window.authManager = authManager;
