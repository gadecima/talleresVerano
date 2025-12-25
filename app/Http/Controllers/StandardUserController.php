<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class StandardUserController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Standard/Dashboard', [
            'user' => auth()->user()->load('role'),
        ]);
    }
}
