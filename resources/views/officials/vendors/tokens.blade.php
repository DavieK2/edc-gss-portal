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
                                    <li class="breadcrumb-item"><a href="#">Vendors</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Token Purchases</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            
            
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4">Token Purchases</h4>
                    </div>
                    <div class="pb-20">
                        <table class="table hover table-bordered multiple-select-row data-table-export nowrap">
                            <thead>
                                <tr>
                                    <th class="table-plus datatable-nosort">S/N</th>
                                    <th>Email</th>
                                    <th>Number of Tokens</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tokens as $key => $token)
                                    <tr>
                                        <td class="table-plus text-center">{{ $key + 1 }}</td>
                                        <td>{{ $token->vendor->email }}</td>
                                        <td>{{ $token->number_of_tokens }}</td>
                                        <td>&#8358; {{ number_format($token->price, 2) }}</td>
                                        <td>{{ $token->type == 'credit' ? 'Purchase' : 'Registration' }}</td>
                                        <td>{{ $token->created_at }}</td>
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