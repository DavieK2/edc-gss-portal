@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Edit Venture</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.ventures.update', [ $role, $ventureId ]) }}" method="POST">
                @method('PATCH')
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Max. Registrations</label>
                                <input type="number" class="form-control" name="max_registrations" value="{{ old('max_registrations') ?? $session_venture?->max_registrations }}" >
                                @error('max_registrations')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Select Registration Type</label>
                                <select class="custom-select" name="registration_type" id="faculty">
                                    <option selected disabled>Select Registration Type...</option>
                                    <option {{ $session_venture?->registration_type == 'supplementary' ? 'selected' : '' }} value="supplementary">Supplementary</option>
                                    <option {{ $session_venture?->registration_type == 'normal' ? 'selected' : '' }} value="normal">Normal</option>
                                </select>
                                @error('registration_type')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
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