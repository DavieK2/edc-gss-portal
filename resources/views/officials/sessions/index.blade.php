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
                        <div class="col-md-6 col-sm-12 text-right">
                            <div class="dropdown">
                                <a class="btn btn-primary" href="{{ route('officials.sessions.create', $role ) }}">
                                Add Session
                                </a>
                            </div>
                        </div>
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
                                    <th>Session</th>
                                    {{-- <th>Semesters</th> --}}
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @foreach ($sessions as $key => $session)
                                    <tr class="text-center">
                                        <td class="table-plus text-left">{{ $key + 1 }}</td>
                                        <td class="text-left">{{ $session->session }}</td>
                                        {{-- <td class="text-left">{{ implode(', ', $session->registration_semesters ?? []) }}</td> --}}
                                        <td class="text-left">{{ $session->status ? 'Active' : 'Not Active' }}</td>
                                        <td class="text-left">
                                            <a href="{{ route('officials.sessions.edit', [ $role, $session ]) }}" class="btn btn-sm font-11 btn-primary">Edit</a>
                                            <a href="{{ route('officials.sessions.toggle', [ $role, $session ]) }}" class="btn btn-sm font-11 btn-dark">{{ $session->status ? 'Deactivate' : 'Activate' }}</a>

                                            @foreach ($schemes as $scheme)
                                                <a href="{{ route('officials.sessions.update.status', [ $role, $session, $scheme ]) }}" class="btn btn-sm font-11 btn-success">{{ in_array($scheme->name, ($session->can_register ?? [])) ? "Stop $scheme->name  Registrations" : "Activate $scheme->name  Registrations" }}</a>
                                            @endforeach
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