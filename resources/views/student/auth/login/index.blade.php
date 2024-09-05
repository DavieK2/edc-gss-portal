@extends('student.auth.layout')
@section('section')
	<div class="login-box bg-white box-shadow border-radius-10">
		<div class="login-title">
			<h2 style="font-size: 2em" class="text-center text-primary">Login</h2>
		</div>
		<form method="POST" action="{{ route('student.auth.login.store') }}">
			@csrf
			@if (session()->has('error'))
				<div class="alert alert-danger text-center" role="alert">
					{{ session('error') }}
				</div>
			@endif
			<div>
				<div class="input-group custom mb-1">
					<input type="text" class="form-control form-control-lg" placeholder="Enter Student Code" name="student_code" value="{{ old('student_code') }}">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
					</div>
				</div>
				@error('student_code') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
			
			<div class="row mt-3">
				<div class="col-lg">
					<select class="custom-select" name="session">
						<option selected disabled>Select Session...</option>
						@foreach (\App\Models\Session::get() as $session)
							<option value="{{ $session->id }}">{{ $session->session }}</option>
						@endforeach
					</select>
					@error('session')
						<small class="text-danger -mt-5"> {{ $message }} </small>
					@enderror
				</div>
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
					<div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">OR</div>
					<div class="input-group mb-0">
						<a class="btn btn-outline-primary btn-lg btn-block" href="{{ route('student.auth.registration.index') }}">Register To Create Account</a>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection