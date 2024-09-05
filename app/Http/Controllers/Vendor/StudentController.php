<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\DataTable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StudentController extends Controller
{
    public function __construct(protected DataTable $datatable){}
    
    public function index()
    {
        return view('vendor.students.index', [
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
            'page' => 'Students', 
            'ajaxUrl' => route('vendors.students.search')
        ]);
    }

    public function searchStudent()
    {
        $query = StudentProfile::query()->join('users', 'users.id', '=', 'student_profiles.user_id')->select('users.email', 'users.phone_number', 'student_profiles.*');

        $student_profiles_columns = collect(Schema::getColumnListing('student_profiles'));
        $student_profiles_table = 'student_profiles';
        $student_profiles_columns = $student_profiles_columns->mapWithKeys(fn($column, $key) => [ $column => $student_profiles_table ])->toArray();
        
        $users_columns = collect(Schema::getColumnListing('users'));
        $users_table = 'users';
        $users_columns = $users_columns->mapWithKeys(fn($column, $key) => [ $column => $users_table ])->toArray();


        $students = $this->datatable->search($query, $student_profiles_columns + $users_columns, $student_profiles_table, withSession: false);

        $items = collect($students->items())->map(function($student, $index) use($students){
            
            $additionalActions = Gate::allows('edit-student-data') ? [ [
                'title' => 'Edit Student',
                'url' => route('vendors.students.edit.profile', [$student->id])
            ] ] : [];
            
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
                            'title' => 'Change Password',
                            'url' => route('vendors.students.edit.password', [$student->id])
                        ],
                        
                        ...$additionalActions
                    ]
                ];
        });

        return $this->datatable->response($items, $students);

    }


    public function editPassword(StudentProfile $student)
    {
        return view('vendor.students.password', [
            'student' => $student
        ]);
    }


    public function editStudent( StudentProfile $student )
    {
        return view('vendor.students.edit', [
            'student' => $student
        ]);
    }

    public function updateStudent( StudentProfile $student )
    {
        $user_data = request()->validate([
            'email' => ['required','email', function($attr, $value, $fail) use($student){
                if( $value != $student->user->email && User::where('email', $value)->exists() ){
                    $fail('Email has already been taken');
                }
            }],
            'phone_number' => ['required', function($attr, $value, $fail) use($student){
                if( $value != $student->user->phone_number && User::where('phone_number', $value)->exists() ){
                    $fail('Phone number has already been registered');
                }
            }]
        ]);

        $profile_data = request()->validate([
            'mat_no' => 'required',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'required|exists:departments,id',
        ]);


        $profile_data['student_code'] = $profile_data['mat_no'];

        $profile_data['edited_by'] = [...($student->edited_by ?? []), ['edited_by' => auth()->guard('vendor')->user()->fullname, 'date' => now()->toDateTimeString()]];


        $student->user->update($user_data);

        $student->update($profile_data);

        Registration::where('student_profile_id', $student->id)->update([
            'reg_no' => $student->student_code, 
            'department' => $student->department->name, 
            'faculty_id' => $student->faculty_id, 
        ]);

        return redirect( route('vendors.students.index') );
    }

    public function updatePassword(User $student)
    {
        $password = request()->validate(['password' => 'required']);

        $student->update([ 'password' => Hash::make( $password['password'] ) ] );
        
        alert( 'Success', 'Password Succesfully Changed' );
            
        return redirect( route('vendors.students.index') );
    }
}
