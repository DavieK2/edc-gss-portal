@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 " style="margin-top:20px;">
        <div class="clearfix">
            <h3 class="mb-30 text-dark mt-4">Edit Registration</h3>
            <hr>
        </div>
        <div class="wizard-content py-2">
            <form action="{{ route('officials.registrations.update', [ $role, $registration ]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <section>
                    <div class="row p-2">
                        
                            <div style="height: 100px; 
                                        width: 100px; 
                                        background-image:url({{ url("images/$registration->student->profile_image") }}); 
                                        background-position: center; 
                                        background-size: cover; 
                                        border-radius: 16px">
                            </div>
                    </div>
                    @error('passport')
                        <p class="text-danger"> {{ $message }}</p>
                    @enderror

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Full name</label>
                                <input type="text" class="form-control" name="student_name" value="{{ $registration->student_name }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Matric no./ Reg No.</label>
                                <input type="text" class="form-control" readonly name="" value="{{ $registration->student->mat_no }}" disabled>
                            </div>
                        </div>
                     
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Email</label>
                                <input type="text" class="form-control" name=""  value="{{ $registration->student->user->email }}"  disabled>
                            </div>
                        </div>
                        
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Phone</label>
                                <input type="text" class="form-control" name="" value="{{ $registration->student->user->phone_number }}"  disabled>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Gender</label>
                                <input type="text" class="form-control" name="" value="{{ $registration->student->user->gender }}"  disabled>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Program Type</label>
                                <input type="text" class="form-control" name="" readonly value="{{ $registration->scheme->name }}"  disabled>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Select Faculty</label>
                                <select class="custom-select" name="faculty_id" id="faculty">
                                    <option selected disabled>Select Faculty...</option>
                                    @foreach (\App\Models\Faculty::get() as $faculty)
                                        <option {{ $registration->faculty->id == $faculty->id ? 'selected' : '' }} value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                    @endforeach
                                </select>
                                @error('faculty_id')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                       
    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Select Department</label>
                                <select class="custom-select" name="department" id="department">
                                    <option value="{{ $registration->department }}">{{ $registration->department }}</option>
                                </select>
                                @error('department')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                       

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Select Session</label>
                                <select class="custom-select" name="session" id="session">
                                    <option selected disabled>Select Session...</option>
                                    @foreach (\App\Models\Session::get() as $session)
                                        <option {{ $registration->session == $session->session ? 'selected' : '' }} value="{{ $session->session }}">{{ $session->session  }}</option>
                                    @endforeach
                                </select>
                                @error('session')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Select Level</label>
                                <select class="custom-select" name="level" id="level">
                                    <option selected disabled>Select Level...</option>
                                    @foreach (\App\Models\Level::get() as $level)
                                        <option {{ $registration->level == $level->level ? 'selected' : '' }} value="{{ $level->level }}">{{ $level->level }}</option>
                                    @endforeach
                                </select>
                                @error('level')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:500">Select Semester</label>
                            <select class="custom-select" name="semester">
                                <option selected disabled>Select Semester...</option>
                                @foreach ($semesters as $semester)
                                    <option {{ $registration->semester == $semester->semester ? 'selected' : '' }} value="{{ $semester->semester }}">{{ $semester->semester }}</option>
                                @endforeach
                                    <option {{ $registration->semester == 'First & Second Semester' ? 'selected' : '' }} value="First & Second Semester">First & Second Semester</option>
                            </select>
                            @error('semester')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:600">Registration Type</label>
                            <select class="custom-select" name="is_supplementary">
                                <option selected disabled>Select Registration Type...</option>
                                <option {{ $registration->is_supplementary == 0 ? 'selected' : '' }} value="0">Normal Registration</option>
                                <option {{ $registration->is_supplementary == 1 ? 'selected' : '' }} value="1">Supplementary Registration</option>
                            </select>
                            @error('is_supplementary')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>

                        <div class="row mx-2">
                        
                            <div class="col-md-6 mt-5 mb-5">
                                <h3 class="text-primary">Available Courses</h3>
                            </div>
                            <div class="col-12 mt-5">
                                @error('courses')
                                    <div class="alert alert-danger" role="alert">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <table class="table responsive table-striped mt-5 mb-0">
                                    @foreach ($courses as $course)
                                   
                                        <tr>
                                            <td style="width: 5%">
                                                <div class="custom-control custom-checkbox mb-5">
                                                    <input {{ in_array($course->id, $registration->courses->pluck('id')->toArray()) ? 'checked' : '' }}  type="checkbox" class="custom-control-input" value="{{ $course->id }}" name="courses[]" id="course{{ $course->id }}">
                                                    <label class="custom-control-label" for="course{{ $course->id }}"></label>
                                                </div>
                                            </td>
                                            <td><span class="text-primary">{{ $course->item_code }}</span>  - {{ $course-> title }}</td>
                                            <td class="float-right pr-5">&#8358; {{ number_format($course->fee) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
    
                        @if ($ventures->isNotEmpty())
                            <div class="row mx-2">
                                <div class="col-md-6 mt-5 mb-5">
                                    <h3 class="text-primary">Available Ventures</h3>
                                </div>
            
                                <div class="col-12">
                                    <table class="table responsive table-striped mt-5 mb-0">
                                        <tr>
                                            <td style="width: 5%">
                                                <div class="custom-control custom-radio mb-5">
                                                    <input type="radio" class="custom-control-input" value="" name="venture" id="no-venture">
                                                    <label class="custom-control-label" for="no-venture"></label>
                                                </div>
                                            </td>
                                            <td><span class="text-primary text-uppercase">None</td>
                                            <td class="float-right pr-5"></td>
                                        </tr>
                                        @foreach ($ventures as $venture)
                                            <tr>
                                                <td style="width: 5%">
                                                    <div class="custom-control custom-radio mb-5">
                                                        <input {{ in_array($venture->id, $registration->courses->pluck('id')->toArray()) ? 'checked' : '' }} type="radio" class="custom-control-input" value="{{ $venture->id }}" name="venture" id="course{{ $venture->id }}">
                                                        <label class="custom-control-label" for="course{{ $venture->id }}"></label>
                                                    </div>
                                                </td>
                                                <td><span class="text-primary">{{ $venture->item_code }}</span>  - {{ $venture-> title }}</td>
                                                <td class="float-right pr-5">&#8358; {{ number_format($venture->fee) }}</td>
                                            </tr>
                                        @endforeach
                                        
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="my-5">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </section>
    
    
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
                option.setAttribute('value', dep.name);
                if(depValue == dep.name){
                    option.setAttribute('selected', true);
                }
                let value = document.createTextNode(dep.name);
                option.appendChild(value);
                department.appendChild(option);
            })
        }
	</script>


@endsection