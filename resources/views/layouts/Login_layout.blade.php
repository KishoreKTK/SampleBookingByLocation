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
    <link rel="icon" type="image/png" href="{{url('/')}}/img/logo/favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="{{url('/')}}/assets/files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{url('/')}}/assets/files/assets/pages/waves/css/waves.min.css" type="text/css"
        media="all">
    <link rel="stylesheet" type="text/css"
        href="{{url('/')}}/assets/files/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/files/assets/icon/icofont/css/icofont.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/assets/files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/css/custom.css">
</head>

<body themebg-pattern="theme1">

    <section class="login-block" style="background-image:url('url('/assets/img/login-bg.jpg')">
        @yield('section_content')
    </section>
    
    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
    <div class="ie-warning">
        <h1>Warning!!</h1>
        <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
        <div class="iew-container">
            <ul class="iew-download">
                <li>
                    <a href="http://www.google.com/chrome/">
                        <img src="{{url('/')}}/assets/files/assets/images/browser/chrome.png" alt="Chrome">
                        <div>Chrome</div>
                    </a>
                </li>
                <li>
                    <a href="https://www.mozilla.org/en-US/firefox/new/">
                        <img src="{{url('/')}}/assets/files/assets/images/browser/firefox.png" alt="Firefox">
                        <div>Firefox</div>
                    </a>
                </li>
                <li>
                    <a href="http://www.opera.com">
                        <img src="{{url('/')}}/assets/files/assets/images/browser/opera.png" alt="Opera">
                        <div>Opera</div>
                    </a>
                </li>
                <li>
                    <a href="https://www.apple.com/safari/">
                        <img src="{{url('/')}}/assets/files/assets/images/browser/safari.png" alt="Safari">
                        <div>Safari</div>
                    </a>
                </li>
                <li>
                    <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                        <img src="{{url('/')}}/assets/files/assets/images/browser/ie.png" alt="">
                        <div>IE (9 & above)</div>
                    </a>
                </li>
            </ul>
        </div>
        <p>Sorry for the inconvenience!</p>
    </div>
    <![endif]-->
    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{url('/')}}/assets/files/bower_components/jquery/js/jquery.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{url('/')}}/assets/files/bower_components/popper.js/js/popper.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/bootstrap/js/bootstrap.min.js"></script>
    <!-- waves js -->
    <script src="{{url('/')}}/assets/files/assets/pages/waves/js/waves.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{url('/')}}/assets/files/bower_components/modernizr/js/modernizr.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/modernizr/js/css-scrollbars.js"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{url('/')}}/assets/files/bower_components/i18next/js/i18next.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/assets/files/bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/assets/files/assets/js/common-pages.js"></script>
</body>

</html>