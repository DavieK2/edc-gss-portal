<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Level;
use App\Models\Registration;
use App\Models\Scheme;
use App\Models\Semester;
use App\Models\Session;
use App\Models\StudentProfile;
use App\Services\DataTable;
use App\Services\SchemeRegistrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VendorStudentRegistrationController extends Controller
{
    protected $errors;

    public function __construct(protected DataTable $datatable) {}
    
    public function index()
    {
        return view('vendor.registration.index', [
            'tableHeadings' => [
                's/n' => 'S/N',
                'full_name' => 'Full name',
                'department' => 'Department',
                'ref_no' => 'Ref No.',
                'session' => 'Session',
                'date' => 'Date',
                'action' => 'Action',
            ], 
            'page' => 'Registrations', 
            'ajaxUrl' => route('vendors.registration.search')
        ]);
    }

    public function searchRegistration()
    {
        $user = auth()->guard('vendor')->user();

        $query = Registration::query();

        $registrations_columns = collect(Schema::getColumnListing('registrations'));
        $registrations_table = 'registrations';
        $registrations_columns = $registrations_columns->mapWithKeys(fn($column, $key) => [ $column => $registrations_table ])->toArray();


        $registrations = $this->datatable->search($query, $registrations_columns, $registrations_table, withSession: false, closure: function($query) use($user){
            return $query->where('vendor_id', $user->id);
        });

        $items = collect($registrations->items())->map(function($registration, $index) use($registrations){
            
            return [
                    's/n' => $registrations->firstItem() + $index,
                    'full_name' => $registration->student_name,
                    'department' => $registration->department,
                    'ref_no' => $registration->invoice_number,
                    'session' => $registration->session,
                    'date' => Carbon::parse($registration->created_at)->toDateTimeString(),
                    'action' => [
                        [
                            'title' => 'Edit',
                            'url' => route('vendor.student.registration.edit', $registration->invoice_number)
                        ],
                        [
                            'title' => 'View',
                            'url' => route('vendor.student.registration.invoice', $registration->invoice_number)
                        ]
                    ]
                ];
        });

        return $this->datatable->response($items, $registrations);
    }

    public function verify()
    {
        $user_scheme = auth()->guard('vendor')->user()->schemes->pluck('name')->toArray()[0];
        $sessions = Session::get();
        $sessions = $sessions->filter(fn($session) => in_array( $user_scheme, $session->can_register ) );
        $semesters = Semester::get();
        $levels = Level::orderBy('level', 'asc')->get();

        return view('vendor.registration.verify', compact('sessions', 'semesters', 'levels'));
    }

    public function verifyStudentCode()
    {
        $form_data = request()->validate([
                                            'student_code' => 'required|exists:student_profiles,student_code',
                                            'scheme' => 'required|exists:schemes,id',
                                            'semester' => 'required',  
                                            'session' => 'required|exists:sessions,id',  
                                            'level' => 'required|exists:levels,id',
                                            'is_supplementary' => ['boolean', Rule::prohibitedIf( ! in_array('sup-vendor', auth()->guard('vendor')->user()->role->pluck('name')->toArray()) )] 
                                        ]);

        $student = StudentProfile::where('student_code', $form_data['student_code'])->where('session_id', $form_data['session'])->first();
        $session = Session::find($form_data['session']);
        $scheme = Scheme::find($form_data['scheme']);
        $semester = Semester::find($form_data['semester']) ?? new Semester(['id' => 3, 'semester' => 'First & Second Semester']);
        $level = Level::find($form_data['level']);
        $program_type = request('program_type');
        $is_supplementary = request('is_supplementary') ?? 0;


        $user_scheme = auth()->guard('vendor')->user()->schemes->pluck('name')->toArray()[0];
        $sessions = Session::get();
        $sessions = $sessions->filter( fn($sessn) => in_array( $user_scheme, $sessn->can_register ) )->pluck('session')->toArray();
        
        $registration_type = $is_supplementary ? 'supplementary' : 'normal';
        //Checking if the current student profile is valid for the given session

        if(is_null($student)){
            alert()->error('Student Code is not valid for this session.');
            return back(); 
        }
    
        if( $session->session == '2020/2021' && $scheme->is_gss && $semester->id == 1 ){
            alert()->error('You cannot register for this semester');
            return back();
        }

        if( ! in_array($student->session?->session, $sessions ) ){
            alert()->error('Student Code is not valid for this session.');
            return back();
        }

        if( $session->session != $student->session?->session ) {
            alert()->error('Student Code is not valid for this session.');
            return back();
        }

        //Check if can register for scheme in a given semester
        if(! in_array($semester->id, $scheme->semesters) && $scheme->is_edc){
            alert()->error($scheme->name. ' is not a '.$semester->semester. ' course.');
            return back();
        }
       
        //Check if student already has a registration
        if($this->checkIfRegistrationExists($student, $scheme, $session, $semester)) {
            alert()->error('Student has already been registered.');
            return back();
        }
        //Save Registration in the session
        session(['registration_data' => [
                                            'student_profile' => $student->id, 
                                            'scheme' => $scheme->id,
                                            'session' => $session->id,
                                            'semester' => $semester->id,
                                            'program_type' => $program_type,
                                            'is_supplementary' => $is_supplementary
                                        ] 
                ]);

        $courses =  $this->getCourses($student, $scheme, $level, $semester);
        
        

        $ventures = $level->courses()
                            ->where('is_venture', true)
                            ->get()
                            ->intersect($scheme->courses)
                            ->filter(function($venture) use($session, $registration_type) { 

                                return $venture->registration_count < $venture->maxRegistrations($session, $registration_type) ?? 0;
                            });

        $registration_type = $is_supplementary ? 'Supplementary Registration' : 'Normal Registration';

        return view('vendor.registration.create', compact('courses', 'ventures', 'scheme', 'student', 'program_type', 'registration_type'));
    }

    public function store()
    {
        if(! session()->has('registration_data') ) return redirect(route('vendor.student.registration.index'));

        $registration_data = collect(session('registration_data'));

        $student_profile = StudentProfile::find( $registration_data->get('student_profile') );
        $scheme = Scheme::find( $registration_data->get('scheme') );
        $semester = Semester::find( $registration_data->get('semester') ) ?? new Semester(['id' => 3, 'semester' => 'First & Second Semester']);
        $session = Session::find( $registration_data->get('session') );
        $program_type = $registration_data->get('program_type');
        $is_supplementary = $registration_data->get('is_supplementary');
        $registration_type = $is_supplementary ? 'supplementary' : 'normal';

        $form_data = collect(request()->validate([
                                            'courses'   => 'required',
                                            'venture'   => 'nullable|exists:courses,id',
                                            'courses.*' => 'exists:courses,id',
                                            'passport'  => Rule::requiredIf( is_null($student_profile->profile_image) )
                                        ]));

        foreach ($form_data->get('courses') as $course) {

           $course = Course::find($course);

           if( $course->requires_venture && is_null( $form_data->get('venture') ) ){

                alert()->error($course->item_code. " requires a venture to be selected");
                
                return back();
                
                break;
           }
        }

        if( $this->checkIfRegistrationExists($student_profile, $scheme, $session, $semester) ) {

            alert()->error('Student has already been registered.');
            return back();
        }

        if( auth()->guard('vendor')->user()->balance($scheme) <= 0 ){

            alert()->error('You have insufficient tokens.');
            return back()->withInput();
        }

        $venture = Course::find( $form_data->get('venture') );


        if( ! is_null( $venture ) && $venture->registration_count >= $venture->maxRegistrations($session, $registration_type) ?? 0 ){
            
            alert()->error( $venture->title." has reached the maximum registrations allowed" );
            
            return back();
        }

        $courses = Course::whereIn('id', array_merge($form_data['courses'], [ $venture?->id ?? [] ]))->get();

        if( request()->has('passport') && is_null($student_profile->profile_image) ){
            
            $file = request()->file('passport');
            $file = $file->store('profile_images', 'public');
            
            rename(public_path("images/$file"), "images/$file");
            
            $student_profile->update(['profile_image' => $file]);
        }

        try {

            $registration_items = SchemeRegistrationService::formatRegistrationItems(scheme: $scheme, courses: $courses, session: $session, semester: $semester, level: $student_profile->level, venture: $venture); 

        } catch (\Throwable $th) {

            alert()->error($th->getMessage());
            return back();
        }


        
        if( isset($registration_items['error']) ){
           
            alert()->error($registration_items['error'][0]);
            
            return back();
        }

        $invoice_number = $this->generatePaymentCode($scheme, $is_supplementary);
        
        try {

            $pos = SchemeRegistrationService::generatePOSReference( $scheme, $student_profile, $registration_items, $semester, $invoice_number );

        } catch (\Throwable $th) {

            alert()->error($th->getMessage());
            return back();
        }
        
        $registration = auth()->guard('vendor')->user()->registrations()->create([
            'scheme_id' => $scheme->id,
            'student_profile_id' => $student_profile->id,
            'reg_no' => $student_profile->student_code,
            'student_name' => $student_profile->fullname,
            'department' => $student_profile->department->name,
            'faculty_id' => $student_profile->faculty_id,
            'venture_id' => $venture?->id,
            'semester' => $semester->semester,
            'program_type' => $program_type,
            'session' => $session->session,
            'level' => $student_profile->level->level,
            'invoice_number' => $invoice_number,
            'items' => $registration_items,
            'is_supplementary' => $is_supplementary,
            'pos_reference' => $pos['offline_reference'],
            'payment_request_code' => $pos['request_code']
        ]);

       
        $courses->each(fn($course) => $course->registrations()->syncWithoutDetaching($registration));

        auth()->guard('vendor')->user()->debit($scheme);

        return redirect(route('vendor.student.registration.index'));
    }


    public function invoice(Registration $registration)
    {
        return view('vendor.registration.invoice', compact('registration'));
    }

    public function edit(Registration $registration)
    {
        $level = Level::firstWhere(['level' => $registration->level]);
        $semester = Semester::firstWhere($registration->semester) ?? new Semester(['id' => 3, 'semester' => $registration->semester]);
        $session = Session::firstWhere('session', $registration->session );
        
        $courses =  $this->getCourses($registration->student, $registration->scheme, $level , $semester);
        $registration_type = $registration->is_supplementary ? 'supplementary' : 'normal';

        $ventures = $registration->student->level->courses()
                                    ->where('is_venture', true)
                                    ->get()
                                    ->intersect($registration->scheme->courses)
                                    ->filter(fn($venture) => $venture->registration_count < $venture->maxRegistrations( $session , $registration_type ) || $venture->id == $registration->venture?->id );

        return view('vendor.registration.edit', compact('registration', 'courses', 'ventures'));
    }

    public function update(Registration $registration)
    {
        $student_profile = $registration->student;
        $scheme = $registration->scheme;
        $semester = Semester::firstWhere('semester', $registration->semester) ?? new Semester(['id' => 3, 'semester' => $registration->semester]);
        $session = Session::firstWhere('session', $registration->session);

        $form_data = collect(request()->validate([
                                            'courses' => 'required',
                                            'venture' => 'nullable|exists:courses,id',
                                            'courses.*' => 'exists:courses,id',
                                            'is_supplementary' => ['boolean', Rule::prohibitedIf( ! in_array('sup-vendor', auth()->guard('vendor')->user()->role->pluck('name')->toArray()) )] 
                                        ]));

        $venture = Course::find($form_data->get('venture'));
        $is_supplementary = $form_data->get('is_supplementary') ?? 0;
        $registration_type = $is_supplementary ? 'supplementary' : 'normal';
      

        if( ! is_null( $venture ) && ( $venture->registration_count >= $venture->maxRegistrations($session, $registration_type) ?? 0 ) && $registration->venture->isNot( $venture ) ){

            alert()->error( $venture->title." has reached the maximum registrations allowed" );
            return back();
        }

        $courses = Course::whereIn('id', array_merge($form_data['courses'], [ $venture?->id ?? [] ]))->get();

        try {

            $registration_items = SchemeRegistrationService::formatRegistrationItems(scheme: $scheme, courses: $courses, session: $session, semester: $semester, level: $student_profile->level, venture: $venture, registration: $registration); 

        } catch (\Throwable $th) {

            alert()->error($th->getMessage());
            return back();
        }

        if( isset($registration_items['error']) ){

            alert()->error($registration_items['error'][0]);
            return back();
        }

        if( $registration->payment_status ){

            $invoice_number = $this->generatePaymentCode($scheme, $is_supplementary);

            try {

                $pos = SchemeRegistrationService::generatePOSReference($scheme, $student_profile, $registration_items, $semester, $invoice_number, registration: $registration);
    
            } catch (\Throwable $th) {
    
                alert()->error($th->getMessage());
                return back();
            }

            $registration = auth()->guard('vendor')->user()->registrations()->create([
                'scheme_id' => $scheme->id,
                'student_profile_id' => $student_profile->id,
                'reg_no' => $student_profile->student_code,
                'student_name' => $student_profile->fullname,
                'department' => $student_profile->department->name,
                'faculty_id' => $student_profile->faculty_id,
                'venture_id' => $venture?->id,
                'registration_id' => $registration->id,
                'semester' => $semester->semester,
                'session' => $session->session,
                'level' => $student_profile->level->level,
                'invoice_number' => $invoice_number,
                'items' => $registration_items,
                'pos_reference' => $pos['offline_reference'],
                'split_code' => $pos['split_code'],
                'payment_request_code' => $pos['request_code']
            ]);

        }
        else
        {
            $invoice_number = match(true){
                                ($is_supplementary && $registration->is_supplementary == false) => ($registration->invoice_number.'S'),
                                ($is_supplementary == true) => $registration->invoice_number,
                                default => preg_split('/S\b/', $registration->invoice_number)[0]
                            };

            
            try {

                $pos = SchemeRegistrationService::generatePOSReference( $scheme, $student_profile, $registration_items, $semester, $invoice_number );
    
            } catch (\Throwable $th) {
    
                alert()->error($th->getMessage());
                return back();
            }

            $registration->update([
                                    'items' => $registration_items, 
                                    'venture_id' => $venture?->id, 
                                    'invoice_number' => $invoice_number,
                                    'is_supplementary' => $is_supplementary,
                                    'pos_reference' => $pos['offline_reference'],
                                    'split_code' => $pos['split_code'],
                                    'payment_request_code' => $pos['request_code']
                                ]);
        }
        
        $registration->courses->each( fn($course) => $course->registrations()->detach($registration) );

        $courses->each( fn($course) => $course->registrations()->syncWithoutDetaching($registration) );

        return redirect( route('vendor.student.registration.index') );
        
    }

    protected function checkIfRegistrationExists(StudentProfile $student, Scheme $scheme, Session $session, Semester $semester)
    {
        return $student->registrations()
                        ->where('scheme_id', $scheme->id)
                        ->where('session', $session->session)
                        ->where('semester', $semester->semester)
                        ->exists();
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

    public function generatePaymentCode($scheme, $is_supplementary)
    {
        $invoice_number = rand( 100000,999999 );
        
        while( Registration::where('invoice_number', substr($scheme->name, 0, 3).$invoice_number.($is_supplementary ? 'S' : ''))->exists() ){
            
            $invoice_number = rand( 100000,999999 );
        }

        return substr($scheme->name, 0, 3).$invoice_number.($is_supplementary ? 'S' : '');
    }
    
}