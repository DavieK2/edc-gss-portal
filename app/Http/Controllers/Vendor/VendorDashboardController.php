<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;

class VendorDashboardController extends Controller
{
    // protected $role;

    // public function __construct(){

    //     $this->role = Role::where('name', request('role'))->firstOrFail()->name;

    // }

    public function index()
    {
        return view('vendor.dashboard.index');
    }
}
