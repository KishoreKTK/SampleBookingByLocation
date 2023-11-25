<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mood</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js does not work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" href="{{ asset('img/logo/favicon.png') }} ">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <!-- waves.css') }}  -->
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/bower_components/bootstrap/css/bootstrap.min.css') }} ">
    <!-- waves.css') }}  -->
    <link rel="stylesheet" href="{{ asset('assets/files/assets/pages/waves/css/waves.min.css') }} " type="text/css" media="all">

    <!-- animation nifty modal window effects css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/assets/css/component.css') }} ">
     <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/assets/icon/icofont/css/icofont.css') }} ">
  <!-- slick css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/bower_components/slick/slick.css') }} ">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/w/slick/slick-theme.css') }} ">
    <!-- themify icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/assets/icon/themify-icons/themify-icons.css') }} ">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <!-- scrollbar.css  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/assets/css/jquery.mCustomScrollbar.css') }} ">
    <!-- radial chart.css -->
    <link rel="stylesheet" href="{{ asset('assets/files/assets/pages/chart/radial/css/radial.css') }} " type="text/css" media="all">
     <!-- Calender css -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/bower_components/fullcalendar/css/fullcalendar.css') }} ">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/bower_components/fullcalendar/css/fullcalendar.print.css') }} " media='print'> -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css ">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css " media='print'>
    <!-- Style.css  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/assets/css/style.css') }} ">

    @yield('pagestyle')
    <!-- Date-time picker css -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/assets/pages/advance-elements/css/bootstrap-datetimepicker.css') }} "> -->
      <!-- Date-range picker css  -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/bower_components/bootstrap-daterangepicker/css/daterangepicker.css') }} " /> -->
      <!-- Date-Dropper css -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/files/bower_components/datedropper/css/datedropper.css') }} " /> -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-migrate-3.1.0.min.js"></script>
    <link href="{{ asset('assets/files/summernote/summernote.css') }} " rel="stylesheet">

    <script type="text/javascript" src="{{ asset('assets/files/assets/pages/form-validation/validate.js') }} "></script>
    <!-- Custom js -->
    <!-- <script type="text/javascript" src="{{ asset('assets/files/assets/pages/form-validation/form-validation.js') }} "></script> -->
    <link href="{{ asset('assets/files/bower_components/gijgo-combined/css/gijgo.min.css') }} " rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/files/bower_components/flatpickr/dist/flatpickr.min.css') }} ">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }} ">

    @yield('pagecss')
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="loader-track">

        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">
                    <div class="navbar-logo">
                        <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
                            <i class="ti-menu"></i>
                        </a>
                    </div>
                    <div class="navbar-container container-fluid">
                        <ul class="nav-right">
                            <li class="user-profile header-notification">
                                <a href="#!" class="waves-effect waves-light">
                                  <img src="{{ asset('img/logo/no-profile-admin.jpg') }} " class="img-radius" alt="User-Profile-Image">
                                  <span>{{Auth::user()->name}}</span>
                                  <i class="ti-angle-down"></i>
                                </a>
                                <ul class="show-notification profile-notification">
                                    <li class="waves-effect waves-light">
                                        <a href="{{env('ADMIN_URL')}}/change_password">
                                          <i class="ti-settings"></i> Change Password
                                        </a>
                                    </li>
                                    <li class="waves-effect waves-light">
                                        <a href="{{env('ADMIN_URL')}}/logout">
                                          <i class="ti-layout-sidebar-left"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Sidebar chat start -->
            <!-- Sidebar inner chat end-->
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    @include("layouts.sidemenu")
                    <div class="container-fluid">
                        @yield('content')
                    </div> <!-- .container-fluid -->
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Section Ends -->
    @yield('pagescript')

    <!-- Required Jquery -->

    <script type="text/javascript" src="{{ asset('assets/files/bower_components/jquery-ui/js/jquery-ui.min.js') }}  "></script>

    <script type="text/javascript" src="{{ asset('assets/files/bower_components/popper.js/js/popper.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('assets/files/bower_components/bootstrap/js/bootstrap.min.js') }}  "></script>
    <script src="{{ asset('assets/files/summernote/summernote.js') }} "></script>

    <script type="text/javascript" src="{{ asset('assets/files/assets/pages/widget/excanvas.js') }}  "></script>
            <!-- calender js -->
            <script type="text/javascript" src="{{ asset('assets/files/bower_components/moment/js/moment.min.js') }} "></script>
    <!-- waves js -->
    <script src="{{ asset('assets/files/assets/pages/waves/js/waves.min.js') }} "></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('assets/files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js') }}  "></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('assets/files/bower_components/modernizr/js/modernizr.js') }}  "></script>


    <!-- jquery file upload js -->
    <script src="{{ asset('assets/files/assets/pages/jquery.filer/js/jquery.filer.min.js') }} "></script>
    <script src="{{ asset('assets/files/assets/pages/filer/custom-filer.js') }} " type="text/javascript"></script>
    <script src="{{ asset('assets/files/assets/pages/filer/jquery.fileuploads.init.js') }} " type="text/javascript"></script>
    <!-- Model animation js -->
    <script src="{{ asset('assets/files/assets/js/classie.js') }} "></script>
    <script src="{{ asset('assets/files/assets/js/modalEffects.js') }} "></script>
    <!-- Google map js -->
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="{{ asset('assets/files/assets/pages/google-maps/gmaps.js') }} "></script>
    <script type="text/javascript" src="{{ asset('assets/files/bower_components/slick/slick.min.js') }} "></script>
    <!-- product detail js -->
    <script type="text/javascript" src="{{ asset('assets/files/assets/pages/product-detail/product-detail.js') }} "></script>

    <!-- menu js -->
    <script src="{{ asset('assets/files/assets/js/pcoded.min.js') }} "></script>
    <script src="{{ asset('assets/files/assets/js/vertical/vertical-layout.min.js') }}  "></script>

    <!-- datepicker -->
     <!-- <script type="text/javascript" src="{{ asset('assets/files/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('assets/files/assets/pages/advance-elements/bootstrap-datetimepicker.min.js') }} "></script> -->
    <!-- Date-range picker js -->
    <!-- <script type="text/javascript" src="{{ asset('assets/files/bower_components/bootstrap-daterangepicker/js/daterangepicker.js') }} "></script> -->
     <!-- product list js -->
    <!-- Date-dropper js -->
    <!-- <script type="text/javascript" src="{{ asset('assets/files/bower_components/datedropper/js/datedropper.min.js') }} "></script> -->
      <!-- <script type="text/javascript" src="{{ asset('assets/files/assets/pages/advance-elements/custom-picker.js') }} "></script> -->


    <!-- <script type="text/javascript" src="{{ asset('assets/files/bower_components/fullcalendar/js/fullcalendar.min.js') }} "></script> -->
    <!-- custom js -->
    <!-- <script type="text/javascript" src="{{ asset('assets/files/assets/pages/dashboard/custom-dashboard.js') }} "></script> -->
     <!-- Custom js -->
    <!-- <script type="text/javascript" src="{{ asset('assets/files/assets/pages/full-calender/calendar.js') }} "></script> -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js">
    </script>
    {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script> --}}
    <script type="text/javascript"  src="{{ asset('assets/files/assets/js/jquery.mCustomScrollbar.concat.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('assets/files/assets/js/jquery.mousewheel.min.js') }} "></script>

    <script src="{{ asset('assets/files/bower_components/gijgo-combined/js/gijgo.min.js') }} " type="text/javascript"></script>
    <script src="{{ asset('assets/files/bower_components/flatpickr/dist/flatpickr.js') }} "></script>
    <script type="text/javascript" src="{{ asset('assets/files/assets/js/script.js') }}  "></script>


</body>

</html>
