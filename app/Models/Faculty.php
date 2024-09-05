<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_faculties', 'faculty_id', 'department_id');
    }

    public function getTitleAttribute()
    {
        return 'Faculty of '.$this->name; 
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'faculty_id');
    }
}
