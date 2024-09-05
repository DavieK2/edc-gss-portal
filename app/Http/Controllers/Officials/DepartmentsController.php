<?php

namespace App\Http\Controllers\Officials;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Validation\Rule;

class DepartmentsController extends BaseController
{
    
    public function index()
    {
        return view('officials.departments.index', ['departments' => Department::get(), 'role' => $this->role, 'page' => 'Departments' ]);
    }

    public function edit($role, Department $department)
    {
        return view('officials.departments.edit', ['role' => $this->role, 'department' => $department, 'faculties' => Faculty::get()]);
    }

    public function create()
    {
        return view('officials.departments.create', ['role' => $this->role, 'faculties' => Faculty::get() ]);
    }

    public function update($role, Department $department)
    {
        $data = request()->validate([
            'name' => [ Rule::when(request('name') != $department->name, ['required', 'string', 'unique:departments'])],
        ]);
        
        $department->update(['name' => $data['name']]);

        return redirect(route('officials.departments.index', $role ));
    }

    public function store($role)
    {
        $data = request()->validate([
            'name' => 'required|string|unique:departments',
            'faculty' => 'required|exists:faculties,id'
        ]);

        $department = Department::create(['name' => $data['name']]);

        $department->faculties()->syncWithoutDetaching($data['faculty']);

        return redirect(route('officials.departments.index', $role ));
    }
}
