<?php

namespace App\Http\Controllers\Admin;

class ContributorController extends AdminController
{
    public function index(): \Illuminate\View\View
    {
        return view('admin.contributors.index');
    }
}
