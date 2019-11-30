<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $titlePage = "Inicio";
        return view('auth.inicio.index', compact('titlePage'));
    }
}
