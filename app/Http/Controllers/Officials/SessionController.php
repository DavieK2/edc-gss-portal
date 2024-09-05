<?php

namespace App\Http\Controllers\Officials;

use App\Models\Scheme;
use App\Models\Semester;
use App\Models\Session;

class SessionController extends BaseController
{
    public function index()
    {
        return view('officials.sessions.index', ['role' => $this->role, 'sessions' => Session::get(), 'page' => 'Sessions', 'schemes' => Scheme::get() ]);
    }

    public function toggle($role, Session $session)
    {
        Session::get()->each(fn($sessions) => $session->is($sessions) ? $sessions->update(['status' => true]) : $sessions->update(['status' => false]));
        return back();
    }

    public function create()
    {
        return view('officials.sessions.create', ['role' => $this->role ]);
    }

    public function store()
    {
        $data = request()->validate([
            'session' => 'required'
        ]);

        Session::create($data);

        return redirect()->route('officials.sessions.index', $this->role);
    }

    public function edit($role, Session $session)
    {
        return view('officials.sessions.edit', ['role' => $this->role, 'semesters' => Semester::get(), 'session' => $session ]);
    }


    public function updateSession($role, Session $session)
    {
        $data = request()->validate([
            'session' => 'required'
        ]);

        $session->update($data);
        
        // $data = request()->validate(['semesters' => 'required', 'semesters.*' => 'exists:semesters,id']);
        // $session->update(['registration_semesters' => $data['semesters']]);

        return redirect(route('officials.sessions.index', [$this->role]));
    }

    public function updateUpdateRegistrationStatus($role, Session $session, Scheme $scheme)
    {
        if( ( ! in_array( $scheme->name, ($session->can_register ?? []) ) ) ){
            
            $schemes = [ ...($session->can_register ?? []), $scheme->name ];

            $session->update(['can_register' => $schemes ]);

            return back();
            
        }

        if( ( in_array( $scheme->name, ($session->can_register ?? []) ) ) ){
            

            $schemes = collect($session->can_register)->filter( fn($sessn) => $scheme->name !== $sessn )->toArray();

            $session->update(['can_register' => $schemes ]);

            return back();
        }

        $session->update(['can_register' => NULL ]);

        return back();
    }

    public function update($role, $session)
    {
        session(['session' => Session::find($session)?->session ?? Session::activeSession() ]);
        return response()->json(['status' => true ]);
    }
}
