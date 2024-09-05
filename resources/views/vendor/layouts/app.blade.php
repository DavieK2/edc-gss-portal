<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>Unical | Vendor</title>

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
                    <div class="form-group mb-0">
                        <i class="dw dw-search2 search-icon"></i>
                        <input type="text" class="form-control search-input" placeholder="Search Here">
                        <div class="dropdown">
                            <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                                <i class="ion-arrow-down-c"></i>
                            </a>
                      
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="header-right">
            <div class="dashboard-setting user-notification">
                <div class="dropdown">
                    <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                        <i class="dw dw-settings2"></i>
                    </a>
                </div>
            </div>
            <div class="user-info-dropdown">
                <div class="dropdown">
                    <a class="dropdown-toggle pt-3 pr-3" href="#">
                        <span class="user-name">{{ auth()->guard('vendor')->user()->fullname }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="left-side-bar">
        <div class="brand-logo">
            <a href="index.html">
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
                        <a href="{{ route('vendor.dashboard.index') }}" class="dropdown-toggle  no-arrow">
                            <span class="micon dw dw-house-1"></span><span class="mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="{{ route('vendor.token.index') }}" class="dropdown-toggle no-arrow">
                            <span class="micon icon-copy ti-wallet"></span><span class="mtext">Token</span>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="{{ route('vendor.student.registration.index') }}" class="dropdown-toggle  no-arrow">
                            <span class="micon dw dw-invoice"></span><span class="mtext">Registrations</span>
                        </a>
                    </li>

                    <li class="dropdown">
                        <a href="{{ route('vendors.students.index') }}" class="dropdown-toggle  no-arrow">
                            <span class="micon dw dw-conference"></span><span class="mtext">Students</span>
                        </a>
                    </li>

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
                            <li><a href="{{ route('vendor.profile.password') }}">Change Password</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <form action="{{ route('vendor.auth.logout') }}" id="logout" method="POST">@csrf</form>
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

    @yield('scripts')

</body>

</html>