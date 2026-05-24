<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $accessKey = config('api.access_key');

        if (! is_string($accessKey) || $accessKey === '') {
            return response()->json(['message' => 'Access key not configured.'], 500);
        }

        $providedKey = $this->extractKey($request);

        if (! is_string($providedKey) || ! hash_equals($accessKey, $providedKey)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }

    private function extractKey(Request $request): ?string
    {
        $headerKey = $request->header('X-Access-Key');
        if (is_string($headerKey) && $headerKey !== '') {
            return $headerKey;
        }

        $authHeader = $request->header('Authorization');
        if (! is_string($authHeader) || $authHeader === '') {
            return null;
        }

        if (preg_match('/^Bearer\s+(.*)$/i', $authHeader, $matches) === 1) {
            return trim($matches[1]);
        }

        return null;
    }
}

