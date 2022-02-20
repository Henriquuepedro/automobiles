<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $titlePage = "Inicio";

        if (Auth::user()->permission === 'master') {
            return view('master.dashboard.index', compact('titlePage'));
        } else {
            return view('admin.dashboard.index', compact('titlePage'));
        }
    }
}
