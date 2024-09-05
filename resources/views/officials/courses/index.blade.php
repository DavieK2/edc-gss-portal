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
                      
                        <div class="col-md-6">
                            <div class="row float-right px-4">
                               
                                <div class="dropdown pr-2">
                                    <a class="btn btn-primary" href="{{ route('officials.courses.create', [$role, $scheme])}}">
                                        Create Course
                                    </a>
                                </div>
                            
                                <div class="dropdown">
                                    <a class="btn btn-success" href="{{ route('officials.courses.add_courses', [$role, $scheme])}}">
                                        Add Course
                                    </a>
                                </div>
                               
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
                                    <th>Course Name</th>
                                    <th>Course Code</th>
                                    <th>Course Fee</th>
                                    <th>Documentation Fee</th>
                                    <th>Account</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $key => $course)
                                    <tr>
                                        <td class="table-plus text-center">{{ $key + 1 }}</td>
                                        <td>{{ $course->title }}</td>
                                        <td>{{ $course->item_code }}</td>
                                        <td>N{{ number_format( floatval($course->fee) ) }}</td>
                                        <td>N{{ number_format( floatval($course->documentation_fee) ) }}</td>
                                        <td>
                                            @if (isset( $course->account->account_name) )
                                                <span>{{ $course->account->account_name }} ({{ $course->account->account_number }})</span>
                                            @else
                                                <ul>
                                                    @foreach ($course->account_ids as $account)
                                                        @isset ($account['account_name'])
                                                            <li> - {{ $account['account_name'] ?? '' }} <span class="text-primary"> ( {{  $account['account_number'] ?? '' }} ) </span></li>
                                                        @endisset
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('officials.courses.edit', [ $role, $course, $scheme ]) }}" class="btn btn-primary btn-sm" style="font-size:8pt; padding:0.3rem">Edit</a>
                                            <a href="{{ route('officials.courses.edit.departments', [ $role, $course, $scheme ]) }}" class="btn btn-info btn-sm" style="font-size:8pt; padding:0.3rem">Edit Dept.</a>
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