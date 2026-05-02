<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function users()
    {
        return view('dashboard.users');
    }

    public function portfolio()
    {
        return view('dashboard.portfolio');
    }

    public function server()
    {
        return view('dashboard.server');
    }

    public function logs()
    {
        return view('dashboard.logs');
    }
}
