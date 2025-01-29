<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocalizationController extends Controller
{
    public function __invoke($locale)
    {
        if (!in_array($locale, ['en', 'ar'])) {
            abort(400);
        }
        session(['localization' => $locale]); // Corrected syntax
        return redirect()->back();
    }
}
