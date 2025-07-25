<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Define supported locales
        $supportedLocales = ['en', 'yoruba', 'igbo', 'hausa'];
        
        // Get locale from multiple sources in priority order
        $locale = null;
        
        // 1. Check if locale is being changed via URL parameter
        if ($request->route() && $request->route()->parameter('locale')) {
            $urlLocale = $request->route()->parameter('locale');
            if (in_array($urlLocale, $supportedLocales)) {
                $locale = $urlLocale;
            }
        }
        
        // 2. Check session if no URL locale
        if (!$locale && Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (is_string($sessionLocale) && in_array($sessionLocale, $supportedLocales)) {
                $locale = $sessionLocale;
            }
        }
        
        // 3. Check cookie if no session locale
        if (!$locale) {
            $cookieLocale = $request->cookie('locale');
            if (is_string($cookieLocale) && in_array($cookieLocale, $supportedLocales)) {
                $locale = $cookieLocale;
            }
        }
        
        // 4. Fall back to config default
        if (!$locale) {
            $locale = config('app.locale', 'en');
            if (!in_array($locale, $supportedLocales)) {
                $locale = 'en';
            }
        }
        
        // Validate final locale
        if (!is_string($locale) || !in_array($locale, $supportedLocales)) {
            $locale = 'en';
            // Clear any corrupted session data
            Session::forget('locale');
        }
        
        // Set the application locale
        App::setLocale($locale);
        
        return $next($request);
    }
}