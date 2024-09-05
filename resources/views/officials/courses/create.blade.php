@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Create Course</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            
            <form action="{{ route('officials.courses.store', [$role, $scheme ]) }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Course Name</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter Course Name" value="{{ old('title') }}" >
                                @error('title')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Course Code</label>
                                <input type="text" class="form-control" name="item_code" placeholder="Enter Course Code" value="{{ old('item_code') }}" >
                                @error('item_code')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Course Fee</label>
                                <input type="number" class="form-control" name="fee" placeholder="Enter Course Fee" value="{{ old('fee')}}" >
                                @error('fee')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <p style="font-size:18px ; font-weight:700" class="col-md-6 text-primary">Accounts</p>
                    </div>

                    <ul id="accounts">
                        <li class="row">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="split_fee[]" placeholder="Split Amount" value="{{ old('fee') }}" >
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="custom-select" name="split_account[]">
                                        <option selected disabled>Select Account...</option>
                                        @foreach ( $accounts as $account )
                                            <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </li>   
                    </ul>

                    <div class="row text-primary">
                        <div class="col-md-6">
                           <a href="#" class="text-primary" onclick="addAccount()">Add Account</a>
                        </div>
                    </div>
                    
                    <div class="input-group mb-0 py-3">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>

                </section>
            </form>
        </div>
    </div>
</div>

<script>

    function addAccount(){

        let accounts = document.getElementById('accounts');
        let account = document.createElement('li');
        account.setAttribute('class', 'row');

        account.innerHTML = `   <div class="col-md-3">
                                    <input type="text" class="form-control" name="split_fee[]" placeholder="Split Amount" value="{{ old('fee') }}" >
                                </div>
    
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="custom-select" name="split_account[]">
                                            <option selected disabled>Select Account...</option>
                                            @foreach ( $accounts as $account )
                                                <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 pt-2">
                                    <a href="#" class="text-danger" onclick="delAccount(this)">Delete</a>
                                </div>
                            `

       

        accounts.appendChild(account);
    }

    function delAccount(account)
    {
        account.parentElement.parentElement.remove();
    }

</script>
@endsection