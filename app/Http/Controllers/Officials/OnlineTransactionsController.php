<?php

namespace App\Http\Controllers\Officials;

use App\Models\OnlineTransaction;
use App\Models\Registration;
use App\Models\Scheme;
use App\Models\TokenTransaction;
use App\Models\User;
use App\Services\DataTable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class OnlineTransactionsController extends BaseController
{
    public function __construct(protected DataTable $datatable){
        parent::__construct();
    }

    public function index()
    {
        $tableHeadings = [
            's/n' => 'S/N',
            'email' => 'Email',
            'reference' => 'Payment Reference',
            'amount' => 'Amount',
            'payment_date' => 'Payment Date',
            'action' => 'Action',
        ];
        

        return view('officials.transactions.index', [
            'tableHeadings' => $tableHeadings, 
            'role' => $this->role, 
            'name' => 'reference',
            'page' => 'Payment Transactions',  
            'ajaxUrl' => route('officials.transactions.get', [$this->role])
        ]);
    }

    public function getTransactions()
    {
        $online_transactions = collect(Schema::getColumnListing('online_transactions'));
        $online_transactions_table = 'online_transactions';
        $online_transactions_columns = $online_transactions->mapWithKeys(fn($column, $key) => [$column => $online_transactions_table ])->toArray();

        $payments = OnlineTransaction::query();
        $payments = $this->datatable->search($payments, $online_transactions_columns, $online_transactions_table);

        $items = collect($payments->items())->map(function($payment, $index) use($payments) {
           
            return [
                's/n' => $payments->firstItem() + $index, 
                'email' => $payment->data['customer']['email'] ?? '',
                'reference' => $payment->reference,
                'amount' => "&#8358;".number_format($payment->amount/100),
                'payment_date' => $payment->created_at->toDateTimeString(),
                'action' => [
                    [
                        'title' => 'Confirm Payment',
                        'url' => route('officials.transactions.confirm',[$this->role, $payment])
                    ]
                ]

            ];
        });


        return $this->datatable->response($items, $payments);
    }

    public function confirmPayment($role, OnlineTransaction $transaction)
    {
        $email = $transaction->data['customer']['email'] ?? '[[[]]]';

        $user = User::where(fn($query) => $query->where('email',strtolower($email)))
                    ->orWhere(fn($query) => $query->where('email',strtoupper($email)))
                    ->first();

        if( is_null($user) ){
            
            alert()->error('Cannot find user');
            return back();
        }

        if( $user->is_vendor ){

            $token_transaction = TokenTransaction::firstWhere('reference', $transaction->reference);   
            
           
            if( is_null($token_transaction) ){

                $token_transaction = TokenTransaction::firstWhere('token_reference', $transaction->data['metadata']['custom_field']['token_reference'] ?? ' ');
            }

            if($token_transaction){

                $token_transaction->update(['payment_status' => true ]);
            }

            else{

                $token_transaction = TokenTransaction::create([
                    'vendor_id' => $user->id,
                    'scheme_id' => $user->schemes->first()->id,
                    'reference' => $transaction->reference,
                    'type'      => 'credit',
                    'payment_status' => true,
                    'amount'    => $transaction->amount,
                    'number_of_tokens' => floor(($transaction->amount)/(300*100)),
                ]);
            }
        }
        
        if($user->is_student){

            $registration = Registration::firstWhere('invoice_number', $transaction->data['metadata']['custom_field']['payment_code'] ?? ' ');

            if( $registration ){

                $registration->update(['payment_status' => true, 'payment_ref' => $transaction->data['reference'], 'payment_date' => $transaction->created_at->toDateTimeString() ]);
            }
        }

        alert('Success', 'Payment Confirmed');
        return back();
    }

    public function verifyTransaction()
    {
        $payment_ref = request('payment_ref');
        $scheme = Scheme::firstWhere('name',request('scheme'));
        $is_registration = request('type') === 'registration';
        $is_token = request('type') === 'token';
        
        if( OnlineTransaction::firstWhere('reference', $payment_ref) ){

            return response()->json(['status' => false, 'message' => 'Transaction has already been verified']);
        }

        $token = match(true){
            $is_registration => $scheme?->registration_payment_key,
            $is_token => $scheme?->token_payment_key,
            default => null

        };

        if( is_null($token) ){

            return response()->json(['status' => false, 'message' => 'Please check inputed values']);
        }

        $transaction = Http::withToken($token)->get('https://api.paystack.co/transaction/verify/'.$payment_ref);
        $response = $transaction->json();

        if(! $response['status'] ?? false){

            return response()->json(['status' => false, 'message' => 'Payment reference does not exist']);
        }

        OnlineTransaction::create([
            'amount' => $response['data']['amount'],
            'reference' => $response['data']['reference'],
            'data'  => $response['data'],
            'created_at' => $response['data']['created_at'],
        ]);

        return response()->json(['status' => true, 'message' => 'Trasaction has been verified']);


    }
}
