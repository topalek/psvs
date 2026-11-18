<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLegacyRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $path = '/'.trim($request->path(), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');

        $redirect = Redirect::query()
            ->where('is_active', true)
            ->whereIn('source_path', array_unique([$path, $path.'/', $request->path()]))
            ->first();

        if (! $redirect) {
            return $next($request);
        }

        $destination = $redirect->destination_path;

        if (! str_starts_with($destination, 'http://') && ! str_starts_with($destination, 'https://')) {
            $destination = '/'.ltrim($destination, '/');

            if (rtrim($destination, '/') === $path) {
                return $next($request);
            }
        }

        $redirect->increment('hits');

        return redirect()->to($destination, $redirect->status_code);
    }
}
