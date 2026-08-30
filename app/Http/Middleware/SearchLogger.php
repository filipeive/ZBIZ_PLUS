<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class SearchLogger
{
    public function handle($request, Closure $next)
    {
        if ($request->has('q') && $request->get('q')) {
            Log::info('Search performed', [
                'query' => $request->get('q'),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        return $next($request);
    }
}