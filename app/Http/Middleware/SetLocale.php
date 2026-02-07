<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Get locale from session, query param, or use default
        $locale = session('locale', config('app.locale'));
        
        // Allow query param to override: ?lang=en
        if ($request->has('lang')) {
            $locale = $request->input('lang');
            if (in_array($locale, ['fr', 'de', 'en'])) {
                session(['locale' => $locale]);
            }
        }
        
        // Set Laravel's locale
        \Illuminate\Support\Facades\App::setLocale($locale);
        
        return $next($request);
    }
}
