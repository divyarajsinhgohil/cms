<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $notices = Notice::whereIn('target_audience', ['all', 'students'])
            ->latest()
            ->limit(6)
            ->get();

        return view('public.home', compact('notices'));
    }

    public function setLocale(Request $request, string $locale)
    {
        $supported = ['en', 'gu', 'hi'];
        if (! in_array($locale, $supported, true)) {
            $locale = 'en';
        }

        $request->session()->put('locale', $locale);

        return redirect()->back()->withCookie(cookie('cms_locale', $locale, 60 * 24 * 365));
    }
}
