<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rcalicdan\FiberAsync\EventLoop\EventLoop;
use Symfony\Component\HttpFoundation\Response;

class UpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        EventLoop::getInstance()->run();

        return $next($request);
    }
}
