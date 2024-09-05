@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Create Department</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.departments.store', $role) }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Department name</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter Full Name" value="{{ old('name') }}" >
                                @error('name')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>
                    <div class="row m-1 pb-2">
                        <h6>Choose a faculty</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @foreach ($faculties as $faculty)
                                <div class="custom-control custom-checkbox mb-5">
                                    <input type="radio" class="custom-control-input" value="{{ $faculty->id }}" name="faculty" id="faculty{{ $faculty->id }}">
                                    <label class="custom-control-label pl-3" for="faculty{{ $faculty->id }}">{{ $faculty->name }}</label>
                                </div>
                            @endforeach
                            
                            @error('faculty')
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