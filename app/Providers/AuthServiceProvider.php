<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Registration Abilities
        Gate::define('view-registrations', function(){
            $roles = ['edc-director', 'edc-admin', 'superadmin', 'edc-verification-officer', 'gss-admin', 'gss-director', 'vc-unical'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('edit-registrations', function(){
            $roles = ['superadmin'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('view-registration-stats', function(){
            $roles = ['edc-director', 'edc-admin', 'superadmin','gss-admin', 'gss-director', 'vc-unical'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('register-student', function(){
            $roles = ['edc-admin', 'superadmin'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });


        //Token Abilities
        Gate::define('view-token-transactions', function(){
            $roles = ['superadmin', 'gss-director'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });



        Gate::define('view-verifications', function(){
            $roles = ['edc-director', 'edc-admin', 'superadmin'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('make-verifications', function(){
            $roles = ['edc-verification-officer', 'superadmin']; 
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('verify', function(){
            $roles = ['edc-verification-officer'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        //Payments
        Gate::define('view-payments', function(){
            $roles = ['edc-director', 'superadmin', 'gss-director', 'vc-unical'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('view-payment-stats', function(){
            $roles = ['edc-director', 'superadmin', 'gss-director', 'vc-unical'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('retrieve-payments', function(){
            $roles = ['superadmin'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('make-payment', function(){
            $roles = ['student', 'vendor'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('view-faculties-and-departments', function(){
            $roles = ['superadmin'];
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });


        //Vendors
        Gate::define('view-vendors', function(){
            $roles = ['superadmin', 'gss-director'];         
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });


        //Verification Offficers
        Gate::define('view-verification-officers', function(){
            $roles = ['edc-director', 'edc-admin', 'superadmin']; 
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('create-verification-officers', function(){
            $roles = ['edc-admin', 'superadmin'];  
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });


        //Ventures
        Gate::define('view-ventures', function(){
            $roles = ['edc-admin', 'edc-director', 'superadmin']; 
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });


        Gate::define('view-sessions', function(){
            $roles = ['superadmin'];  
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('perform-super', function(){
            $roles = ['superadmin'];  
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('view-supplementary', function(){
            $roles = ['superadmin', 'sup-vendor'];  
            return ! empty( array_intersect($roles, auth()->guard(request('role'))->user()->role->pluck('name')->toArray()) );
        });

        Gate::define('view-sessions-list', function(){
            $roles = ['superadmin', 'gss-admin', 'edc-admin'];  
            return in_array(auth()->guard(request('role'))->user()->active_role, $roles);
        });

        Gate::define('edit-student-data', function(){ 
            return in_array('editor', auth()->guard('vendor')->user()->role->pluck('name')->toArray() );
        });
    }
}
