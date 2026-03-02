<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddEarlyHintsHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $contentType = (string) $response->headers->get('Content-Type');

        if (! $request->isMethod('GET') || ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $links = [
            '<' . Vite::asset('resources/css/app.css') . '>; rel=preload; as=style',
            '<' . Vite::asset('resources/js/app.js') . '>; rel=preload; as=script',
            '<' . url('/logo-horizontal.svg') . '>; rel=preload; as=image',
            '<' . url('/logo-vertical.svg') . '>; rel=preload; as=image',
        ];

        foreach ($links as $link) {
            $response->headers->set('Link', $link, false);
        }

        return $response;
    }
}
