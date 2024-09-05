@extends('vendor.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Verify Student</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('vendor.student.registration.create') }}" method="GET">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:600">Enter Student Code</label>
                                <input type="text" class="form-control" name="student_code" placeholder="Enter Student Code" value="{{ old('student_code') }}" >
                                @error('student_code')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:600">Select Scheme</label>
                            <select class="custom-select" name="scheme">
                                <option selected disabled>Select Scheme...</option>
                                @foreach (auth()->guard('vendor')->user()->schemes as $scheme)
                                    <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                @endforeach
                            </select>
                            @error('scheme')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:600">Select Session</label>
                            <select class="custom-select" name="session">
                                <option selected disabled>Select Session...</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->session }}</option>
                                @endforeach
                            </select>
                            @error('session')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:600">Select Semester</label>
                            <select class="custom-select" name="semester">
                                <option selected disabled>Select Semester...</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->semester }}</option>
                                @endforeach
                                    <option value="3">First & Second Semester</option>
                            </select>
                            @error('semester')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:600">Select Level</label>
                            <select class="custom-select" name="level">
                                <option selected disabled>Select Level...</option>
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">{{ $level->level }}</option>
                                @endforeach
                            </select>
                            @error('level')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label style="font-size:16px ; font-weight:600">Select Program Type</label>
                            <select class="custom-select" name="program_type">
                                <option selected disabled>Select Program Type...</option>
                                <option value="NUC">NUC</option>
                                <option value="CES">CES</option>
                            </select>
                            @error('program_type')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    @can('view-supplementary')
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label style="font-size:16px ; font-weight:600">Select Registration Type</label>
                                <select class="custom-select" name="is_supplementary">
                                    <option selected disabled>Select Registration Type...</option>
                                    <option value="0">Normal Registration</option>
                                    <option value="1">Supplementary Registration</option>
                                </select>
                                @error('is_supplementary')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    @endcan
                    
                    <div class="input-group mb-0 py-3 mt-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </section>
    
            </form>
        </div>
    </div>
</div>
@endsection