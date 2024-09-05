<?php

namespace App\Http\Controllers\Officials;

use App\Models\Course;
use App\Models\Level;
use App\Models\Registration;
use App\Models\Scheme;
use App\Models\Semester;
use App\Models\Session;
use App\Models\StudentProfile;
use App\Services\RegistrationService;
use App\Services\SchemeRegistrationService;

class RegistrationsController extends BaseController
{
    public function __construct(public RegistrationService $registrationService){
        parent::__construct();
    }

    public function index($role, Scheme $scheme)
    {
        if(auth()->guard($this->role)->user()->schemes->intersect([$scheme])->isEmpty()) abort(404);

        $scheme = $scheme->name;
        
        return view('officials.registrations.index', [
            'tableHeadings' => $this->registrationService->getHeadings($scheme),
            'filters' => [
                'payment_status' => [ 0 => 'Unpaid', 1 => 'Paid' ],
                'is_supplementary' => [ 0 => 'Regular', 1 => 'Supplementary' ],
                'program_type' => [ 'NUC' => 'NUC', 'CES' => 'CES' ],
                'level' => Level::get()->mapWithKeys(fn($level) => [ $level->level => $level->level ])->toArray()
            ],
            'role' => $this->role, 
            'page' => strtoupper($scheme).' Registrations', 
            'ajaxUrl' => url("$this->role/search/registrations/$scheme")
        ]);
    }

    public function show($role, Registration $registration)
    {
        return view('officials.registrations.show', ['registration' => $registration, 'role' => $this->role ]);
    }

    public function edit($role, Registration $registration)
    {
        $semester = Semester::firstWhere('semester', $registration->semester) ?? new Semester(['semester' => 'First & Second Semester', 'id' => 3]);
        $level = $registration->student->level;

        $courses = $this->getCourses($registration->student, $registration->scheme, $level, $semester);
        $session = Session::firstWhere('session', $registration->session );

        $registration_type = $registration->is_supplementary ? 'supplementary' : 'normal';        
        
        // $ventures =  $registration->student->level->courses()
        //                     ->where('is_venture', true)
        //                     ->get()
        //                     ->intersect($registration->scheme->courses)
        //                     ->filter(function($venture) use($session, $registration_type) { 

        //                         return $venture->registration_count < $venture->maxRegistrations($session, $registration_type) ?? 0;
        //                     });

        $ventures = $registration->student->level->courses()
                                ->where('is_venture', true)
                                ->get()
                                ->intersect($registration->scheme->courses)
                                ->filter(fn($venture) => $venture->registration_count < $venture->maxRegistrations( $session , $registration_type ) || $venture->id == $registration->venture?->id );

        return view('officials.registrations.edit', ['registration' => $registration , 'role' => $this->role, 'courses' => $courses, 'ventures' => $ventures, 'semesters' => Semester::get()  ]);
    }

