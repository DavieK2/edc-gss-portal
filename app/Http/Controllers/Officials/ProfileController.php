<?php

namespace App\Http\Controllers\Officials;

use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseController
{
    public function changePassword()
    {
        return view('officials.profile.password', ['role' => $this->role]);
    }

    public function updatePassword()
    {
        $user =  auth()->guard($this->role)->user();

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
