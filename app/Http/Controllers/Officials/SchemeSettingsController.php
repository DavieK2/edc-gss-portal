<?php

namespace App\Http\Controllers\Officials;

use App\Models\Account;
use App\Models\Scheme;

class SchemeSettingsController extends BaseController
{
    public function onlineIndex($role, Scheme $scheme)
    {
        return view('officials.schemes.settings.online_index', [
                                                            'role' => $this->role, 
                                                            'charges_online' => $scheme->charges_online, 
                                                            'token_accounts' => $scheme->token_accounts, 
                                                            'page' => $scheme->name.' Online Charges', 
                                                            'scheme' => $scheme 
                                                        ]);
    }

    public function bankIndex($role, Scheme $scheme)
    {
        return view('officials.schemes.settings.bank_index', [
                                                            'role' => $this->role, 
                                                            'charges_online' => $scheme->charges, 
                                                            'page' => $scheme->name.' Bank Charges', 
                                                            'scheme' => $scheme 
                                                        ]);
    }

    public function edit($role, Scheme $scheme, $key, $type, $channel)
    {

        return view('officials.schemes.settings.edit', [
                                                            'role' => $this->role, 
                                                            'charge' => $type == 'token' ? $scheme->token_accounts[$key] : ($channel === 'bank' ? $scheme->charges[$key] : $scheme->charges_online[$key]), 
                                                            'scheme' => $scheme, 
                                                            'key' => $key, 
                                                            'type' => $type, 
                                                            'channel' => $channel,
                                                            'accounts' => Account::get() 
                                                        ]);
    }

    public function create($role, Scheme $scheme, $channel)
    {
        return view('officials.schemes.settings.create', ['role' => $this->role, 'scheme' => $scheme, 'accounts' => Account::get(), 'channel' => $channel ]);
    }

    public function store($role, Scheme $scheme, $channel)
    {
        $type = request()->validate(['type' => 'in:token,course']);
        
        $data = request()->validate([
            'title' => 'required|string',
            'item_code' => 'required|string',
            'fee'  => 'required|integer',
            'account_id' => 'required|exists:accounts,account_id'
       ]);

       if( $channel === 'online' ){

            if( $type['type'] == 'course' ){

                $charges = ! is_array($scheme->charges_online) ? [] : $scheme->charges_online;
                array_push( $charges, $data );
                $scheme->update(['charges_online' => $charges]);

            }

            if( $type['type'] == 'token' ){

                $charges = ! is_array($scheme->token_accounts) ? [] : $scheme->token_accounts;
                array_push( $charges, $data );
                $scheme->update(['token_accounts' => $charges]);

            }
       }

       if( $channel === 'bank'){
       
            $charges = ! is_array($scheme->charges) ? [] : $scheme->charges;
            array_push( $charges, $data );
            $scheme->update(['charges' => $charges]);
        
       }
       

       return redirect(route("officials.schemes.settings.index.".$channel, [$this->role, $scheme]));
    }

    public function update($role, Scheme $scheme, $key, $channel)
    {
        $type = request()->validate(['type' => 'in:token,course']);
        
        $data = request()->validate([
            'title' => 'required|string',
            'item_code' => 'required|string',
            'fee'  => 'required|integer',
            'account_id' => 'required|exists:accounts,account_id'
       ]);


        if( $channel === 'online'){

            if($type['type'] == 'course'){

                $charges = $scheme->charges_online;
                $charges[$key] = $data;
                $scheme->update(['charges_online' => $charges]);

            }
    
            if($type['type'] == 'token'){

                $charges = $scheme->token_accounts;
                $charges[$key] = $data;
                $scheme->update(['token_accounts' => $charges]);

            }
        }

        if( $channel === 'bank' ){

            $charges = $scheme->charges;
            $charges[$key] = $data;
            $scheme->update(['charges' => $charges]);
        }
       

        return redirect(route("officials.schemes.settings.index.".$channel, [$this->role, $scheme]));
    }

    public function delete($role, Scheme $scheme, $type, $key, $channel)
    {

        if( $channel === 'online'){

            if( $type == 'course' ){

                $charges = $scheme->charges_online;
                unset($charges[$key]);
                $scheme->update(['charges_online' => $charges]);
            }
    
            if( $type == 'token' ){

                $charges = $scheme->token_accounts;
                unset($charges[$key]);
                $scheme->update(['token_accounts' => $charges]);
            }
        }

        if( $channel === 'bank'){

            $charges = $scheme->charges;
            unset($charges[$key]);
            $scheme->update(['charges' => $charges]);

        }

        return redirect(route("officials.schemes.settings.index.".$channel, [$this->role, $scheme]));
    }

    public function toggleOnlinePayments($role, Scheme $scheme)
    {
        $scheme->update(['is_online_payment_enabled' => ! $scheme->is_online_payment_enabled ]);
        return back();
    }

    public function toggleBankPayments($role, Scheme $scheme)
    {
        $scheme->update(['is_bank_payment_enabled' => ! $scheme->is_bank_payment_enabled ]);
        return back();
    }


}
