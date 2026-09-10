<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['en', 'gu', 'hi'];
        $locale = $request->query('lang')
            ?? $request->session()->get('locale')
            ?? $request->cookie('cms_locale')
            ?? config('app.locale', 'en');

        if (! in_array($locale, $supported, true)) {
            $locale = 'en';
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        $response = $next($request);
        if (method_exists($response, 'withCookie')) {
            return $response->withCookie(cookie('cms_locale', $locale, 60 * 24 * 365));
        }

        return $response;
    }
}
