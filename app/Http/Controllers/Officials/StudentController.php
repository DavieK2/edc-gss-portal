<?php

namespace App\Http\Controllers\Officials;

use App\Models\Level;
use App\Models\Registration;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\DataTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StudentController extends BaseController
{
    public function __construct(public DataTable $datatable){
        parent::__construct();
    }

    public function index()
    {
        return view('officials.students.index', [
            'tableHeadings' => [
                's/n' => 'S/N',
                'student_name' => 'Student Name',
                'email' => 'Email',
                'phone_number' => 'Phone Number',
                'mat_no' => 'Matric Number',
                'department' => 'Department',
                'faculty' => 'Faculty',
                'level' => 'Level',
                'action' => 'Action',
            ],
            'filters' => [
                'level_id' => Level::get()->mapWithKeys(fn($level) => [ $level->id => $level->level ])->toArray()
            ],
            'role' => $this->role, 
            'page' => 'Students', 
            'ajaxUrl' => route('officials.students.search', [$this->role])
        ]);
    }

    public function create()
    {
        return view('officials.students.create', [
            'role' => $this->role,
        ]);
    }

    public function edit($role, StudentProfile $student)
    {
        return view('officials.students.edit', [
            'role' => $this->role,
            'student' => $student
        ]);
    }


    public function store()
    {
        $user_data = request()->validate([
            'fullname' => 'required|string',
            'email' => 'required',
            'password' => 'required',
            'phone_number' => 'required',
            'gender' => 'required|string',
        ]);


        $profile_data = request()->validate([
            'mat_no' => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'level_id' => 'required|exists:levels,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        $user_data['password'] = Hash::make($user_data['password']);

        $user = User::firstWhere('email', $user_data['email']);

        $profile_data['student_code'] = $profile_data['mat_no'];

        if( is_null( $user ) ){

            request()->validate([
                'email' => 'required|email|unique:users,email',
            ]);

            $user = User::create($user_data);

            $user->assignRole('student');

        }

        if( $user->profile()->where('session_id', $profile_data['session_id'])->exists() ){
            
            alert()->error('Student Profile already exists for this session');
            return back();
        }
        
        $profile_data['fullname'] = $user->fullname;

        $user->profile()->create($profile_data);

        return redirect(route('officials.students.index', $this->role ));


    }

    public function update($role, StudentProfile $student)
    {        
        $user_data = request()->validate([
            'fullname' => 'required|string',
            'email' => ['required','email', function($attr, $value, $fail) use($student){
                if($value != $student->user->email && User::where('email', $value)->exists()){
                    $fail('Email has already been taken');
                }
            }],
            'phone_number' => 'required',
            'gender' => 'required|string',
        ]);

        $student->user->update($user_data);

        $profile_data = request()->validate([
            'mat_no' => 'nullable',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
            'level_id' => 'required|exists:levels,id',
            'session_id' => 'required|exists:sessions,id',
        ]);

        $profile_data['fullname'] = $student->user->fullname;
        $profile_data['student_code'] = $profile_data['mat_no'] ?? $student->student_code;
        
        $student->update($profile_data);

        if( request()->has('passport') ){
            
            $file = request()->file('passport');
        
            $student->update(['profile_image' => $file->store('profile_images', 'public')]); $file = request()->file('passport');
            $file = $file->store('profile_images', 'public');
            
            rename(public_path("images/$file"), "images/$file");
            
            $student->update(['profile_image' => $file]);
        }

        Registration::where('student_profile_id', $student->id)->update([
            'reg_no' => $student->student_code, 
            'level' => $student->level->level, 
            'department' => $student->department->name, 
            'faculty_id' => $student->faculty_id, 
        ]);

        return redirect(route('officials.students.index', $this->role ));


    }

    public function searchStudent()
    {
        $query = StudentProfile::query()->join('users', 'users.id', '=', 'student_profiles.user_id')->select('users.email', 'users.phone_number', 'student_profiles.*');

        $student_profiles_columns = collect(Schema::getColumnListing('student_profiles'));
        $student_profiles_table = 'student_profiles';
        $student_profiles_columns = $student_profiles_columns->mapWithKeys(fn($column, $key) => [$column => $student_profiles_table ])->toArray();
        
        $users_columns = collect(Schema::getColumnListing('users'));
        $users_table = 'users';
        $users_columns = $users_columns->mapWithKeys(fn($column, $key) => [$column => $users_table ])->toArray();


        $students = $this->datatable->search($query, $student_profiles_columns + $users_columns, $student_profiles_table);

        $items = collect($students->items())->map(function($student, $index) use($students){
            
            return [
                    's/n' => $students->firstItem() + $index,
                    'student_name' => $student->fullname,
                    'email' => $student->email,
                    'phone_number' => $student->phone_number,
                    'mat_no' => $student->student_code,
                    'department' => $student->department?->name,
                    'faculty' => $student->faculty?->name,
                    'level' => $student->level?->level,
                    'action' => [
                        [
                            'title' => 'Edit Student',
                            'url' => route('officials.students.edit', [$this->role, $student->id])
                        ],
                        [
                            'title' => 'Change Password',
                            'url' => route('officials.students.edit.password', [$this->role, $student->id])
                        ]
                    ]
                ];
        });

        return $this->datatable->response($items, $students);

    }

    public function editPassword($role, StudentProfile $student)
    {
        return view('officials.students.password', [
            'role' => $this->role,
            'student' => $student
        ]);
    }


    public function updatePassword($role, User $student)
    {
        $password = request()->validate(['password' => 'required']);

        $student->update([ 'password' => Hash::make( $password['password'] ) ] );
        
        alert('Success','Password Succesfully Changed');
            
        return redirect(route('officials.students.index', $this->role ));
    }

    public function deleteStudentProfile($role, StudentProfile $student)
    {
        $student->delete();
        return redirect(route('officials.students.index', $this->role ));
    }
}
