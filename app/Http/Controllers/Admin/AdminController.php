<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function apicuron()
    {
        return view('admin.apicuron');
    }

    public function ontologies()
    {
        return view('admin.ontologies.index');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
