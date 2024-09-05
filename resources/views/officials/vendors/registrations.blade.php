@extends('officials.layouts.app')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Vendor's Report</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('officials.vendors.index', $role) }}">Vendors</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Vendor Registrations</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            
            
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4">Student Registrations</h4>
                    </div>
                    <div class="pb-20">
                        <table class="table hover table-bordered multiple-select-row data-table-export nowrap" style="overflow-x: auto">
                            <thead>
                                <tr>
                                    <th class="table-plus datatable-nosort">S/N</th>
                                    <th>Student Name</th>
                                    <th>Matric/ Reg No.</th>
                                    <th>Department</th>
                                    <th>Faculty</th>
                                    <th>Level</th>
                                    <th>Venture</th>
                                    <th>Courses</th>
                                    <th>Session</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registrations as $key => $registration)
                                    <tr>
                                        <td class="table-plus text-center">{{ $key + 1 }}</td>
                                        <td>{{ $registration->student->fullname }}</td>
                                        <td>{{ $registration->student->student_code }}</td>
                                        <td>{{ $registration->student->department->name }}</td>
                                        <td>{{ $registration->student->faculty->name }}</td>
                                        <td>{{ $registration->student->level->level }}</td>
                                        <td>{{ $registration->venture?->title ?? 'N/A' }}</td>
                                        <td>
                                            <ul>
                                                @php
                                                    $courses = $registration->courses->where('is_venture', false);
                                                @endphp
                                                @foreach ($courses as $course)
                                                    <li>{{ $course->item_code }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>{{ $registration->session }}</td>
                                        <td>{{ $registration->created_at }}</td>
                                        <td>
                                            <a href="{{ route('officials.registrations.show', [$role, $registration->invoice_number] )}}" class="btn btn-success btn-sm"><span class=" icon-copy ti-receipt mr-1 fw-50 "></span></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                    <!-- Export Datatable End -->
            </div>

        </div>
    </div>
@endsection