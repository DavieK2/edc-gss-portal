@extends('officials.layouts.app')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="title">
                                <h4>{{ $page }}</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-6 col-sm-12 text-right">
                            <div class="dropdown">
                                <a class="btn btn-primary" data-toggle="modal" data-target="#Medium-modal" type="button" href="#">
                                    Verify Transaction
                                </a>
                                <div class="modal fade" id="Medium-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myLargeModalLabel">Verify Transaction</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>

                                            <form action="#" method="post" class="text-left" id="verify-transaction">
                                                <div class="modal-body">
                                                    <div class="alert text-left" id="alert" role="alert" style="display: none"></div>
                                                    <section>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label style="font-size:16px ; font-weight:700">Enter Payment Reference</label>
                                                                    <input type="text" class="form-control" id="payment_ref" name="payment_ref" placeholder="Enter Payment Reference" required >
                                                                </div>
                                                            </div>
                                                        </div>
                                    
                                                        <div class="row mt-1">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label style="font-size:16px ; font-weight:700">Select Scheme</label>
                                                                    <select class="custom-select" id="scheme" name="scheme">
                                                                        @foreach (\App\Models\Scheme::get() as $scheme)
                                                                            <option value="{{ $scheme->name }}">{{ $scheme->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mt-1">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label style="font-size:16px ; font-weight:700">Select Payment Type</label>
                                                                    <select class="custom-select" id="type" name="type">
                                                                        <option value="registration">Registration</option>
                                                                        <option value="token">Token</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" id="verify-button">Verify</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <x-datatable page="{{ $page }}" :headings="$tableHeadings" name="{{ $name }}" role="{{ $role }}" ajaxUrl="{{ $ajaxUrl }}" searchLabel="Enter Payment Reference" /> 
            </div>

        </div>
    </div>

    <script>

        let form = document.getElementById('verify-transaction');

        console.log(form);

        form.addEventListener('submit', async (e) => {

            e.preventDefault();

            let button = document.getElementById('verify-button');
            let alert = document.getElementById('alert');

            button.setAttribute('disabled', true);

            let payment_ref = document.getElementById('payment_ref').value;
            let scheme = document.getElementById('scheme').value;
            let type = document.getElementById('type').value;

            let response =  await fetch("{{ route('officials.transaction.verify', [$role]) }}", { 
                                        method: "POST", 
                                        body: JSON.stringify({payment_ref,scheme,type }),
                                        headers: {
                                            'X-CSRF-TOKEN' : "{{ csrf_token() }}",
                                            "Content-Type": "application/json",
                                        }
                                    });
            response = await response.json();

            if(response.status){
                alert.classList.add('alert-success');
            }else{
                alert.classList.add('alert-danger');
            }
            
            alert.textContent = response.message;
            alert.setAttribute('style', 'display:block');
            
            button.removeAttribute('disabled');

            setTimeout(() => {
                alert.setAttribute('style', 'display:none');
            }, 10000);
            
        })
    </script>
@endsection