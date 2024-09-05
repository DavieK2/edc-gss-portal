<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'semesters' => 'array',
        'charges' => 'array',
        'charges_online' => 'array',
        'token_accounts' => 'array',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_schemes', 'scheme_id', 'course_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'scheme_users', 'scheme_id', 'user_id');
    }

    public function token_transactions()
    {
        return $this->hasMany(TokenTransaction::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getIsEdcAttribute()
    {
        return $this->name == 'EDC';
    }

    public function getIsGssAttribute()
    {
        return $this->name == 'GSS';
    }

}
