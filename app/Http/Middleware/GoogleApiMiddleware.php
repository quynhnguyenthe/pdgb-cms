<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Google\Client;

class GoogleApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            // Bearer token is missing, handle accordingly
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Create an instance of Google_Client
        $client = new Client();

        // Set other Google_Client configuration if needed
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // Set the Bearer token
        $client->setAccessToken(['access_token' => $bearerToken]);


        // Perform any additional configuration or authentication here if needed

        // Add the Google_Client instance to the request
        $request->attributes->set('google_client', $client);

        // Set user information in the guard
        if (!Auth::guard('google-member')->validate(['access_token' => $bearerToken])) {
            return response(['error' => 'Invalid Token'], 403);
        }

        return $next($request);
    }
}
