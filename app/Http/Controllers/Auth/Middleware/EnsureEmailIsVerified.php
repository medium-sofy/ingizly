<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->email_verified_at) {
            return redirect()->route('verification.email.form')
                ->with('error', 'Please verify your email address first.');
        }

        return $next($request);
    }
}