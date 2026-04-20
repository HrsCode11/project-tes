<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function ketua()
    {
        return view('dashboard.ketua');
    }

    public function bendahara()
    {
        return view('dashboard.bendahara');
    }

    public function warga()
    {
        return view('dashboard.warga');
    }
}