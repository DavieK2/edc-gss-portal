@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Create Student Profile</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            
            <form action="{{ route('officials.students.store', [$role]) }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Full Name</label>
                                <input type="text" class="form-control" name="fullname" placeholder="Enter Student Full Name" value="{{ old('fullname') }}" >
                                @error('fullname')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Student Email" value="{{ old('email') }}" >
                                @error('email')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <select class="custom-select col-lg mb-3" name="gender">
                                <option disabled selected>Select Gender...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('gender') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Phone Number</label>
                                <input type="number" class="form-control" name="phone_number" placeholder="Enter Student Phone Number" value="{{ old('phone_number') }}" >
                                @error('phone_number')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Enter Student Password" value="{{ old('student_code') }}" >
                                @error('password')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Mat. No</label>
                                <input type="text" class="form-control" name="mat_no" placeholder="Enter Student Mat. No" value="{{ old('mat_no') }}" >
                                @error('mat_no')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <select class="custom-select" name="faculty_id" id="faculty">
                                <option selected disabled>Select Faculty...</option>
                                @foreach (\App\Models\Faculty::get() as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <select class="custom-select" name="department_id" id="department">
                                <option selected disabled>Select Department...</option>
                            </select>
                            @error('department_id')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <select class="custom-select" name="session_id">
                                <option selected disabled>Select Session...</option>
                                @foreach (\App\Models\Session::orderBy('session', 'asc')->get() as $session)
                                    <option value="{{ $session->id }}">{{ $session->session }}</option>
                                @endforeach
                            </select>
                            @error('session_id')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <select class="custom-select" name="level_id">
                                <option selected disabled>Select Level...</option>
                                @foreach (\App\Models\Level::orderBy('level', 'asc')->get() as $level)
                                    <option value="{{ $level->id }}">{{ $level->level }}</option>
                                @endforeach
                            </select>
                            @error('level_id')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="input-group mb-0 py-3 mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </section>
    
    
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
		<script>
			let dep = document.getElementById('department');
            let faculty = document.getElementById('faculty');

            faculty.addEventListener('change', (e) => getDep(e.target.value, dep));
			
			const getDep = async(fac, department) => {
				res = await fetch("{{ url('/get-departments') }}/"+fac);
				res = await res.json();
				department.innerHTML = '';

				res.departments.forEach((dep) => {
					let option = document.createElement('option');
					option.setAttribute('value', dep.id);
					let value = document.createTextNode(dep.name);
					option.appendChild(value);
					department.appendChild(option);
				})
			}
		</script>
	
@endsection