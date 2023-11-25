@extends('website.home_layout')
@section('home_page_content')

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

                            <img src="{{ url('/')}}/public/webassets/images/apple-black.png" alt="">

                            <img src="{{ url('/')}}/public/webassets/images/apple-icon.png" alt="">
                        </a>
                        <a href="#" class="app-btn">

                            <img src="{{ url('/')}}/public/webassets/images/google-black.png" alt="">
                            <img src="{{ url('/')}}/public/webassets/images/google-icon.png" alt="">

                        </a>
                    </div>

                </div>
                <div class="col-sm-4 col-12 display-flex wow fadeInUp">
                    <div class="images-container flex">
                        <img class="first-image" src="{{ url('/')}}/public/webassets/images/white-phone.png" alt="">
                    </div>
                </div>
            </div>

        </div>
    </section>


    {{-- About Us --}}
    <section class="features-area-five" id="about">
<<<<<<< HEAD:resources/views/Website - Server/Home.blade.php
        <img class="shape-img" src="{{ url('/')}}/public/webassets/images/shape.png" alt="">
=======
        <img class="shape-img" src="{{ url('/')}}/WebPageAsset/images/shape.png" alt="">
>>>>>>> f794234a551e71f1a35558615cdda1c8e88f311e:resources/views/Website-Server/Home.blade.php
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


    {{-- Features --}}
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


    {{-- ScreenShots --}}
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
                            <div class="swiper-slide"><img class="img-responsive" src="{{ url('/')}}/public/webassets/images/one.jpg" alt=""></div>
                            <div class="swiper-slide"><img class="img-responsive" src="{{ url('/')}}/public/webassets/images/two.jpg" alt=""></div>
                            <div class="swiper-slide"><img class="img-responsive" src="{{ url('/')}}/public/webassets/images/three.jpg" alt=""></div>
                            <div class="swiper-slide"><img class="img-responsive" src="{{ url('/')}}/public/webassets/images/four.jpg" alt=""></div>
                            <!-- <div class="swiper-slide"><img class="img-responsive" src="{{ url('/')}}/public/webassets/images/five.jpg" alt=""></div> -->

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
