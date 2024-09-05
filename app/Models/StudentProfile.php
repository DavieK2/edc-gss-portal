<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'edited_by' => 'array'
    ];

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'student_profile_id');
    }

    public function department()
    {
       return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
       return $this->belongsTo(Faculty::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function scopeActiveSession($query)
    {
        return $query->where('session_id', is_null(session('session')) ? Session::where('status', true)->first()?->id : Session::firstWhere('session', session('session'))?->id);
    }
}
