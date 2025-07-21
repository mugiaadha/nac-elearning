import React, { createContext, useContext, useReducer, useEffect } from 'react';

// User state types
const USER_ACTIONS = {
    SET_LOADING: 'SET_LOADING',
    LOGIN_SUCCESS: 'LOGIN_SUCCESS',
    LOGIN_ERROR: 'LOGIN_ERROR',
    LOGOUT: 'LOGOUT',
    SET_USER: 'SET_USER',
    SET_ERROR: 'SET_ERROR',
    CLEAR_ERROR: 'CLEAR_ERROR'
};

// Initial state
const initialState = {
    user: null,
    token: null,
    sessionKey: null,
    isAuthenticated: false,
    isLoading: false,
    error: null
};

// Reducer
const userReducer = (state, action) => {
    switch (action.type) {
        case USER_ACTIONS.SET_LOADING:
            return {
                ...state,
                isLoading: action.payload,
                error: null
            };

        case USER_ACTIONS.LOGIN_SUCCESS:
            return {
                ...state,
                user: action.payload.user,
                token: action.payload.token,
                sessionKey: action.payload.sessionKey,
                isAuthenticated: true,
                isLoading: false,
                error: null
            };

        case USER_ACTIONS.LOGIN_ERROR:
            return {
                ...state,
                isLoading: false,
                error: action.payload,
                isAuthenticated: false
            };

        case USER_ACTIONS.LOGOUT:
            return {
                ...initialState
            };

        case USER_ACTIONS.SET_USER:
            return {
                ...state,
                user: action.payload,
                isAuthenticated: !!action.payload
            };

        case USER_ACTIONS.SET_ERROR:
            return {
                ...state,
                error: action.payload,
                isLoading: false
            };

        case USER_ACTIONS.CLEAR_ERROR:
            return {
                ...state,
                error: null
            };

        default:
            return state;
    }
};

// Create context
const UserContext = createContext();

// Provider component
export const UserProvider = ({ children }) => {
    const [state, dispatch] = useReducer(userReducer, initialState);

    // Initialize from localStorage
    useEffect(() => {
        const token = localStorage.getItem('api_token');
        const user = localStorage.getItem('user');
        const sessionKey = localStorage.getItem('session_key');

        if (token && user) {
            dispatch({
                type: USER_ACTIONS.LOGIN_SUCCESS,
                payload: {
                    token,
                    user: JSON.parse(user),
                    sessionKey
                }
            });

            // Validate session
            validateSession(token);
        }
    }, []);

    // API request helper
    const apiRequest = async (url, options = {}) => {
        if (!state.token) {
            throw new Error('No authentication token');
        }

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${state.token}`,
            ...(options.headers || {})
        };

        try {
            const response = await fetch(url, {
                ...options,
                headers
            });

            const data = await response.json();

            if (response.status === 401) {
                logout();
                throw new Error('Unauthorized');
            }

            return data;
        } catch (error) {
            if (error.message === 'Unauthorized') {
                logout();
            }
            throw error;
        }
    };

    // Login function
    const login = async (email, password, rememberMe = false) => {
        dispatch({ type: USER_ACTIONS.SET_LOADING, payload: true });

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
                dispatch({
                    type: USER_ACTIONS.LOGIN_SUCCESS,
                    payload: {
                        token,
                        user,
                        sessionKey: session_key
                    }
                });

                return data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            dispatch({
                type: USER_ACTIONS.LOGIN_ERROR,
                payload: error.message
            });
            throw error;
        }
    };

    // Logout function
    const logout = async () => {
        dispatch({ type: USER_ACTIONS.SET_LOADING, payload: true });

        try {
            if (state.token) {
                await fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${state.token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });
            }
        } catch (error) {
            console.error('Logout API error:', error);
        } finally {
            // Clear localStorage
            localStorage.removeItem('api_token');
            localStorage.removeItem('user');
            localStorage.removeItem('session_key');

            // Clear state
            dispatch({ type: USER_ACTIONS.LOGOUT });
        }
    };

    // Refresh user data
    const refreshUser = async () => {
        if (!state.token) return null;

        dispatch({ type: USER_ACTIONS.SET_LOADING, payload: true });

        try {
            const data = await apiRequest('/api/auth/me');

            if (data.success) {
                const user = data.data;
                localStorage.setItem('user', JSON.stringify(user));

                dispatch({
                    type: USER_ACTIONS.SET_USER,
                    payload: user
                });

                return user;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            dispatch({
                type: USER_ACTIONS.SET_ERROR,
                payload: error.message
            });
            return null;
        }
    };

    // Validate session
    const validateSession = async (token = state.token) => {
        if (!token) return false;

        try {
            const response = await fetch('/api/auth/validate-token', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (!data.success) {
                logout();
                return false;
            }

            return true;
        } catch (error) {
            console.error('Session validation error:', error);
            logout();
            return false;
        }
    };

    // Redirect to backend
    const redirectToBackend = (path = '/dashboard') => {
        if (state.sessionKey) {
            const backendUrl = window.location.origin + path;
            const separator = path.includes('?') ? '&' : '?';
            window.location.href = `${backendUrl}${separator}session_key=${state.sessionKey}`;
        } else {
            window.location.href = '/login';
        }
    };

    // Helper functions
    const hasRole = (role) => state.user && state.user.role === role;
    const isAdmin = () => hasRole('admin');
    const getUserProperty = (property, defaultValue = null) => 
        state.user ? state.user[property] : defaultValue;

    const value = {
        // State
        ...state,
        
        // Actions
        login,
        logout,
        refreshUser,
        validateSession,
        apiRequest,
        redirectToBackend,
        
        // Helpers
        hasRole,
        isAdmin,
        getUserProperty,
        
        // Dispatch for custom actions
        dispatch
    };

    return (
        <UserContext.Provider value={value}>
            {children}
        </UserContext.Provider>
    );
};

// Custom hook to use user context
export const useUser = () => {
    const context = useContext(UserContext);
    if (!context) {
        throw new Error('useUser must be used within a UserProvider');
    }
    return context;
};

// HOC for protected routes
export const withAuth = (Component) => {
    return function AuthenticatedComponent(props) {
        const { isAuthenticated, isLoading } = useUser();

        if (isLoading) {
            return <div>Loading...</div>;
        }

        if (!isAuthenticated) {
            window.location.href = '/login';
            return null;
        }

        return <Component {...props} />;
    };
};

// Export actions for use in components
export { USER_ACTIONS };
