@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Edit Venture</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.ventures.update_fee', [ $role, $venture ]) }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Enter Fee Amount</label>
                                <input required type="number" class="form-control" name="fee" value="{{ old('fee') ?? $venture?->fee }}" >
                                @error('fee')
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