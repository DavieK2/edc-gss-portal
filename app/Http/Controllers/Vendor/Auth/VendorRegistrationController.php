<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VendorRegistrationController extends Controller
{
    public function index()
    {
        return view('vendor.auth.register', [ 'schemes' => Scheme::get() ]);
    }

    public function store()
    {
       $data =  request()->validate([
            'fullname' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
            'scheme' => 'required|exists:schemes,id',
            'email' => 'required|unique:users'
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = false;
        
        $scheme = Scheme::find($data['scheme']);

        unset($data['scheme']);

        $user = User::create($data);

        $user->assignRole('vendor');

        $user->registerVendorAs($scheme);

        return redirect(route('vendor.auth.login'))->with('success', 'Registration Successful');
    }

}
