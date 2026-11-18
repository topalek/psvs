<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeTrailingSlash
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        if (
            ($request->isMethod('GET') || $request->isMethod('HEAD'))
            && $path !== '/'
            && str_ends_with($path, '/')
            && ! str_starts_with($path, '/admin')
            && ! str_starts_with($path, '/livewire')
        ) {
            return redirect()->to(rtrim($path, '/'), 301);
        }

        return $next($request);
    }
}
