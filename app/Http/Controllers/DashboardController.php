<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Homepage now only shows the search/marketing content
        return view('dashboard');
    }
}