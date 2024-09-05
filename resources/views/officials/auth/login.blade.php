@extends('officials.auth.layout')
@section('section')
	<div class="login-box bg-white box-shadow border-radius-10">
		<div class="login-title">
			<h2 class="text-center text-primary">Login</h2>
		</div>
		<form method="POST" action="{{ route('officials.auth.login', $role) }}">
			@csrf

			@if (session()->has('error'))
				<div class="alert alert-danger text-center" role="alert">
					{{ session('error') }}
				</div>
			@endif

			<div>
				<div class="input-group custom mb-1">
					<input type="text" class="form-control form-control-lg" placeholder="Enter Email" name="email" value="{{ old('email') }}">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
					</div>
				</div>
				@error('email') <small class="text-danger">{{ $message }}</small>@enderror
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
			<div class="row pb-30 pt-30">
				<div class="col-6">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" id="customCheck1">
						<label class="custom-control-label" for="customCheck1">Remember</label>
					</div>
				</div>
				<div class="col-6">
					<div class="forgot-password"><a href="#forgot">Forgot Password</a></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="input-group mb-0">
						<button type="submit" class="btn btn-primary btn-lg btn-block">Log In</button>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection