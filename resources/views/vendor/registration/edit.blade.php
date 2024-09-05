@extends('vendor.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 " style="margin-top:20px;">
        <div class="clearfix">
            <h3 class="mb-30 text-dark mt-4">Student Registration</h3>
            <hr>
        </div>
        <div class="wizard-content py-2">
            <form action="{{ route('vendor.student.registration.update', $registration->invoice_number) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <section>
                    <div class="row p-2">
                        <div style="height: 300px; 
                                    width: 300px; 
                                    background-image:url({{ url("images/".$registration->student->profile_image) }}); 
                                    background-position: center; 
                                    background-size: cover; 
                                    border-radius: 16px">
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Full name</label>
                                <input type="text" class="form-control" name="" value="{{ $registration->student->fullname }}" disabled>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Matric no.</label>
                                <input type="text" class="form-control" name="" value="{{ $registration->student->mat_no }}"  disabled>
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
                                <label style="font-size:16px ;">Department</label>
                                <input type="text" class="form-control" name="" readonly value="{{ $registration->student->department->name }}"  disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Faculty</label>
                                <input type="text" class="form-control" name="" readonly value="{{ $registration->student->faculty->name }}"  disabled>
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
                                <label style="font-size:16px ;">Year of study</label>
                                <input type="text" class="form-control" readonly value="{{ $registration->student->session->session }}"  disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Level</label>
                                <input type="text" class="form-control" readonly value="{{ $registration->student->level->level }}"  disabled>
                            </div>
                        </div>

                        @can('view-supplementary')
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
                        @endcan
                        
                    </div>
                 
                    <div class="row">
                        
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
        
                    <div class="my-5">
                        <button class="btn btn-primary">Submit</button>
                    </div>
                </section>
    
    
            </form>
        </div>
    </div>
</div>
@endsection