<?php

namespace App\Http\Controllers\Officials;

use App\Models\TokenTransaction;
use App\Services\DataTable;
use Illuminate\Support\Facades\Schema;

class TokensController extends BaseController
{
    public function __construct(protected DataTable $datatable){
        parent::__construct();
    }

    public function purchases()
    {
        $tableHeadings = [
            's/n'           =>     'S/N',
            'vendor'        =>     'Vendor Name',
            'email'         =>     'Email',
            'reference'     =>     'Reference',
            'no_of_tokens'  =>     'Number of Tokens',
            'amount'        =>     'Amount',
            'payment_status'=>     'Payment Status',
            'description'   =>     'Type',
            'date'          =>     'Date',
        ];
        

        return view('officials.tokens.purchases', [
            'tableHeadings' => $tableHeadings, 
            'role' => $this->role, 
            'name' => 'reference,email,fullname',
            'page' => 'Token Purchases',  
            'ajaxUrl' => route('officials.token.transactions.get', [$this->role])
        ]);
    }

    public function registrations()
    {
       
        $tableHeadings = [
            's/n'           =>     'S/N',
            'vendor'        =>     'Vendor Name',
            'email'         =>     'Email',
            'no_of_tokens'  =>     'Number of Tokens',
            'amount'        =>     'Amount',
            'description'   =>     'Type',
            'date'          =>     'Date',
        ];
        

        return view('officials.tokens.purchases', [
            'tableHeadings' => $tableHeadings, 
            'role' => $this->role, 
            'name' => 'email,fullname',
            'page' => 'Token Registrations',  
            'ajaxUrl' => route('officials.token.registrations.get', [$this->role])
        ]);
    }


    public function getTokenTransactions()
    {
        $schemes = auth()->guard($this->role)->user()->schemes->pluck('id')->toArray();

        $token_transactions = collect(Schema::getColumnListing('token_transactions'));
        $token_transactions_table = 'token_transactions';
        $token_transactions_columns = $token_transactions->mapWithKeys(fn($column, $key) => [$column => $token_transactions_table ])->toArray();

        $users = collect(Schema::getColumnListing('users'));
        $users_table = 'users';
        $users_columns = $users->mapWithKeys(fn($column, $key) => [$column => $users_table ])->toArray();

        $transactions = TokenTransaction::query()
                                        ->join('users', 'token_transactions.vendor_id', '=', 'users.id')
                                        ->select('users.email', 'users.fullname', 'token_transactions.number_of_tokens','token_transactions.reference', 'token_transactions.amount', 'token_transactions.payment_status','token_transactions.type', 'token_transactions.created_at');

        $transactions = $this->datatable->search($transactions, $token_transactions_columns + $users_columns, $token_transactions_table, function($query) use($schemes){
            $query->whereIn('token_transactions.scheme_id', $schemes)
                ->where('token_transactions.payment_status', true)
                ->where('token_transactions.type', 'credit');
        });

        $items = collect($transactions->items())->map(function($transaction, $index) use($transactions) {
           
            return [
                's/n' => $transactions->firstItem() + $index, 
                'vendor' => $transaction->fullname ?? '',
                'email' => $transaction->email ?? '',
                'reference' => $transaction->reference,
                'no_of_tokens' => $transaction->number_of_tokens,
                'payment_status' => $transaction->payment_status ? 'Paid' : 'Pending',
                'amount' => "&#8358;".number_format($transaction->amount/100),
                'description' => $transaction->type,
                'date' => $transaction->created_at->toDateTimeString(),

            ];
        });


        return $this->datatable->response($items, $transactions);
    }


    public function getTokenRegistrations()
    {
        $schemes = auth()->guard($this->role)->user()->schemes->pluck('id')->toArray();
        
        $token_transactions = collect(Schema::getColumnListing('token_transactions'));
        $token_transactions_table = 'token_transactions';
        $token_transactions_columns = $token_transactions->mapWithKeys(fn($column, $key) => [$column => $token_transactions_table ])->toArray();


        $users = collect(Schema::getColumnListing('users'));
        $users_table = 'users';
        $users_columns = $users->mapWithKeys(fn($column, $key) => [$column => $users_table ])->toArray();


        $transactions = TokenTransaction::query()
                                        ->join('users', 'token_transactions.vendor_id', '=', 'users.id')
                                        ->select('users.email', 'users.fullname', 'token_transactions.number_of_tokens', 'token_transactions.amount', 'token_transactions.type', 'token_transactions.created_at');

        $transactions = $this->datatable->search($transactions, $token_transactions_columns + $users_columns, $token_transactions_table, function($query) use($schemes){
            $query->whereIn('token_transactions.scheme_id', $schemes)
                ->where('token_transactions.type', 'debit')
                ->where('token_transactions.payment_status', true);
        });

        $items = collect($transactions->items())->map(function($transaction, $index) use($transactions) {
           
            return [
                's/n' => $transactions->firstItem() + $index, 
                'vendor' => $transaction->fullname ?? '',
                'email' => $transaction->email ?? '',
                'no_of_tokens' => $transaction->number_of_tokens,
                'amount' => "&#8358;".number_format($transaction->amount/100),
                'description' => $transaction->type,
                'date' => $transaction->created_at->toDateTimeString(),

            ];
        });


        return $this->datatable->response($items, $transactions);
    }
}
