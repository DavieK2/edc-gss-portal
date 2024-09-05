<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
    {
        return $this->hasMany(StudentProfile::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_levels', 'level_id', 'course_id');
    }
}
