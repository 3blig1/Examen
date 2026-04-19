<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['fr', 'de', 'en'];
        $locale = null;

        // Allow query param to override: ?lang=en
        if ($request->has('lang')) {
            $requestedLocale = $request->input('lang');
            if (in_array($requestedLocale, $supportedLocales, true)) {
                $locale = $requestedLocale;
                session(['locale' => $locale]);
                Cookie::queue('locale', $locale, 60 * 24 * 365);

                $user = $request->user();
                if ($user && $user->locale !== $locale) {
                    $user->forceFill(['locale' => $locale])->save();
                }
            }
        }

        if (!$locale) {
            $user = $request->user();
            if ($user && in_array($user->locale, $supportedLocales, true)) {
                $locale = $user->locale;
            }
        }

        if (!$locale) {
            $cookieLocale = $request->cookie('locale');
            if (in_array($cookieLocale, $supportedLocales, true)) {
                $locale = $cookieLocale;
                session(['locale' => $locale]);
            }
        }

        if (!$locale) {
            $locale = session('locale', config('app.locale'));
        }

        // Set Laravel's locale
        \Illuminate\Support\Facades\App::setLocale($locale);
        
        return $next($request);
    }
}
