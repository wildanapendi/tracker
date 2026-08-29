<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->header('User-Agent', '');

        // Paksa format JSON jika:
        // 1. Request mengarah ke calendar/* atau api/*
        // 2. Request dikirim dari tools API testing (Postman, Insomnia, cURL, HTTPie)
        // 3. Request menyertakan query param 'format=json'
        // 4. Request secara eksplisit meminta JSON
        if (
            $request->is('calendar/*') ||
            $request->is('api/*') ||
            str_contains(strtolower($userAgent), 'postman') ||
            str_contains(strtolower($userAgent), 'insomnia') ||
            str_contains(strtolower($userAgent), 'curl') ||
            str_contains(strtolower($userAgent), 'httpie') ||
            $request->query('format') === 'json' ||
            $request->expectsJson()
        ) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
