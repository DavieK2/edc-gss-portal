@extends('student.layouts.app')
@section('content')
    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Dashboard</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <h4 class="pr-4 text-primary">Student Code: {{ $student_profile->student_code }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div class="pd-ltr-20">
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Registrations</h4>
                </div>
                <div class="pb-20">
                    <table class="table hover table-bordered multiple-select-row data-table-export nowrap">
                        <thead>
                            <tr>
                                <th class="table-plus datatable-nosort">S/N</th>
                                <th>Ref No.</th>
                                <th>Session</th>
                                <th>Payment Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $key => $registration)
                                <tr>
                                    <td class="table-plus text-center">{{ $key + 1 }}</td>
                                    <td>{{ $registration->invoice_number }}</td>
                                    <td>{{ $registration->session }}</td>
                                    <td>{{ $registration->payment_status ? 'Paid' : 'Pending' }}</td>
                                    <td>{{ $registration->created_at }}</td>
                                    <td>
                                        <a href="{{ route('student.registration.invoice', $registration->invoice_number )}}" class="btn btn-success"><span class=" icon-copy ti-receipt mr-1 fw-50 "></span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection