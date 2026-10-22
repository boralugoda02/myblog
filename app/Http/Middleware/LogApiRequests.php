<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $start    = microtime(true);
        $response = $next($request);

        
        try {
            ApiRequestLog::create([
                'method'          => $request->method(),
                'path'            => $request->path(),
                'full_url'        => Str::limit($request->fullUrl(), 250, ''),
                'status_code'     => $response->getStatusCode(),
                'ip_address'      => $request->ip(),
                'user_agent'      => Str::limit((string) $request->userAgent(), 250, ''),
                'user_id'         => $request->user('sanctum')?->id,
                'request_headers' => json_encode(Arr::except($request->headers->all(), ['authorization', 'cookie', 'x-xsrf-token'])),
                'request_body'    => Str::limit((string) json_encode($request->except(['password', 'password_confirmation', 'current_password', 'token'])), 5000, ''),
                'response_body'   => $request->is('api/login')
                    ? '[hidden]'
                    : Str::limit((string) $response->getContent(), 5000, ''),
                'duration_ms'     => (int) round((microtime(true) - $start) * 1000),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }
}