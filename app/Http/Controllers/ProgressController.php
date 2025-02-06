<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('Trainee')) {
            return view('progress.index');
        }

        return abort('403');
    }
}