    public function update($role, Registration $registration)
    {
        $scheme = $registration->scheme;
        $session = Session::firstWhere('session', $registration->session);
        $student_profile = $registration->student;

        $form_data = collect(request()->validate([
                                            'student_name'  => 'required',
                                            'courses'       => 'required',
                                            'venture'       => 'nullable|exists:courses,id',
                                            'courses.*'     => 'exists:courses,id',
                                            'level'         => 'required|exists:levels,level',
                                            'semester'      => 'required',
                                            'session'       => 'required|exists:sessions,session',
                                            'department'    => 'required|exists:departments,name',
                                            'faculty_id'    => 'required|exists:faculties,id',
                                        ]));

        $venture = Course::find($form_data->get('venture'));
        $semester = Semester::firstWhere('semester', $form_data->get('semester')) ?? new Semester(['id' => 3, 'semester' => $form_data->get('semester') ]);
        $level = Level::firstWhere('level', $form_data->get('level'));
        $is_supplementary = request('is_supplementary') ?? 0;
        $registration_type = $is_supplementary ? 'supplementary' : 'normal';
        $pos = null;


        if( ! is_null($venture) && ( $venture->registration_count >= $venture->maxRegistrations($session, $registration_type) ?? 0 ) && $registration->venture->isNot($venture) ){

            alert()->error($venture->title." has reached the maximum registrations allowed");
            return back();
        }

        $courses = Course::whereIn('id', array_merge($form_data['courses'], [ $venture?->id ?? [] ]))->get();

        try {

            $registration_items = SchemeRegistrationService::formatRegistrationItems(scheme: $scheme, level: $level, semester: $semester, courses: $courses, session: $session, venture: $venture, registration: $registration, role: $role); 

        } catch (\Throwable $th) {

            alert()->error($th->getMessage());
            return back();
        }

        if( isset($registration_items['error']) ){

            alert()->error($registration_items['error'][0]);
            return back();
        }

        unset($form_data['courses']);
        unset($form_data['venture']);
        

        $invoice_number = match(true){
            ($is_supplementary && $registration->is_supplementary == false) => ($registration->invoice_number.'S'),
            $is_supplementary == true => $registration->invoice_number,
            default => preg_split('/S\b/', $registration->invoice_number)[0]
        };

        try {

            $pos = SchemeRegistrationService::generatePOSReference($scheme, $student_profile, $registration_items, $semester, $invoice_number, registration: $registration);

        } catch (\Throwable $th) {

            alert()->error($th->getMessage());
            return back();
        }
        

        $registration->update($form_data->toArray() + ['items' => $registration_items, 'venture_id' => $venture?->id, 'is_supplementary' => $is_supplementary, 'invoice_number' => $invoice_number,  'pos_reference' => $pos['offline_reference'], 'split_code' => $pos['split_code'], 'payment_request_code' => $pos['request_code'] ]);

        $registration->courses->each(fn($course) => $course->registrations()->detach($registration));

        $courses->each(fn($course) => $course->registrations()->syncWithoutDetaching($registration));

        return redirect(route('officials.registrations.index', [ $this->role, $registration->scheme ]));
    }

    public function confirmPayment($role, Registration $registration)
    {
        $registration->update(['payment_status' => ! $registration->payment_status ]);
        alert('Payment Confirmed', 'Payment has successfully been confirmed');
        return back();

    }

    public function searchForRegistration($role, $searchType, Scheme $scheme)
    {
        return $this->registrationService->getData(auth()->guard($this->role)->user(), $this->role, $searchType == 'verifications' ? true : false, $scheme);                                                                          
    }

    protected function getCourses(StudentProfile $student, Scheme $scheme, Level $level, Semester $semester)
    {
        $student_courses =  $student->department->courses->filter(function($course) use($semester, $level) {

            $level = match( true ){

                intval($level->level) > 200 => '300+',
                $level->level == '200 (Direct Entry)' => '200 DE',
                default => $level->level

            };

            if( $semester->id == 3 ){

               return in_array( $level  , json_decode($course->pivot->levels) );
            }

            return in_array( $semester->id, json_decode( $course->pivot->semester_id ) ) && in_array( $level, json_decode($course->pivot->levels) );
        });
        
        // if( $scheme->is_gss ){

        //     return $student_courses->intersect($scheme->courses)->filter(function($course) use($student) {

        //         $is_course_available_for_student_session = in_array( $student->session_id, $course?->course_sessions->pluck('id')->toArray() );
        //         $is_course_available_for_student_level = in_array( $student->level->level, json_decode( DB::table('session_courses')->where('session_id', $student->session_id )->where('course_id', $course->id )->first()?->levels ) );

        //         return $is_course_available_for_student_level && $is_course_available_for_student_session;
                
        //     });
        
        // }

        if( $scheme->is_gss ){

            return $student_courses->intersect($scheme->courses)->filter(fn($course) => in_array($student->session_id, $course?->course_sessions->pluck('id')->toArray() )  );
        
        }

        else{

            return $level->courses()->where('is_venture', false)->get()->intersect($scheme->courses);
        }
    }
}
