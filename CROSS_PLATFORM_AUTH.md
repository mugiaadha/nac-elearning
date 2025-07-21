# Cross-Platform Authentication System

Sistem autentikasi yang memungkinkan sharing session antara frontend dan backend Laravel yang berbeda technology stack.

## Komponen Sistem

### 1. Backend API (Laravel)
- **AuthController**: Menangani login, logout, validasi token
- **CrossPlatformAuth Middleware**: Middleware untuk validasi multi-platform
- **BaseController**: Helper untuk caching dan error handling

### 2. Frontend JavaScript
- **auth.js**: Library untuk mengelola autentikasi di frontend

## Cara Kerja

### Token-Based Authentication
```
Frontend Login → API generates token → Store in cache & localStorage
Frontend Request → Send token in header → Middleware validates → Allow access
```

### Session Sharing
```
Frontend Token → Generate session_key → Store session data in cache
Backend Access → Use session_key → Retrieve session from cache → Set Laravel session
```

## API Endpoints

### Public Endpoints
```
POST /api/auth/login
POST /api/auth/validate-token
POST /api/auth/check-session
```

### Protected Endpoints (Require Authentication)
```
POST /api/auth/logout
GET /api/auth/me
POST /api/auth/refresh
GET /api/user
```

### Site Settings
```
GET /api/site-settings/
DELETE /api/site-settings/clear-cache
```

## Frontend Usage

### 1. Include auth.js
```html
<script src="/frontend/js/auth.js"></script>
```

### 2. Login User
```javascript
try {
    const result = await authManager.login('user@example.com', 'password');
    console.log('Login successful:', result);
    
    // Redirect to dashboard
    authManager.redirectToBackend('/dashboard');
} catch (error) {
    console.error('Login failed:', error.message);
}
```

### 3. Check Authentication Status
```javascript
if (authManager.isLoggedIn()) {
    const user = authManager.getUser();
    console.log('Current user:', user);
} else {
    console.log('User not logged in');
}
```

### 4. Make Authenticated API Requests
```javascript
try {
    const data = await authManager.apiRequest('/api/user');
    console.log('User data:', data);
} catch (error) {
    console.error('API request failed:', error);
}
```

### 5. Logout User
```javascript
await authManager.logout();
console.log('User logged out');
```

## Backend Integration

### 1. Web Routes (Laravel)
```php
// Use cross.auth middleware for protected routes
Route::middleware('cross.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

### 2. Manual Session Check
```php
// In any controller
public function dashboard(Request $request)
{
    // User automatically available if middleware passed
    $user = auth()->user();
    
    // Or check session manually
    if (session('user_id')) {
        $user = User::find(session('user_id'));
    }
    
    return view('dashboard', compact('user'));
}
```

### 3. Frontend to Backend Transition
```javascript
// From frontend, redirect to backend with session
authManager.redirectToBackend('/dashboard');

// Or manually construct URL
const sessionKey = localStorage.getItem('session_key');
window.location.href = `/dashboard?session_key=${sessionKey}`;
```

## Configuration

### 1. Middleware Registration
File: `app/Http/Kernel.php`
```php
protected $middlewareAliases = [
    // ... existing middleware
    'cross.auth' => \App\Http\Middleware\CrossPlatformAuth::class,
];
```

### 2. API Routes
File: `routes/api.php`
```php
// Use cross.auth instead of auth:sanctum
Route::middleware('cross.auth')->group(function () {
    // Protected routes
});
```

## Cache Keys

### Token Storage
```
api_token_{token} => user_id
```

### Session Storage
```
session_{session_key} => {
    user_id: 123,
    user_email: 'user@example.com',
    user_name: 'User Name',
    expires_at: '2024-01-01 00:00:00'
}
```

### Site Settings Cache
```
site_settings_all => [settings_data]
```

## Authentication Flow Examples

### 1. Frontend Login → Backend Access
```
1. User login di frontend
2. Frontend dapat token + session_key
3. User klik "Dashboard" 
4. Frontend redirect ke backend dengan session_key
5. Backend middleware baca session dari cache
6. User berhasil masuk dashboard Laravel
```

### 2. Backend Login → Frontend Access
```
1. User login di backend Laravel (traditional)
2. Backend generate session_key dan simpan di cache
3. User akses frontend dengan session_key
4. Frontend baca session dari cache
5. Frontend set token dan localStorage
```

### 3. API Access from Frontend
```
1. Frontend kirim request dengan Bearer token
2. Middleware validasi token dari cache
3. Set user untuk request
4. Controller process request dengan user data
```

## Error Handling

### Token Expired
```javascript
// Auto-handled in authManager.apiRequest()
// Akan clear localStorage dan redirect ke login
```

### Session Invalid
```php
// Middleware akan return 401 Unauthorized
// Frontend akan catch dan clear auth data
```

### API Errors
```javascript
try {
    const data = await authManager.apiRequest('/api/endpoint');
} catch (error) {
    if (error.message === 'Unauthorized') {
        // Redirect to login
        window.location.href = '/login';
    } else {
        // Handle other errors
        console.error('API Error:', error);
    }
}
```

## Security Considerations

1. **Token Expiration**: Token di-cache dengan TTL
2. **Session Validation**: Session key memiliki expiration time
3. **HTTPS Required**: Untuk production harus menggunakan HTTPS
4. **CSRF Protection**: Tetap aktif untuk form Laravel
5. **Rate Limiting**: API endpoints memiliki rate limiting

## Troubleshooting

### Problem: Token tidak valid
**Solution**: Clear localStorage dan login ulang
```javascript
authManager.clearAuth();
```

### Problem: Session expired
**Solution**: Refresh session atau login ulang
```php
// Check session expiration in backend
if (session('user_id') && !cache("session_" . request('session_key'))) {
    return redirect('/login');
}
```

### Problem: CORS issues
**Solution**: Configure CORS in Laravel
```php
// config/cors.php
'supports_credentials' => true,
'allowed_origins' => ['http://localhost:3000'], // frontend URL
```
