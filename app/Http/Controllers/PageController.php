<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function privacy(): View
    {
        return view('home.privacy');
    }

    public function terms(): View
    {
        return view('home.terms');
    }

    public function changelog(): View
    {
        return view('home.changelog');
    }
}