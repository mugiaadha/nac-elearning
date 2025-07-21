/**
 * Contoh penggunaan User Context di berbagai framework
 */

// ========================================
// 1. VANILLA JAVASCRIPT
// ========================================

// Include di HTML
// <script src="/frontend/js/user-context.js"></script>

// Contoh login component
class LoginComponent {
    constructor() {
        this.init();
        this.subscribeToUserChanges();
    }

    init() {
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', this.handleLogin.bind(this));
    }

    subscribeToUserChanges() {
        // Subscribe ke perubahan user state
        userContext.subscribe((newState, prevState) => {
            if (newState.isAuthenticated && !prevState.isAuthenticated) {
                this.onLoginSuccess(newState.user);
            }
            
            if (!newState.isAuthenticated && prevState.isAuthenticated) {
                this.onLogout();
            }
            
            if (newState.error) {
                this.showError(newState.error);
            }
        });
    }

    async handleLogin(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const email = formData.get('email');
        const password = formData.get('password');
        const rememberMe = formData.get('remember_me') === 'on';

        try {
            await loginUser(email, password, rememberMe);
            // State akan otomatis update via subscription
        } catch (error) {
            this.showError(error.message);
        }
    }

    onLoginSuccess(user) {
        // Hide login form
        document.getElementById('loginForm').style.display = 'none';
        
        // Show user dashboard
        document.getElementById('userDashboard').style.display = 'block';
        document.getElementById('userName').textContent = user.name;
        
        // Optional: redirect to backend
        // userContext.redirectToBackend('/dashboard');
    }

    onLogout() {
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('userDashboard').style.display = 'none';
    }

    showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new LoginComponent();
});

// Contoh menggunakan state di component lain
class UserProfileComponent {
    constructor() {
        this.render();
        this.subscribeToUserChanges();
    }

    subscribeToUserChanges() {
        userContext.subscribe((state) => {
            if (state.user) {
                this.updateProfile(state.user);
            }
        });
    }

    updateProfile(user) {
        document.getElementById('profileName').textContent = user.name;
        document.getElementById('profileEmail').textContent = user.email;
        document.getElementById('profileRole').textContent = user.role;
    }

    async handleLogout() {
        await logoutUser();
        // State akan otomatis clear via subscription
    }

    render() {
        const currentUser = getCurrentUser();
        if (currentUser) {
            this.updateProfile(currentUser);
        }
    }
}

// ========================================
// 2. REACT USAGE
// ========================================

/*
// App.jsx
import React from 'react';
import { UserProvider } from './UserContext';
import LoginComponent from './components/LoginComponent';
import Dashboard from './components/Dashboard';

function App() {
    return (
        <UserProvider>
            <div className="App">
                <LoginComponent />
                <Dashboard />
            </div>
        </UserProvider>
    );
}

export default App;

// LoginComponent.jsx
import React, { useState } from 'react';
import { useUser } from '../UserContext';

function LoginComponent() {
    const { login, isAuthenticated, isLoading, error } = useUser();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [rememberMe, setRememberMe] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await login(email, password, rememberMe);
        } catch (error) {
            console.error('Login failed:', error);
        }
    };

    if (isAuthenticated) {
        return null; // Hide login form
    }

    return (
        <form onSubmit={handleSubmit}>
            <div>
                <label>Email:</label>
                <input 
                    type="email" 
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required 
                />
            </div>
            
            <div>
                <label>Password:</label>
                <input 
                    type="password" 
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required 
                />
            </div>
            
            <div>
                <label>
                    <input 
                        type="checkbox" 
                        checked={rememberMe}
                        onChange={(e) => setRememberMe(e.target.checked)}
                    />
                    Remember Me
                </label>
            </div>
            
            <button type="submit" disabled={isLoading}>
                {isLoading ? 'Logging in...' : 'Login'}
            </button>
            
            {error && <div className="error">{error}</div>}
        </form>
    );
}

// Dashboard.jsx
import React from 'react';
import { useUser, withAuth } from '../UserContext';

function Dashboard() {
    const { user, logout, isAdmin, redirectToBackend } = useUser();

    const handleLogout = async () => {
        await logout();
    };

    return (
        <div className="dashboard">
            <h2>Welcome, {user?.name}!</h2>
            <p>Email: {user?.email}</p>
            <p>Role: {user?.role}</p>
            
            {isAdmin() && (
                <div>
                    <h3>Admin Panel</h3>
                    <button onClick={() => redirectToBackend('/admin')}>
                        Go to Backend Admin
                    </button>
                </div>
            )}
            
            <button onClick={handleLogout}>Logout</button>
        </div>
    );
}

export default withAuth(Dashboard);
*/

