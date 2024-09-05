@extends('vendor.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 " style="margin-top:20px;">
        <div class="clearfix">
            <h3 class="mb-30 text-dark mt-4">Student Registration</h3>
            <hr>
        </div>
        <div class="wizard-content py-2">
            <form action="" method="POST" enctype="multipart/form-data">
                <section>
                    <div class="row p-2">
                        @if (is_null($student->profile_image))
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
                                    <img style="rounded" class="img" src="" height="200" alt="" srcset="">
                                </div>
                            </div>
                        @else
                            <div style="height: 300px; 
                                        width: 300px; 
                                        background-image:url({{ url("images/$student->profile_image") }}); 
                                        background-position: center; 
                                        background-size: cover; 
                                        border-radius: 16px">
                            </div>
                        @endif
                    </div>
                    @error('passport')
                        <p class="text-danger"> {{ $message }}</p>
                    @enderror

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Full name</label>
                                <input type="text" class="form-control" name="" value="{{ $student->fullname }}" disabled>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Matric no./ Reg No.</label>
                                <input type="text" class="form-control" name="" value="{{ $student->mat_no ?? $student->school_fees_pin }}"  disabled>
                            </div>
                        </div>
                     
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Email</label>
                                <input type="text" class="form-control" name=""  value="{{ $student->user->email }}"  disabled>
                            </div>
                        </div>
                        
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Phone</label>
                                <input type="text" class="form-control" name="" value="{{ $student->user->phone_number }}"  disabled>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Gender</label>
                                <input type="text" class="form-control" name="" value="{{ $student->user->gender }}"  disabled>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Department</label>
                                <input type="text" class="form-control" name="" readonly value="{{ $student->department->name }}"  disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Faculty</label>
                                <input type="text" class="form-control" name="" readonly value="{{ $student->faculty->name }}"  disabled>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Program Type</label>
                                <input type="text" class="form-control" name="" readonly value="{{ $program_type }}"  disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Year of study</label>
                                <input type="text" class="form-control" readonly value="{{ $student->session->session }}"  disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Level</label>
                                <input type="text" class="form-control" readonly value="{{ $student->level->level }}"  disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Registration Type</label>
                                <input type="text" class="form-control" readonly value="{{ $registration_type }}"  disabled>
                            </div>
                        </div>
                    </div>
                 
                    <div class="row">
                        <div class="col-md-6 mt-5 mb-5">
                            <h3 class="text-primary">Available Courses</h3>
                        </div>

                        <div class="col-12">
                            <table class="table responsive table-striped mt-5 mb-0">
                                @foreach ($courses as $course)
                                    <tr>
                                        <td style="width: 5%">
                                            <div class="custom-control custom-checkbox mb-5">
                                                <input type="checkbox" class="custom-control-input" value="{{ $course->id }}" name="courses[]" id="course{{ $course->id }}">
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
                        <div class="row">
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
                                        <td><span class="text-primary">No Venture</td>
                                    </tr>
                                    @foreach ($ventures as $venture)
                                        <tr>
                                            <td style="width: 5%">
                                                <div class="custom-control custom-radio mb-5">
                                                    <input type="radio" class="custom-control-input" value="{{ $venture->id }}" name="venture" id="course{{ $venture->id }}">
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
                   

                    <div class="my-5">
                        <button class="btn btn-primary">Submit</button>
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
@endsection