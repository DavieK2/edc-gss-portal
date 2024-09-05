<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentNotification extends Model
{
    use HasFactory;

    protected $guarded =  [];

    // protected $casts = [
    //     'PaymentItems' => 'array',
    //     'Location' => 'array',
    //     'TerminalId' => 'array',
    //     'FeeName' => 'array',
    //     'CustomerName' => 'array',
    //     'CustomerAddress' => 'array',
    //     'OriginalPaymentLogId' => 'array',
    //     'OriginalPaymentReference' => 'array',
    //     'DepositorName'  => 'array',
    //     'CustomerPhoneNumber' => 'array',
    //     'ThirdPartyCode' => 'array',
    // ];

    public function transaction() 
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }    
}
