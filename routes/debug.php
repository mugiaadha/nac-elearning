<?php

Route::get('/debug-session/{key}', function ($key) {
    $sessionData = cache($key);
    
    if ($sessionData) {
        return response()->json([
            'found' => true,
            'session_key' => $key,
            'data' => $sessionData,
            'expires_in' => cache()->getRedis()->ttl($key) . ' seconds'
        ]);
    } else {
        return response()->json([
            'found' => false,
            'session_key' => $key,
            'message' => 'Session not found or expired'
        ]);
    }
});
