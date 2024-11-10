<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class AuthController extends Controller
{
    // User Registration
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // User Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate JWTs
        $accessToken = $this->createJwtToken($user, 'access');
        $refreshToken = $this->createJwtToken($user, 'refresh');

        // Store the refresh token in the database
        $user->refresh_token = $refreshToken;
        $user->save();

        // Set the refresh token as an HTTP-only, SameSite Strict cookie
        $cookie = cookie(
            'refresh_token',
            $refreshToken,
            (int) env('REFRESH_TOKEN_LIFESPAN', 604800) / 60, // Cookie lifespan in minutes
            '/',
            null,
            true, // Secure (only sent over HTTPS)
            true, // HttpOnly (not accessible via JavaScript)
            false, // Raw (should be false)
            'Strict' // SameSite Strict
        );

        // Return the access token and set the refresh token cookie
        return response()->json(['access_token' => $accessToken], 200)->withCookie($cookie);
    }

    // Create JWT Token
    private function createJwtToken($user, $type) {
        $secret = $type === 'access'
            ? env('ACCESS_TOKEN_SECRET')
            : env('REFRESH_TOKEN_SECRET');

        $expiry = $type === 'access'
            ? (int) env('ACCESS_TOKEN_LIFESPAN', 900)  // Default: 15 minutes
            : (int) env('REFRESH_TOKEN_LIFESPAN', 604800);  // Default: 7 days

        $payload = [
            'sub' => $user->id,
            'type' => $type,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addSeconds($expiry)->timestamp,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    // Refresh Token
public function refresh(Request $request)
{
    $request->validate(['refresh_token' => 'required']);

    $decoded = $this->decodeJwtToken($request->cookie('refresh_token'), 'refresh');

    if (!$decoded || $decoded->type !== 'refresh') {
        return response()->json(['error' => 'Invalid refresh token'], 401);
    }

    $user = User::find($decoded->sub);

    if (!$user) {
        return response()->json(['error' => 'Invalid refresh token'], 401);
    }

    // Generate new tokens
    $accessToken = $this->createJwtToken($user, 'access');
    $refreshToken = $this->createJwtToken($user, 'refresh');

    // Set the refresh token as an HTTP-only, SameSite Strict cookie
    $cookie = cookie(
        'refresh_token',
        $refreshToken,
        (int) env('REFRESH_TOKEN_LIFESPAN', 604800) / 60, // Cookie lifespan in minutes
        '/',
        null,
        true, // Secure (only sent over HTTPS)
        true, // HttpOnly (not accessible via JavaScript)
        false, // Raw (should be false)
        'Strict' // SameSite Strict
    );

    // Return the new access token and set the refresh token cookie
    return response()->json(['access_token' => $accessToken], 200)->withCookie($cookie);
}


    // Decode JWT Token
    private function decodeJwtToken($token, $type) {
        $secret = $type === 'access'
            ? env('ACCESS_TOKEN_SECRET')
            : env('REFRESH_TOKEN_SECRET');

        try {
            return JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    // Get Authenticated User
    public function user(Request $request) {
        $token = $request->bearerToken();
        $decoded = $this->decodeJwtToken($token, 'access');

        if (!$decoded || $decoded->type !== 'access') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::find($decoded->sub);

        return response()->json($user);
    }

    // User Logout
    public function logout(Request $request) {
        // Get the current user
        $user = $request->user();

        // Clear the refresh token in the database
        if ($user) {
            $user->refresh_token = null;
            $user->save();
        }

        // Clear the refresh token cookie
        $cookie = cookie('refresh_token', '', -1, '/', null, true, true, false, 'Strict');

        return response()->json(['message' => 'Logged out successfully'], 200)->withCookie($cookie);
    }

}
