@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Create Verification Officer</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.verification.officers.store', $role) }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Full name</label>
                                <input type="text" class="form-control" name="fullname" placeholder="Enter Full Name" value="{{ old('fullname') }}" >
                                @error('fullname')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Email" value="{{ old('email') }}" >
                                @error('email')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="" style="font-size:16px; font-weight:500">Select Scheme</label>
                            <select class="custom-select" name="scheme">
                                <option selected disabled>Select Scheme...</option>
                                @foreach ($schemes as $scheme)
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
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Enter Password" value="{{ old('password') }}" >
                                @error('password')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row m-1 pb-2">
                        <h6>Assign to a faculty</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @foreach ($faculties as $faculty)
                                <div class="custom-control custom-checkbox mb-5">
                                    <input type="checkbox" class="custom-control-input" value="{{ $faculty->id }}" name="faculties[]" id="faculty{{ $faculty->id }}">
                                    <label class="custom-control-label pl-3 text-uppercase" for="faculty{{ $faculty->id }}">{{ $faculty->title }}</label>
                                </div>
                            @endforeach
                            
                            @error('faculties')
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