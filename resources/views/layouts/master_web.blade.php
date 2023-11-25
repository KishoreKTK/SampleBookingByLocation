<!DOCTYPE html>
<html lang="en">

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="robots" content="noarchive">
   <title>Mood - Your favorite salons - One booking platform</title>
   <!-- favicon -->
   <link rel="icon" type="image/png" href="favicon.png">
   <!-- bootstrap -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/bootstrap.min.css">
   <!-- icofont -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/fontawesome.5.7.2.css">
   <!-- flaticon -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/flaticon.css">
   <!-- animate.css -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/animate.css">
   <!-- Owl Carousel -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
   <!-- magnific popup -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/magnific-popup.css">
   <!-- stylesheet -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/style.css">
   <!-- responsive -->
   <link rel="stylesheet" href="{{url('/')}}/public/assets_web/css/responsive.css">

   @yield('pagecss')
</head>
<body>
     <!-- navbar area start -->
   <nav class="navbar navbar-area navbar-expand-lg nav-absolute white nav-style-01 header-style-09">
      <div class="container nav-container">
         <div class="responsive-mobile-menu">
            <div class="logo-wrapper">
               <a href="{{url('/')}}" class="logo">
                  <img src="{{url('/')}}/public/assets_web/img/mood-logo.svg" alt="mood logo">
               </a>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#moodmenu"
               aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
         </div>
         <div class="collapse navbar-collapse" id="moodmenu">
            <ul class="navbar-nav">
               <li class="current-menu-item">
                  <a href="{{url('/')}}">Home</a>
               </li>
               <li><a href="{{url('/')}}/#howitworks">How it works</a></li>
               <li><a href="{{url('/')}}/#screens">Screens</a></li>
               <li><a href="{{url('/')}}/#download">Download</a></li>
               <li><a href="{{url('/')}}/#about">About Us</a></li>
               <li><a href="{{url('/')}}/partner">Become a Partner</a></li>
            </ul>
         </div>
      </div>
   </nav>
   <!-- navbar area end -->

   @yield('content')
 <!-- footer area start -->
   <footer class="footer-area style-02 bg-blue">
      <div class="footer-top">
         <div class="container">
            <div class="row">
               <div class="col-lg-3 col-md-6 align-self-center">
                  <div class="footer-widget about_widget">
                     <a href="{{url('/')}}" class="footer-logo"><img src="{{url('/')}}/public/assets_web/img/mood-logo-brand.svg" alt=""></a>
                     <ul class="social-icon mt-3 mb-3">
                        <!-- <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                        <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li> -->
                        <li><a href="https://www.instagram.com/mood.ae/"><i class="fab fa-instagram"></i></a></li>
                     </ul>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-6 align-self-center">
                  <div class="footer-widget nav_menus_widget">
                     <!-- <h4 class="widget-title">Useful Links</h4> -->
                     <ul>
                        <li><a href="{{url('/')}}/#about">About Us</a></li>
                        <li><a href="{{url('/')}}/#howitworks">How it works</a></li>
                        <li><a href="{{url('/')}}/#screens">Screens</a></li>

                     </ul>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-6 align-self-center">
                  <div class="footer-widget nav_menus_widget">
                     <!-- <h4 class="widget-title">Need Help?</h4> -->
                     <ul>
                        <li><a href="{{url('/')}}/#download">Download</a></li>
                        <li><a href="{{url('/')}}/partner">Become a Partner</a></li>
                        <li><a href="{{url('/')}}/contact">Contact Us</a></li>
                     </ul>
                  </div>
               </div>
               <div class="col-lg-3 col-md-6 col-6 align-self-center">
                  <div class="footer-widget nav_menus_widget">
                     <!-- <h4 class="widget-title">Download</h4> -->
                     <ul>
                        <li><a href="{{url('/')}}/privacy-policy">Privacy Policy</a></li>
                        <li><a href="{{url('/')}}/terms-and-conditions">Terms & Conditions</a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="copyright-area">
         <!-- copyright area -->
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <div class="copyright-inner text-center">
                     <!-- left content area -->
                     &copy; {{date('Y')}} Mood. All rights reserved.
                  </div>
                  <!-- //.copyright inner wrapper -->
               </div>
            </div>
         </div>
      </div>
      <!-- //. copyright area -->
   </footer>
   <!-- footer area end -->

    <!-- footer ends -->
   <!-- preloader area start -->
   <div class="preloader-wrapper" id="preloader">
      <div class="preloader">
         <div class="sk-circle">
            <div class="sk-circle1 sk-child"></div>
            <div class="sk-circle2 sk-child"></div>
            <div class="sk-circle3 sk-child"></div>
            <div class="sk-circle4 sk-child"></div>
            <div class="sk-circle5 sk-child"></div>
            <div class="sk-circle6 sk-child"></div>
            <div class="sk-circle7 sk-child"></div>
            <div class="sk-circle8 sk-child"></div>
            <div class="sk-circle9 sk-child"></div>
            <div class="sk-circle10 sk-child"></div>
            <div class="sk-circle11 sk-child"></div>
            <div class="sk-circle12 sk-child"></div>
         </div>
      </div>
   </div>
   <!-- preloader area end -->
   <!-- back to top area start -->
   <div class="back-to-top">
      <i class="fas fa-angle-up"></i>
   </div>
   <!-- back to top area end -->
   <!-- jquery -->
   <script src="{{url('/')}}/public/assets_web/js/jquery.js"></script>
   <!-- popper -->
   <script src="{{url('/')}}/public/assets_web/js/popper.min.js"></script>
   <!-- bootstrap -->
   <script src="{{url('/')}}/public/assets_web/js/bootstrap.min.js"></script>
   <!-- owl carousel -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
   <!-- magnific popup -->
   <script src="{{url('/')}}/public/assets_web/js/jquery.magnific-popup.js"></script>
   <!-- contact js-->
   <script src="{{url('/')}}/public/assets_web/js/contact.js"></script>
   <!-- wow js-->
   <script src="{{url('/')}}/public/assets_web/js/wow.min.js"></script>
   <!-- way points js-->
   <script src="{{url('/')}}/public/assets_web/js/waypoints.min.js"></script>
   <!-- counterup js-->
   <script src="{{url('/')}}/public/assets_web/js/jquery.counterup.min.js"></script>
   <!-- main -->
   <script src="{{url('/')}}/public/assets_web/js/main.js"></script>

   @yield('pagescript')
</body>

</html>
