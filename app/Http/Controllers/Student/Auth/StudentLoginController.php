<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentLoginController extends Controller
{
    
    public function index()
    {
        return view('student.auth.login.index');
    }

    public function login()
    {
        $data = request()->validate([
            'student_code' => 'required|exists:student_profiles,student_code',
            'session' => 'required|exists:sessions,id',
            'password' => 'required'
        ]);

        $student_profile = StudentProfile::where('student_code', $data['student_code'])->where('session_id', $data['session'])->first();
        
        if(is_null($student_profile)) return back()->with('error', 'Invalid Credentials');

        $user = $student_profile->user;

        if(! Hash::check($data['password'], $user->password)){
            return back()->with('error', 'Invalid Credentials')->withInput($data);
        }

        if($user->active_role != 'student'){
            return back()->with('error', 'Invalid Credentials')->withInput($data);
        }

        Auth::guard('student')->login($user);

        session(['student_profile_id' => $student_profile->id]);
       
        return redirect(route('student.dashboard.index'));
    }

    public function logout()
    {
        Auth::guard('student')->logout(auth()->guard('student')->user());
        
        session()->forget('student_profile_id');
        
        return redirect(route('student.auth.login.index'));
    }
}
