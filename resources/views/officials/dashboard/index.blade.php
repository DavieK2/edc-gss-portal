@extends('officials.layouts.app')
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
				</div>
			</div>
        </div> 
        <div class="pd-ltr-20">
            @foreach ($stats as $stat)
                <div class="row">
                    @foreach ($stat as $total)
                        <div class="col-xl-3 mb-30">
                            <div class="card-box height-100-p px-2 text-center py-5 widget-style1">
                                <div class="d-flex flex-wrap align-items-center pt-4">
                                    <div class="col-12">
                                        <div class="h5 mb-2 text-primary">{{ $total['title'] ?? '' }}</div>
                                        <div class="weight-600 font-18">{{ $total['total'] ?? ''}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
           
        </div>
    </div>
@endsection