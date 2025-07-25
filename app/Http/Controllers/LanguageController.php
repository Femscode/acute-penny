<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change the application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLanguage(Request $request, $locale)
    {
        // Define supported locales
        $supportedLocales = ['en', 'yoruba', 'igbo', 'hausa'];
        
        // Check if the locale is supported
        if (in_array($locale, $supportedLocales)) {
            // Set the application locale
            App::setLocale($locale);
            
            // Store the locale in session
            Session::put('locale', $locale);
            
            // Explicitly save the session
            Session::save();
            
            // Store in cookie for persistence (optional)
            cookie()->queue('locale', $locale, 60 * 24 * 30); // 30 days
        }
        
        return redirect()->back();
    }
}