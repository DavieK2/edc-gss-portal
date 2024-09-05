<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendorLoginController extends Controller
{
    protected $role;

    public function __construct(){

        // $this->role = Role::where('name', request('role'))->firstOrFail()->name;

    }

    public function index()
    {
        return view('vendor.auth.login');
    }

    public function login()
    {
        $credentials = request()->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();
        
        if(is_null($user)){
            return back()->with('error', 'Invalid Credentials')->withInput($credentials);
        } 

        if(! Hash::check($credentials['password'], $user->password)){
            return back()->with('error', 'Invalid Credentials')->withInput($credentials);
        }

        if($user->active_role != 'vendor'){
            return back()->with('error', 'Invalid Credentials')->withInput($credentials);
        }

        Auth::guard('vendor')->login($user); 

        return redirect(route('vendor.dashboard.index', ['role' => $this->role ]));
    }

    public function logout()
    {
        Auth::guard('vendor')->logout(auth()->user());

        return redirect(route('vendor.auth.login'));
    }
}
