@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Update Verification Officer</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.verification.officers.update', [ $role, $officer]) }}" method="POST">
                @method('PATCH')
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" disabled class="form-control" value="{{ $officer->fullname }}" >
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" disabled class="form-control" value="{{ $officer->email }}" >
                            </div>
                        </div>
                    </div>

                    <div class="row m-1 pb-2">
                        <h6>Assign to a faculty</h6>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @foreach ($faculties as $key => $faculty)
                                <div class="custom-control custom-checkbox mb-5">
                                    <input type="checkbox" {{ in_array($faculty->id, $officer->faculties->pluck('id')->toArray()) ? 'checked' : '' }} class="custom-control-input" value="{{ $faculty->id }}" name="faculties[]" id="faculty{{ $faculty->id }}">
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