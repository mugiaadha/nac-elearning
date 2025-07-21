# Debug Session Key Login Issue

## 🔍 **Troubleshooting Steps**

### **Step 1: Verify Session Key Exists**

Akses URL debug untuk cek apakah session key ada di cache:
```
http://202.10.41.215/debug-session/user_session_3
```

**Expected Response jika session ada:**
```json
{
    "found": true,
    "session_key": "user_session_3",
    "data": {
        "user_id": 3,
        "email": "user@gmail.com",
        "name": "User",
        "role": "user",
        "token": "token_3_...",
        "logged_in_at": "2025-07-21 10:00:00",
        "expires_at": "2025-07-22 10:00:00"
    },
    "cache_driver": "redis",
    "expires_in": "Available"
}
```

**Response jika session tidak ada:**
```json
{
    "found": false,
    "session_key": "user_session_3",
    "message": "Session not found or expired",
    "cache_driver": "redis"
}
```

### **Step 2: Check Laravel Logs**

Buka log file untuk lihat debug info dari middleware:
```bash
# Windows
type storage\logs\laravel.log

# Atau cari entry dengan "CrossPlatformAuth"
findstr "CrossPlatformAuth" storage\logs\laravel.log
```

**Expected Log Entries:**
```
[2025-07-21 10:00:00] local.INFO: CrossPlatformAuth: Checking session key {"session_key":"user_session_3"}
[2025-07-21 10:00:01] local.INFO: CrossPlatformAuth: Session data from cache {"data":{"user_id":3,"email":"user@gmail.com"}}
[2025-07-21 10:00:02] local.INFO: CrossPlatformAuth: User found {"user_id":3,"user":{"id":3,"name":"User"}}
[2025-07-21 10:00:03] local.INFO: CrossPlatformAuth: User authenticated successfully {"user_id":3}
```

### **Step 3: Verify Route Middleware**

Check apakah route `/dashboard` menggunakan middleware yang benar:
```bash
php artisan route:list | findstr dashboard
```

**Expected Output:**
```
GET     dashboard    cross.auth    App\Http\Controllers\...
```

### **Step 4: Test Session Key Format**

Karena kita sudah update format session key, coba test dengan format baru:

1. **Login ulang untuk generate session key baru**
2. **Copy session key dari response**
3. **Test dengan URL baru**

## 🚨 **Kemungkinan Masalah**

### **1. Session Key Expired atau Tidak Ada**
```bash
# Check cache driver
php artisan config:cache
php artisan cache:clear
```

### **2. Middleware Tidak Terpasang**
Check file `app/Http/Kernel.php`:
```php
protected $middlewareAliases = [
    'cross.auth' => \App\Http\Middleware\CrossPlatformAuth::class,
];
```

### **3. Cache Driver Issue**
Check `.env` file:
```env
CACHE_DRIVER=redis
# atau
CACHE_DRIVER=file
```

### **4. Route Conflict**
Pastikan tidak ada conflict dengan route `/dashboard` lain:
```bash
php artisan route:list | findstr dashboard
```

## 🔧 **Quick Fix Commands**

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recreate config cache
php artisan config:cache
```

## 🧪 **Manual Test**

### **1. Test API Login Dulu**
```javascript
// Di browser console atau Postman
fetch('http://202.10.41.215/api/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        email: 'user@gmail.com',
        password: 'password'
    })
})
.then(r => r.json())
.then(data => {
    console.log('Login response:', data);
    
    if (data.success) {
        const sessionKey = data.data.session_key;
        console.log('Session key:', sessionKey);
        
        // Test dashboard access
        window.open(`http://202.10.41.215/dashboard?session_key=${sessionKey}`, '_blank');
    }
});
```

### **2. Test Session Key Validation**
```javascript
// Test session key via API
fetch('http://202.10.41.215/api/auth/check-session', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        session_key: 'user_session_3'
    })
})
.then(r => r.json())
.then(data => {
    console.log('Session validation:', data);
});
```

## 🎯 **Debugging Checklist**

- [ ] Session key ada di cache (`/debug-session/user_session_3`)
- [ ] Middleware terpasang di `app/Http/Kernel.php`
- [ ] Route `/dashboard` menggunakan middleware `cross.auth`
- [ ] Log menunjukkan proses authentication
- [ ] Cache driver bekerja normal
- [ ] Session key format benar

## 📝 **Common Solutions**

### **Solution 1: Session Key Expired**
```bash
# Login ulang untuk generate session key baru
# Copy session key yang baru dari response
```

### **Solution 2: Cache Issue**
```bash
php artisan cache:clear
php artisan config:cache
```

### **Solution 3: Middleware Issue**
```php
// Pastikan di routes/web.php
Route::get('/dashboard', function () {
    return view('frontend.dashboard.index');
})->middleware(['cross.auth'])->name('dashboard');
```

### **Solution 4: Check User Exists**
```bash
# Check apakah user ID 3 ada di database
php artisan tinker
>>> App\Models\User::find(3)
```

Mari test debug URL dulu untuk lihat apakah session key ada di cache! 🔍
