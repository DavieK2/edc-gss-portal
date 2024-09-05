@extends('vendor.layouts.app')
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
                                <a class="btn btn-primary" href="{{ route('vendor.student.registration.verify') }}">
                                Register student
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <x-datatable page="{{ $page }}" :headings="$tableHeadings" role="vendor" ajaxUrl="{{ $ajaxUrl }}" name="student_name,invoice_number" searchLabel="Search For Registration" />

            </div>

        </div>
    </div>
@endsection