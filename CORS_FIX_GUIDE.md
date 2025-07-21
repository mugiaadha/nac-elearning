# 🔧 Quick CORS Fix & API Test

## 1. **Test API dengan CURL (Terminal)**

Buka PowerShell dan test API dulu:

```powershell
# Test server status
curl http://202.10.41.215

# Test API login
curl -X POST http://202.10.41.215/api/auth/login `
  -H "Content-Type: application/json" `
  -H "Accept: application/json" `
  -d '{\"email\":\"admin@gmail.com\",\"password\":\"123\"}'
```

## 2. **Jika Server Tidak Running**

Start Laravel server:
```powershell
cd c:\workspace\nac-elearning
php artisan serve --host=0.0.0.0 --port=8000
```

Atau dengan IP spesifik:
```powershell
php artisan serve --host=202.10.41.215 --port=80
```

## 3. **Update BASE_URL di Debug Tool**

Jika server running di localhost:
```javascript
const BASE_URL = 'http://localhost:8000';
```

Atau pakai IP lain:
```javascript
const BASE_URL = 'http://127.0.0.1:8000';
```

## 4. **Alternative - Test dengan Postman**

**URL:** `POST http://202.10.41.215/api/auth/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "email": "admin@gmail.com", 
    "password": "123"
}
```

## 5. **CORS Fix untuk Production**

Update `.env`:
```env
CORS_ALLOWED_ORIGINS=*
```

Atau specific domain:
```env
CORS_ALLOWED_ORIGINS=http://localhost,http://127.0.0.1,http://202.10.41.215
```

## 6. **Quick Test Commands**

```powershell
# Test basic connectivity
ping 202.10.41.215

# Test port
telnet 202.10.41.215 80

# Check Laravel server
cd c:\workspace\nac-elearning
php artisan route:list | findstr auth

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## 7. **If All Else Fails - Local Test**

Change debug tool to use localhost:
1. Start server: `php artisan serve`
2. Update `BASE_URL = 'http://localhost:8000'` in debug-session-test.html
3. Test dari browser yang sama

**Masalah CORS biasanya karena:**
- Server tidak running di IP yang benar
- Port tidak match
- Browser blocking cross-origin requests
- Laravel CORS middleware tidak aktif
