@extends('officials.layouts.app')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>Ventures</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Ventures</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4">Ventures</h4>
                    </div>
                    <div class="pb-20">
                        <table class="table hover table-bordered multiple-select-row data-table-export nowrap">
                            <thead>
                                <tr>
                                    <th class="table-plus datatable-nosort">S/N</th>
                                    <th>Venture Name</th>
                                    <th class="text-center">Item Code</th>
                                    <th class="text-center">Fee</th>
                                    <th class="text-center">Total Registrations</th>
                                    <th class="text-center">Max Registrations</th>
                                    <th class="text-center">Session</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventures as $key => $venture)
                                    <tr>
                                        <td class="table-plus text-center">{{ $key + 1 }}</td>
                                        <td>{{ $venture['title'] }}</td>
                                        <td class="text-center">{{ $venture['item_code'] }}</td>
                                        <td class="text-center">&#8358;{{ $venture['fee'] }}</td>
                                        <td class="text-center">{{ $venture['total_registrations'] }}</td>
                                        <td>
                                            <ul>
                                                @foreach ( ($venture['max_registrations'] ?? []) as $key => $item)
                                                    <li style="text-transform: capitalize" class="">{{ $key }} : {{ $item }}</li>
                                                @endforeach
                                            </ul>
                                           
                                        </td>
                                        
                                        <td class="text-center">{{ $venture['session'] }}</td>
                                        <td>
                                            <a href="{{ url("$role/venture/".$venture['id']."/edit") }}" class="btn btn-primary"><span class=" icon-copy ti-pencil-alt mr-1 fw-50 "></span></a>
                                            <a href="{{ url("$role/venture/".$venture['id']."/edit_fee") }}" class="btn btn-secondary btn-sm">Edit Fee</span></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection