
<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="ltr" lang="en" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="ltr" lang="en" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->

<head>
    <meta charset="UTF-8" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <link href="{{url('/')}}/public/assets/content/slick.css" rel="stylesheet" />
    <link href="{{url('/')}}/public/assets/content/slick-theme.css" rel="stylesheet" />
    <link href="{{url('/')}}/public/assets/content/awesomplete.css" rel="stylesheet">
    <!-- CSS Files -->
    <!-- <link href="assets/css/bootstrap.min.css" rel="stylesheet" /> -->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <link href="{{url('/')}}/public/assets//content/style.css" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="{{url('/')}}/public/assets//content/eo.css" rel="stylesheet" />
    <link href="{{url('/')}}/public/assets//content/responsive.css" rel="stylesheet" /> 
    <script src="{{url('/')}}/public/assets//content//jquery.min.js" type="text/javascript"></script>
    
</head>

<body class="hero-page">
    <div class="section section-how-works">
        <div class="container">

        <div class="row">


            <div class="col-sm-12">
                <div class="static-page-box-outer">
                    <div class="static-page-box">
                        <h1 style="font-family:'Montserrat-Medium'; color:#8F8F8F;">{{$terms->title}}</h1>


                        <p style="font-family:'Montserrat-Medium'; color:#8F8F8F; font-size: 15px;">{!!$terms->description!!}</p>            


                    </div>
                </div>

            </div>

        </div>
        <div class="clearfix"></div>

    </div>
    </div>
</body>

</html>