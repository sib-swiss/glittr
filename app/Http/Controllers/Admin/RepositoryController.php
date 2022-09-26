<?php

namespace App\Http\Controllers\Admin;

class RepositoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.repositories.index');
    }
}
