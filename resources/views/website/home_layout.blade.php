<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Appart" content="Appart Onepage or Multiconcept template ">
    <title>Mood</title>
    <!-- favicon-->
    <link rel="shortcut icon" href="{{ asset('webassets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('webassets/images/favicon.png') }}">
    <!-- plugins -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/owl-carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/owl-carousel/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/themify-icon/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/slick/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/swipper/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('webassets/vendor/intl-tel-input-master/build/css/intlTelInput.min.css') }}">

    <!--main css file-->
    <link href="{{ asset('webassets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('webassets/css/responsive.css') }}" rel="stylesheet">
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="75">
    <!--  navbar -->
    <nav id="fixed-top" class="navbar navbar-toggleable-sm transparent-nav navbar-expand-lg menu_two">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}/">
                <img src="{{ asset('webassets/images/mood-icon.svg') }}" alt="logo">
                <img src="{{ asset('webassets/images/mood-logo.svg') }}" alt="logo">
            </a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}/#home">Home</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}/joinmood#service-provider">Join
                            Mood</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#contact">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!--end navigation-->

    @yield('home_page_content')


    {{-- Doanload APP Link --}}
    <section class="call-action-area call-action-area-five wow fadeInUp" id="download">
        <div class="video-bg">
        </div>
        <div class="container">
            <div class="row call-action text-md-left text-center">
                <div class="col-md-7 col-12 flex-fill mb-md-5 mb-0">
                    <div class="call-text">
                        <div class="title-four title-six d-md-block d-none">
                            <h2>Download Now!</h2>
                            <div class="br"></div>

                        </div>
                        <div class="title-four title-six text-center d-block d-md-none">
                            <h2>Download Now!</h2>
                            <div class="br"></div>

                        </div>
                        <p>Mood is a on-demand beauty and wellness home-service business. We are female-founded and
                            UAE-born.
                            Mood aggregates the best home-service businesses to ensure only top quality experiences to
                            you. Book
                            anywhere, anytime. </p>

                        <a href="#" class="app-btn2 active">

                            <img src="{{ asset('webassets/images/apple-black.png') }}" alt="">
                            <img src="{{ asset('webassets/images/apple-icon.png') }}" alt="">
                        </a>
                        <a href="#" class="app-btn2">
                            <img src="{{ asset('webassets/images/google-black.png') }}" alt="">
                            <img src="{{ asset('webassets/images/google-icon.png') }}" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-sm-5 align-self-end flex-fill">
                    <div class="call-mobile-img wow fadeInUp">
                        <img class="img-fluid" src="{{ asset('webassets/images/image_double_iphone.png') }}" alt="">

                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- Contact Us Footer --}}
    <footer class="footer-area " id="contact">
        <div class="footer-top">
            <div class="container">
                <div class="row footer_sidebar">
                    <div class="widget widget1 about_us_widget col-12 col-sm-6 col-md-5 wow fadeIn" data-wow-delay="0ms"
                        data-wow-duration="1500ms" data-wow-offset="0">
                        {{-- <a href="index.html" class="logo">
                            <img src="../images/mood-icon.svg" alt="f-logo">
                        </a> --}}
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ asset('webassets/images/mood-icon.svg') }}" alt="f-logo">
                        </a>
                        <p class="pr-lg-4">Mood is a on-demand beauty and wellness home-service business. We are
                            female-founded and UAE-born. Mood aggregates the best hom you. Book anywhere, anytime</p>
                        <ul class="nav social_icon row m0">
                            <li><a href="https://www.facebook.com/moodapp.ae" target="_blank"><i
                                        class="fa fa-facebook"></i></a></li>
                            <li><a href="https://www.instagram.com/moodapp.ae/" target="_blank"><i
                                        class="fa fa-instagram"></i></a></li>
                            <li><a href="https://www.linkedin.com/company/mood-application/" target="_blank"><i
                                        class="fa fa-linkedin"></i></a></li>
                        </ul>
                    </div>
                    <div class="widget widget2 widget_contact col-12 col-sm-6 col-md-4 wow fadeIn"
                        data-wow-delay="100ms" data-wow-duration="1500ms">
                        <h4 class="widget_title">Contact Info</h4>
                        <div class="widget_inner">
                            <ul>
                                <li>
                                    <div class="fleft contact_no"> <a href="{{ url('privacy-policy') }}">Privacy
                                            Policy</a></div>
                                </li>
                                <li>
                                    <div class="fleft contact_no"><a href="{{ url('terms-and-condtions') }}">Terms &
                                            Conditions</a></div>
                                </li>
                                <li>
                                    <div class="fleft contact_no"><a href="{{ url('/') }}#contact">Contact Us</a></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="widget widget2 widget_contact col-12 col-sm-6 col-md-3 wow fadeIn"
                        data-wow-delay="100ms" data-wow-duration="1500ms">
                        <h4 class="widget_title">Contact Info</h4>
                        <div class="widget_inner row m0">
                            <ul>
                                <li>
                                    <i class="ti-location-pin"></i>
                                    <div class="location_address fleft">
                                        Dubai, UAE
                                    </div>
                                </li>
                                <li>
                                    <i class="ti-mobile"></i>
                                    <div class="fleft contact_no">
                                        <a href="tel:+971043311170">+971043311170</a>
                                    </div>
                                </li>
                                <li>
                                    <i class="ti-email"></i>
                                    <div class="fleft contact_mail">
                                        <a href="mailto:info@moodapp.ae">info@moodapp.ae</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m0 footer_bottom">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        © 2021 All Right Reserved
                        | <a href="{{ url('/') }}">Mood</a>. All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- End of the footer -->



    <!-- jQuery plugins-->
    <!-- jQuery plugins-->
    <script src="{{ asset('webassets/js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('webassets/vendor/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('webassets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('webassets/js/parallax.js') }}"></script>
    <script src="{{ asset('webassets/js/parallax-scroll.js') }}"></script>
    <script src="{{ asset('webassets/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('webassets/js/modernizr-custom.js') }}"></script>
    <script src="{{ asset('webassets/js/smooth-scroll.min.js') }}"></script>
    <script src="{{ asset('webassets/vendor/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('webassets/js/jquery.sticky.js') }}"></script>
    <script src="{{ asset('webassets/vendor/sckroller/sckroller.js') }}"></script>
    <script src="{{ asset('webassets/vendor/video-player/jquery.mb.YTPlayer.js') }}"></script>
    <script src="{{ asset('webassets/vendor/slick/slick.js') }}"></script>
    <script src="{{ asset('webassets/vendor/swipper/swiper.min.js') }}"></script>
    <script src="{{ asset('webassets/js/plugins.js') }}"></script>
    <script src="{{ asset('webassets/js/mchimpsubs.js') }}"></script>
    <script src="{{ asset('webassets/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('webassets/js/custom.js') }}"></script>
    <script src="{{ asset('webassets/js/wow.js') }}"></script>
    <script src="{{ asset('webassets/vendor/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
    <script>
    new WOW().init();
    // ================================================

    $(".category-checkbox input:checkbox").click(function() {
        var lim = $("input:checkbox:checked").length >= 2;
        $(".category-checkbox input:checkbox").not(":checked").attr("disabled", lim);
    });
    // =====================================
    // jQuery
    $("#telephone").intlTelInput({
        preferredCountries: ["ae"],
        separateDialCode: true,
    });
    </script>
</body>

</html>