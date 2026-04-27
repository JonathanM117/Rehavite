<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogLivewireRequests
{
    public function handle(Request $request, Closure $next)
    {
        if (strpos($request->path(), 'livewire') !== false) {
            Log::info('LIVEWIRE REQUEST START: ' . $request->path());
            $response = $next($request);
            Log::info('LIVEWIRE REQUEST END: Status ' . $response->getStatusCode());
            if ($response->getStatusCode() !== 200) {
                Log::error('LIVEWIRE RESPONSE BODY: ' . substr($response->getContent(), 0, 2000));
            } else {
                Log::info('LIVEWIRE RESPONSE (200): ' . substr(method_exists($response, 'getContent') ? $response->getContent() : '', 0, 800));
            }
            return $response;
        }
        return $next($request);
    }
}
