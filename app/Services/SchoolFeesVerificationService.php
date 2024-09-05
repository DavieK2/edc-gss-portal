<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SchoolFeesVerificationService 
{
    public $status = false;
    public $fullname;
    public $session;
    public $faculty;
    public $department;
    public $level_id;
    public $school_fees_pin;
    public $mat_no;

    public function verifyFees($payload) : SchoolFeesVerificationService
    {
        $level = Level::find($payload['level'])->level;
        $session = Session::find($payload['session'])->session;

        $this->verifyFeesForFirstYearStudents($payload['pin'], $session , $level);
       
        if($this->status == false) $this->verifyFeesForAboveFirstYearStudents($payload['pin'], $session, $level);

        $this->level_id = Level::firstWhere('level', intval($this->level_id))?->id;
        $this->faculty = Faculty::get()->filter(fn($faculty) => strtoupper($faculty->name) == strtoupper($this->faculty) )->first()?->id;
        $this->department = Department::get()->filter(fn($department) => strtoupper($department->name) == strtoupper($this->department) )->first()?->id;
        $this->session = Session::get()->filter(fn($session) => $session->session == $this->session )->first()?->id;
        
        return $this;

    }

    protected function verifyFeesForFirstYearStudents($reg_no, $session, $level)
    {

        try {
            
            $credentials = [
                "username" => "gssbackend@portal.com",
                "password" =>"kvzM5_K[t<VD"
            ];

      
            $auth = Http::post('https://portalapi.unical.edu.ng/prod/unicore/v1/api/ExternalQuery/Authenticate', $credentials);


            $authResponse = $auth->json();

            $cipherKey = $authResponse['cipherKey'];
            $token = $authResponse['token'];

            $reg_no = utf8_encode($reg_no);
            $reg_no = str_replace( '\/', '/', $reg_no);

            $payload = [
                "reg_no" => $reg_no,
                "session" => $session,
                "level_paid"=> intval($level)
            ];

            $cipherKey = utf8_encode($cipherKey);

            $payloader = collect($payload)->toJson(); json_encode($payload);
            $payloader = utf8_encode($payloader);
            $payloader = str_replace( '\/', '/', $payloader);

            $key = hash_hmac('SHA512',$payloader, $cipherKey);

      
            $response = Http::withHeaders(['X-TIM-Signature' => strtoupper($key), 'Authorization' => 'Bearer '.$token ])
                    ->post('https://portalapi.unical.edu.ng/prod/unicore/v1/api/ExternalQuery/ValidateFees',$payload);
    
            if(! $response->ok() ) {

                Log::error($response);
                $this->status = false;
                return $this;
            }

            $this->status = true;

            $data =  collect($response->json());

            $this->setData($data->get('fullname'), $data->get('session'), $data->get('faculty'), $data->get('department'), $data->get('level_paid'), $data->get('reg_no'), $data->get('mat_no') );

        } catch (\Throwable $th) {
           
            Log::error($th);
            $this->status = false;
            return $this;
        }

    }
    
    protected function verifyFeesForAboveFirstYearStudents($pin, $session, $level) 
    {
        try {

            $response = Http::get('https://myunical.edu.ng/verifyfee/GetSchoolFee.ashx?pin='.$pin);

        } catch (\Throwable $th) {
            
            $this->status = false;
            return $this;

        }
        
        $response = $response->json();

        if(is_null($response)) {
            $this->status = false;
            return $this;
        }
        
        if($response['status'] != 'success' && $response['data'] == '') {
            $this->status = false;
            return $this;
        }

        if($response['data']['session'] != $session ||  $response['data']['level'] != $level ) {
            $this->status = false;
            return $this;
        }

        $this->status = true;

        $data =  collect($response['data']);
        
        $this->setData($data->get('fullname'), $data->get('session'), $data->get('faculty'), $data->get('department'), $data->get('level'), $data->get('pin'), $data->get('mat_no') );

    }

    protected function setData($fullname, $session, $faculty, $department, $level, $school_fees_pin, $mat_no)
    {
        $this->fullname = $fullname;
        $this->session = $session;
        $this->faculty = $faculty;
        $this->department = $department;
        $this->level_id = $level;
        $this->school_fees_pin = $school_fees_pin;
        $this->mat_no = $mat_no;
    }
}