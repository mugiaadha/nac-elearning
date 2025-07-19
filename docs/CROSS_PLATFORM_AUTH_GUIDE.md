# Cross-Platform Authentication Guide

## Overview
Sistem authentication yang memungkinkan sharing session/login antara backend Laravel dan frontend yang berbeda stack. User bisa login di frontend dan session-nya bisa digunakan di backend Laravel admin panel.

## Architecture

### 1. **Multiple Authentication Methods**
- **API Token (Sanctum)** - Untuk frontend (React, Vue, etc.)
- **Laravel Session** - Untuk backend admin panel
- **Cache Session** - Bridge untuk cross-platform

### 2. **Session Storage**
- **Laravel Session** - Traditional web session
- **Redis/Cache** - Cross-platform session dengan key `user_session_{user_id}`
- **Sanctum Token** - API authentication dengan expiration

## API Endpoints

### Authentication Routes

#### 1. Login
**POST** `/api/auth/login`

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "remember_me": false
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "1|abcd1234...",
        "token_type": "Bearer",
        "expires_at": "2025-07-20 10:30:00",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "user"
        },
        "session_key": "user_session_1"
    },
    "message": "Login berhasil"
}
```

#### 2. Logout
**POST** `/api/auth/logout`
```
Headers: Authorization: Bearer {token}
```

#### 3. Get User Info
**GET** `/api/auth/me`
```
Headers: Authorization: Bearer {token}
```

#### 4. Refresh Token
**POST** `/api/auth/refresh`
```
Headers: Authorization: Bearer {token}
```

#### 5. Validate Token (For Backend Integration)
**POST** `/api/auth/validate-token`

**Request:**
```json
{
    "token": "1|abcd1234..."
}
```

#### 6. Check Session (Cross-Platform)
**POST** `/api/auth/check-session`

**Request:**
```json
{
    "session_key": "user_session_1"
}
```

## Frontend Implementation

### React/Vue Example
```javascript
// Login
const login = async (email, password, rememberMe = false) => {
    const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email,
            password,
            remember_me: rememberMe
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Store token and session key
        localStorage.setItem('auth_token', data.data.token);
        localStorage.setItem('session_key', data.data.session_key);
        localStorage.setItem('user', JSON.stringify(data.data.user));
        
        return data.data;
    }
    
    throw new Error(data.message);
};

// API calls with token
const apiCall = async (url, options = {}) => {
    const token = localStorage.getItem('auth_token');
    
    return fetch(url, {
        ...options,
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            ...options.headers
        }
    });
};

// Check if logged in
const isLoggedIn = () => {
    const token = localStorage.getItem('auth_token');
    const user = localStorage.getItem('user');
    return token && user;
};
```

## Backend Laravel Integration

### 1. Using Helper Class
```php
use App\Helpers\CrossPlatformAuth;

// In your controller
public function dashboard(Request $request)
{
    // Get user from any authentication method
    $user = CrossPlatformAuth::getUser(
        $request->bearerToken(),
        $request->input('session_key')
    );
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    return view('admin.dashboard', compact('user'));
}

// Check if authenticated
public function someMethod(Request $request)
{
    if (!CrossPlatformAuth::check($request->bearerToken())) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $userId = CrossPlatformAuth::getUserId($request);
    // Your logic here
}
```

### 2. Using Middleware
```php
// Register middleware in app/Http/Kernel.php
protected $routeMiddleware = [
    'cross.auth' => \App\Http\Middleware\CrossPlatformAuth::class,
];

// Use in routes
Route::middleware('cross.auth')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
});
```

### 3. Traditional Web Routes with Cross-Platform Support
```php
// In web.php
Route::get('/admin/login-bridge', function (Request $request) {
    $sessionKey = $request->query('session_key');
    $token = $request->query('token');
    
    $user = CrossPlatformAuth::getUser($token, $sessionKey);
    
    if ($user) {
        // Login user in Laravel session
        auth()->login($user);
        return redirect()->route('admin.dashboard');
    }
    
    return redirect()->route('login');
});
```

## Usage Scenarios

### Scenario 1: User Login di Frontend, Akses Backend
```javascript
// Frontend login
const loginData = await login('user@example.com', 'password');

// Redirect ke backend dengan session key
window.location = `/admin/login-bridge?session_key=${loginData.session_key}`;
```

### Scenario 2: Backend Check Frontend Login Status
```php
// In backend controller
public function checkFrontendAuth(Request $request)
{
    $sessionKey = $request->input('session_key');
    $sessionData = CrossPlatformAuth::validateSession($sessionKey);
    
    if ($sessionData) {
        $user = User::find($sessionData['user_id']);
        auth()->login($user);
        return redirect()->intended('/admin/dashboard');
    }
    
    return redirect()->route('login');
}
```

### Scenario 3: API Call dari Backend ke Frontend System
```php
// Backend to frontend API call
public function syncWithFrontend()
{
    $user = auth()->user();
    $sessionKey = CrossPlatformAuth::generateSessionKey($user->id);
    
    $response = Http::withHeaders([
        'X-Session-Key' => $sessionKey
    ])->get('https://frontend-app.com/api/sync');
    
    return $response->json();
}
```

## Configuration

### Environment Variables
```env
# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,localhost:3000,frontend-app.com
SESSION_DOMAIN=.yourdomain.com

# Cache for cross-platform sessions
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
```

### Sanctum Config (config/sanctum.php)
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort()
))),

'expiration' => 1440, // 24 hours default
```

## Security Features

### 1. **Token Expiration**
- Default: 24 hours
- Remember me: 30 days
- Configurable per login

### 2. **Session Validation**
- Token validation before each request
- Cache session expiration check
- User existence validation

### 3. **Cross-Platform Session Management**
- Automatic cleanup of expired sessions
- Single logout affects all platforms
- Session key rotation on refresh

### 4. **Multiple Authentication Guards**
- API guard for frontend
- Web guard for backend
- Automatic guard switching

## Best Practices

### 1. **Frontend**
- Store token in localStorage (atau secure cookie)
- Include session_key untuk emergency access
- Implement automatic token refresh
- Handle logout across tabs

### 2. **Backend**
- Always validate session before important operations
- Log authentication events
- Implement proper CORS settings
- Use HTTPS in production

### 3. **Cache Management**
- Set appropriate TTL for sessions
- Clean up expired sessions regularly
- Monitor cache usage

## Troubleshooting

### Common Issues

1. **CORS Errors**
```php
// In config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000', 'https://frontend-app.com'],
'supports_credentials' => true,
```

2. **Session Not Found**
```php
// Check cache connection
Cache::put('test', 'value', 60);
$test = Cache::get('test');
```

3. **Token Expiration**
```javascript
// Frontend token refresh
const refreshToken = async () => {
    const response = await fetch('/api/auth/refresh', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
    });
    
    const data = await response.json();
    if (data.success) {
        localStorage.setItem('auth_token', data.data.token);
    }
};
```

## Migration from Traditional Auth

### Step 1: Install Sanctum (if not installed)
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Step 2: Update User Model
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
```

### Step 3: Update Existing Controllers
```php
// Replace this:
if (!auth()->check()) {
    return redirect()->route('login');
}

// With this:
if (!CrossPlatformAuth::check($request->bearerToken(), $request->input('session_key'))) {
    return redirect()->route('login');
}
```

Dengan sistem ini, user bisa login di mana saja (frontend/backend) dan session-nya bisa dipakai di platform lain! 🚀
