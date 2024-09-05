<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        if( ! session()->has('student_profile_id') ){
            return redirect(route('student.auth.login.index'));
        }

        // $role = $this->role;
        $student_profile = auth()->user()->profile()->where('id', session('student_profile_id'))->first();
        $registrations = $student_profile->registrations;

        return view('student.dashboard.index', compact('student_profile', 'registrations'));
    }

    public function invoice(Registration $registration)
    {
        return view('student.registration.invoice', compact('registration'));
    }
}
