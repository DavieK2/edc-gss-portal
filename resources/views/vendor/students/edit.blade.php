@extends('vendor.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Edit Student Profile</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            
            <form action="{{ route('vendors.students.update.profile', [$student->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <section>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;font-weight:700">Enter Student Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Student Email" value="{{ old('email') ?? $student->user->email }}" >
                                @error('email')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Student Phone Number</label>
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
                                <label style="font-size:16px ; font-weight:700">Enter Student Mat. No</label>
                                <input type="text" class="form-control" name="mat_no" placeholder="Enter Student Mat. No" value="{{ old('mat_no') ?? $student->mat_no }}" >
                                @error('mat_no')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:700">Select Faculty</label>
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
                            <label style="font-size:16px ; font-weight:700">Select Department</label>
                            <select class="custom-select" name="department_id" id="department">
                                <option disabled>Select Department...</option>
                                <option value="{{ $student->department_id }}">{{ $student->department->name }}</option>
                            </select>
                            @error('department_id')
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