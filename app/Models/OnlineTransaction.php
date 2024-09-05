<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function online_registration()
    {
        return $this->belongsTo(Registration::class, 'reference', 'payment_ref');
    }

    public function scopeActiveSession($query)
    {
        return $query;
    }
}
