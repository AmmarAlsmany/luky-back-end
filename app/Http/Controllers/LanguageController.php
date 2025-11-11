<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application locale and persist it in session.
     */
    public function switch(string $locale): RedirectResponse
    {
        $supported = ['en', 'ar'];
        if (!in_array($locale, $supported)) {
            $locale = config('app.fallback_locale');
        }

        // Persist and apply for current request
        Session::put('locale', $locale);
        App::setLocale($locale);

        // Redirect back and force immediate locale via query param
        $previous = url()->previous();
        $separator = str_contains($previous, '?') ? '&' : '?';
        return redirect()->to($previous . $separator . 'lang=' . $locale);
    }
}
