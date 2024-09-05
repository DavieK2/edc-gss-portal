@extends('officials.layouts.app')
@section('content')
    <div class="main-container">
        <div class="pd-20 card-box mb-30 m-4 p-4">
            <div class="clearfix">
                <h5 class="mb-30 text-primary">Update Faculty</h5>
                <hr>
            </div>
            <div class="wizard-content py-3">
                <form action="{{ route('officials.faculties.update', [ $role, $faculty ]) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <section>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:16px ; font-weight:700">Enter Faculty Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter Faculty Name" value="{{ $faculty->name }}" >
                                    @error('name')
                                        <small class="text-danger -mt-5"> {{ $message }} </small>
                                    @enderror
                                </div>
                                
                            </div>
                        </div>

                    <div class="row m-1 pb-2">
                        <h6>Choose a department</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @foreach ($departments as $department)
                                <div class="custom-control custom-checkbox mb-5">
                                    <input type="checkbox" {{ in_array($faculty->id, $department->faculties->pluck('id')->toArray()) ? 'checked' : '' }} class="custom-control-input" value="{{ $department->id }}" name="departments[]" id="faculty{{ $department->id }}">
                                    <label class="custom-control-label pl-3" for="faculty{{ $department->id }}">{{ $department->name }}</label>
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