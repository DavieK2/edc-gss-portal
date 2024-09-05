<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Level;
use App\Models\Registration;
use App\Models\Scheme;
use App\Models\Semester;
use App\Models\Session;
use App\Models\StudentProfile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SchemeRegistrationService {

    public $errors;

    public static function formatRegistrationItems(Scheme $scheme, Collection $courses, Session $session, Semester|null $semester = null, Level|null $level = null, Course|null $venture = null, Registration|null $registration = null, $role = 'vendor')
    {

        $isErrors= [];
        
        foreach( $courses as $course ){

            if( $course->requires_venture && is_null($venture ) ) {
                $isErrors = [...$isErrors, $course->title.' requires you to offer a venture'];
            }
        };

        if( ! empty($isErrors) ){

            return [
                'error' => $isErrors
            ];
        }

        $items = $courses->map(function($course){

            if( $course->account_id ){

                return [
                    'title' => $course->title,
                    'item_code' => $course->item_code,
                    'fee' => $course->fee,
                    'account_id' => $course->account->account_id
                ];

            }else{

                return [
                    'title'     => $course->title,
                    'item_code' => $course->item_code,
                    'fee'       => $course->fee,
                    'accounts'  => collect($course->account_ids)->map(function($account){
                        return [
                            'fee'           => $account['fee'],
                            'account_id'    => $account['account_id'],
                        ];
                    })->toArray()
                ];
            }
            
        });

        $documentation_items = collect();
        $documentation_items_invoice = collect();

        if( $scheme->has_documentation_fees ){

            $documentation_items = static::getDocumentationFees($courses);
            $documentation_items_invoice = static::getDocumentationFees($courses, true);
    
        }

        $bank_charges = ( new static )->schemeCharges( $scheme, $semester->semester, $level->level, 'bank', $registration, $role );
                
        $payments_items = $items->merge($documentation_items);
        $invoice_items = $items->merge($documentation_items_invoice);

        $payments_items = $payments_items->all();
        $invoice_items = $invoice_items->all();

        $total_amount = collect($payments_items)->sum('fee') + collect($bank_charges)->sum('fee');

        $transaction_fee = ( $total_amount * 0.02 ) + 100;
        
        return [
            'total_amount'      => $total_amount,
            'payment_items'     => $payments_items,
            'invoice_items'     => $invoice_items,
            'other_charges'     => $bank_charges,
            'transaction_fee'   => $transaction_fee
        ];
    }

    protected  static function getDocumentationFees($courses, $for_invoice = false)
    {
         return $courses->filter(fn($course) => $course->has_documentation_fee)->map(function($course) use($courses, $for_invoice){
                    
            $documentation_title = $course->documentation_title;
            $documentation_item_code = $course->documentation_item_code;
            $documentation_fee = $course->documentation_fee;
            
            $latest_level = $courses->where('carryover_id', '!=', null)->map(fn($course) => $course->start_level)->sort()->last();

            if($latest_level != $course->start_level && $course->has_carryover){

                $documentation_title = $for_invoice ? $course->documentation_title : $course->carryover->documentation_title;
                $documentation_item_code = $for_invoice ? $course->documentation_item_code : $course->carryover->documentation_item_code;
                $documentation_fee = $course->carryover->documentation_fee;
            }


            return [
                'title' => $documentation_title,
                'item_code' => $documentation_item_code,
                'fee' => $documentation_fee,
                'account_id' => $course->documentation_fee_account->account_id
            ];
               
        });
    }

    public function schemeCharges( Scheme $scheme, $semester, $level, $type, ?Registration $registration = null, $role = 'vendor' )
    {
        $charges = $type == 'bank' ? $scheme->charges : $scheme->charges_online;

        $is_gss_first_year_or_direct_entry_student = ( $scheme->is_gss ) && ( $level == 100 || $level == '200 (Direct Entry)');

        $is_first_semester = ( $semester == 'First Semester' || $semester == 'First & Second Semester');
        
        $gss_portal_charges_cbt_charges = collect($charges)->filter(fn($charge) => ( $charge['item_code'] == 'REG_PORTAL_FEE') || ($charge['item_code'] == 'CBT_PROJECT') )->toArray();
        
        // $portal_charges = collect($charges)->filter(fn($charge) => ( $charge['item_code'] == 'REG_PORTAL_FEE') )->toArray();
        
        $is_vendor = auth()->guard($role)->user()?->is_vendor;

    
        return match(true){

           ( ($registration?->payment_status && $is_vendor) || ($registration?->registration_id && $is_vendor) ) => [],

            $scheme->is_edc => $charges,

            ( $is_gss_first_year_or_direct_entry_student && $is_first_semester ) => $charges,

            ( $scheme->is_gss ) && ( $is_gss_first_year_or_direct_entry_student === false ) && $is_first_semester => $gss_portal_charges_cbt_charges,

            default => []
        };

    }

    public static function createCustomerAccount(StudentProfile $student, Scheme $scheme) : bool | string | array
    {
        $customerID = null;

        if( $scheme->is_gss ){

            $customerID = $student->user->gss_customer_id;
             
        }

        if( $scheme->is_edc ){

            $customerID = $student->user->edc_customer_id;
             
        }

       
        if( ( $scheme->is_gss && ! is_null( $customerID ) ) ||  ( $scheme->is_edc && ! is_null( $customerID ) ) ){

           return $customerID ;
            
        }

       
        $student_name = preg_split('/\s+/', $student->user->fullname );

        try {

            $email = strtolower($student->user?->email);
            $res = Http::withToken($scheme->registration_payment_key)->post('https://api.paystack.co/customer', [
                'first_name' => $student_name[0],
                'last_name' => ($student_name[1])." ".($student_name[2] ?? ""),
                'phone_no' => $student->user?->phone_number,
                'email' => $email
            ]);

        } catch (\Throwable $th) {
            
            Log::info($th);

            dd( $th );
            return false;
        }
        

        if( ! $res->ok() ){

            $message = $res->json()['message'] ?? false;

            if( str_contains($message, 'email') ){

               return [
                    'status' => false,
                    'message' => $message
               ];
            }

            return false;
        }
        

        // dd( $res->json());

        $customerID = $res->json()['data']['customer_code'];


       

        if( $scheme->is_gss ){

            $student->user->update(['gss_customer_id' => $customerID]);
        }

        if( $scheme->is_edc ){

            $student->user->update(['edc_customer_id' => $customerID]);
        }
        
        return $customerID;
    }

    public function generateTransactionSplits($registrationItems, $semester, $level, $scheme, ?Registration $registration = null)
    {
        $registration_charges = collect( $registrationItems['payment_items'] )->flatMap(function($item){

                $data = [];

                if( isset( $item['account_id'] ) ){
                    
                    $data[] =   [ 
                                    'item_code'        => $item['item_code'],
                                    'title'            => $item['title'],
                                    'fee'              => $item['fee'],
                                    'account_id'       => $item['account_id']
                                ];
                }

                if( isset( $item['accounts'] ) ){

                    foreach(  $item['accounts'] as $account ){

                        $data[] =   [ 
                            'item_code'        => $item['item_code'],
                            'title'            => $item['title'],
                            'fee'              => intval($account['fee']),
                            'account_id'       => $account['account_id']
                        ];
                    }
                }

                return $data;

        })->toArray();

       
        $scheme_charges = ( new static )->schemeCharges( scheme: $scheme, semester: $semester, level: $level, type: 'online', registration: $registration );


        // dd( $scheme_charges );

        $subaccounts = collect( array_merge($registration_charges, $scheme_charges) )
                                ->groupBy('account_id')
                                ->map(fn($item, $key) => ['subaccount' => $key , 'share' => $item->sum('fee') * 100 ])
                                ->values();
        
        try {

            $res = Http::withToken($scheme->registration_payment_key)->post('https://api.paystack.co/split', [
                        'name' =>  ($scheme->name)." Payments",
                        'currency' =>  'NGN',
                        'type' => 'flat',
                        'bearer_type' => 'account', 
                        'subaccounts' => $subaccounts,
                    ]);

        } catch (\Throwable $th) {
            
            return false;
        }

        if(! $res->ok()){

            return false;
        }

        return $res->json()['data']['split_code'];
                
    }

    public static function generateOfflineReference(Scheme $scheme, $amount, $customerID, $split = null, $invoice_number)
    {
        try {

            $res = Http::withToken($scheme->registration_payment_key)->post('https://api.paystack.co/paymentrequest', [
                        'customer' => $customerID,
                        'split_code' => $split,
                        'amount' => ($amount) * 100,
                        'description' =>  $scheme->name.' Registration',
                        'metadata' => [
                            'custom_field' => [
                                'payment_code' => $invoice_number
                            ]
                        ]
                    ]);

        } catch (\Throwable $th) {
            
            return false;
        }

        if(! $res->ok()){

            return false;
        }
        

        $data = $res->json()['data'];

        return [
            'offline_reference' => $data['offline_reference'],
            'request_code' => $data['request_code'],
        ];

    }

    public static function generatePOSReference(Scheme $scheme, StudentProfile $student_profile, array $registration_items, Semester $semester, $invoice_number, ?Registration $registration = null)
    {
        $customerID = null;
        $splitID = null;
        $posReference = null;

        
        $customerID = static::createCustomerAccount($student_profile, $scheme);


        if( isset($customerID['status']) && $customerID['status'] === false ){

            throw new Exception( $customerID['message'] );
        }

        if( $customerID ){

            $splitID = ( new static )->generateTransactionSplits( $registration_items, $semester->semester, $student_profile->level->level, $scheme, registration: $registration );

        }



        if( $splitID ){

            $posReference = static::generateOfflineReference( $scheme, $registration_items['total_amount'] + $registration_items['transaction_fee'], $customerID, $splitID, $invoice_number );
        }

        return [
            'offline_reference' => $posReference['offline_reference'],
            'request_code' => $posReference['request_code'],
            'split_code' => $splitID
        ];
    }
}