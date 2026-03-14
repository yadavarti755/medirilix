<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($lang)
    {
        if (in_array($lang, ['en', 'hi'])) {
            session(['locale' => $lang]);
        }
        return redirect()->back();
    }
}
