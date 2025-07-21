/**
 * User Context State Management (Vanilla JavaScript)
 * Global state untuk mengelola user data di seluruh aplikasi
 */

class UserContext {
    constructor() {
        this.state = {
            user: null,
            isAuthenticated: false,
            isLoading: false,
            error: null,
            token: null,
            sessionKey: null
        };
        
        this.listeners = [];
        this.init();
    }

    /**
     * Initialize context dari localStorage
     */
    init() {
        const token = localStorage.getItem('api_token');
        const user = localStorage.getItem('user');
        const sessionKey = localStorage.getItem('session_key');

        if (token && user) {
            this.setState({
                token: token,
                sessionKey: sessionKey,
                user: JSON.parse(user),
                isAuthenticated: true
            });
        }

        // Auto-validate token on init
        if (token) {
            this.validateSession();
        }
    }

    /**
     * Update state dan notify listeners
     */
    setState(newState) {
        const prevState = { ...this.state };
        this.state = { ...this.state, ...newState };
        
        // Notify all listeners
        this.listeners.forEach(listener => {
            listener(this.state, prevState);
        });
    }

    /**
     * Subscribe ke state changes
     */
    subscribe(listener) {
        this.listeners.push(listener);
        
        // Return unsubscribe function
        return () => {
            this.listeners = this.listeners.filter(l => l !== listener);
        };
    }

    /**
     * Get current state
     */
    getState() {
        return { ...this.state };
    }

    /**
     * Login user
     */
    async login(email, password, rememberMe = false) {
        this.setState({ isLoading: true, error: null });

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email,
                    password,
                    remember_me: rememberMe
                })
            });

            const data = await response.json();

            if (data.success) {
                const { token, user, session_key } = data.data;

                // Store in localStorage
                localStorage.setItem('api_token', token);
                localStorage.setItem('user', JSON.stringify(user));
                localStorage.setItem('session_key', session_key);

                // Update state
                this.setState({
                    user,
                    token,
                    sessionKey: session_key,
                    isAuthenticated: true,
                    isLoading: false,
                    error: null
                });

                return data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.setState({
                isLoading: false,
                error: error.message
            });
            throw error;
        }
    }

    /**
     * Logout user
     */
    async logout() {
        this.setState({ isLoading: true });

        try {
            if (this.state.token) {
                await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.state.token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
            }
        } catch (error) {
            console.error('Logout API error:', error);
        } finally {
            // Clear state regardless of API result
            this.clearAuth();
        }
    }

    /**
     * Clear authentication data
     */
    clearAuth() {
        localStorage.removeItem('api_token');
        localStorage.removeItem('user');
        localStorage.removeItem('session_key');

        this.setState({
            user: null,
            token: null,
            sessionKey: null,
            isAuthenticated: false,
            isLoading: false,
            error: null
        });
    }

    /**
     * Refresh user data
     */
    async refreshUser() {
        if (!this.state.token) {
            return null;
        }

        this.setState({ isLoading: true });

        try {
            const response = await fetch('/api/auth/me', {
                headers: {
                    'Authorization': `Bearer ${this.state.token}`,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                const user = data.data;
                localStorage.setItem('user', JSON.stringify(user));

                this.setState({
                    user,
                    isLoading: false,
                    error: null
                });

                return user;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.setState({
                isLoading: false,
                error: error.message
            });

            // If unauthorized, clear auth
            if (error.message.includes('401') || error.message.includes('Unauthorized')) {
                this.clearAuth();
            }

            return null;
        }
    }

    /**
     * Validate current session
     */
    async validateSession() {
        if (!this.state.token) {
            return false;
        }

        try {
            const response = await fetch('/api/auth/validate-token', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.state.token}`,
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
            console.error('Session validation error:', error);
            this.clearAuth();
            return false;
        }
    }

    /**
     * Make authenticated API request
     */
    async apiRequest(url, options = {}) {
        if (!this.state.token) {
            throw new Error('No authentication token');
        }

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.state.token}`,
            ...(options.headers || {})
        };

        try {
            const response = await fetch(url, {
                ...options,
                headers
            });

            const data = await response.json();

            // Handle unauthorized
            if (response.status === 401) {
                this.clearAuth();
                throw new Error('Unauthorized');
            }

            return data;
        } catch (error) {
            if (error.message === 'Unauthorized') {
                this.clearAuth();
            }
            throw error;
        }
    }

    /**
     * Check if user has specific role
     */
    hasRole(role) {
        return this.state.user && this.state.user.role === role;
    }

    /**
     * Check if user is admin
     */
    isAdmin() {
        return this.hasRole('admin');
    }

    /**
     * Get user property safely
     */
    getUserProperty(property, defaultValue = null) {
        return this.state.user ? this.state.user[property] : defaultValue;
    }

    /**
     * Redirect to backend with session
     */
    redirectToBackend(path = '/dashboard') {
        if (this.state.sessionKey) {
            const backendUrl = window.location.origin + path;
            const separator = path.includes('?') ? '&' : '?';
            window.location.href = `${backendUrl}${separator}session_key=${this.state.sessionKey}`;
        } else {
            window.location.href = '/login';
        }
    }
}

// Create global instance
const userContext = new UserContext();

// Helper functions untuk kemudahan penggunaan
const useUser = () => userContext.getState();
const loginUser = (email, password, rememberMe) => userContext.login(email, password, rememberMe);
const logoutUser = () => userContext.logout();
const refreshUser = () => userContext.refreshUser();
const isAuthenticated = () => userContext.getState().isAuthenticated;
const getCurrentUser = () => userContext.getState().user;

// Export untuk digunakan di module lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        userContext,
        useUser,
        loginUser,
        logoutUser,
        refreshUser,
        isAuthenticated,
        getCurrentUser
    };
}

// Make available globally
window.userContext = userContext;
window.useUser = useUser;
window.loginUser = loginUser;
window.logoutUser = logoutUser;
window.refreshUser = refreshUser;
window.isAuthenticated = isAuthenticated;
window.getCurrentUser = getCurrentUser;
