<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Appart" content="Appart Onepage or Multiconcept template ">
    <title>Mood</title>
    <!-- favicon-->
    <link rel="shortcut icon" href="{{ asset('public/webassets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('public/webassets/images/favicon.png') }}">
    <!-- plugins -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/owl-carousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/owl-carousel/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/themify-icon/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/slick/slick-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/swipper/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/webassets/vendor/intl-tel-input-master/build/css/intlTelInput.min.css') }}">

    <!--main css file-->
    <link href="{{ asset('public/webassets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('public/webassets/css/responsive.css') }}" rel="stylesheet">
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="75">
    <!--  navbar -->
    <nav id="fixed-top" class="navbar navbar-toggleable-sm transparent-nav navbar-expand-lg menu_two">
        <div class="container">
            <a class="navbar-brand" href="http://moodapp.ae/">
                <img src="{{ asset('public/webassets/images/mood-icon.svg') }}" alt="logo">
                <img src="{{ asset('public/webassets/images/mood-logo.svg') }}" alt="logo">
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
                        <a class="nav-link" href="http://moodapp.ae/#home">Home</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="http://moodapp.ae#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="http://moodapp.ae#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="http://moodapp.ae/join-mood#service-provider">Join Mood</a></li>
                    <li class="nav-item"><a class="nav-link" href="http://moodapp.ae#contact">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!--end navigation-->
    <!-- Start Banner Area -->
    <section class="page-main page-current" id="home">
        <div class="page-toload home-page">
            <div class="page-header">
                <div class="circles-container">
                    <div class="first">
                        <span class="circle circle-1"></span>
                        <span class="circle circle-2"></span>
                        <span class="circle circle-3"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="container display-flex">
            <div class="row">
                <div class="col-sm-7 col-12 display-flex">
                    <div class="page_contain flex wow fadeInUp">
                        <h2>We bring the best <span>mood</span> to your doorstep</h2>
                        <p>Choose from your favorite on-demand beauty and wellness providers - anytime, anywhere!</p>
                        <a href="#" class="app-btn">

                            <img src="{{ asset('public/webassets/images/apple-black.png') }}" alt="">

                            <img src="{{ asset('public/webassets/images/apple-icon.png') }}" alt="">
                        </a>
                        <a href="#" class="app-btn">

                            <img src="{{ asset('public/webassets/images/google-black.png') }}" alt="">
                            <img src="{{ asset('public/webassets/images/google-icon.png') }}" alt="">

                        </a>
                    </div>

                </div>
                <div class="col-sm-4 col-12 display-flex wow fadeInUp">
                    <div class="images-container flex">
                        <img class="first-image" src="{{ asset('public/webassets/images/white-phone.png') }}" alt="">
                    </div>
                </div>
            </div>

        </div>
    </section>



    <section class="features-area-five" id="about">
        <img class="shape-img" src="{{ asset('public/webassets/images/shape.png') }}" alt="">
        <div class="f-round circle-one"></div>
        <div class="f-round circle-two"></div>
        <div class="container">
            <div class="title-four title-six text-center wow fadeInUp">
                <h2>About Us</h2>
                <div class="br"></div>
                <p>Mood is a on-demand beauty and wellness home-service business. We are female-founded and UAE-born.
                    Mood aggregates the best home-service businesses to ensure only top quality experiences to you. Book
                    anywhere, anytime. </p>
            </div>

        </div>
    </section>
    <section class="features-area-five light-bg" id="features">
        <div class="round-one skrollable skrollable-before" data-top="transform:translatey(-100px);"
            data-bottom="transform:translatey(20px)"></div>
        <div class="round-two skrollable-before" data-bottom="transform:translatey(15%)"
            data-top="transform:translatey(-100%);"></div>

        <div class="container">
            <div class="title-four title-six text-center wow fadeInUp">
                <h2>Features</h2>
                <div class="br"></div>
                <p>The features are designed to provide you the most personalized experience to make your life easy!</p>
            </div>
            <div class="pl-lg-4 pr-lg-4">
                <div class="d-flex flex-md-row  justify-content-center mt-5">
                    <div class="col-auto col-sm-6 feature-five-item wow fadeInUp">
                        <div class="round">
                            <i class="ti-check-box"></i>
                        </div>
                        <h2>Select your <br>Category</h2>

                    </div>

                    <div class="col-auto col-sm-6 feature-five-item wow fadeInUp">
                        <div class="round">
                            <i class="ti-hand-point-up"></i>
                        </div>
                        <h2>Choose your desired Service provider</h2>

                    </div>

                    <div class="col-auto col-sm-6 feature-five-item wow fadeInUp">
                        <div class="round">
                            <i class="ti-thumb-up"></i>
                        </div>
                        <h2>Curate your experience with Your favorite services</h2>

                    </div>
                    <div class="col-auto col-sm-6 feature-five-item wow fadeInUp">
                        <div class="round">
                            <i class="ti-mobile"></i>
                        </div>
                        <h2>Book your <br>Appointment in a click!</h2>

                    </div>
                    <div class="col-auto col-sm-6 feature-five-item wow fadeInUp">
                        <div class="round">
                            <i class="ti-face-smile"></i>
                        </div>
                        <h2>Get ready to have <br>Your mood uplifted</h2>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="screenshot_area_three" id="screenshot">
        <div class="container wow fadeInUp">
            <div class="title-four title-six text-center wow fadeInUp">
                <h2>HERE’S A SNEAK PEAK!</h2>
                <div class="br"></div>
                <p>Mood is a on-demand beauty and wellness home-service business. We are female-founded and UAE-born. Mood aggregates the best home-service businesses to ensure only top quality experiences to you. Book anywhere, anytime.</p>

            </div>
            <div class="row wow fadeInUp">
                <div class="col-sm-12">

                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img class="img-responsive" src="{{ asset('public/webassets/images/one.jpg') }}" alt=""></div>
                            <div class="swiper-slide"><img class="img-responsive" src="{{ asset('public/webassets/images/two.jpg') }}" alt=""></div>
                            <div class="swiper-slide"><img class="img-responsive" src="{{ asset('public/webassets/images/three.jpg') }}" alt=""></div>
                            <div class="swiper-slide"><img class="img-responsive" src="{{ asset('public/webassets/images/four.jpg') }}" alt=""></div>
                            <!-- <div class="swiper-slide"><img class="img-responsive" src="{{ asset('public/webassets/images/five.jpg') }}" alt=""></div> -->

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <section class="call-action-area call-action-area-five" id="download">
        <div class="video-bg">
        </div>
        <div class="container">
            <div class="row call-action">
                <div class="col-md-7">
                    <div class="call-text wow fadeInUp">
                        <div class="title-four title-w">
                            <h2>DOWNLOAD NOW!</h2>
                            <div class="br"></div>
                        </div>
                        <p>Mood is a on-demand beauty and wellness home-service business. We are female-founded and UAE-born.
                            Mood aggregates the best home-service businesses to ensure only top quality experiences to you. Book
                            anywhere, anytime. </p>

                        <a href="#" class="app-btn2 active">

                            <img src="{{ asset('public/webassets/images/apple-black.png') }}" alt="">
                            <img src="{{ asset('public/webassets/images/apple-icon.png') }}" alt="">
                        </a>
                        <a href="#" class="app-btn2">
                            <img src="{{ asset('public/webassets/images/google-black.png') }}" alt="">
                            <img src="{{ asset('public/webassets/images/google-icon.png') }}" alt="">
                        </a>
                    </div>
                </div>
                <div class="col-sm-5 align-self-end">
                    <div class="call-mobile-img wow fadeInUp">
                        <img class="img-fluid" src="{{ asset('public/webassets/images/image_double_iphone.png') }}" alt="">

                    </div>
                </div>
            </div>
        </div>
    </section>




    <footer class="footer-area" id="contact">
        <div class="footer-top">
            <div class="container">
                <div class="row footer_sidebar">
                    <div class="widget widget1 about_us_widget col-12 col-sm-6 col-md-5 wow fadeIn" data-wow-delay="0ms"
                        data-wow-duration="1500ms" data-wow-offset="0">
                        <a href="index-2.html" class="logo">
                            <img src="{{ asset('public/webassets/images/mood-icon.svg') }}" alt="f-logo">
                        </a>
                        <p class="pr-lg-4">Mood is a on-demand beauty and wellness home-service business. We are female-founded and UAE-born.
                            Mood aggregates the best home-service businesses to ensure only top quality experiences to you. Book
                            anywhere, anytime.</p>
                        <ul class="nav social_icon row m0">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-pinterest-p"></i></a></li>
                            <li><a href="#"><i class="fa fa-behance"></i></a></li>
                        </ul>
                    </div>
                    <div class="widget widget2 widget_contact col-12 col-sm-6 col-md-5 wow fadeIn"
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
                                        <a href="#">+971043311170</a>
                                    </div>
                                </li>
                                <li>
                                    <i class="ti-email"></i>
                                    <div class="fleft contact_mail">
                                        <a href="#">info@moodapp.ae</a>
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
                        © 2021 All Right Reserved | <a href="index.html">Mood</a>
                    </div>

                </div>
            </div>
        </div>
    </footer>
    <!-- End of the footer -->



    <!-- jQuery plugins-->
    <!-- jQuery plugins-->
    <script src="{{ asset('public/webassets/js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/webassets/js/parallax.js') }}"></script>
    <script src="{{ asset('public/webassets/js/parallax-scroll.js') }}"></script>
    <script src="{{ asset('public/webassets/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('public/webassets/js/modernizr.custom.97074.js') }}"></script>
    <script src="{{ asset('public/webassets/js/smooth-scroll.min.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('public/webassets/js/jquery.sticky.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/sckroller/sckroller.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/video-player/jquery.mb.YTPlayer.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/slick/slick.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/swipper/swiper.min.js') }}"></script>
    <script src="{{ asset('public/webassets/js/plugins.js') }}"></script>
    <script src="{{ asset('public/webassets/js/mchimpsubs.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('public/webassets/js/custom.js') }}"></script>
    <script src="{{ asset('public/webassets/js/wow.js') }}"></script>
    <script src="{{ asset('public/webassets/vendor/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
    <script>
        new WOW().init();
        // ================================================

        $(".category-checkbox input:checkbox").click(function () {
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
