<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class Session extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'registration_semesters' => 'array',
        'can_register' => 'array'
    ];

    public function scopeActiveSession($query)
    {
        return session('session') ?? $query->where('status', true)->first()?->session ?? $query->latest()->first()?->session;
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'session_courses', 'session_id', 'course_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('registration_sessions', function(Builder $builder){

            if( ! Gate::allows('view-sessions-list') ){
                
                $builder->whereNotNull('can_register')->orderBy('session', 'asc');
            }
            $builder->orderBy('session', 'asc');
        });
    }

}
