<?php

namespace App\Http\Controllers\Officials;

use App\Models\Department;
use App\Models\Faculty;

class FacultiesController extends BaseController
{
    public function index()
    {
        return view('officials.faculties.index', ['faculties' => Faculty::get(), 'role' => $this->role, 'page' => 'Faculties' ]);
    }

    public function edit($role, Faculty $faculty)
    {
        return view('officials.faculties.edit', ['role' => $this->role, 'faculty' => $faculty, 'departments' => Department::all() ]);
    }

    public function show($role, Faculty $faculty)
    {
        return view('officials.faculties.show', ['role' => $this->role, 'faculty' => $faculty ]);
    }

    public function create()
    {
        return view('officials.faculties.create', ['role' => $this->role ]);
    }

    public function update($role, Faculty $faculty)
    {
        $data = request()->validate(['name' => 'required|string', 'departments' => 'required', 'departments.*' => 'exists:departments,id']);

        $faculty->update(['name' => $data['name']]);

        $faculty->departments()->sync($data['departments']);

        return redirect(route('officials.faculties.index', $role ));
    }

    public function store($role)
    {
        Faculty::create(request()->validate(['name' => 'required|string']));

        return redirect(route('officials.faculties.index', $role ));
    }
}
