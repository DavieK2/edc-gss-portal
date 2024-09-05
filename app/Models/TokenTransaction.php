<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public const TRANSACTION_TYPE 

    public function scheme()
    {
        return $this->belongsTo(Scheme::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function getPriceAttribute()
    {
        return $this->amount / 100;
    }

    public function scopeActiveSession($query)
    {
        return $query;
    }
}
