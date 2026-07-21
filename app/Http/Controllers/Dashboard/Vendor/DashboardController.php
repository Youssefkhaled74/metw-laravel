<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('vendor.urgent-tasks');
    }

    public function reports()
    {
        // Add reporting logic here
        return view('dashboard.vendor.reports');
    }
}
