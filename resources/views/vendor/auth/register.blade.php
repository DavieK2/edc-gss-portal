@extends('vendor.auth.layout')
@section('section')
	<div class="login-box bg-white box-shadow border-radius-10">
		<div class="login-title">
			<h2 class="text-center text-primary">Register</h2>
		</div>
		<form method="POST" action="{{ route('vendor.auth.register') }}">
			@csrf
			@if (session()->has('error'))
				<div class="alert alert-danger text-center" role="alert">
					{{ session('error') }}
				</div>
			@endif
			<div>
				<div class="input-group custom mb-1">
					<input type="text" class="form-control form-control-lg" placeholder="Enter Full Name" name="fullname" value="{{ old('fullname') }}">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
					</div>
				</div>
				@error('fullname') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
			<div>
				<div class="input-group custom mb-1 mt-3">
					<input type="email" class="form-control form-control-lg" placeholder="Enter Email" name="email" value="{{ old('email') }}">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="icon-copy dw dw-email1"></i></span>
					</div>
				</div>
				@error('email') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
            <div>
				<div class="input-group custom mb-1 mt-3">
					<input type="number" class="form-control form-control-lg" placeholder="Enter Phone Number" name="phone_number" value="{{ old('phone_number') }}">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="icon-copy dw dw-phone-call"></i></span>
					</div>
				</div>
				@error('phone_number') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
			<div>
                <select class="custom-select col-lg mt-3" name="scheme">
                    <option selected disabled>Registering As...</option>
					@foreach ($schemes as $scheme)
                    	<option value="{{ $scheme->id }}">{{ $scheme->name }} Vendor</option>
					@endforeach
                </select>
				@error('scheme') <small class="text-danger">{{ $message }}</small>@enderror
            </div>
			<div>
				<div class="input-group custom mt-3 mb-1">
					<input type="password" class="form-control form-control-lg" placeholder="**********" name="password">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
					</div>
				</div>
				@error('password') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
			<div class="row mt-5">
				<div class="col-sm-12">
					<div class="input-group mb-0">
						<button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
					</div>
					<div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">OR</div>
					<div class="input-group mb-0">
						<a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('vendor.auth.login') }}">Login To Account</a>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection