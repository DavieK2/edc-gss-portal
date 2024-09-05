@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Update Charge</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.schemes.charges.update', [$role, $scheme, $key, $channel ]) }}" method="POST">
                @method('PATCH')
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Charge Name</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter Charge Name" value="{{ old('title') ?? $charge['title'] ?? '' }}" >
                                @error('title')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Item Code</label>
                                <input type="text" class="form-control" name="item_code" placeholder="Enter Charge Code" value="{{ old('item_code') ?? $charge['item_code'] ?? '' }}" >
                                @error('item_code')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Charge Amount</label>
                                <input type="text" class="form-control" name="fee" placeholder="Enter Charge Fee" value="{{ old('fee') ?? $charge['fee'] ?? '' }}" >
                                @error('fee')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Select Charge Account</label>
                                <select class="custom-select" name="account_id">
                                    <option selected disabled>Select Account...</option>
                                    @foreach ($accounts as $account)
                                        <option {{ $charge['account_id'] ?? '' == $account->account_id ? 'selected' : '' }} value="{{ $account->account_id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @error('account_id')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Select Charge Type</label>
                                <select class="custom-select" name="type">
                                    <option selected disabled>Select Charge Type...</option>
                                    <option {{ $type == 'course' ? 'selected' : '' }} value="course">Course</option>
                                    <option {{ $type == 'token' ? 'selected' : '' }} value="token">Token</option>
                                </select>
                            </div>
                            
                            @error('type')
                                <small class="text-danger -mt-5"> {{ $message }} </small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-0 py-3">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                        <div class="col-md-6 float-right">
                            <a class="text-danger" onclick="event.preventDefault(); document.getElementById('delete').submit()" href="#">Delete Charge</a>
                        </div>
                    </div>
                </section>
            </form>

            <form method="POST" id="delete" action="{{ route('officials.schemes.charges.delete', [$role, $scheme, $type, $key, $channel ]) }}"> @csrf @method('DELETE')</form>
        </div>
    </div>
</div>
@endsection