# Site Settings API Documentation

## Base URL
```
/api/site-settings
```

## Endpoints

### 1. Get All Site Settings
**GET** `/api/site-settings`

**Response:**
```json
{
    "success": true,
    "data": {
        "title": "NAC E-Learning",
        "description": "Platform pembelajaran online",
        "keywords": "elearning, education, online",
        "logo": "path/to/logo.png",
        "favicon": "path/to/favicon.ico",
        "email": "info@nac.com",
        "phone": "+6281234567890",
        "address": "Jakarta, Indonesia",
        "facebook": "https://facebook.com/nac",
        "twitter": "https://twitter.com/nac",
        "linkedin": "https://linkedin.com/company/nac",
        "instagram": "https://instagram.com/nac",
        "youtube": "https://youtube.com/nac",
        "whatsapp": "+6281234567890"
    },
    "message": "Data pengaturan situs berhasil diambil"
}
```

### 2. Clear Cache (Admin Only)
**DELETE** `/api/site-settings/cache`

**Response:**
```json
{
    "success": true,
    "data": {
        "tags_cleared": true,
        "key_cleared": true
    },
    "message": "Cache berhasil dibersihkan"
}
```

## Error Responses

### Field Not Allowed (400)
```json
{
    "success": false,
    "message": "Field tidak diizinkan"
}
```

### Data Not Found (404)
```json
{
    "success": false,
    "message": "Data pengaturan situs tidak ditemukan"
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Terjadi kesalahan pada server. Silakan coba lagi nanti."
}
```

## Environment Variables

Untuk Slack logging, tambahkan di `.env`:
```env
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK
```

## Features

- ✅ BaseController dengan try catch otomatis
- ✅ Logging error ke file dan Slack
- ✅ Selective field query (tidak select all)
- ✅ Input validation untuk security
- ✅ Consistent API response format
- ✅ Multiple endpoint variations for different needs
- ✅ **Smart caching system with multiple strategies**
- ✅ **Cache with tags for selective clearing**
- ✅ **Auto cache key generation**
- ✅ **Cache TTL optimization (24h for site settings)**

## Cache Information

### Cache TTL (Time To Live)
- **All settings**: 24 hours (1440 minutes)

### Cache Keys Generated
- `site_settings_all:/api/site-settings:GET:hash`

### Cache Tags (for Redis/Memcached)
- `site_settings` - All site settings data

### Cache Clearing
Otomatis ketika admin update site settings, atau manual via:
```bash
DELETE /api/site-settings/cache
```
