<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'items' => 'array'
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function online_transaction()
    {
        return $this->hasOne(OnlineTransaction::class, 'reference', 'payment_ref');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_registrations', 'registration_id', 'course_id');
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function venture()
    {
        return $this->belongsTo(Course::class, 'venture_id');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function bank_payments()
    {
        return $this->hasMany(PaymentNotification::class, 'registration_id');
    }

    public function bank_payment()
    {
        return $this->bank_payments()->where('IsReversal', 'False')->first();
    }

    public function scopeActiveSession($query)
    {
        $session = session('session') ?? Session::activeSession();
        return $query->where('registrations.session', $session);
    }
}
