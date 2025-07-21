<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Simple debug routes tanpa log
Route::get('/test-session/{key}', function ($key) {
    $sessionData = cache($key);
    
    if ($sessionData) {
        return response()->json([
            'status' => 'FOUND',
            'session_key' => $key,
            'user_id' => $sessionData['user_id'] ?? 'unknown',
            'email' => $sessionData['email'] ?? 'unknown',
            'data' => $sessionData
        ]);
    } else {
        return response()->json([
            'status' => 'NOT_FOUND',
            'session_key' => $key,
            'message' => 'Session tidak ditemukan atau expired'
        ]);
    }
});

// Test auth status dengan session key
Route::get('/test-auth', function (Request $request) {
    $sessionKey = $request->input('session_key');
    
    return response()->json([
        'has_session_key' => $sessionKey ? 'YES' : 'NO',
        'session_key_value' => $sessionKey,
        'laravel_auth' => auth()->check() ? 'YES' : 'NO',
        'user_id' => auth()->check() ? auth()->user()->id : null,
        'user_name' => auth()->check() ? auth()->user()->name : null,
        'middleware_ran' => 'YES'
    ]);
})->middleware(['cross.auth']);

// Test middleware directly
Route::get('/test-middleware/{sessionKey}', function ($sessionKey) {
    // Manual test middleware logic
    $sessionData = cache($sessionKey);
    
    if ($sessionData && isset($sessionData['user_id'])) {
        $user = \App\Models\User::find($sessionData['user_id']);
        
        if ($user) {
            // Set user manually
            auth()->setUser($user);
            
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'User authenticated manually',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'auth_check' => auth()->check() ? 'YES' : 'NO'
            ]);
        } else {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'User not found in database',
                'user_id' => $sessionData['user_id']
            ]);
        }
    } else {
        return response()->json([
            'status' => 'ERROR',
            'message' => 'Session data invalid or not found',
            'session_data' => $sessionData
        ]);
    }
});
