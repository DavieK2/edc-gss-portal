<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OnlineTransaction;
use App\Models\TokenTransaction;
use App\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class VendorTokenController extends Controller
{
    public function __construct(protected DataTable $datatable){}

    public function index()
    {
        return view('vendor.token.index', [
            'tableHeadings' => [
                's/n' => 'S/N',
                'no_of_tokens' => 'No. of Tokens',
                'description' => 'Description',
                'scheme' => 'Scheme',
                'amount' => 'Amount',
                'reference' => 'Reference',
                'payment_status' => 'Payment Status',
                'date' => 'Date',
            ], 
            'page' => 'Token', 
            'ajaxUrl' => route('vendors.token.search')
        ]);
    }
    
    public function search()
    {
        $user = auth()->guard('vendor')->user();

        $query = TokenTransaction::query();

        $token_transactions_columns = collect(Schema::getColumnListing('token_transactions'));
        $token_transactions_table = 'token_transactions';
        $token_transactions_columns = $token_transactions_columns->mapWithKeys(fn($column, $key) => [ $column => $token_transactions_table ])->toArray();


        $token_transactions = $this->datatable->search($query, $token_transactions_columns, $token_transactions_table, withSession: false, closure: function($query) use($user){
            return $query->where('vendor_id', $user->id)->with('scheme');
        });

        $items = collect($token_transactions->items())->map(function($token_transaction, $index) use($token_transactions){
           
            return [
                    's/n' => $token_transactions->firstItem() + $index,
                    'no_of_tokens' => $token_transaction->number_of_tokens,
                    'description' => $token_transaction->type == 'debit' ? 'Registration' : 'Purchase',
                    'scheme' => $token_transaction->scheme->name,
                    'amount' => '&#8358; '.number_format($token_transaction->price, 2),
                    'reference' => $token_transaction->reference,
                    'payment_status' => ($token_transaction->payment_status || $token_transaction->type == 'debit') ? 'Success' : 'Failed',
                    'date' => Carbon::parse($token_transaction->created_at)->toDateTimeString()
                ];
        });

        return $this->datatable->response($items, $token_transactions);
    }

    public function create()
    {
        return view('vendor.token.create');
    }

    public function store()
    {
        sleep(30);

        $online_payment = OnlineTransaction::firstWhere('reference', request('reference'));

        auth()->guard('vendor')->user()->credit($online_payment);

        return redirect(route('vendor.token.index'));

    }
   
}
