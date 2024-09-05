<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Session;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\SchoolFeesVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentRegistrationController extends Controller
{
    public function index()
    {
        $levels = [100,200,'200 (Direct Entry)',300,400,500,600,700, 'Contact1', 'Contact2', 'Contact3', 'Contact4', 'Contact5', 'Contact6'];

        $sessions = Session::get();
        $sessions = $sessions->filter(fn($session) => ! empty( $session->can_register ) );

        return view('student.auth.registration.index', ['sessions' => $sessions, 'levels' => Level::orderBy('level', 'asc')->get()->filter(fn($level) => in_array($level->level, $levels)) ]);
    }

    public function verifyFees()
    {
        
        $data = request()->validate(['pin' => 'required', 'session' => 'required|exists:sessions,id', 'level' => 'required|exists:levels,id']);

        $student_data = (new SchoolFeesVerificationService())->verifyFees($data);

        if($student_data->status === false) return back()->with('error', 'Invalid Payment Pin');

        $student_data = (array) $student_data;
        $student_data['level'] = $data['level'];

        unset($student_data['status']);

        session(['student_data' => $student_data]);

        return view('student.auth.registration.create', [
            'student_data' => $student_data,
        ]);

    }

    public function store()
    {
        if( ! session()->has('student_data') ) return redirect(route('student.auth.login.index'));

        $student_data = collect( session('student_data') );

        $student = StudentProfile::firstWhere('student_code', $student_data->get('mat_no') ?? $student_data->get('school_fees_pin'));
       
        $data = collect(request()->validate([
            'level'         => [ Rule::requiredIf( is_null($student_data['level_id']) ), 'exists:levels,id' ],
            'session'       => [ Rule::requiredIf( is_null($student_data['session']) ), 'exists:sessions,id' ],
            'faculty'       => [ Rule::requiredIf( is_null($student_data['faculty']) ), 'exists:faculties,id' ],
            'department'    => [ Rule::requiredIf( is_null($student_data['department']) ), 'exists:departments,id' ],
        ]));

        $mat_no = $student_data->get('mat_no') ?? $data->get('mat_no');
        $student_code = $student_data->get('mat_no') ?? $student_data->get('school_fees_pin');

        if( ( $student ) &&  ( $student->session_id == ( $student_data->get('session') ?? $data->get('session') ) ) ){
            return back()->with('error', 'Account has already been created for this session');
        }

       
        if( $student && ! Hash::check(request('password'), $student->user->password) ){
            return back()->with('error', 'Invalid Password Provided');
        }
        
        if( $student ){
            $extra_data = ['mat_no' => $student->mat_no, 'student_code' => $student->student_code ];
            $student_profile = $this->createStudentProfile($student->user, $student_data, $data, $extra_data); 
            return $this->loginAndRedirect($student_profile, $student->user);
        }
        
        // if( $user = User::firstWhere('email', request('email')) ){
        //     $extra_data = ['mat_no' => $mat_no, 'student_code' => $student_code ];
        //     $student_profile = $this->createStudentProfile($user, $student_data, $data); 
        //     return $this->loginAndRedirect($student_profile, $user);
        // }

        $new_user_data = collect( request()->validate([
            'email' => [ 'email',function($attr, $value, $fail){
                if(User::where(fn($query) => $query->where('email', $value))->orWhere('email', strtolower($value))->exists()){
                    return $fail('Invalid Email');
                }
            }],
            'gender' => ['required'],
            'phone_number' => ['required', 'min:8'],
            'password' => ['required'],
            'mat_no' => ['nullable'],
        ]));
       

        $user =  User::create([
                    'fullname' => $student_data['fullname'],
                    'email' => $new_user_data['email'],
                    'password' => Hash::make($new_user_data['password']),
                    'gender' => $new_user_data['gender'],
                    'phone_number' => $new_user_data['phone_number']
                ]);

        $extra_data = ['mat_no' => $mat_no, 'student_code' => $student_code ];

        $user->assignRole('student');

        $student_profile = $this->createStudentProfile($user, $student_data, $data, $extra_data);
        
        return $this->loginAndRedirect($student_profile, $user);
    }

    public function createStudentProfile(User $student, $student_data, $data, $extra_data = [])
    {
        return $student->profile()->create([
            "fullname" => $student_data->get("fullname"),
            "session_id" => $student_data->get('session') ?? $data->get('session'),
            "faculty_id" => $student_data->get('faculty') ?? $data->get('faculty'),
            "department_id" => $student_data->get('department') ?? $data->get('department'),
            "level_id" =>  $student_data->get('level') ?? $data->get('level'),
            "school_fees_pin" => $student_data->get('school_fees_pin'),
        ] + $extra_data);
    }

    public function loginAndRedirect(StudentProfile $student_profile, User $student)
    {
        session(['student_profile_id' => $student_profile->id ]);

        Auth::guard('student')->login($student);

        return redirect(route('student.dashboard.index'));
    }
}
