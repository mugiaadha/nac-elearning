<?php

use Illuminate\Http\Client\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/session-login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login success']);
    }
    return response()->json(['message' => 'Invalid credentials'], 401);
});
