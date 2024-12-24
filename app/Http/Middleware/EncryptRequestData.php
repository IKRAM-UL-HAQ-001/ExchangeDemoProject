<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;

class EncryptRequestData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $encryptedData = [];
        // foreach ($request->all() as $key => $value) {
        //     $encryptedData[$key] = Crypt::encryptString($value);
        // }

        // // Replace request data with encrypted values
        // $request->merge($encryptedData);

        return $next($request);
    }
}
