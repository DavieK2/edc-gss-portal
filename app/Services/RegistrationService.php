<?php
namespace App\Services;

use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class RegistrationService {

    public function __construct(public DataTable $datatable){}

    public function getHeadings($scheme, $is_verification = false)
    {
        $tableHeadings = [
            's/n' => 'S/N',
            'student_name' => 'Student Name',
            'email' => 'Email',
            'phone_no' => 'Phone No.',
            'reg_no' => 'Reg. No.',
            'invoice_number' => 'Payment Code',
            'department' => 'Department',
            'faculty' => 'Faculty',
            'level' => 'Level',
            'program_type' => 'Program Type',
            'registration_type' => 'Registration Type',
            'session' => 'Session',
            'vendor' => 'Vendor',
            'payment_status' => 'Payment Status',
            'payment_date' => 'Payment Date',
            'invoice_items' => [
                'type' => 'list',
                'title' => 'Courses'
            ],
            
        ];

        if($scheme == 'EDC'){
            $tableHeadings['venture'] = 'Venture';
            $tableHeadings['verification_status'] = 'Is Verified';
        } 

        if($is_verification){
            $tableHeadings['verified_at'] = 'Verified At';
            $tableHeadings['verified_by'] = 'Verified By';
        }

        $tableHeadings['action'] = 'Action';

        return $tableHeadings;
    }

    public function getData($user, $role, $is_verification = false, $scheme)
    {

        $schemes = auth()->guard($role)->user()->schemes->intersect(collect([$scheme]))->pluck('id')->toArray();

        $registrations = collect(Schema::getColumnListing('registrations'));
        $registrations_table = 'registrations';
        $registrations_columns = $registrations->mapWithKeys(fn($column, $key) => [$column => $registrations_table ])->toArray();

        $excludes = ['created_at', 'updated_at'];
        
        $select_columns = $registrations->flatMap(function($column) use($excludes, $registrations_table){
            if(! in_array($column, $excludes) ){
                return [ "$registrations_table.$column" ];
            }
        })->toArray();

        $select_columns = implode(',', $select_columns);

        $subQuery = Registration::query()->selectRaw("$select_columns, IFNULL(registrations.payment_date, registrations.created_at) as created_at");

        $registrations = $subQuery->fromSub($subQuery, 'registrations');

        $registrations = $this->datatable->search($registrations, $registrations_columns, $registrations_table, closure: function(Builder|QueryBuilder $query) use($schemes, $is_verification, $user){
            
            $query->whereIn('registrations.scheme_id', $schemes);
            
            $is_verification ?
                $query->where(function($query) use($user){
                    $query->where('registrations.is_verified', true);
                    if($user->is_edc_verification_officer) $query->where('registrations.verified_by', $user->id);
                }) : 
                $query;

            $query->with('faculty', 'vendor', 'venture', 'verifiedBy');

        });

        $items = collect($registrations->items())->map(function($registration, $index) use($registrations, $is_verification, $role) {

                $data =  [
                        's/n' => $registrations->firstItem() + $index, 
                        'student_name' => $registration->student_name,
                        'email' => $registration->student?->user?->email,
                        'phone_no' => $registration->student?->user?->phone_number,
                        'reg_no' => $registration->reg_no,
                        'invoice_number' => $registration->invoice_number,
                        'department' => $registration->department,
                        'faculty' => $registration->faculty?->name ?? 'N/A',
                        'level' => $registration->level,
                        'program_type' => $registration->program_type ?? 'N/A',
                        'registration_type' => $registration->is_supplementary ? 'SUPPLEMENTARY' : 'NORMAL',
                        'venture' => $registration->venture?->title ?? 'N/A',
                        'invoice_items' => collect($registration->items['invoice_items'])->map(function($item){
                                return $item['item_code'];
                        }),
                        'session' => $registration->session,
                        'vendor' => $registration->vendor?->fullname ?? 'N/A',
                        'payment_status' => $registration->payment_status ? 'PAID' : 'UNPAID',
                        'payment_date' => $registration->payment_status ? Carbon::parse($registration->payment_date)->toDateTimeString() : 'N/A',
                        'verification_status' =>  $registration->is_verified ? 'Yes' : 'No',
                        'verified_at' => $registration->verified_at ?? 'N/A',
                        'action' => [
                            [
                                'title' => 'View Invoice',
                                'url' => route('officials.registrations.show', [$role, $registration->invoice_number])
                            ],
                        ]
                    ];

                if($is_verification) $data['verified_by'] = $registration->verifiedBy?->fullname ?? 'N/A';
                
                if(Gate::allows('edit-registrations')) {
                    array_push($data['action'], [
                                
                        'title' => 'Edit Registration',
                        'url' => route('officials.registrations.edit', [$role, $registration->id])
                    ]);

                }
                return $data;
        });

        return $this->datatable->response($items->toArray(), $registrations);     
    }
}