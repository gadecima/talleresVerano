<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ViewerController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Viewer/Dashboard', [
            'user' => auth()->user()->load('role'),
        ]);
    }
}
