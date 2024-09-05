<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['account_ids' => 'array'];

    public function schemes()
    {
        return $this->belongsToMany(Scheme::class, 'course_schemes', 'course_id', 'scheme_id');
    }

    public function levels()
    {
        return $this->belongsToMany(Level::class, 'course_levels', 'course_id', 'level_id');
    }

    public function getStartLevelAttribute()
    {
        return $this->levels()->orderBy('level', 'asc')->first()?->level;
    }

    public function carryover()
    {
        return $this->belongsTo(Course::class, 'carryover_id');
    }

    public function getHasCarryoverAttribute()
    {
        return ! is_null($this->carryover);
    }

    public function getHasDocumentationFeeAttribute()
    {
        return ! is_null($this->documentation_fee);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function documentation_fee_account()
    {
        return $this->belongsTo(Account::class, 'documentation_fee_account_id');
    }

    public function registrations()
    {
        return $this->belongsToMany(Registration::class, 'course_registrations', 'course_id', 'registration_id');
    }

    public function getRegistrationCountAttribute()
    {
        return $this->registrations()->activeSession()->count();
    }

    public function venture()
    {
        return $this->belongsTo(Course::class, 'venture_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'course_departments', 'course_id', 'department_id')->withPivot(['semester_id', 'levels']);
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'course_semesters', 'course_id', 'semester_id')->withPivot(['max_registrations', 'registration_type']);
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'session_ventures', 'course_id', 'session')->withPivot(['max_registrations', 'registration_type']);
    }

    public function maxRegistrations(Session $session, $registration_type)
    {
        $registration_types = DB::table('session_ventures')
                                ->where('session_ventures.course_id', $this->id)
                                ->where('session_ventures.session', $session->session)
                                ->first()
                                ?->registration_type;

        $max_registration = json_decode( $registration_types, true );

        return $max_registration[$registration_type] ?? 0;
    
    }

    public function course_sessions()
    {
        return $this->belongsToMany( Session::class, 'session_courses', 'course_id', 'session_id' );
    }
    
    public function scopeActiveSession($query)
    {
        $session = session('session') ?? Session::activeSession();
        return $this->sessions()->where('session_id', $session);
    }
}
