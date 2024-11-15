<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class AuthController extends Controller
{
    // generate and store a new refresh token
    private function createRefreshToken(string $userId, Carbon $now): string
    {
        // generate new refresh token
        $tokenSecret = env('REFRESH_TOKEN_SECRET');
        $tokenLifespan = env('REFRESH_TOKEN_LIFESPAN', 604800);
        $exp = $now->copy()->addSeconds((int)$tokenLifespan);

        // generate new refresh token with the user id
        $token = JWT::encode([
            'sub' => $userId,
            'type' => 'refresh',
            'iat' => $now->timestamp,
            'exp' => $exp->timestamp,
        ], $tokenSecret, 'HS256');

        // store the refresh token in the database
        Token::create([
            'token' => $token,
            'user_id' => $userId,
            'token_type' => 'refresh',
            'expires_at' =>  $exp,
        ]);

        // return the token string
        return $token;
    }

    // generate a new access token
    private function createAccessToken(string $userId, Carbon $now): string
    {
        // generate new access token
        $tokenSecret = env('ACCESS_TOKEN_SECRET');
        $tokenLifespan = env('ACCESS_TOKEN_LIFESPAN', 604800);
        $exp = $now->copy()->addSeconds((int)$tokenLifespan);

        // generate new access token with the user id
        $token = JWT::encode([
            'sub' => $userId,
            'type' => 'access',
            'iat' => $now->timestamp,
            'exp' => $exp->timestamp,
        ], $tokenSecret, 'HS256');
        
        // return the token string
        return $token;
    }

    // user registration
    public function register(Request $request) 
    {
        // validate request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // create the user in the database
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // return HTTP 201 Created with no content
        return response()->noContent(201);
    }

    // user login
    public function login(Request $request)
    {
        // validate request
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // fetch user from database
        $user = User::where('email', $request->email)->first();

        // check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // store the current time to ensure consistent timestamps
        $now = Carbon::now();

        // generate access token
        $accessToken = $this->createAccessToken($user->id, $now);


        // generate refresh token
        $refreshToken = $this->createRefreshToken($user->id, $now);

        // store the refresh token an cookie
        $refreshTokenLifespan = env('REFRESH_TOKEN_LIFESPAN', 604800);
        $cookieLifespan = (int)$refreshTokenLifespan / 60; # convert seconds to minutes
        $secure = App::environment('production'); // only send cookies over https in production
        $cookie = cookie(
            name: 'refresh_token',      // cookie name
            value: $refreshToken,       // refresh token value
            minutes: $cookieLifespan,   // cookie lifespan in minutes
            path: '/',                  // only accessible via /api/refresh
            secure: $secure,            // send HTTPS only
            httpOnly: true,             // HttpOnly (protect against XSS attacks)
            sameSite: 'Strict'          // SameSite Strict (protect against CSRF attacks)
        );

        // return the access token in the body and refresh token in a cookie
        return response()->json(['access_token' => $accessToken], 200)->withCookie($cookie);
    }

    // refresh access token using refresh token
    public function refresh(Request $request)
    {
        // get the refresh token from the cookie
        $refreshToken = $request->cookie('refresh_token');

        // verify refresh token exists
        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token not found'], 401);
        }

        // decode the refresh token
        try {
            $secret = env('REFRESH_TOKEN_SECRET');
            $decoded = JWT::decode($refreshToken, new Key($secret, 'HS256'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        // verify the token type
        if (($decoded->type ?? null) !== 'refresh') {
            return response()->json(['message' => 'Invalid refresh token type'], 401);
        }

        // verify that the refresh token hasn't been revoked
        $tokenRecord = Token::where('token', $refreshToken)->first();

        if (!$tokenRecord) {
            // clear the refesh token cookie if the token is revoked
            $cookie = cookie('refresh_token', '', 0);
            return response()->json(['message' => 'Refresh token has been revoked'], 401)->withCookie($cookie);
        }

        // get the user using the 'sub' claim from the token
        $user = User::find($decoded->sub);

        // verify the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // store the current time to ensure consistent timestamps
        $now = Carbon::now();
        
        // generate new access token
        $accessToken = $this->createAccessToken($user->id, $now);

        // return the access token in the body
        return response()->json(['access_token' => $accessToken], 200);
    }

    // user logout
    public function logout(Request $request) {
        // get the refresh token from the cookie
        $refreshToken = $request->cookie('refresh_token');

        // verify refresh token exists
        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token not found'], 401);
        }

        // decode the refresh token
        try {
            $secret = env('REFRESH_TOKEN_SECRET');
            $decoded = JWT::decode($refreshToken, new Key($secret, 'HS256'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        // get the user using the 'sub' claim from the token
        $user = User::find($decoded->sub);

        // verify the user exists
        if (!$user) {
            return response()->json(['message' => 'Invalid user'], 401);
        }

        // invalidate the token by deleting it from the database
        Token::where('token', $refreshToken)->delete();


        // clear the refresh token cookie
        $cookie = cookie('refresh_token', '', 0);

        return response()->noContent(204);

    }

    // get authenticated user
    public function user(Request $request) {
        $user = $request->user();
        return response()->json($user);
    }

}
