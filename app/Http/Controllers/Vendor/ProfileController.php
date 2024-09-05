<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function changePassword()
    {
        return view('vendor.profile.password', ['role' => 'vendor']);
    }

    public function updatePassword()
    {
        $user =  auth()->guard('vendor')->user();

        $password = request()->validate(['password' => 'required', 'old_password' => [function($attr, $val, $fail) use($user){
            if(! Hash::check($val, $user->password)){
                return $fail('Password is incorrrect');
            }
        }]]);

        $user->update(['password' => Hash::make($password['password'])]);

        alert('Success', 'Password Successfully Changed');

        return back();
    }
}
