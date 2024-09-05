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
                            <div class="row float-right">
                                <div class="dropdown p-2">
                                    <a class="btn btn-primary btn-sm" style="font-size: 9pt" href="{{ route('officials.schemes.settings.toggle.online', [$role,$scheme]) }}">
                                        {{ $scheme->is_online_payment_enabled ? 'Disable Online Payments' : 'Enable Online Payments' }}
                                    </a>
                                </div>
                                <div class="dropdown p-2">
                                    <a class="btn btn-primary btn-sm" style="font-size: 9pt" href="{{ route('officials.schemes.charges.create', [ $role, $scheme, 'online']) }}">
                                        Add New Charge
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
                                    <th>Title</th>
                                    <th>Item Code</th>
                                    <th>Charge</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($charges_online ?? [] as $key => $charge)
                                    <tr>
                                        <td class="table-plus text-center">{{ $key + 1 }}</td>
                                        <td>{{ $charge['title'] ?? '' }}</td>
                                        <td>{{ $charge['item_code'] ?? '' }}</td>
                                        <td>N{{ number_format($charge['fee'] ?? 0) }}</td>
                                        <td>
                                            @php
                                                $account = App\Models\Account::firstWhere('account_id', $charge['account_id'] ?? '');
                                            @endphp

                                            {{ $account?->account_name }} {{ $account?->account_number ? '('.$account?->account_number.')' : '' }}
                                        </td>
                                        <td>Course</td>
                                        <td>
                                            <a href="{{ route('officials.schemes.charges.edit', [ $role, $scheme, $key, 'course', 'online' ]) }}" class="btn btn-primary btn-sm" style="font-size:8pt; padding:0.3rem">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                               
                                @forelse($token_accounts ?? [] as $key => $charge)
                                    <tr>
                                        <td class="table-plus text-center">{{ count($charges_online) + $key + 1 }}</td>
                                        <td>{{ $charge['title'] ?? '' }}</td>
                                        <td>{{ $charge['item_code'] ?? '' }}</td>
                                        <td>N{{ number_format($charge['fee'] ?? 0) }}</td>
                                        <td>
                                            @php
                                                $account = App\Models\Account::firstWhere('account_id', $charge['account_id'] ?? '');
                                            @endphp

                                            {{ $account?->account_name }} {{ $account?->account_number ? '('.$account?->account_number.')' : '' }}
                                        </td>
                                        <td>Token</td>
                                        <td>
                                            <a href="{{ route('officials.schemes.charges.edit', [ $role, $scheme, $key, 'token', 'online' ]) }}" class="btn btn-primary btn-sm" style="font-size:8pt; padding:0.3rem">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                    <!-- Export Datatable End -->
            </div>

        </div>
    </div>
@endsection