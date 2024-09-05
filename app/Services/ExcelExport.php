<?php

namespace App\Services;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExport implements FromCollection, WithHeadings, WithStyles
{
    // public function __construct(protected string $start_date, protected string $end_date){}

    public function collection()
    {
        return Registration::where('scheme_id', 2)->whereBetween('created_at', ['2023-09-12 00:00:00', now()->toDateTimeString()])->get()->map(function($registration){
            return [
                'student_name'  =>  strtoupper($registration->student_name),
                'mat_no/reg_no' =>  $registration->reg_no,
                'profile_image' =>  substr($registration->student?->profile_image, 15)
            ];
        });
    }

    public function headings(): array
    {
        return ['Student Name', 'Mat No/Reg No', 'Passport'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}