// ========================================
// 3. VUE.JS USAGE
// ========================================

/*
// main.js
import { createApp } from 'vue'
import App from './App.vue'
import { userPlugin } from './composables/useUser'

const app = createApp(App)
app.use(userPlugin)
app.mount('#app')

// LoginComponent.vue
<template>
  <form @submit.prevent="handleLogin" v-if="!isAuthenticated">
    <div>
      <label>Email:</label>
      <input v-model="email" type="email" required />
    </div>
    
    <div>
      <label>Password:</label>
      <input v-model="password" type="password" required />
    </div>
    
    <div>
      <label>
        <input v-model="rememberMe" type="checkbox" />
        Remember Me
      </label>
    </div>
    
    <button type="submit" :disabled="isLoading">
      {{ isLoading ? 'Logging in...' : 'Login' }}
    </button>
    
    <div v-if="error" class="error">{{ error }}</div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useUser } from '../composables/useUser'

const { login, isAuthenticated, isLoading, error } = useUser()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)

const handleLogin = async () => {
  try {
    await login(email.value, password.value, rememberMe.value)
  } catch (error) {
    console.error('Login failed:', error)
  }
}
</script>

// Dashboard.vue
<template>
  <div class="dashboard" v-if="isAuthenticated">
    <h2>Welcome, {{ currentUser?.name }}!</h2>
    <p>Email: {{ currentUser?.email }}</p>
    <p>Role: {{ currentUser?.role }}</p>
    
    <div v-if="isAdmin">
      <h3>Admin Panel</h3>
      <button @click="redirectToBackend('/admin')">
        Go to Backend Admin
      </button>
    </div>
    
    <button @click="handleLogout">Logout</button>
  </div>
</template>

<script setup>
import { useUser } from '../composables/useUser'

const { 
  isAuthenticated, 
  currentUser, 
  isAdmin, 
  logout, 
  redirectToBackend 
} = useUser()

const handleLogout = async () => {
  await logout()
}
</script>
*/

// ========================================
// 4. GLOBAL HELPER FUNCTIONS
// ========================================

// Check authentication for any page
function requireAuth() {
    if (!isAuthenticated()) {
        window.location.href = '/login';
        return false;
    }
    return true;
}

// Auto-redirect based on user state
function autoRedirect() {
    const currentPath = window.location.pathname;
    const isAuth = isAuthenticated();
    
    // If on login page but already authenticated
    if (currentPath === '/login' && isAuth) {
        window.location.href = '/dashboard';
    }
    
    // If on protected page but not authenticated
    const protectedPaths = ['/dashboard', '/profile', '/admin'];
    if (protectedPaths.includes(currentPath) && !isAuth) {
        window.location.href = '/login';
    }
}

// Initialize auto-redirect on page load
document.addEventListener('DOMContentLoaded', autoRedirect);

// Periodic session check
setInterval(async () => {
    if (isAuthenticated()) {
        const isValid = await userContext.validateSession();
        if (!isValid) {
            alert('Session expired. Please login again.');
            window.location.href = '/login';
        }
    }
}, 5 * 60 * 1000); // Check every 5 minutes
