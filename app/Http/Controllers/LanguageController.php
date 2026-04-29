<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        if (in_array($locale, ['en', 'lv'])) {
            session(['locale' => $locale]);
            if (auth()->check()) {
                auth()->user()->forceFill(['locale' => $locale])->save();
            }
        }
        return redirect()->back();
    }
}