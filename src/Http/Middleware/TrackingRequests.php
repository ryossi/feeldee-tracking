<?php

namespace Feeldee\Tracking\Http\Middleware;

use Feeldee\Tracking\Facades\Tracking;
use Closure;
use Illuminate\Http\Request;

class TrackingRequests
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
        // トラッキング開始
        Tracking::start();

        return $next($request);
    }
}
