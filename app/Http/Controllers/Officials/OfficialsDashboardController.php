<?php

namespace App\Http\Controllers\Officials;

use App\Models\Registration;
use App\Models\Role;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Support\Facades\Gate;

class OfficialsDashboardController extends BaseController
{
    public function __construct(protected RegistrationService $registrationService)
    {
        parent::__construct();
    }

    public function index()
    {
        $view = match($this->role){
            'edc-verification-officer' => "officials.dashboard.$this->role",
            default => 'officials.dashboard.index'
        };

        $data = match($this->role){
            'edc-verification-officer' => $this->getEDCVerificationOfficerData(),
            default => [ 'stats' => $this->getStats() ]
        };
        
        // dd($data);
        return view($view, [ 'role' => $this->role ] + $data );
        
    }

    protected function getEDCVerificationOfficerData()
    {
        return [
            'tableHeadings' => $this->registrationService->getHeadings(true),  
            'page' => 'Verifications', 
            'ajaxUrl' => url("$this->role/search/verifications")
        ];
    }

    public function getStats()
    {
        $schemes = auth()->guard($this->role)->user()->schemes;

        return $schemes->map(function($scheme){

                $data = collect();
                
                if(Gate::allows('view-registration-stats')){

                    $registrationData = [
                        'title' => "Total ".strtoupper($scheme->name).' Registrations', 
                        'total' => Registration::where('scheme_id', $scheme->id)->where('payment_status', true)->activeSession()->count()
                    ];

                    $data->push($registrationData);

                }
                
                if( Gate::allows('view-payment-stats') ){

                    $totalActiveVendors = [
                        'title' => "Total ".strtoupper($scheme->name).' Active Vendors',
                        'total' => User::join('scheme_users', 'scheme_users.user_id', '=', 'users.id')
                                        ->join('role_users', 'role_users.user_id', '=', 'users.id')
                                        ->where( fn($query) => $query->where('users.status', true)
                                                                     ->where('scheme_users.scheme_id', $scheme->id)
                                                                     ->where('role_users.role_id', Role::firstWhere('name', 'vendor')->id)
                                        )->count()
                    ];


            
                    $bankPaymentData = [
                        'title' => "Total ".strtoupper($scheme->name).' Bank Payments',
                        'total' => 'N'.number_format(
                                            Registration::join('payment_notifications', 'registrations.invoice_number', '=', 'payment_notifications.CustReference')
                                                        ->where( fn($query) => $query->where('registrations.scheme_id', $scheme->id)
                                                                                    ->where('registrations.payment_status', true)
                                                                                    ->activeSession()
                                                        )
                                                        ->sum('payment_notifications.Amount')
                                        , 2)
                    ];


                    $data->push($totalActiveVendors);
                    $data->push($bankPaymentData);

                }
 
                return $data;

        })->toArray();
        
    }
}
