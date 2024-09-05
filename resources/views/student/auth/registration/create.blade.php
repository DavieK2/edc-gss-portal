@extends('student.auth.layout')
@section('section')
	<div style="max-width: 800px" class="login-box bg-white box-shadow border-radius-10">
		<div class="login-title">
			<h2 style="font-size: 2em" class="text-primary">Register</h2>
		</div>
		<form class="" style="font-weight: 600" method="POST" action="{{ route('student.auth.registration.store') }}">
			@csrf
			@if (session()->has('error'))
				<div class="alert alert-danger text-center" role="alert">
					{{ session('error') }}
				</div>
			@endif
			<div>
				<label for="">Full Name</label>
				<div class="input-group custom mb-1">
					<input type="text" disabled  class="form-control form-control-lg text-uppercase" value="{{ $student_data['fullname'] }}">
					<div class="input-group-append custom">
						<span class="input-group-text"></span>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-lg">
					<label class="">Select Faculty</label>
					<select class="custom-select" name="faculty" id="faculty">
						<option selected disabled value="">Select Faculty...</option>
						@foreach (\App\Models\Faculty::get() as $faculty)
							<option {{ isset($student_data['faculty']) && $student_data['faculty'] == $faculty->id ? 'selected' : '' }} value="{{ $faculty->id }}">{{ $faculty->name }}</option>
						@endforeach
					</select>
					@error('faculty')
						<small class="text-danger -mt-5"> {{ $message }} </small>
					@enderror
				</div>
			</div>
		
			<div class="row mt-3">
				<div class="col-lg">
					<label class="">Select Department</label>
					<select class="custom-select department" name="department" id="department">
						<option>Select Department...</option>
						@if (isset($student_data['department']))
							<option selected value="{{ $student_data['department'] }}">{{ \App\Models\Department::find($student_data['department'])->name }}</option>
						@endif
					</select>
					@error('department')
						<small class="text-danger -mt-5"> {{ $message }} </small>
					@enderror
				</div>
			</div>
			

			@if (is_null( $student_data['session']))
				<div class="row mt-3">
					<div class="col-lg">
						<label class="">Select Session</label>
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
			@else
				<div style="font-weight: 600" class="mt-3">
					<label for="">Session</label>
					<div class="input-group custom mb-1">
						<input type="text" disabled  class="form-control form-control-lg text-uppercase" value="{{ \App\Models\Session::find($student_data['session'])->session }}">
						<div class="input-group-append custom">
							<span class="input-group-text"></span>
						</div>
					</div>
				</div>
			@endif

			<div class="row mt-3">
				<div class="col-lg">
					<label class="">Select Level</label>
					<select class="custom-select" name="level">
						<option selected disabled>Select Level...</option>
						@foreach (\App\Models\Level::orderBy('level', 'asc')->get() as $level)
							<option {{ $level->id == $student_data['level_id'] ? 'selected' : ''  }}  value="{{ $level->id }}">{{ $level->level }}</option>
						@endforeach
					</select>
					@error('level')
						<small class="text-danger -mt-5"> {{ $message }} </small>
					@enderror
				</div>
			</div>

			@if ( ( is_null($student_data['mat_no']) ) && \App\Models\Level::find($student_data['level_id'])->level > 100)
				<div>
					<div class="input-group custom mb-1 mt-3">
						<input type="text" class="form-control form-control-lg text-uppercase" placeholder="Enter Matric No" name="mat_no" value="{{ old('mat_no') }}">
						<div class="input-group-append custom">
							<span class="input-group-text"></span>
						</div>
					</div>
					@error('mat_no') <small class="text-danger">{{ $message }}</small>@enderror
				</div>
			@else
				<div style="font-weight: 600" class="mt-3">
					<label for="">Matric No.</label>
					<div class="input-group custom mb-1">
						<input type="text" disabled  class="form-control form-control-lg text-uppercase" value="{{ $student_data['mat_no'] ??  $student_data['school_fees_pin']}}">
						<div class="input-group-append custom">
							<span class="input-group-text"></span>
						</div>
					</div>
				</div>
			@endif

			@php
				$student_profile = \App\Models\StudentProfile::firstWhere('student_code', $student_data['mat_no'] ?? $student_data['school_fees_pin']);
			@endphp
			
			@if (is_null($student_profile))
				<div>
					<label class="mt-3" for="">Enter Email Address</label>
					<div class="input-group custom mb-1">
						<input type="email" class="form-control form-control-lg text-uppercase" placeholder="Enter Email" name="email" value="{{ old('email') }}">
						<div class="input-group-append custom">
							<span class="input-group-text"><i class="icon-copy dw dw-email1"></i></span>
						</div>
					</div>
					@error('email') <small class="text-danger">{{ $message }}</small>@enderror
				</div>
				<div>
					<label class="mt-3" for="">Enter Phone Number</label>
					<div class="input-group custom mb-1">
						<input type="number" class="form-control form-control-lg text-uppercase" placeholder="Enter Phone Number" name="phone_number" value="{{ old('phone_number') }}">
						<div class="input-group-append custom">
							<span class="input-group-text"><i class="icon-copy dw dw-phone-call"></i></span>
						</div>
					</div>
					@error('phone_number') <small class="text-danger">{{ $message }}</small>@enderror
				</div>
				<div>
					<label class="mt-3" for="">Select Gender</label>
					<select class="custom-select col-lg text-uppercase" name="gender">
						<option disabled selected>Select Gender...</option>
						<option value="Male">Male</option>
						<option value="Female">Female</option>
					</select>
					@error('gender') <small class="text-danger">{{ $message }}</small>@enderror
				</div>
			@endif

			<div>
				<label class="mt-3">{{ is_null($student_profile) ? 'Create Password' : 'Enter Student Login Password' }}</label>
				<div class="input-group custom mb-1">
					<input type="password" class="form-control form-control-lg text-uppercase" placeholder="**********" name="password">
					<div class="input-group-append custom">
						<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
					</div>
				</div>
				@error('password') <small class="text-danger">{{ $message }}</small>@enderror
			</div>
			<div class="row mt-5">
				<div class="col-sm-12">
					<div class="input-group mb-0">
						<button type="submit" class="btn btn-primary btn-lg text-uppercase btn-block">Register</button>
					</div>
					<div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">OR</div>
					<div class="input-group mb-0">
						<a class="btn btn-outline-primary btn-lg text-uppercase btn-block" href="{{ route('student.auth.login.index') }}">Login To Account</a>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection

@section('script')
	
		<script>
			let dep = document.getElementById('department');
			let faculty = document.getElementById('faculty');
			let depValue = dep.value;


            window.addEventListener('load', (e) => getDep(faculty.value, dep));
        
            faculty.addEventListener('change', (e) => getDep(e.target.value, dep));
        
            const getDep = async(fac, department) => {
                res = await fetch("{{ url('/get-departments') }}/"+fac);
                res = await res.json();
                department.innerHTML = '';

                res.departments.forEach((dep) => {
                    let option = document.createElement('option');
                    option.setAttribute('value', dep.id);
                    if(depValue == dep.id){
                        option.setAttribute('selected', true);
                    }
                    let value = document.createTextNode(dep.name);
                    option.appendChild(value);
                    department.appendChild(option);
                })
            }
		</script>
	
@endsection
