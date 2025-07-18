<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class BaseController extends Controller
{
    /**
     * Success response method
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = 'Success', $code = 200)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Return error response
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Handle try catch and send to slack
     *
     * @param Exception $exception
     * @param string $context
     * @return \Illuminate\Http\Response
     */
    protected function handleException(Exception $exception, $context = '')
    {
        // Log error ke file
        Log::error('API Error: ' . $context, [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Log ke Slack (jika sudah dikonfigurasi)
        try {
            Log::channel('slack')->error('API Error: ' . $context, [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => request()->url(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (Exception $slackException) {
            // Jika slack gagal, log ke file saja
            Log::error('Slack logging failed: ' . $slackException->getMessage());
        }

        // Return default error response
        return $this->sendError(
            'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
            [],
            500
        );
    }

    /**
     * Cache with try catch wrapper
     * 
     * @param string $cacheKey
     * @param callable $callback
     * @param int $ttl Time to live in minutes (default: 60)
     * @param string $context Context for error logging
     * @return \Illuminate\Http\Response
     */
    protected function cacheResponse($cacheKey, callable $callback, $ttl = 60, $context = '')
    {
        try {
            return Cache::remember($cacheKey, $ttl * 60, function () use ($callback) {
                return $callback();
            });
        } catch (Exception $e) {
            return $this->handleException($e, $context ?: 'Cache operation');
        }
    }

    /**
     * Cache with tags (for selective cache clearing)
     * 
     * @param string $cacheKey
     * @param array $tags
     * @param callable $callback
     * @param int $ttl Time to live in minutes (default: 60)
     * @param string $context Context for error logging
     * @return \Illuminate\Http\Response
     */
    protected function cacheWithTags($cacheKey, array $tags, callable $callback, $ttl = 60, $context = '')
    {
        try {
            // Check if cache driver supports tags
            if (in_array(config('cache.default'), ['redis', 'memcached'])) {
                return Cache::tags($tags)->remember($cacheKey, $ttl * 60, function () use ($callback) {
                    return $callback();
                });
            } else {
                // Fallback to regular cache if tags not supported
                return $this->cacheResponse($cacheKey, $callback, $ttl, $context);
            }
        } catch (Exception $e) {
            return $this->handleException($e, $context ?: 'Tagged cache operation');
        }
    }

    /**
     * Simple cache check and execute
     * 
     * @param string $cacheKey
     * @param callable $callback
     * @param int $ttl Time to live in minutes (default: 60)
     * @param string $successMessage
     * @param string $context Context for error logging
     * @return \Illuminate\Http\Response
     */
    protected function cacheOrExecute($cacheKey, callable $callback, $ttl = 60, $successMessage = 'Data berhasil diambil', $context = '')
    {
        try {
            $data = Cache::remember($cacheKey, $ttl * 60, function () use ($callback) {
                return $callback();
            });

            return $this->sendResponse($data, $successMessage);
        } catch (Exception $e) {
            return $this->handleException($e, $context ?: 'Cache or execute operation');
        }
    }

    /**
     * Generate cache key from request
     * 
     * @param string $prefix
     * @param array $additional Additional parameters for cache key
     * @return string
     */
    protected function generateCacheKey($prefix, array $additional = [])
    {
        $request = request();
        $keyParts = [
            $prefix,
            $request->getPathInfo(),
            $request->getMethod(),
            md5($request->getQueryString() ?: ''),
        ];

        if (!empty($additional)) {
            $keyParts[] = md5(serialize($additional));
        }

        return implode(':', array_filter($keyParts));
    }

    /**
     * Clear cache by key or pattern
     * 
     * @param string $key
     * @return bool
     */
    protected function clearCache($key)
    {
        try {
            return Cache::forget($key);
        } catch (Exception $e) {
            Log::error('Cache clear failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear cache by tags
     * 
     * @param array $tags
     * @return bool
     */
    protected function clearCacheByTags(array $tags)
    {
        try {
            if (in_array(config('cache.default'), ['redis', 'memcached'])) {
                Cache::tags($tags)->flush();
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::error('Tagged cache clear failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cache info (for debugging)
     * 
     * @param string $key
     * @return array
     */
    protected function getCacheInfo($key)
    {
        try {
            $exists = Cache::has($key);
            $value = $exists ? Cache::get($key) : null;
            
            return [
                'key' => $key,
                'exists' => $exists,
                'has_data' => !is_null($value),
                'type' => $exists ? gettype($value) : null,
                'size' => $exists ? strlen(serialize($value)) : 0
            ];
        } catch (Exception $e) {
            return [
                'key' => $key,
                'error' => $e->getMessage()
            ];
        }
    }
}
