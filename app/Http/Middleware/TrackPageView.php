<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->isSuccessful() && ! $request->is('admin*') && ! $request->is('api/*') && ! $request->is('up')) {
            PageView::create([
                'path' => '/'.ltrim($request->path(), '/'),
                'route_name' => $request->route()?->getName(),
                'referrer' => Str::limit((string) $request->headers->get('referer'), 500, ''),
                'visitor_hash' => hash('sha256', $request->ip().'|'.now()->toDateString()),
                'occurred_at' => now(),
            ]);
        }

        return $response;
    }
}
