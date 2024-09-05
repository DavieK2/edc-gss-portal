<!DOCTYPE html>
<html>
    <head>
        <!-- Basic Page Info -->
        <meta charset="utf-8">
        <title>Unical | Officials</title>

        <!-- Site favicon -->
        {{-- <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/img/custom/favicon.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/img/custom/favicon.png"> --}}

        <!-- Mobile Specific Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/styles/core.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/styles/icon-font.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/src/plugins/jvectormap/jquery-jvectormap-2.0.3.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/styles/style.css') }}">
        @yield('styles')
    </head>

    <body>
        <div class="pre-loader">
            <div class="pre-loader-box">
                <div class="loader-logo"><img src="{{ asset('assets/images/logo.png')}}" alt="" width="250"></div>
                <div class='loader-progress' id="progress_div">
                    <div class='bar' id='bar1'></div>
                </div>
                <div class='percent' id='percent1'>0%</div>
                <div class="loading-text">
                    Loading...
                </div>
            </div>
        </div>
        
            
        <div class="header d-print-none">
            <div class="header-left">
                <div class="menu-icon dw dw-menu"></div>
                <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
            <div class="header-search">
                    <form>
                        <div class="row align-items-center" id="row-header">
                            <div class="col-md-2">
                                <span class="mr-2 text-primary"><strong>SESSION:</strong></span>

                            </div>

                            <div class="col-md-4">
                                <div class="form-group pt-3">
                                    <select class="custom-select text-uppercase" name="session"
                                            onchange="changeSession(this)">
                                            @foreach (\App\Models\Session::all() as $session)
                                                <option {{  (is_null(session('session')) && \App\Models\Session::activeSession() == $session->session) || (session('session') ==  $session->session) ? 'selected' : '' }}  value="{{ $session->id }}">{{ $session->session }}</option>
                                            @endforeach
                                        </select>
                            </div>
                            </div>


                            </div>
                        </div>
                    </form>
                </div>

            <div class="header-right">

                <div class="user-info-dropdown">
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <span class="user-icon"></span>
                            <span class="user-name">{{ auth()->guard($role)->user()->fullname }}</span>
                        </a>

                    </div>
                </div>

            </div>
        </div>


        <div class="left-side-bar">
            <div class="brand-logo">
                <a href="#">
                    <img src="{{ asset('assets/images/logo.png')}}" alt="" class="dark-logo">
                    <img src="{{ asset('assets/images/logo-light.png')}}" width="160" height="80" style="margin-left: -10px" alt="" class="light-logo">
                </a>
                <div class="close-sidebar" data-toggle="left-sidebar-close">
                    <i class="ion-close-round"></i>
                </div>
            </div>
            <div class="menu-block customscroll">
                <div class="sidebar-menu">
                    <ul id="accordion-menu">
                        <li class="dropdown mt-5">
                            <a href="{{ route('officials.dashboard.index', $role) }}" class="dropdown-toggle  no-arrow">
                                <span class="micon dw dw-house-1"></span><span class="mtext">Dashboard</span>
                            </a>
                        </li>

                        @can('view-token-transactions')
                            <li>
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-ticket-1"></span><span class="mtext">Tokens</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('officials.tokens.purchases', $role) }}">Purchases</a></li>
                                    <li><a href="{{ route('officials.tokens.registrations', $role) }}">Registrations</a></li>
                                </ul>
                            </li>
                        @endcan

                        @can('view-registrations')
                            <li>
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-clipboard"></span><span class="mtext">Registrations</span>
                                </a>
                                <ul class="submenu">
                                    @foreach (auth()->guard($role)->user()->schemes as $scheme)
                                        <li><a href="{{ route('officials.registrations.index', [ $role, $scheme->name ]) }}">{{ strtoupper($scheme->name) }} Registrations</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @endcan
                    
                        @can('view-verifications')
                            <li class="dropdown">
                                <a href="{{ route('officials.verifications.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-check"></span><span class="mtext">Verifications</span>
                                </a>
                            </li>
                        @endcan

                        @can('view-payments')
                            <li>
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-money"></span><span class="mtext">Payments</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('officials.payments.bank', $role) }}">Bank Payments</a></li>
                                    <li><a href="{{ route('officials.payments.online', $role) }}">Online Payments</a></li>
                                </ul>
                            </li>
                        @endcan
                        

                        @can('view-faculties-and-departments')
                            <li class="dropdown">
                                <a href="{{ route('officials.faculties.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-certificate"></span><span class="mtext">Faculties</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('officials.departments.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-group"></span><span class="mtext">Departments</span>
                                </a>
                            </li>
                        @endcan
                        
                        @can('view-vendors')
                            <li class="dropdown">
                                <a href="{{ route('officials.vendors.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-user"></span><span class="mtext">Vendors</span>
                                </a>
                            </li>
                        @endcan
                        
                        @can('view-verification-officers')
                            <li class="dropdown">
                                <a href="{{ route('officials.verification.officers.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-deal"></span><span class="mtext">Verification Officers</span>
                                </a>
                            </li>
                        @endcan

                        @can('view-ventures')
                            <li class="dropdown">
                                <a href="{{ route('officials.ventures.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-profits"></span><span class="mtext">EDC Ventures</span>
                                </a>
                            </li>
                        @endcan

                        @can('view-sessions')
                            <li class="dropdown">
                                <a href="{{ route('officials.sessions.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-wall-clock1"></span><span class="mtext">Sessions</span>
                                </a>
                            </li>
                            
                            <li class="dropdown">
                                <a href="{{ route('officials.students.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-conference"></span><span class="mtext">Students</span>
                                </a>
                            </li>
                        @endcan

                        @can('perform-super')

                            <li class="dropdown">
                                <a href="{{ route('officials.transactions.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-credit-card"></span><span class="mtext">Transactions</span>
                                </a>
                            </li>

                            <li>
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-sheet"></span><span class="mtext">Courses</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('officials.courses.index', [$role, 'EDC']) }}">EDC Courses</a></li>
                                    <li><a href="{{ route('officials.courses.index', [$role, 'GSS']) }}">GSS Courses</a></li>
                                </ul>
                            </li>
                       
                            <li>
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-settings2"></span><span class="mtext">Scheme Settings</span>
                                </a>
                                <ul class="submenu">
                                    <li><a href="{{ route('officials.schemes.settings.index.online', [$role, 'EDC']) }}">EDC Online Settings</a></li>
                                    <li><a href="{{ route('officials.schemes.settings.index.bank', [$role, 'EDC']) }}">EDC Bank Settings</a></li>
                                    <li><a href="{{ route('officials.schemes.settings.index.online', [$role, 'GSS']) }}">GSS Online Settings</a></li>
                                    <li><a href="{{ route('officials.schemes.settings.index.bank', [$role, 'GSS']) }}">GSS Bank Settings</a></li>
                                </ul>
                            </li>
                       
                            <li class="dropdown">
                                <a href="{{ route('officials.accounts.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-user-11"></span><span class="mtext">Accounts</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('officials.users.index', $role) }}" class="dropdown-toggle  no-arrow">
                                    <span class="micon dw dw-user"></span><span class="mtext">Users</span>
                                </a>
                            </li>
                        @endcan
                        
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>

                        <li>
                            <div class="sidebar-small-cap">Settings</div>
                        </li>

                        <li>
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon dw dw-edit-2"></span><span class="mtext">Profile</span>
                            </a>
                            <ul class="submenu">
                                <li><a href="{{ route('officials.profile.password', $role) }}">Change Password</a></li>
                            </ul>
                        </li>
                        
                        <li>
                            <form action="{{ route('officials.auth.logout', $role) }}" id="logout" method="POST">@csrf</form>
                            <a href="" onclick="event.preventDefault(); document.getElementById('logout').submit()" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-logout "></span>
                                <span class="mtext">Logout </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mobile-menu-overlay d-print-none"></div>

        @include('sweetalert::alert')
        @yield('content')
        
        @yield('script')
    
        <!-- js -->
        <script src="{{ asset('assets/vendors/scripts/core.js') }}"></script>
        <script src="{{ asset('assets/vendors/scripts/script.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/scripts/process.js') }}"></script>
        <script src="{{ asset('assets/vendors/scripts/layout-settings.js') }}"></script>
        <script src="{{ asset('assets/vendors/scripts/layout-settings.js') }}"></script>
        <script src="{{ asset('assets/src/plugins/jQuery-Knob-master/jquery.knob.min.js') }}"></script>
        <script src="{{ asset('assets/src/plugins/highcharts-6.0.7/code/highcharts.js') }}"></script>
        <script src="{{ asset('assets/src/plugins/highcharts-6.0.7/code/highcharts-more.js') }}"></script>
        <script src="{{ asset('assets/src/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js') }}"></script>
        <script src="{{ asset('assets/src/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

        <script>
            async function changeSession(params) {
                await fetch( "{{ url($role.'/session') }}/" + params.value ); 
                location.reload();
            }
        </script>

        @yield('scripts')
    </body>
</html>