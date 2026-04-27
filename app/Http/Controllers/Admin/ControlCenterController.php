<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ControlCenterController extends Controller
{
    public function index()
    {
        // Only admins can access
        if (!auth()->user()->admin) {
            return redirect()->route('admin.home')
                ->with('error', 'No tienes permisos para acceder al Centro de Control.');
        }

        return view('admin.control-center.index');
    }
}
