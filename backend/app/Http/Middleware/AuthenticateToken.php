<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;



class AuthenticateToken {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // retrieve the access token from the request
        $token = $request->bearerToken();

        // verify token was provided
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        try {
            // decode the access token
            $decoded = JWT::decode($token, new Key(env('ACCESS_TOKEN_SECRET'), 'HS256'));

            // verify the token type
            if (($decoded->type ?? null) !== 'access') {
                return response()->json(['message' => 'Invalid token type'], 401);
            }

            // get the user using the 'sub' claim from the token
            $user = User::find($decoded->sub);

            // verify the user exists
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // set the authenticated user in the request context
            $request->setUserResolver(fn() => $user);

        } catch (ExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json(['message' => 'Invalid token signature'], 401);
        } catch (BeforeValidException $e) {
            return response()->json(['message' => 'Token is not yet valid'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token is invalid or malformed'], 401);
        }

        // continue processing the request
        return $next($request);
    }
}
