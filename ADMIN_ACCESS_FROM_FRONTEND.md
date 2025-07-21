# Admin Access dari Frontend

## 🎯 Route Admin yang Tersedia

### **1. Standard Laravel Admin Routes**
```php
// Dengan middleware auth Laravel biasa
Route::middleware(['auth', 'roles:admin'])->group(function () {
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin');
    
    Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');
    // ... dan route admin lainnya
});
```

### **2. Cross-Platform Admin Routes**
```php
// Dengan middleware cross.auth untuk frontend integration
Route::middleware(['cross.auth'])->group(function () {
    Route::get('/admin', function () {
        if (auth()->user() && auth()->user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/login')->with('error', 'Access denied');
    });
    
    Route::get('/admin/dashboard', function () {
        if (auth()->user() && auth()->user()->role === 'admin') {
            return app(AdminController::class)->AdminDashboard();
        }
        return redirect('/login')->with('error', 'Access denied');
    });
});
```

## 🚀 Cara Akses Admin dari Frontend

### **1. Redirect Otomatis setelah Login**

```javascript
// Di TypeScript/React frontend
const loginUser = async (e: any) => {
  e.preventDefault();
  setLoading(true);
  
  try {
    const response = await fetch('/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email: loginData.email,
        password: loginData.password,
        remember_me: loginData.rememberMe || false
      })
    });

    const data = await response.json();

    if (data.success) {
      // Store auth data
      localStorage.setItem("api_token", data.data.token);
      localStorage.setItem("session_key", data.data.session_key);
      localStorage.setItem("user", JSON.stringify(data.data.user));
      
      toast.success(`Welcome back, ${data.data.user.name}!`);
      
      // Role-based redirect
      const user = data.data.user;
      if (user.role === 'admin') {
        // Redirect ke Laravel admin dengan session key
        const sessionKey = data.data.session_key;
        window.location.href = `/admin?session_key=${sessionKey}`;
      } else {
        // Redirect ke dashboard user biasa
        router.push('/dashboard');
      }
    }
  } catch (err: any) {
    toast.error(err.message || "Terjadi kesalahan");
  } finally {
    setLoading(false);
  }
};
```

### **2. Manual Redirect ke Admin**

```javascript
// Function untuk redirect manual ke admin
const goToAdminPanel = () => {
  const sessionKey = localStorage.getItem('session_key');
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  
  if (user.role === 'admin' && sessionKey) {
    window.location.href = `/admin?session_key=${sessionKey}`;
  } else {
    alert('Access denied: Admin role required');
  }
};

// Button di frontend
<button onClick={goToAdminPanel} className="admin-btn">
  🔐 Admin Panel
</button>
```

### **3. Direct URL Access**

```
// URL yang bisa diakses langsung
https://yourdomain.com/admin?session_key=user_session_123
https://yourdomain.com/admin/dashboard?session_key=user_session_123
```

## 🔒 Security & Access Control

### **Middleware Priority:**
1. **cross.auth** - Validasi token/session dari frontend
2. **Role check** - Verifikasi user adalah admin
3. **Redirect** - Arahkan ke dashboard admin atau login

### **Flow Authentication:**
```
Frontend Login → API Token → Session Key → Laravel Admin
    ↓              ↓           ↓             ↓
  React/Vue    Cache Token   Cross Auth   Admin Panel
```

## 🎨 Frontend Integration Examples

### **React Component untuk Admin Access**

```tsx
import React from 'react';
import { useRouter } from 'next/router';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

const AdminAccessButton: React.FC = () => {
  const router = useRouter();
  
  const user: User = JSON.parse(localStorage.getItem('user') || '{}');
  const sessionKey = localStorage.getItem('session_key');
  
  const handleAdminAccess = () => {
    if (user.role === 'admin' && sessionKey) {
      // Redirect ke Laravel admin
      window.location.href = `/admin?session_key=${sessionKey}`;
    } else {
      alert('Access denied: Admin privileges required');
    }
  };
  
  // Only show button if user is admin
  if (user.role !== 'admin') {
    return null;
  }
  
  return (
    <button 
      onClick={handleAdminAccess}
      className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
    >
      🛠️ Admin Panel
    </button>
  );
};

export default AdminAccessButton;
```

### **Vue.js Component untuk Admin Access**

```vue
<template>
  <button 
    v-if="isAdmin"
    @click="goToAdmin"
    class="admin-button"
  >
    🔧 Admin Dashboard
  </button>
</template>

<script setup>
import { computed } from 'vue'

const user = computed(() => {
  const userData = localStorage.getItem('user')
  return userData ? JSON.parse(userData) : null
})

const isAdmin = computed(() => user.value?.role === 'admin')

const goToAdmin = () => {
  const sessionKey = localStorage.getItem('session_key')
  
  if (isAdmin.value && sessionKey) {
    window.location.href = `/admin?session_key=${sessionKey}`
  } else {
    alert('Access denied')
  }
}
</script>

<style scoped>
.admin-button {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.admin-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
</style>
```

## 🔧 Testing & Troubleshooting

### **Test Admin Access:**

1. **Login sebagai admin di frontend**
2. **Check localStorage untuk token dan session_key**
3. **Access URL**: `/admin?session_key={your_session_key}`
4. **Verify redirect ke admin dashboard**

### **Debug Commands:**

```javascript
// Check auth status
console.log('Token:', localStorage.getItem('api_token'));
console.log('Session Key:', localStorage.getItem('session_key'));
console.log('User:', JSON.parse(localStorage.getItem('user') || '{}'));

// Test API auth
fetch('/api/auth/me', {
  headers: {
    'Authorization': `Bearer ${localStorage.getItem('api_token')}`,
    'Accept': 'application/json'
  }
}).then(r => r.json()).then(console.log);
```

### **Common Issues:**

| Issue | Solution |
|-------|----------|
| Access Denied | Check user role is 'admin' |
| Session Not Found | Verify session_key in localStorage |
| Redirect Loop | Check middleware order in web.php |
| Token Expired | Login again or refresh token |

## 📝 Summary

✅ **Admin route `/admin` sudah tersedia**  
✅ **Cross-platform authentication working**  
✅ **Role-based access control implemented**  
✅ **Frontend integration ready**  
✅ **Security measures in place**

Sekarang admin bisa login di frontend dan langsung akses admin panel Laravel! 🚀
