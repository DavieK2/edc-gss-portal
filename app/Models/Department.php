<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'department_faculties', 'department_id', 'faculty_id');
    }

    public function users()
    {
       return $this->belongsToMany(User::class, 'department_users', 'department_id', 'user_id');
    }

    public function getTitleAttribute()
    {
        return 'Dept. of '.$this->name; 
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_departments', 'department_id', 'course_id')->withPivot(['semester_id', 'levels']);
    }
}
