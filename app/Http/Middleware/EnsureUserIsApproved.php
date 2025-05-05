<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isRenter() && !auth()->user()->is_approved) {
            // Don't show the not approved page if they're already on it
            if ($request->route()->getName() !== 'not-approved') {
                return redirect()->route('not-approved');
            }
        }

        return $next($request);
    }
} 