<?php

namespace App\Http\Controllers\Officials\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OfficialsLoginController extends Controller
{
    protected $role;

    public function __construct(){

        $this->role = Role::where('name', request('role'))->firstOrFail()->name;

    }

    public function index()
    {
        return view('officials.auth.login', ['role' => $this->role ]);
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

        if(! in_array( $this->role, $user->role->pluck('name')->toArray() )){
            return back()->with('error', 'Invalid Credentials')->withInput($credentials);
        }

        Auth::guard($this->role)->login($user); 

        return redirect(route('officials.dashboard.index', $this->role));
    }

    public function logout()
    {
        Auth::guard($this->role)->logout(auth()->user());

        return redirect(route('officials.auth.login', $this->role));
    }
}
