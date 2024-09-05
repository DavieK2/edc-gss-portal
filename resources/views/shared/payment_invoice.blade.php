<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="invoice-wrap r_i">
            <div class="invoice-box">
                <div class="invoice-header">
                    <div class="logo">
                        <div class="r_i_logo_wrapper">
                            <img class="r_i_logo" src="{{ asset('assets/images/uglogo.png') }}">
                        </div>
                    </div>
                </div>
                <h4 class="mb-30 weight-800">{{ strtoupper($registration->scheme?->name) }} Registration</h4>
                @if ($registration->payment_status)
                    <div class="row p-3">
                        <div style="height: 150px; 
                                    width: 150px; 
                                    background-image:url({{ asset("images/".$registration->student?->profile_image) }}); 
                                    background-position: center; 
                                    background-size: cover; 
                                    border-radius: 16px">
                        </div>
                    </div>
                @endif
                <div class="row pb-30">
                    <div class="col-md-6">
                        <div class="col-12 p-0 mb-3">
                            <h6 class="mb-2">Faculty:</h6>
                            <p class="font-16 mb-5">{{ strtoupper($registration->student?->faculty->name ?? 'N/A') }}</p>
                        </div>
                        <div class="col-12 p-0">
                            <h6 class="mb-2">Department:</h6>
                            <p class="font-16 mb-5">{{ strtoupper($registration->student?->department->name) }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="col-12 p-0 mb-3">
                            <h6 class="mb-2">Student Payment Code:</h6>
                            <p class="font-16 mb-5">{{ strtoupper($registration->invoice_number) }}</p>
                        </div>
                        {{-- @if ($registration->pos_reference)
                            <div class="col-12 p-0 mb-3">
                                <h6 class="mb-2">POS Reference Code:</h6>
                                <p class="font-16 mb-5">{{ $registration->pos_reference }}</p>
                            </div>
                        @endif --}}
                        <div class="col-12 p-0 mb-3">
                            <h6 class="mb-2">Payment Status:</h6>
                            <p class="font-16 mb-5">{{ strtoupper($registration->payment_status ? 'paid' : 'unpaid') }}</p>
                        </div>

                        @if ($registration->payment_status)
                            <div class="col-12 p-0 mb-3">
                                <h6 class="mb-2">Payment Date:</h6>
                                <p class="font-16 mb-5">{{ $registration->bank_payment()?->PaymentDate ?? \Carbon\Carbon::parse($registration->payment_date)->toDateTimeString() }}</p>
                            </div>
                        @endif
                       
                    </div>
                </div>
                <div class="row pb-30">
                    <div class="col-md-6">
                        <h5 class="mb-15">Student details:</h5>
                        <p class="font-14 mb-5">Name: <strong class="weight-600">{{ $registration->student?->student_name }}</strong></p>
                        <p class="font-14 mb-5">Year: <strong class="weight-600">{{ $registration->level }}</strong></p>
                        <p class="font-14 mb-5">Session: <strong class="weight-600">{{ $registration->session }}</strong></p>
                        <p class="font-14 mb-5">Matric number/ Reg No: <strong class="weight-600">{{ $registration->student?->student_code }}</strong></p>
                        <p class="font-14 mb-5">Registration Date: <strong class="weight-600">{{ $registration->created_at }}</strong></p>
                    
                    </div>
                </div>
                <div class="invoice-desc">
                    <div class="invoice-desc-head clearfix">
                        <div class="invoice-sub weight-700" style="width: 50%">Course title </div>
                        <div class="invoice-rate weight-700">Fee</div>
                        <div class="invoice-hours weight-700">Item Code</div>
                    </div>
                    <div class="">
                        <ul class="mt-4">
                            @foreach ($registration->items['invoice_items'] as $item)
                                <li class="clearfix">
                                    <div class="invoice-sub" style="width: 50%">{{ $item['title'] }}</div>
                                    <div class="invoice-rate">&#8358; {{ number_format($item['fee'], 2) }}</div>
                                    <div class="invoice-hours">{{ $item['item_code'] }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if (isset($registration->items['other_charges']))
                        <h5 class="mt-5">Other Payments</h5>

                        <div class="invoice-desc-head clearfix mt-4 mb-10">
                            <div class="invoice-sub">Item</div>
                            <div class="invoice-rate">Fee</div>
                            <div class="invoice-hours">Item Code</div>
                        </div>
                        <div class="">
                            <ul>
                                @foreach ($registration->items['other_charges'] as $item)
                                    <li class="clearfix">
                                        <div class="invoice-sub">{{ $item['title'] }}</div>
                                        <div class="invoice-rate">&#8358; {{ number_format($item['fee'], 2) }}</div>
                                        <div class="invoice-rate">{{ $item['item_code'] }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    

                    @php
                        $subtotal = collect(array_merge($registration->items['invoice_items'], isset($registration->items['other_charges']) ? $registration->items['other_charges'] : []))->sum('fee');
                        $total = $subtotal;
                    @endphp

                    <div class="float-right pr-4 mt-5">
                        <ul>
                            <li class="clearfix">
                                <div style="font-weight: 600; font-size:15px; color:rgb(56, 56, 56)" class="h6">Subtotal: &#8358; {{ number_format($subtotal, 2) }}</div>
                                <div style="font-weight: 800; font-size:16px" class="">Total: {{ number_format($total, 2) }}</div>
                            </li>
                        </ul>
                    </div>
                    @if ($registration->payment_status)
                        <div class="invoice-desc-footer mt-5 pb-5 pt-5"></div>
                    @endif

                    @if (! $registration->payment_status)
                        <div class="invoice-desc-footer mt-5 pt-5" style="font-size: 12px">
                            <div class="clearfix">
                                <div class="container">
                                    <p class="text-dark">Payment can be made using any of the following methods</p>
                                </div>
                                <div class="container">
                                    <h5 class="h6">Bank payment</h5>
                                    <ol>
                                        <li class="clearfix">Visit any bank, collect and fill a deposit slip account name as <br>UNICAL {{ strtoupper($registration->scheme?->name) }} PAYMENT(Must use pay direct).</li>
                                        <li class="clearfix mt-2">Ensure you add important information such as payment code, amount, payment item.</li>
                                    </ol>
                                </div>
                                <div class="container mt-4 mb-4">
                                    <h6 class="h6">Online payment</h6>
                                    <ol>
                                        <li>Please log in to your student dashboard.</li>
                                        <li>Locate your registration and click on the invoice button.</li>
                                        <li>Locate the Make Payment button and click on it.</li>
                                        <li>Fill in your card details to make your payment.</li>
                                        <li><strong>Additional transaction fee of N{{ $registration->items['transaction_fee'] }} may apply.</strong></li>
                                    </ol>
                                </div>
                                {{-- @if ($registration->pos_reference)
                                    <div class="container mt-4 mb-4">
                                        <h6 class="h6">POS Terminal</h6>
                                        <ol>
                                            <li>Payment Reference: {{ $registration->pos_reference }} </li>
                                        </ol>
                                    </div>
                                @endif --}}
                                
                            </div>
                            
                        </div>
                    @endif
                </div>
            </div>
        </div>
           
        <div class="row text-center align-items-center w-100 mb-5 pb-5">
            <div class="col-md-6 d-print-none text-center pb-20 mt-5">
                <button class="d-print-none btn btn-primary" onclick="print()">Print invoice</button>
            </div>
            @can('make-payment')
                @if ( ! $registration->payment_status &&  $registration?->scheme->is_online_payment_enabled )
                    <div class="col-md-6 d-print-none text-center pb-20 mt-5">
                        <a href="{{ route('registration.payment', $registration->invoice_number ) }}" class="d-print-none btn btn-success">Pay Now</a>
                    </div>
                @endif
            @endcan

            @can('verify')
                @if (! $registration->is_verified)
                    <div class="col-md-6 d-print-none text-center pb-20 mt-5">
                        <a href="{{ route('officials.verifications.verify', [ $role, $registration->id ] ) }}" class="d-print-none btn btn-success">Verify</a>
                    </div>
                @endif
            @endcan
            @can('perform-super')
               
                <div class="col-md-6 d-print-none text-center pb-20 mt-5">
                    <a href="{{ route('officials.registrations.confirm.payment', [ $role, $registration] ) }}" class="d-print-none btn btn-success">Confirm Payment</a>
                </div>
             
            @endcan
           
        </div>  
    </div>
</div>