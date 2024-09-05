<?php

namespace App\Http\Controllers\Officials;

use App\Models\Faculty;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class VerificationOfficersController extends BaseController
{
    public function index()
    {
        $schemes = auth()->guard($this->role)->user()->schemes;

        $verificationOfficers = $schemes->map(fn($scheme) => $scheme->users->filter(fn($user) => $user->active_role == 'edc-verification-officer'))->flatten();

        return view('officials.verification_officers.index', ['verificationOfficers' => $verificationOfficers, 'role' => $this->role, 'page' => 'Verification Officers' ]);
    }

    public function create()
    {
        Gate::authorize('create-verification-officers');
        
        $schemes = auth()->guard($this->role)->user()->schemes->intersect(Scheme::get());
        return view('officials.verification_officers.create',['role' => $this->role, 'schemes' => $schemes, 'faculties' => Faculty::get() ]);
    }

    public function store()
    {
        Gate::authorize('create-verification-officers');

        $data = request()->validate([
            'fullname' => 'required',
            'faculties' => 'required',
            'faculties.*' => 'exists:faculties,id',
            'scheme' => 'required|exists:schemes,id',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        $data['password'] = Hash::make($data['password']);
        
        $scheme = $data['scheme'];
        $faculties = Faculty::whereIn('id', $data['faculties'])->get();

        unset($data['scheme']);
        unset($data['faculties']);

        $user = User::create($data);

        $user->assignRole('edc-verification-officer');
        $user->assignUserToScheme(Scheme::find($scheme));
        $faculties->each(fn($faculty) => $user->assignToFaculty($faculty));
    
        return redirect(route('officials.verification.officers.index', $this->role));
    }

    public function edit($role, User $officer)
    {
        return view('officials.verification_officers.edit',['role' => $this->role, 'faculties' => Faculty::get(), 'officer' => $officer ]);
    }

    public function update($role, User $officer)
    {
        $data = request()->validate([
            'faculties' => 'required',
            'faculties.*' => 'exists:faculties,id',
        ]);

        $faculties = Faculty::whereIn('id', $data['faculties'])->get();

        $officer->faculties->each(fn($faculty) => $officer->unassignFromFaculty($faculty));
        $faculties->each(fn($faculty) => $officer->assignToFaculty($faculty));

        return redirect(route('officials.verification.officers.index', $this->role));
    }
    
    
    
    public function activate($role, User $officer)
    {
        $officer->update(['status' => $officer->status ? false : true ]);

        alert('Success', $officer->status ? 'Active' : 'Inactive');

        return back();
    }


}
