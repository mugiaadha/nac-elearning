# Cache Implementation Guide

## BaseController Cache Methods

BaseController sudah dilengkapi dengan beberapa method untuk caching yang praktis dan mudah digunakan:

### 1. `cacheOrExecute()` - Simple Cache with Auto Response
```php
// Paling praktis - auto return response dengan try catch
return $this->cacheOrExecute(
    'cache_key', 
    function() {
        return Model::all(); // Your data logic
    },
    60, // TTL in minutes
    'Data berhasil diambil', // Success message
    'Context for error logging'
);
```

### 2. `cacheResponse()` - Basic Cache with Try Catch
```php
$result = $this->cacheResponse(
    'cache_key',
    function() {
        return Model::all();
    },
    60, // TTL in minutes
    'Context for error logging'
);
return $this->sendResponse($result, 'Success message');
```

### 3. `cacheWithTags()` - Cache with Tags (Redis/Memcached)
```php
$result = $this->cacheWithTags(
    'cache_key',
    ['tag1', 'tag2'], // Tags for selective clearing
    function() {
        return Model::all();
    },
    60, // TTL in minutes
    'Context for error logging'
);
```

### 4. Helper Methods

#### Generate Cache Key
```php
$cacheKey = $this->generateCacheKey('prefix', ['param' => 'value']);
// Result: prefix:/api/endpoint:GET:hash_of_query:hash_of_params
```

#### Clear Cache
```php
$this->clearCache('cache_key'); // Single key
$this->clearCacheByTags(['tag1', 'tag2']); // By tags
```

#### Cache Debug Info
```php
$info = $this->getCacheInfo('cache_key');
// Returns: key, exists, has_data, type, size
```

## Implementation Examples

### Example 1: Simple Endpoint (Most Common)
```php
public function getAllCourses()
{
    $cacheKey = $this->generateCacheKey('courses_all');
    
    return $this->cacheOrExecute(
        $cacheKey,
        function () {
            return Course::with('category')
                ->where('status', 'active')
                ->get();
        },
        60, // Cache for 1 hour
        'Daftar kursus berhasil diambil',
        'Getting all courses'
    );
}
```

### Example 2: With Parameters
```php
public function getCoursesByCategory($categoryId)
{
    $cacheKey = $this->generateCacheKey('courses_by_category', [
        'category_id' => $categoryId
    ]);
    
    return $this->cacheOrExecute(
        $cacheKey,
        function () use ($categoryId) {
            return Course::where('category_id', $categoryId)
                ->where('status', 'active')
                ->get();
        },
        30, // Cache for 30 minutes
        'Kursus berdasarkan kategori berhasil diambil',
        "Getting courses by category: {$categoryId}"
    );
}
```

### Example 3: With Tags for Selective Clearing
```php
public function getUserProfile($userId)
{
    $cacheKey = $this->generateCacheKey('user_profile', ['user_id' => $userId]);
    
    return $this->cacheWithTags(
        $cacheKey,
        ['users', 'profiles', "user_{$userId}"],
        function () use ($userId) {
            return User::with(['profile', 'courses'])
                ->findOrFail($userId);
        },
        120, // Cache for 2 hours
        'Getting user profile'
    );
}

// Clear specific user cache
public function updateUserProfile($userId, $data)
{
    // Update user logic here...
    
    // Clear related caches
    $this->clearCacheByTags(["user_{$userId}", 'profiles']);
    
    return $this->sendResponse($user, 'Profile updated successfully');
}
```

### Example 4: Without Auto Response (More Control)
```php
public function getStatistics()
{
    try {
        $cacheKey = $this->generateCacheKey('dashboard_stats');
        
        $stats = $this->cacheResponse(
            $cacheKey,
            function () {
                return [
                    'total_users' => User::count(),
                    'total_courses' => Course::count(),
                    'total_orders' => Order::count(),
                    'revenue' => Order::sum('total_amount')
                ];
            },
            30, // Cache for 30 minutes
            'Getting dashboard statistics'
        );

        // Additional processing if needed
        $stats['formatted_revenue'] = 'Rp ' . number_format($stats['revenue']);

        return $this->sendResponse($stats, 'Statistik berhasil diambil');
        
    } catch (Exception $e) {
        return $this->handleException($e, 'Getting dashboard statistics');
    }
}
```

## Cache Configuration

### Recommended Cache Drivers

1. **Redis** (Recommended for production)
   - Supports tags
   - High performance
   - Persistent storage

2. **Memcached** 
   - Supports tags
   - Good performance
   - Memory-only

3. **File/Database** (Development only)
   - No tags support
   - Lower performance

### Environment Configuration

Add to `.env`:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Best Practices

### 1. Cache TTL Guidelines
- **Static data** (site settings): 24 hours (1440 minutes)
- **Semi-static data** (categories, courses): 1-6 hours (60-360 minutes)
- **User data** (profiles, preferences): 1-2 hours (60-120 minutes)
- **Dynamic data** (cart, real-time stats): 5-15 minutes
- **Frequently changing** (notifications): No cache or 1-5 minutes

### 2. Cache Key Naming
```php
// Good
'courses_all'
'courses_by_category'
'user_profile'
'dashboard_stats'

// Bad
'data'
'cache1'
'temp'
```

### 3. Use Tags for Related Data
```php
// Group related caches
['users', 'profiles', 'user_123']
['courses', 'categories', 'education']
['orders', 'payments', 'user_456']
```

### 4. Clear Cache Strategically
```php
// When data changes
public function updateCourse($id, $data)
{
    $course = Course::findOrFail($id);
    $course->update($data);
    
    // Clear related caches
    $this->clearCacheByTags(['courses', 'categories']);
    
    return $this->sendResponse($course, 'Course updated');
}
```

### 5. Error Handling
Cache methods sudah include try-catch dan logging otomatis. Jika cache fail, akan return error response dengan logging ke Slack.

## Migration from Non-Cached Controller

### Before:
```php
public function index()
{
    try {
        $data = Model::all();
        return $this->sendResponse($data, 'Success');
    } catch (Exception $e) {
        return $this->handleException($e, 'Getting data');
    }
}
```

### After:
```php
public function index()
{
    $cacheKey = $this->generateCacheKey('model_all');
    
    return $this->cacheOrExecute(
        $cacheKey,
        function () {
            return Model::all();
        },
        60,
        'Data berhasil diambil',
        'Getting data'
    );
}
```

Jauh lebih simpel dan sudah include error handling + caching!
