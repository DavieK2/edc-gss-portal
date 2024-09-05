<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Faculty;

class DepartmentsController extends Controller
{
    public function getDepartments(Faculty $faculty)
    {
        return response()->json(['departments' => $faculty->departments->map(fn($dep) => ['id' => $dep->id, 'name' => $dep->name ]) ]);
    }
}
