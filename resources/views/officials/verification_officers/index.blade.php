@extends('officials.layouts.app')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>{{ $page }}</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                                </ol>
                            </nav>
                        </div>
                        @can('create-verification-officers')
                            <div class="col-md-6 col-sm-12 text-right">
                                <div class="dropdown">
                                    <a class="btn btn-primary" href="{{ route('officials.verification.officers.create', $role) }}">
                                    Create Verification Officer
                                    </a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            
            
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4">{{ $page }}</h4>
                    </div>
                    <div class="pb-20">
                        <table class="table hover table-bordered multiple-select-row data-table-export nowrap">
                            <thead>
                                <tr>
                                    <th class="table-plus datatable-nosort">S/N</th>
                                    <th>Full name</th>
                                    <th>Email</th>
                                    <th>Total Verifications</th>
                                    <th>Status</th>
                                    <th>Sign Up Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verificationOfficers as $key => $verificationOfficer)
                                    <tr>
                                        <td class="table-plus text-center">{{ $key + 1 }}</td>
                                        <td>{{ $verificationOfficer->fullname }}</td>
                                        <td>{{ $verificationOfficer->email }}</td>
                                        <td></td>
                                        <td>{{ $verificationOfficer->status ? 'Active' : 'Inactive' }}</td>
                                        <td>{{ $verificationOfficer->created_at }}</td>
                                        <td>
                                            <a href="{{ route('officials.verification.officers.edit', [ $role, $verificationOfficer->id ]) }}" class="btn btn-primary"><span class=" icon-copy ti-pencil-alt mr-1 fw-50 "></span></a>
                                            <a href="{{ route('officials.verification.officers.activate', [ $role, $verificationOfficer->id ]) }}" class="btn btn-sm btn-dark">{{ $verificationOfficer->status ? 'Deactivate' : 'Activate' }} Officer</a>
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