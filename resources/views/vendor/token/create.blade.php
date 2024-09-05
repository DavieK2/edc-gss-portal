@extends('vendor.layouts.app')
@section('content')
<div class="main-container">
    <div class="page-header m-4">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Buy Tokens</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('vendor.token.index') }}">Tokens</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buy Token</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="pd-20 m-4 p-4 card-box mb-30">
        <div class="clearfix">
            <h5 class="mb-30 text-primary">Purchase Token</h5>
            <hr>
        </div>
        <div class="wizard-content py-3">
            <form action="{{ route('vendor.token.store') }}" method="POST">
                @csrf
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:16px ;">Number of token to purchase</label>
                                <input type="number" class="form-control" name="number_of_tokens" placeholder="Enter Number of tokens" value="{{ old('number_of_tokens') }}" >
                                @error('amount')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            @if (auth()->guard('vendor')->user()->schemes()->count() > 1)
                                <select class="custom-select" name="scheme">
                                    <option selected disabled>Purchase Token For...</option>
                                    @foreach (auth()->guard('vendor')->user()->schemes as $scheme)
                                        <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                                    @endforeach
                                </select>
                                @error('scheme')
                                    <small class="text-danger -mt-5"> {{ $message }} </small>
                                @enderror
                            @endif
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
@endsection