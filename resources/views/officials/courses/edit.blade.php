@extends('officials.layouts.app')
@section('content')
<div class="main-container">
    <div class="pd-20 card-box mb-30 m-4 p-4">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Update Course</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('officials.courses.update', [$role, $course, $scheme ]) }}" method="POST">
                @method('PATCH')
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Course Name</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter Course Name" value="{{ old('title') ?? $course->title }}" >
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
                                <input type="text" class="form-control" name="item_code" placeholder="Enter Course Code" value="{{ old('item_code') ?? $course->item_code }}" >
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
                                <input type="text" class="form-control" name="fee" placeholder="Enter Course Fee" value="{{ old('fee') ?? $course->fee }}" >
                                @error('fee')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ; font-weight:700">Enter Documentation Fee</label>
                                <input type="text" class="form-control" name="documentation_fee" placeholder="Enter Documentation Fee" value="{{ old('documentation_fee') ?? $course->documentation_fee }}" >
                                @error('documentation_fee')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if ( $course->account_id )
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label style="font-size:16px ; font-weight:700">Enter Course Account</label>
                                    <select class="custom-select" name="account_id">
                                        <option selected disabled>Select Account...</option>
                                        @foreach ($accounts as $account)
                                            <option {{ $course->account_id == $account->id ? 'selected' : '' }} value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                @error('account_id')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="row mt-5">
                            <p style="font-size:18px ; font-weight:700" class="col-md-6 text-primary">Accounts</p>
                        </div>

                        <ul id="accounts">
                            @foreach ($course->account_ids as $index => $id)
                                @if ($index === 0)
                                    <li class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="split_fee[]" placeholder="Split Amount" value="{{ $id['fee'] ?? old('fee') }}" >
                                        </div>
                
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="custom-select" name="split_account[]">
                                                    <option selected disabled>Select Account...</option>
                                                    @foreach ( $accounts as $account )
                                                        <option {{ $id['account_id'] === $account->account_id ? 'selected' : '' }} value="{{ $account->id }}">{{ $account->account_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </li>   
                                @else
                                    <li class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="split_fee[]" placeholder="Split Amount" value="{{ $id['fee'] ?? old('fee') }}" >
                                        </div>
                
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select class="custom-select" name="split_account[]">
                                                    <option selected disabled>Select Account...</option>
                                                    @foreach ( $accounts as $account )
                                                        <option {{ $id['account_id'] === $account->account_id ? 'selected' : '' }} value="{{ $account->id }}">{{ $account->account_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 pt-2">
                                            <a href="#" class="text-danger" onclick="delAccount(this)">Delete</a>
                                        </div>
                                    </li>   
                                @endif
                            @endforeach
                        </ul>

                        <div class="row text-primary">
                            <div class="col-md-6">
                            <a href="#" class="text-primary" onclick="addAccount()">Add Account</a>
                            </div>
                        </div>
                    @endif
                    

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
                                                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
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