<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\OnlineTransaction;
use App\Models\PaymentNotification;
use Illuminate\Support\Facades\Http;
use App\Models\Registration;
use App\Models\Scheme;
use App\Models\TokenTransaction;
use App\Models\User;
use App\Services\ArrayToXml;
use App\Services\SchemeRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentTransactionsController extends Controller
{
    protected $url;
    protected $authorization_url;
    protected $reference;
    protected $data;
    protected $marchantId = [8195, 8194, 8199, 8200, 8201, 8202, 8203];
    protected const HEADER =  [
        "Content-Type" => 'text/xml'
    ];
    protected const VALID =  0;
    protected const INVALID =  1;
    protected const EXPIRED =  2;

    public function __construct(protected SchemeRegistrationService $schemeRegistrationService)
    {
        $this->url = 'https://api.paystack.co/transaction/initialize';
        $this->data = new ArrayToXml();
    }


    public function onlinePayment(Registration $registration)
    {
        if( $registration->payment_status ){

            alert()->error('Payment already made');
            return back();
        }

        if( ( ! $registration->scheme->is_online_payment_enabled ) || $registration->is_supplementary ){

            alert()->error('Payments are closed');
            return back();
        }

        $registrationItems = $registration->items;

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

        
        try {

            $scheme_charges = $this->schemeRegistrationService->schemeCharges( $registration->scheme, $registration->semester, $registration->level, 'online' );

        } catch (\Throwable $th) {

            alert()->error($th->getMessage());
            return back();
        }
        

        $subaccounts = collect( array_merge( $registration_charges, $scheme_charges ) )
                                ->groupBy('account_id')
                                ->map(fn($item, $key) => ['subaccount' => $key , 'share' => $item->sum('fee') * 100 ])
                                ->values();
        
        $payload = [
                    'email' => $registration->student?->user->email,
                    'amount' => ( $subaccounts->sum('share') + ($registrationItems['transaction_fee'] * 100) ),
                    'callback_url' => route('registration.payment.success'),
                    'split' => [
                        'type' => 'flat',
                        'bearer_type' => 'account', 
                        'subaccounts' => $subaccounts
                    ],
                    'metadata' => [
                        'custom_field' => [
                            'payment_code' => $registration->invoice_number
                        ]
                    ]
                ];
        
        $payment = $this->makeOnlinePayment($payload, $registration->scheme->registration_payment_key);
        
        if( ! $payment ){
            
            alert()->error('There was an error');
            return back();
        }

        session([ $this->reference => [
            'initial_url' => url()->current(),
        ]]);

        $registration->update(['temp_payment_ref' => $this->reference ]);

        return redirect( $this->authorization_url );
    }


    public function onlinePaymentWithPaymentRequest(Registration $registration)
    {

        if( $registration->payment_status ){

            alert()->error('Payment already made');
            return back();
        }

        if( ( $registration->scheme->is_online_payment_enabled ) == false ){

            alert()->error('Payments are closed');
            return back();
        }

        if( ( $registration->scheme->is_online_payment_enabled ) == 0 ){

            alert()->error('Payments are closed');
            return back();
        }

        if( $registration->is_supplementary ){

            alert()->error('Payments are closed');
            return back();
        }
        
        if( $registration->payment_request_code ){

            return redirect( 'https://paystack.com/pay/'.$registration->payment_request_code );
        }

        return $this->onlinePayment( $registration );
    }

    public function bankPayment(Request $request)
    {
        // $ips = ['41.223.145.174', '154.72.34.174'];
            
        // if(!in_array(request()->ip(), $ips)) abort(403);
    
        $data = json_decode(
            json_encode(
                simplexml_load_string($request->getContent())
            ), 1
        );

        if (! array_key_exists("Payments", $data)) {
            return $this->validateStudent($data);
        } else {
            return self::getTransaction($data);
        }
    }

    public function buyToken()
    {
        $user = auth()->guard('vendor')->user();

        $data = request()->validate([
            'number_of_tokens' => 'required|integer',
            'scheme' => [ Rule::requiredIf($user->schemes->count() > 1), 'exists:schemes,id']
        ]);

        $scheme = isset($data['scheme']) ? Scheme::find($data['scheme']) : $user->schemes->first(); 

        $tokens = $data['number_of_tokens'];
        $amount = $tokens * 300 * 100;
        $token_reference = Str::random(8);

        $payload = [
            'email' => $user->email,
            'amount' => $amount ,
            'callback_url' => route('vendor.token.success'),
            'metadata' => [
                'custom_field' => [
                    'token_reference' => $token_reference,
                    'no_of_tokens'  => $tokens
                ]
            ]
        ];

       
        $subaccounts = collect($scheme->token_accounts)->map(fn($account) => ['subaccount' => $account['account_id'], 'share' => ($tokens * $account['fee'] * 100)])->values();

        if($subaccounts->isNotEmpty()){

            $payload['split'] = [
                'type' => 'flat',
                'bearer_type' => 'account', 
                'subaccounts' => $subaccounts->toArray()
            ];
    
            $amount = $subaccounts->sum('share');

            $payload['amount'] = $amount + (($amount * 0.0155) + (100 * 100));
            
        }

       
        $payment = $this->makeOnlinePayment($payload, $scheme->token_payment_key);

        if(! $payment ){
            alert()->error('There was an error');
            return back();
        }

        $data = [
            'amount' => $amount,
            'number_of_tokens' => $data['number_of_tokens'],
            'reference' => $this->reference,
            'token_reference' => $token_reference,
            'scheme_id' => $scheme->id,
        ];
        
        auth()->guard('vendor')->user()->createTokenTransaction($data);

        return redirect($this->authorization_url);

    }

    protected function makeOnlinePayment($payload, $key)
    {
        try {
            $request = Http::withToken($key)->post($this->url, $payload);

        } catch (\Throwable $th) {

           Log::error($th->getMessage());
           return false;
        }
        
        $response = $request->json();

        if($request->status() != 200){
            Log::error($response);
            return false;
        }
        
        $this->authorization_url = $response['data']['authorization_url'];
        $this->reference = $response['data']['reference'];
        
        return true;
    }

    protected function getTransaction($payment)
    {

        $payment = new Fluent($payment['Payments']['Payment']);
       
        $registration = match(substr($payment->CustReference, 0 ,3)){
            'EDC' => $this->validateCustRefEDCAndGSS($payment->CustReference),
            'GSS' => $this->validateCustRefEDCAndGSS($payment->CustReference),
            'PRE' => $this->validateCustRefPREAndDIP($payment->CustReference),
            'DIP' => $this->validateCustRefPREAndDIP($payment->CustReference),
            'DPP' => $this->validateCustRefDPP($payment->CustReference)
        };

        if (!$registration) {
            return $this->transactionResponse($payment->PaymentLogId, "1", "Incorrect CustReference", 400);
        }

        if($registration instanceof Registration && ! $registration->scheme?->is_bank_payment_enabled ){
            return $this->transactionResponse($payment->PaymentLogId, "1", "Payments are closed", 200);
        }

        if ($payment->IsReversal == 'True') {
            
            if(PaymentNotification::where("PaymentLogId", $payment->PaymentLogId)
                                    ->where("PaymentReference", $payment->PaymentReference)
                                    ->where("ReceiptNo", $payment->ReceiptNo)
                                    ->where("IsReversal", 'True')
                                    ->first()){
                                        
                return $this->transactionResponse($payment->PaymentLogId, "0", "Duplicate Reversal Payment", 200);
                                        
            }else{
                
                $this->addNotification($registration, $payment);
                return $this->transactionResponse($payment->PaymentLogId, "0", "Reversal Payment Successful", 200);
            
            }

        }else{
            
            if ( floatval( $registration->items['total_amount'] ?? $registration['invoice_summary']['total_amount_with_payment_gateway_charge'] ?? $registration['amount'] ) != floatval($payment->Amount)) {
                return $this->transactionResponse($payment->PaymentLogId, "1", "Incorrect Amount", 200);
            }
            
            $dupNotification = PaymentNotification::where("PaymentLogId", $payment->PaymentLogId)
                                ->where("PaymentReference", $payment->PaymentReference)
                                ->where("Amount", $payment->Amount)
                                ->where("ReceiptNo", $payment->ReceiptNo)
                                ->first();

            if ($dupNotification) {
                return $this->transactionResponse($payment->PaymentLogId, "0", "Duplicate Payment", 200);
            }
            
            $this->addNotification($registration, $payment);
            return $this->transactionResponse($payment->PaymentLogId, "0", "Payment Successful Received", 200);
        }
        
    }

    public function transactionResponse($logId, $error, $errorMessage, $errorCode = 404) {

        $this->data->rootTag('PaymentNotificationResponse');
        $this->data->appendToRootTag('Payments');
        $this->data->appendToElement('Payments','Payment');
        $this->data->appendToElement('Payment','PaymentLogId', $logId);
        $this->data->appendToElement('Payment','Status', $error);
        $this->data->appendToElement('Payment','StatusMessage', $errorMessage);

        return response($this->data->getContent(), 200)->header('Content-Type', 'text/xml');

    }


    public function addNotification($registration, $payments){
        
        $payments = new Fluent (collect($payments->toArray())->map(function($payment) {
            return is_array($payment) ? json_encode($payment) : $payment;
        })->toArray());
        
        $paymentNotification = new PaymentNotification([
            "IsRepeated" => $payments->IsRepeated,
            "ProductGroupCode" => $payments->ProductGroupCode,
            "PaymentLogId" => $payments->PaymentLogId,
            "CustReference" => $payments->CustReference,
            "AlternateCustReference" => $payments->AlternateCustReference,
            "Amount" => $payments->Amount,
            "PaymentStatus" => $payments->PaymentStatus,
            "PaymentMethod" => $payments->PaymentMethod,
            "PaymentReference" => $payments->PaymentReference,
            "TerminalId" => $payments->TerminalId,
            "ChannelName" => $payments->ChannelName,
            "Location" => $payments->Location,
            "IsReversal" => $payments->IsReversal,
            "PaymentDate" => $payments->PaymentDate,
            "SettlementDate" => $payments->SettlementDate,
            "InstitutionId" => $payments->InstitutionId,
            "InstitutionName" => $payments->InstitutionName,
            "BranchName" => $payments->BranchName,
            "BankName" => $payments->BankName,
            "FeeName" => $payments->FeeName,
            "CustomerName" => $payments->CustomerName,
            "OtherCustomerInfo" => $payments->OtherCustomerInfo,
            "ReceiptNo" => $payments->ReceiptNo,
            "CollectionsAccount" => $payments->CollectionsAccount,
            "ThirdPartyCode" => $payments->ThirdPartyCode,
            "PaymentItems" => $payments->PaymentItems,
            "BankCode" => $payments->BankCode,
            "CustomerAddress" => $payments->CustomerAddress,
            "CustomerPhoneNumber" => $payments->CustomerPhoneNumber,
            "DepositorName" => $payments->DepositorName,
            "DepositSlipNumber" => $payments->DepositSlipNumber,
            "PaymentCurrency" => $payments->PaymentCurrency,
            "OriginalPaymentLogId" => $payments->OriginalPaymentLogId,
            "OriginalPaymentReference" => $payments->OriginalPaymentReference,
            "Teller" => $payments->Teller,
        ]);

        if(( ! $registration instanceof Registration) && ( (substr($payments->CustReference, 0 ,3) === 'DIP') || (substr($payments->CustReference, 0 ,3) === 'PRE') ) ){
                      
            $response = Http::post('https://nondegree.myunical.online/api/student/verification/confirm_invoice', [
                'amount' => $registration['invoice_summary']['total_amount_with_payment_gateway_charge'],
                "authorization_key" => "F00di3.",
                "payment_code" => $payments->CustReference
            ]);

        }
        
        if( ( ! $registration instanceof Registration) && ( ( substr($payments->CustReference, 0 ,3) === 'DPP') ) ){

            try {
                
                $res = Http::post('https://diploma.myunical.online/api/confirm-fees/'.$payments->CustReference.'/c3po20', ['amount' => $payments->Amount]);

            } catch (\Throwable $th) {
               
                Log::info($th);
            }
           

        }

        if( $registration instanceof Registration ){

            $paymentNotification->registration_id = $registration->id;

            if(strtolower($payments->IsReversal) == strtolower('True')){
                $registration->update(['payment_status' => false ]);
            }else{
                $registration->update(['payment_status' => true, 'payment_date' => now() ]);
            }
        }

        $paymentNotification->save();
        
    }

    protected function validateStudent($data)
    { 
        $data = new Fluent($data);
        
        if ( ! in_array($data['MerchantReference'], $this->marchantId) ) {
            return $this->error('Invalid Marchant Reference', $data);
        }
        
        $merchantRef = match(substr($data->CustReference, 0 ,3)){
            'EDC' =>  8194,
            'GSS' =>  8195,
            'PRE' =>  8199,
            'SIW' =>  8200,
            'DIP' =>  8201,
            'DPP' =>  8201,
            default => null
        };

        if($merchantRef != $data['MerchantReference']){
            return $this->error('Invalid Marchant Reference', $data);
        }

        $registration = match(substr($data->CustReference, 0 ,3)){
            'EDC' => $this->validateCustRefEDCAndGSS($data->CustReference),
            'GSS' => $this->validateCustRefEDCAndGSS($data->CustReference),
            'PRE' => $this->validateCustRefPREAndDIP($data->CustReference),
            'DIP' => $this->validateCustRefPREAndDIP($data->CustReference),
            'DPP' => $this->validateCustRefDPP($data->CustReference),
            default => null
        };
        

        if (! $registration) {
            return $this->error('Student Not Found', $data);
        }
        
        if($registration instanceof Registration && ! $registration->scheme?->is_bank_payment_enabled ){
            return $this->error("Payments are closed", $data);
        }

        $hasInvoiceItems = match(substr($data->CustReference, 0 ,3)){
            'EDC' => $this->checkForEDCAndGSSInvoiceItems($registration),
            'GSS' => $this->checkForEDCAndGSSInvoiceItems($registration),
            'PRE' => $this->checkForPREAndDIPInvoiceItems($registration),
            'DIP' => $this->checkForPREAndDIPInvoiceItems($registration),
            'DPP' => $this->checkForPREAndDIPInvoiceItems($registration),
            default => null
        };
        
        
        if( ! $hasInvoiceItems ){
            return $this->error('No Registration Items Found', $data);
        }

        
        $formattedRegistrationData = match(substr($data->CustReference, 0 ,3)){
            'EDC' => $this->formatRegistrationData($data, $registration->student?->fullname, $registration->items['total_amount'], array_merge($registration->items['payment_items'], $registration->items['other_charges'] )),
            'GSS' => $this->formatRegistrationData($data, $registration->student?->fullname, $registration->items['total_amount'], array_merge($registration->items['payment_items'], $registration->items['other_charges'] )),
            'PRE' => $this->formatRegistrationData($data, $registration['surname'].' '.$registration['othernames'], $registration['invoice_summary']['total_amount_with_payment_gateway_charge'], $registration['invoice_information_for_predegree_fee']),
            'DIP' => $this->formatRegistrationData($data, $registration['surname'].' '.$registration['othernames'], $registration['invoice_summary']['total_amount_with_payment_gateway_charge'], $registration['invoice_information_for_predegree_fee']),
            'DPP' => $this->formatRegistrationData($data, $registration['studentName'], $registration['amount'], $registration['paymentItems']),
        };
        
        
        return response($this->data->getContent(), 200)->header('Content-Type', 'text/xml');
    }

    protected function error($message = "Bad Url",  $data = null) {

        $this->data->rootTag('CustomerInformationResponse');
        $this->data->appendToRootTag('MerchantReference', $data['MerchantReference']);
        $this->data->appendToRootTag('Customers');
        $this->data->appendToElement('Customers','Customer');
        $this->data->appendToElement('Customer', 'Status', self::INVALID);
        $this->data->appendToElement('Customer','CustReference', $data['CustReference']);
        $this->data->appendToElement('Customer','FirstName');
        $this->data->appendToElement('Customer','Amount', 0);
        $this->data->appendToElement('Customer','StatusMessage', $message);

        return response($this->data->getContent(), 200)->header('Content-Type', 'text/xml');
    }

   
    protected function itemContent($tag, $title, $code, $price, $key)
    {
        $this->data->appendToElement($tag, 'ProductName', $title, $key);
        $this->data->appendToElement($tag, 'ProductCode', $code, $key);
        $this->data->appendToElement($tag, 'Quantity', 1, $key);
        $this->data->appendToElement($tag, 'Price', $price, $key);
        $this->data->appendToElement($tag, 'Subtotal', $price, $key);
        $this->data->appendToElement($tag, 'Tax', "0", $key);
        $this->data->appendToElement($tag, 'Total', $price, $key);
    }

    protected function validateCustRefEDCAndGSS($ref)
    {
        return Registration::where('invoice_number', $ref)->first();
    }

    protected function validateCustRefPREAndDIP($ref)
    {
        $response = Http::get('https://www.nondegree.myunical.online/api/student/verification/view_invoice/'.$ref);
        $response = $response->json();

        return isset($response['result']) ? new Fluent($response['result'][0]) : false;
    }

    protected function validateCustRefDPP($ref)
    {
        try {
           
            $response = Http::get('https://diploma.myunical.online/api/get-fees/'.$ref);
            $response = $response->json();
    
            return $response['status'] ? $response['data'] : false;
            
        } catch (\Throwable $th) {
            
            Log::info($th);
            return false;
        }
      
    }

    protected function checkForEDCAndGSSInvoiceItems($registration) : bool
    {
        return isset($registration->items['payment_items']) || isset($registration->items['other_charges']);
    }

    protected function checkForPREAndDIPInvoiceItems($registration)
    {
        return ! empty($registration);
    }

    

    protected function formatRegistrationData(Fluent $payloadData, $studentName, $totalAmount, $items )
    {
        $this->data->rootTag('CustomerInformationResponse');
        $this->data->appendToRootTag('MerchantReference', $payloadData['MerchantReference'] );
        $this->data->appendToRootTag('Customers');
        $this->data->appendToElement('Customers', 'Customer');
        $this->data->appendToElement('Customer', 'Status', "0");
        $this->data->appendToElement('Customer', 'CustReference', $payloadData['CustReference']);
        $this->data->appendToElement('Customer', 'CustomerReferenceAlternate');
        $this->data->appendToElement('Customer', 'FirstName', Str::upper($studentName));
        $this->data->appendToElement('Customer', 'LastName');
        $this->data->appendToElement('Customer', 'Email', "enquiries@normatechsystems.com");
        $this->data->appendToElement('Customer', 'Phone', "09038509510");
        $this->data->appendToElement('Customer', 'ThirdPartyCode');
        $this->data->appendToElement('Customer', 'Amount', $totalAmount);
        $this->data->appendToElement('Customer','PaymentItems');
        
        
        foreach ($items as $key => $item) {
            $item_name = htmlspecialchars($item['title'] ?? $item['program_of_study_name'] ?? $item['item_name']);
            $this->data->appendToElement('PaymentItems', 'Item');
            $this->itemContent('Item', $item_name, $item['item_code'], $item['fee'] ?? $item['program_fee'] ?? $item['amount'], $key);
        }
    }

    public function webhook()
    {
        try {

            if( request('event') == 'charge.success'){

                $data = request('data');

                OnlineTransaction::create([
                    'reference' => $data['reference'],
                    'amount' => $data['amount'],
                    'data' => $data
                ]);

                $registration = Registration::firstWhere('invoice_number', $data['metadata']['custom_field']['payment_code'] ?? false);

                if( $registration && ! $registration->payment_status ){

                    $registration->update(['payment_status' => true, 'payment_ref' => $data['reference'], 'payment_date' => now() ]);
                }

                $email = $data['customer']['email'] ?? '[[[]]]';

                $user = User::where(fn($query) => $query->where('email',strtolower($email)))
                            ->orWhere(fn($query) => $query->where('email',strtoupper($email)))
                            ->first();


                if( $user && $user->is_vendor ){

                    $token_transaction = TokenTransaction::firstWhere('reference', $data['reference']);   
                    
                    if( is_null($token_transaction) ){
        
                        $token_transaction = TokenTransaction::whereNotNull('token_reference')->where('token_reference', $data['metadata']['custom_field']['token_reference'] ?? null)->first();
                    }
        
                    if( $token_transaction ){
        
                        $token_transaction->update(['payment_status' => true ]);
                    }
        
                    else{
        
                        $token_transaction = TokenTransaction::create([
                            'vendor_id' => $user->id,
                            'scheme_id' => $user->schemes->first()->id,
                            'reference' => $data['reference'],
                            'type'      => 'credit',
                            'payment_status' => true,
                            'amount'    => $data['amount'],
                            'number_of_tokens' => floor( ($data['amount'])/(300*100) ),
                        ]);
                    }
                }

            }

            if( request('event') == 'paymentrequest.success'){

                $data = request('data');

                $registration = Registration::firstWhere('pos_reference', $data['offline_reference'] ?? false);
    
                if( $registration && ! $registration->payment_status ){

                    $registration->update(['payment_status' => true, 'payment_date' => now() ]);
                }  

            }
            

        } catch (\Throwable $th) {
            
            Log::error(['payment_failed' => request()->all() ]);
        }

        return response()->json(['message' => 'success'], 200);

    }

    public function settlePOSTransactions()
    {
        OnlineTransaction::where('data->channel', 'pos')
                        ->join('registrations', 'registrations.pos_reference', '=', 'online_transactions.reference')
                        ->where( fn($query) => $query->where('registrations.'))
                        ->select('registrations.*');
    }
}
