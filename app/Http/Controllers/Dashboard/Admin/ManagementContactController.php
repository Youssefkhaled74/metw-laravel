<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class ManagementContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.employees.index')) {
            return view('dashboard.admin.no-permission');
        }

        $employees = Employee::select('id', 'employee_number', 'first_name', 'last_name', 'email', 'phone', 'position')
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->orderBy('first_name')
            ->get();

        return view('dashboard.admin.management-contact.index', compact('employees'));
    }
}
