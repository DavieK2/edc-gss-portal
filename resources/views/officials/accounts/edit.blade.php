@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Update Account</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.accounts.update', [$role,$account]) }}" method="POST">
                @method('PATCH')
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Account Name</label>
                                <input type="text" class="form-control" name="account_name" placeholder="Enter Account Name" value="{{ old('account_name') ?? $account->account_name }}" >
                                @error('account_name')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Account No.</label>
                                <input type="text" class="form-control" name="account_number" placeholder="Enter Account No." value="{{ old('account_number') ?? $account->account_number }}" >
                                @error('account_number')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" placeholder="Enter Bank Name" value="{{ old('fee') ?? $account->bank_name }}" >
                                @error('bank_name')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Account ID</label>
                                <input type="text" class="form-control" name="account_id" placeholder="Enter Account ID" value="{{ old('fee') ?? $account->account_id }}" >
                                @error('account_id')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group mb-0 py-3">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                        <div class="col-md-6 float-right">
                            <a class="text-danger" onclick="event.preventDefault(); document.getElementById('delete').submit()" href="#">Delete Account</a>
                        </div>
                    </div>
                </section>
            </form>

            <form method="POST" id="delete" action="{{ route('officials.accounts.delete', [ $role, $account ]) }}"> @csrf @method('DELETE')</form>
        </div>
    </div>
</div>
@endsection