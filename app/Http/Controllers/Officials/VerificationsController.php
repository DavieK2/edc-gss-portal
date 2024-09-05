<?php

namespace App\Http\Controllers\Officials;

use App\Models\Registration;
use App\Services\RegistrationService;

class VerificationsController extends BaseController
{
    public function __construct(protected RegistrationService $registrationService){
        parent::__construct();
    }

    public function index()
    {
        return view('officials.verifications.index', [
            'tableHeadings' => $this->registrationService->getHeadings(true), 
            'role' => $this->role, 
            'page' => 'Verifications', 
            'ajaxUrl' => url("$this->role/search/verifications/EDC")
        ]);
    }

    
    public function verify($role, Registration $registration)
    {
        auth()->guard($role)->user()->verify($registration);
        alert('Success', 'Registration has been verified');
        return back();
    }
}
