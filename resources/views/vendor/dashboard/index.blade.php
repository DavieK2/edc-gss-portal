@extends('vendor.layouts.app')
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
                            <a class="btn btn-primary btn-rounded-sm" href="{{ route('vendor.token.create') }}">Buy Token</a>
                        </div>
					</div>
				</div>
			</div>
        </div> 
        <div class="pd-ltr-20">
            @foreach (auth()->guard('vendor')->user()->schemes as $scheme)
                <div class="row">
                    <div class="col-xl-3 mb-30">
                        <div class="card-box height-100-p px-2 text-center py-5 widget-style1">
                            <div class="d-flex flex-wrap align-items-center pt-4">
                                <div class="col-12">
                                    <div class="h4 mb-0 text-primary">{{ auth()->guard('vendor')->user()->totalTokensBought($scheme) }}</div>
                                    <div class="weight-600 font-14">Tokens purchased for {{ $scheme->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <div class="col-xl-3 mb-30">
                        <div class="card-box height-100-p px-2 text-center py-5 widget-style1">
                            <div class="d-flex flex-wrap align-items-center pt-4">
                                <div class="col-12">
                                    <div class="h4 mb-0 text-primary">{{ auth()->guard('vendor')->user()->totalTokensUsed($scheme) }}</div>
                                    <div class="weight-600 font-14">Tokens used for {{ $scheme->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 mb-30">
                        <div class="card-box height-100-p px-2 text-center py-5 widget-style1">
                            <div class="d-flex flex-wrap align-items-center pt-4">
                                <div class="col-12">
                                    <div class="h4 mb-0 text-primary">{{ auth()->guard('vendor')->user()->balance($scheme) }}</div>
                                    <div class="weight-600 font-14">Tokens remaining for {{ $scheme->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 mb-30">
                        <div class="card-box height-100-p px-2 text-center py-5 widget-style1">
                            <div class="d-flex flex-wrap align-items-center pt-4">
                                <div class="col-12">
                                    <div class="h4 mb-0 text-primary">{{ auth()->guard('vendor')->user()->totalRegistrations($scheme) }}</div>
                                    <div class="weight-600 font-14">Total registrations for {{ $scheme->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
           
        </div>
    </div>
@endsection