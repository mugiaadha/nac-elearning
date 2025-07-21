import { ref, reactive, computed, watch } from 'vue'

// User state
const userState = reactive({
  user: null,
  token: null,
  sessionKey: null,
  isAuthenticated: false,
  isLoading: false,
  error: null
})

// Initialize from localStorage
const initializeAuth = () => {
  const token = localStorage.getItem('api_token')
  const user = localStorage.getItem('user')
  const sessionKey = localStorage.getItem('session_key')

  if (token && user) {
    userState.token = token
    userState.user = JSON.parse(user)
    userState.sessionKey = sessionKey
    userState.isAuthenticated = true

    // Validate session
    validateSession()
  }
}

// API request helper
const apiRequest = async (url, options = {}) => {
  if (!userState.token) {
    throw new Error('No authentication token')
  }

  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${userState.token}`,
    ...(options.headers || {})
  }

  try {
    const response = await fetch(url, {
      ...options,
      headers
    })

    const data = await response.json()

    if (response.status === 401) {
      clearAuth()
      throw new Error('Unauthorized')
    }

    return data
  } catch (error) {
    if (error.message === 'Unauthorized') {
      clearAuth()
    }
    throw error
  }
}

// Login function
const login = async (email, password, rememberMe = false) => {
  userState.isLoading = true
  userState.error = null

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
    })

    const data = await response.json()

    if (data.success) {
      const { token, user, session_key } = data.data

      // Store in localStorage
      localStorage.setItem('api_token', token)
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('session_key', session_key)

      // Update state
      userState.token = token
      userState.user = user
      userState.sessionKey = session_key
      userState.isAuthenticated = true
      userState.isLoading = false
      userState.error = null

      return data
    } else {
      throw new Error(data.message)
    }
  } catch (error) {
    userState.isLoading = false
    userState.error = error.message
    throw error
  }
}

// Logout function
const logout = async () => {
  userState.isLoading = true

  try {
    if (userState.token) {
      await fetch('/api/auth/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${userState.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        }
      })
    }
  } catch (error) {
    console.error('Logout API error:', error)
  } finally {
    clearAuth()
  }
}

// Clear authentication
const clearAuth = () => {
  localStorage.removeItem('api_token')
  localStorage.removeItem('user')
  localStorage.removeItem('session_key')

  userState.user = null
  userState.token = null
  userState.sessionKey = null
  userState.isAuthenticated = false
  userState.isLoading = false
  userState.error = null
}

// Refresh user data
const refreshUser = async () => {
  if (!userState.token) return null

  userState.isLoading = true

  try {
    const data = await apiRequest('/api/auth/me')

    if (data.success) {
      const user = data.data
      localStorage.setItem('user', JSON.stringify(user))

      userState.user = user
      userState.isLoading = false
      userState.error = null

      return user
    } else {
      throw new Error(data.message)
    }
  } catch (error) {
    userState.isLoading = false
    userState.error = error.message
    return null
  }
}

// Validate session
const validateSession = async () => {
  if (!userState.token) return false

  try {
    const response = await fetch('/api/auth/validate-token', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${userState.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      }
    })

    const data = await response.json()

    if (!data.success) {
      clearAuth()
      return false
    }

    return true
  } catch (error) {
    console.error('Session validation error:', error)
    clearAuth()
    return false
  }
}

// Redirect to backend
const redirectToBackend = (path = '/dashboard') => {
  if (userState.sessionKey) {
    const backendUrl = window.location.origin + path
    const separator = path.includes('?') ? '&' : '?'
    window.location.href = `${backendUrl}${separator}session_key=${userState.sessionKey}`
  } else {
    window.location.href = '/login'
  }
}

// Computed properties
const hasRole = (role) => computed(() => 
  userState.user && userState.user.role === role
)

const isAdmin = computed(() => 
  userState.user && userState.user.role === 'admin'
)

const getUserProperty = (property, defaultValue = null) => computed(() =>
  userState.user ? userState.user[property] : defaultValue
)

// Composable function
export const useUser = () => {
  // Initialize on first use
  if (!userState.token && !userState.user) {
    initializeAuth()
  }

  return {
    // State (reactive)
    userState: readonly(userState),
    
    // Computed
    isAuthenticated: computed(() => userState.isAuthenticated),
    currentUser: computed(() => userState.user),
    isLoading: computed(() => userState.isLoading),
    error: computed(() => userState.error),
    isAdmin,
    
    // Methods
    login,
    logout,
    refreshUser,
    validateSession,
    apiRequest,
    redirectToBackend,
    clearAuth,
    hasRole,
    getUserProperty
  }
}

// Plugin for Vue app
export const userPlugin = {
  install(app) {
    const userComposable = useUser()
    
    // Make available globally
    app.config.globalProperties.$user = userComposable
    app.provide('user', userComposable)
  }
}

// Navigation guard for protected routes
export const authGuard = (to, from, next) => {
  const { isAuthenticated } = useUser()
  
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    next('/login')
  } else {
    next()
  }
}
