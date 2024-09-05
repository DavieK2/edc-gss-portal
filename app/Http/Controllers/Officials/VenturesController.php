<?php

namespace App\Http\Controllers\Officials;

use App\Models\Course;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class VenturesController extends BaseController
{
    public function index()
    {
        $course_ventures = Course::where('courses.is_venture', true)
                            ->leftJoin('session_ventures','session_ventures.course_id', '=', 'courses.id')
                            ->where( fn($query) => $query->where('session_ventures.session', Session::activeSession() ) )
                            ->get();

        $ventures =  Course::where('courses.is_venture', true)->get();
        
        $ventures = $ventures->merge($course_ventures);

        $ventures = $ventures->map( function( $venture ) {
           
            return [
                'id'                    => $venture->id,
                'title'                 => $venture->title,
                'fee'                   => $venture->fee,
                'item_code'             => $venture->item_code,
                'total_registrations'   => $venture->registrations()->where('registrations.session', Session::activeSession() )->count(),
                'max_registrations'     => json_decode($venture?->registration_type, true),
                'session'               => $venture?->session,
            ];
        });
        
        return view( 'officials.ventures.index', ['role' => $this->role , 'ventures' => $ventures ] );
    }

    public function edit($role, Course $venture, $registration_type = null)
    {
        $session_venture = DB::table('session_ventures')
                                ->where(function($query) use($venture, $registration_type) {
                                    $query->where('session_ventures.course_id', $venture->id)
                                            ->where('session_ventures.session', Session::activeSession() );
                                            if( $registration_type ){
                                                $query->where('session_ventures.registration_type', $registration_type);
                                            }
                                })
                                ->first();

       
        $ventureId = $venture->id;

        return view( 'officials.ventures.edit', compact('role', 'session_venture', 'ventureId') );
    }


    public function editFee( $role, Course $venture )
    {
        return view( 'officials.ventures.edit_fee', compact('role', 'venture') );
    }

    public function update( $role, Course $venture )
    {
        $data = request()->validate([
            'max_registrations' => 'required|integer',
            'registration_type' => 'required|string|in:supplementary,normal'
        ]);

        $is_supplementary = $data['registration_type'] === 'supplementary' ? true : false;

        $total_ventures = $venture->registrations()->where( 'registrations.session', Session::activeSession() )->where('registrations.is_supplementary', $is_supplementary)->count();

        if( $data['max_registrations'] < $total_ventures )
        {
            alert()->error('Venture already has '.$total_ventures.' registrations. Cannot assign value lower.');
            return back();
        }
        
        if(  DB::table('session_ventures')->where('session', Session::activeSession())->where('course_id', $venture->id)->exists() ){

            $venture_session = DB::table('session_ventures')->where('session', Session::activeSession())->where('course_id', $venture->id);

            $registration_types = json_decode( $venture_session->first()->registration_type, true ) ?? [];

            $registration_types[ $data['registration_type'] ] =  $data['max_registrations'];

            $venture_session->update( ['registration_type' => json_encode( $registration_types ) ]  );


        }else{
            

            $registration_types = [];
            $registration_types[ $data['registration_type'] ] =  $data['max_registrations'];

            DB::table('session_ventures')->insert( [ 'registration_type' => json_encode( $registration_types), 'session' => Session::activeSession(), 'course_id' => $venture->id ] );

        }

        return redirect(route('officials.ventures.index', $role));
    }

    public function updateFee( $role, Course $venture )
    {
        $data = request()->validate([
            'fee' => 'required|numeric'
        ]);

        $venture->update( ['fee' => $data['fee'] ] );

        return redirect(route('officials.ventures.index', $role) );

    }
}
