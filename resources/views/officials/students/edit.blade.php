@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Edit Student Profile</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            
            <form action="{{ route('officials.students.update', [$role, $student->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <section>
                    <div class="row p-2">
                        <div class="" style="height: 300px; 
                                            width: 300px;
                                            overflow: hidden">
                            <div class="card p-2 m-1 row align-items-center justify-content-center" style="background: rgb(186, 185, 185); 
                                                    border-radius : 0.5em;
                                                    height: 300px; 
                                                    width: 300px;
                                                    overflow: hidden">
                                <input type="file" accept="image/*" name="passport" id="passport" onchange="previewFile()" hidden>
                                <label class="btn btn-dark btn-sm" style="font-size: 12px" class="text-sm" for="passport">Upload Passport</label>
                                <img style="rounded" class="img" src="{{ url("images/$student->profile_image") }}" height="200" alt="" srcset="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Full Name</label>
                                <input type="text" class="form-control" name="fullname" placeholder="Enter Student Full Name" value="{{ old('fullname') ?? $student->fullname }}" >
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
                                <input type="email" class="form-control" name="email" placeholder="Enter Student Email" value="{{ old('email') ?? $student->user->email }}" >
                                @error('email')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <select class="custom-select col-lg mb-3" name="gender">
                                <option disabled>Select Gender...</option>
                                <option {{ $student->user->gender == 'Male' ? 'selected' : '' }} value="Male">Male</option>
                                <option {{ $student->user->gender == 'Female' ? 'selected' : '' }} value="Female">Female</option>
                            </select>
                            @error('gender') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Phone Number</label>
                                <input type="number" class="form-control" name="phone_number" placeholder="Enter Student Phone Number" value="{{ old('phone_number') ?? $student->user->phone_number }}" >
                                @error('phone_number')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Student Mat. No</label>
                                <input type="text" class="form-control" name="mat_no" placeholder="Enter Student Mat. No" value="{{ old('mat_no') ?? $student->mat_no }}" >
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
                                    <option {{ $student->faculty_id == $faculty->id ? 'selected' : '' }} value="{{ $faculty->id }}">{{ $faculty->name }}</option>
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
                                <option disabled>Select Department...</option>
                                <option value="{{ $student->department_id }}">{{ $student->department->name }}</option>
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
                                    <option {{ $student->session_id == $session->id ? 'selected' : '' }} value="{{ $session->id }}">{{ $session->session }}</option>
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
                                    <option {{ $student->level_id == $level->id ? 'selected' : '' }} value="{{ $level->id }}">{{ $level->level }}</option>
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

            <div class="row">
                <div class="d-flex flex-row col-12 justify-content-end">
                    <form id="studentDelete" action="{{ route('officials.students.delete', [$role, $student->id]) }}" method="POST">@csrf</form>
                    <button class="btn btn-danger" onclick="document.getElementById('studentDelete').submit()">Delete Profile</button>
                </div>
            </div>
            
        </div>
    </div>

</div>
@endsection


@section('script')
        <script>
            function previewFile() {
                var preview = document.querySelector('.img');
                var file    = document.querySelector('input[type=file]').files[0];
                var reader  = new FileReader();

                reader.onloadend = function () {
                    preview.src = reader.result;
                }

                if (file) {
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                }
            }
        </script>
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