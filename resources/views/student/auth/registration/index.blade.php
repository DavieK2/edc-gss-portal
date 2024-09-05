@extends('student.auth.layout')
@section('section')
	<div class="login-box bg-white box-shadow border-radius-10">
		<div class="login-title">
			<h2 style="font-size: 2em" class="text-center text-primary">Verify Fees</h2>
		</div>
		<form method="GET" action="{{ route('student.auth.registration.verify') }}">
			@csrf
			@if (session()->has('error'))
				<div class="alert alert-danger text-center" role="alert">
					{{ session('error') }}
				</div>
			@endif

			<div>
				<div class="input-group custom mb-1">
					<input type="text" class="form-control form-control-lg" placeholder="Enter Payment Pin or Reg No." name="pin" value="{{ old('pin') }}">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
					</div>
				</div>
				@error('pin') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
			
			<div>
                <select class="custom-select col-lg mt-3" name="session">
                    <option selected disabled>Select Session</option>
					@foreach ($sessions as $session)
                    	<option value="{{ $session->id }}">{{ $session->session }}</option>
					@endforeach
                </select>
				@error('session') <small class="text-danger">{{ $message }}</small>@enderror
            </div>

			<div>
                <select class="custom-select col-lg mt-3" name="level">
                    <option selected disabled>Select Level</option>
					@foreach ($levels as $level)
                    	<option value="{{ $level->id }}">{{ $level->level }} Level</option>
					@endforeach
                </select>
				@error('level') <small class="text-danger">{{ $message }}</small>@enderror
            </div>
			
			<div class="row mt-5">
				<div class="col-sm-12">
					<div class="input-group mb-0">
						<button type="submit" class="btn btn-primary btn-lg btn-block">Verify</button>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection