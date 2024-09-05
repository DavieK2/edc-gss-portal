<?php

namespace App\Http\Controllers\Officials;

use App\Models\Registration;
use App\Services\DataTable;
use Illuminate\Support\Facades\Schema;

class PaymentsController extends BaseController
{
    public function __construct(protected DataTable $datatable){
        parent::__construct();
    }

    public function bank()
    {
        $tableHeadings = [
            's/n' => 'S/N',
            'student_name' => 'Student Name',
            'invoice_number' => 'Payment Code',
            'amount_paid' => 'Amount Paid',
            'payment_ref' => 'Payment Ref.',
            'payment_date' => 'Payment Date',
            'action' => 'Action',
        ];

        return view('officials.payments.bank', [
            'tableHeadings' => $tableHeadings, 
            'role' => $this->role,
            'name' => 'PaymentReference,student_name,invoice_number',
            'page' => 'Bank Payments',  
            'ajaxUrl' => route('officials.payments.get', [$this->role, 'bank'])
        ]);
    }

    public function online()
    {
        $tableHeadings = [
            's/n' => 'S/N',
            'student_name' => 'Student Name',
            'invoice_number' => 'Payment Code',
            'amount_paid' => 'Amount Paid',
            'payment_ref' => 'Payment Ref.',
            'payment_date' => 'Payment Date',
            'action' => 'Action',
        ];
        

        return view('officials.payments.online', [
            'tableHeadings' => $tableHeadings, 
            'role' => $this->role, 
            'name' => 'reference,student_name,invoice_number',
            'page' => 'Online Payments',  
            'ajaxUrl' => route('officials.payments.get', [$this->role, 'online'])
        ]);
    }

    public function getPayments($role, $paymentType)
    {
        $schemes = auth()->guard($this->role)->user()->schemes->pluck('id');
        $root_table = $paymentType == 'online' ? 'online_transactions' : 'payment_notifications';

        $online_transactions = collect(Schema::getColumnListing('online_transactions'));
        $online_transactions_table = 'online_transactions';
        $online_transactions_columns = $online_transactions->mapWithKeys(fn($column, $key) => [$column => $online_transactions_table ])->toArray();
        
        $payment_notifications = collect(Schema::getColumnListing('payment_notifications'));
        $payment_notifications_table = 'payment_notifications';
        $payment_notifications_columns = $payment_notifications->mapWithKeys(fn($column, $key) => [$column => $payment_notifications_table ])->toArray();

        $registrations = collect(Schema::getColumnListing('registrations'));
        $registrations_table = 'registrations';
        $registrations_columns = $registrations->mapWithKeys(fn($column, $key) => [$column => $registrations_table ])->toArray();

        $payments = Registration::when($paymentType == 'online', function($query, $condition) use($schemes){
                                    $query->join('online_transactions', 'registrations.payment_ref', '=', 'online_transactions.reference')
                                         ->select('online_transactions.*', 'online_transactions.created_at as date', 'registrations.invoice_number', 'registrations.student_name', 'registrations.session', 'registrations.scheme_id', 'registrations.payment_status');

                                }, function($query) use($schemes){
                                    
                                    $query->join('payment_notifications', 'registrations.invoice_number', '=', 'payment_notifications.CustReference')
                                        ->select('payment_notifications.*', 'payment_notifications.created_at as date', 'registrations.student_name', 'registrations.invoice_number', 'registrations.session', 'registrations.scheme_id', 'registrations.payment_status');
                    });
       
        
        $payments = $this->datatable->search($payments, $payment_notifications_columns + $registrations_columns + $online_transactions_columns, $root_table, function($query) use($schemes){
                $query->whereIn('registrations.scheme_id', $schemes)
                    ->where('registrations.payment_status', true);
        });
        
        $items = collect($payments->items())->map(function($payment, $index) use($payments) {
            
            $amount = (($payment->amount ?? $payment->Amount)/($payment->amount ? 100 : 1));
           
            return [
                's/n' => $payments->firstItem() + $index, 
                'student_name' => $payment->student_name,
                'invoice_number' => $payment->invoice_number,
                'amount_paid' => "&#8358;".number_format($amount, 2),
                'payment_ref' => $payment->reference ?? $payment->PaymentReference,
                'payment_date' => $payment->PaymentDate ?? $payment->created_at->toDateTimeString(),
                'action' => [
                    [
                        'title' => 'View Invoice',
                        'url' => route('officials.registrations.show', [$this->role, $payment->invoice_number])
                    ]
                ]

            ];
        });


        return $this->datatable->response($items, $payments);
    }
}
