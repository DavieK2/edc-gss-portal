<?php

namespace App\Http\Controllers\Officials;

use App\Models\Account;
use App\Models\Course;
use App\Models\Department;
use App\Models\Level;
use App\Models\Scheme;
use App\Models\Semester;
use App\Models\Session;

class CourseController extends BaseController
{
    public function index($role, Scheme $scheme)
    {
        
        $session_courses = Session::firstWhere( 'session', Session::activeSession() )->courses;
        $courses = $scheme->courses->intersect( $session_courses );

        return view('officials.courses.index', ['role' => $this->role, 'courses' => $courses, 'page' => ucwords($scheme->name.' Courses'), 'scheme' => $scheme ]);
    }


    public function create($role, Scheme $scheme)
    {
        return view('officials.courses.create', ['role' => $this->role, 'scheme' => $scheme, 'accounts' => Account::all() ]);
    }

    public function store($role, Scheme $scheme)
    {
        $data = request()->validate([
            'fee'               =>  'required',
            'item_code'         =>  'required',
            'title'             =>  'required',
            'split_fee'         =>  'required',
            'split_account'     =>  'required',
            'split_account.*'   =>  'exists:accounts,id'
        ]);

        $accounts = [];
        
        foreach ($data['split_account'] as $key => $value) {

            $account = Account::find($value);

            $accounts[] = [
                'account_id'      =>    $account?->account_id,
                'account_name'    =>    $account?->account_name,
                'account_number'  =>    $account?->account_number,
                'fee'             =>    $data['split_fee'][$key]
            ];
        }

        if( collect($accounts)->sum('fee') != $data['fee']){

            alert()->error('Total Fee and Split Fee does not match');

            return back();
        }

        $course = Course::create([
            'title'        => $data['title'],
            'item_code'    => $data['item_code'],
            'fee'          => $data['fee'],
            'account_ids'  => $accounts
        ]);

        $scheme->courses()->syncWithoutDetaching($course);

        return redirect(route('officials.courses.index', [ $this->role, $scheme ]));
    }

    public function addCourses($role, Scheme $scheme)
    {
        $courses = $scheme->courses;
        $session_courses = Session::firstWhere( 'session', Session::activeSession() )->courses;
        $levels = Level::get();

        return view('officials.courses.add_courses', ['role' => $this->role, 'courses' => $courses, 'scheme' => $scheme, 'session_courses' => $session_courses->pluck('id')->toArray(), 'levels' => $levels ]);
    }

    public function addCoursesToSession($role, Scheme $scheme)
    {
        $data = request()->validate([
            'courses'   => 'required|array',
            // 'levels'    => 'required|array',
            // 'courses.*' => 'exists:courses,id',
            // 'levels.*'  => 'exists:levels,level'
        ]);

        Session::firstWhere( 'session', Session::activeSession() )->courses()->syncWithoutDetaching( $data['courses'] );

        return redirect(route('officials.courses.index', [$this->role, $scheme ]));

    }

    public function edit($role, Course $course, Scheme $scheme)
    {
        return view('officials.courses.edit', ['role' => $this->role, 'course' => $course, 'accounts' => Account::get(), 'scheme' => $scheme ]);
    }

    public function update($role, Course $course, Scheme $scheme)
    {
        $data = request()->validate([
            'title'                 => 'required|string',
            'item_code'             => 'required|string',
            'fee'                   => 'required|integer',
            'documentation_fee'     => 'nullable|numeric',
            'split_account'         => 'nullable|array',
            'split_account.*'       => 'exists:accounts,id',
            'split_fee'             => 'nullable|array'
        ]);

        $account_id = request('account_id');

        if( $account_id ){
           
            unset($data['split_account']);
            unset($data['split_fee']);

            $course->update( $data + ['account_id' => $account_id] );

            return redirect(route('officials.courses.index', [$this->role, $scheme ]));

        }

        $accounts = [];
        
        foreach ($data['split_account'] as $key => $value) {

            $account = Account::find($value);

            $accounts[] = [
                'account_id'      =>    $account?->account_id,
                'account_name'    =>    $account?->account_name,
                'account_number'  =>    $account?->account_number,
                'fee'             =>    $data['split_fee'][$key]
            ];
        }

        if( collect($accounts)->sum('fee') != $data['fee'] ){

            alert()->error('Total Fee and Split Fee does not match');

            return back();
        }

        unset($data['split_account']);
        unset($data['split_fee']);

        $course->update($data + ['account_ids' => $accounts ]);
       
        return redirect( route('officials.courses.index', [ $this->role, $scheme ]) );
      
    }

    public function editDepartments($role, Course $course, Scheme $scheme)
    {
        return view('officials.courses.edit_departments', [
                                                'role' => $this->role, 
                                                'scheme' => $scheme, 
                                                'course' => $course, 
                                                'departments' => Department::get(), 
                                                'semesters' => Semester::all(), 
                                                'levels' => [100,200,'200 DE','300+']
                                            ]);
    }

    public function updateDepartments($role, Course $course, Scheme $scheme)
    {
        
        $data = request()->validate(['departments' => 'required'], ['departments.required' => 'Please select a department']);

        $errors = collect();
        
        collect($data['departments'])->each(function($department, $key) use($errors){

            if(! isset($department['semesters']) ){
                $errors->push('Please select a semester for '.Department::find($key)->name);
            }
    
            if(! isset($department['levels']) ){
                $errors->push('Please select a level for '.Department::find($key)->name);
            }
        });

        if($errors->isNotEmpty()){
            return back()->with(['error' => $errors->toArray(), 'departments' => $data['departments'] ]);
        }

        collect($data['departments'])->each(function($department, $key) use($course){
    
            $course->departments()->syncWithoutDetaching([ $key => [
                                                                    'semester_id' => json_encode($department['semesters']),
                                                                    'levels' => json_encode($department['levels']) 
                                                                   ]
                                                         ]); 
        });

        return redirect(route('officials.courses.index', [$this->role, $scheme ]));
    }
}
