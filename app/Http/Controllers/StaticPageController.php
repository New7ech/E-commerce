<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function mentionsLegales(): View
    {
        return view('static.mentions-legales');
    }

    public function cgv(): View
    {
        return view('static.cgv');
    }

    public function politiqueConfidentialite(): View
    {
        return view('static.politique-confidentialite');
    }

    public function contact(): View
    {
        return view('static.contact');
    }

    public function promotions(): View
    {
        // Logique pour récupérer les promotions si nécessaire
        return view('static.promotions');
    }
}
