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
                    </div>
                </div>

                <x-datatable page="{{ $page }}" :filters="$filters" :headings="$tableHeadings" role="{{ $role }}" name="reg_no,student_name,invoice_number" ajaxUrl="{{ $ajaxUrl }}" searchLabel="Enter Payment Code | Reg No." />

            </div>

        </div>
    </div>
@endsection