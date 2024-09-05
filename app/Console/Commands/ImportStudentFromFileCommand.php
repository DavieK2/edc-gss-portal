<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Level;
use App\Models\Session;
use App\Models\User;
use App\Services\DataImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudentFromFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = Excel::toArray( new DataImport(), base_path('GSS.xlsx'));

        collect($data[0])->each(function($student){

            $user = User::firstOrCreate(['email' => $student['email'] ?? null ], [
                'fullname' => $student['name_of_student'],
                'phone_number' => $student['phone_no'] ?? fake()->e164PhoneNumber(),
                'email' => $student['email'] ?? fake()->email(),
                'gender' => $student['sex'] === 'M' ? 'Male' : 'Female',
                'password' => Hash::make('1234ASDF')
            ]);

            $student_profile = [
                'fullname' => $user->fullname,
                'student_code' =>  $student['matricreg_no'],
                'mat_no' => $student['matricreg_no'],
                'faculty_id' => Faculty::where('name', 'like', '%'.$student['faculty'].'%')->first()?->id ?? 1,
                'department_id' => Department::where('name', 'like', '%'.$student['department'].'%')->first()?->id ?? 1,
                'level_id' => Level::where('level', $student['class'])->first()?->id,
                'session_id' => Session::where('session', '2023/2024')->first()?->id,
            ];

            $user->profile()->create($student_profile);

        });


         // $rows = Excel::toArray(new DataImport, request()->file('upload'));

    // $errors = [];

    // foreach ($rows[0] as $key => $row) {

    //    try {

    //         $user = User::firstOrCreate(['email' => $row['email']],[
    //             'fullname' => $row['name_of_student'],
    //             'email' => $row['email'],
    //             'gender' => $row['sex'],
    //             'password' => Hash::make('password1234')
    //         ]);

    //         $user->profile()->create([
    //             "fullname" => $user->fullname,
    //             "session_id" => Session::firstWhere('session', '2021/2022')->id,
    //             "faculty_id" => Faculty::firstWhere('name', 'like', "%". trim( substr($row['faculty'], 0, 3) ). "%" )?->id,
    //             "department_id" => Department::firstWhere('name', 'like', "%". trim( substr($row['department'], 0, 3) ). "%" )?->id,
    //             "level_id" =>  Level::firstWhere('level', $row['class'])?->id,
    //             'mat_no' => $row['matricreg_no'],
    //             'student_code' => $row['matricreg_no'],
    //         ]);

    //    } catch (\Throwable $th) {
        
    //       array_push($errors, $row);
    //    }
        

    //     $user->assignRole('student');
    // }
    }
}
