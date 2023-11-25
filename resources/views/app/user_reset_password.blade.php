<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mood</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
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
    <link rel="icon" type="image/png" href="{{url('/')}}/public/img/logo/favicon.png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="{{url('/')}}/public/assets/files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{url('/')}}/public/assets/files/assets/pages/waves/css/waves.min.css" type="text/css"
        media="all">
    <link rel="stylesheet" type="text/css"
        href="{{url('/')}}/public/assets/files/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/assets/files/assets/icon/icofont/css/icofont.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/assets/files/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{url('/')}}/public/css/custom.css">


</head>


<body themebg-pattern="theme1">


    <section class="login-block">
        <!-- Container-fluid starts -->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Authentication card start -->
                    {!! Form::open(['url' => 'app/user/reset_password','id'=>'validate-form','class'=>"md-float-material
                    form-material",'method'=>'post']) !!}
                    @csrf
                    <!-- <form class="md-float-material form-material"> -->
                    <div class="text-center">
                        <!-- <img src="{{url('/')}}/public/assets/files/assets/images/logo.png" alt="logo.png"> -->

                        <a href=""> <img src="{{url('/')}}/public/img/logo/mood-white-logo.png"
                                style="width:120px;" alt="logo.png"></a>

                    </div>
                    <div class="auth-box card">
                        <div class="card-block">
                            @include('includes.msg')
                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    <h3 class="text-left">Reset your password</h3>
                                </div>
                            </div>
                            <div class="form-group form-primary">
                                <label class="">Password</label>
                                <input type="password" name="password" class="form-control" required="">
                                <span class="form-bar"></span>

                            </div>
                            <div class="form-group form-primary">
                                <label class="">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required="">
                                <span class="form-bar"></span>

                            </div>



                            <div class="row">
                                <div class="col-md-12">
                                    <!-- <button type="button" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Reset Password</button> -->
                                    {!! Form::hidden('email',$email) !!}
                                    {!! Form::hidden('token',$token) !!}
                                    {!! Form::submit('Save',['class'=>'btn btn-primary btn-md btn-block waves-effect
                                    text-center m-b-20']) !!}

                                </div>
                            </div>
                            <div class="row m-t-10">
                                <div class="col-md-10">
                                    <a href="{{url('/')}}" class="text-right f-w-600"> Go to website</a>
                                </div>
                                <div class="col-md-2">
                                    <a href="#"> <img src="{{url('/')}}/public/img/logo/logo_icon.png"
                                            style="width:32px;" alt="small-logo.png"></a>

                                </div>
                            </div>
                           
                        </div>
                    </div>
                    <!-- </form> -->
                    {!! Form::close() !!}

                    <!--<div class="login-card card-block auth-body mr-auto ml-auto">-->
                    <!--<form class="md-float-material form-material">-->
                    <!--<div class="text-center">-->
                    <!--<img src="{{url('/')}}/public/assets/files/assets/images/logo.png" alt="logo.png">-->
                    <!--</div>-->
                    <!--<div class="auth-box">-->
                    <!---->
                    <!--</div>-->
                    <!--</form>-->
                    <!--&lt;!&ndash; end of form &ndash;&gt;-->
                    <!--</div>-->
                    <!-- Authentication card end -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
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
                    <img src="{{url('/')}}/public/assets/files/assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="{{url('/')}}/public/assets/files/assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="{{url('/')}}/public/assets/files/assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="{{url('/')}}/public/assets/files/assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="{{url('/')}}/public/assets/files/assets/images/browser/ie.png" alt="">
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
    <script type="text/javascript" src="{{url('/')}}/public/assets/files/bower_components/jquery/js/jquery.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/public/assets/files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{url('/')}}/public/assets/files/bower_components/popper.js/js/popper.min.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/public/assets/files/bower_components/bootstrap/js/bootstrap.min.js"></script>
    <!-- waves js -->
    <script src="{{url('/')}}/public/assets/files/assets/pages/waves/js/waves.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript"
        src="{{url('/')}}/public/assets/files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{url('/')}}/public/assets/files/bower_components/modernizr/js/modernizr.js">
    </script>
    <script type="text/javascript"
        src="{{url('/')}}/public/assets/files/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script type="text/javascript" src="{{url('/')}}/public/assets/files/assets/js/common-pages.js"></script>
</body>

</html>