@include('Auth/Main')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--    internet explorer compatibility meta-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--    first mobile meta-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{env('BRAND')}}</title>
    <link rel="icon" href="images/{{env('LOGO')}}">
    <!--    bootstrap-->
    <link rel="stylesheet" href="css/bootstrap-arabic.min.css">
    <!--    fontawesome-->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!--    opensasn fonts-->
    <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
    <!--    animate-->
    <link rel="stylesheet" href="css/animate.css">
    <!--    my stylesheet-->
    <link rel="stylesheet" href="css/style3.css">
    <!--    animate-->
    <link rel="stylesheet" href="css/animate.css">
</head>
<!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script src="js/respond.min.js"></script>
<![endif]-->
<body>
    <!-- start header -->
    <header class="tt">
        <nav class="navbar navbar-default">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" ><img class="LOGO" src="images/{{env('LOGO')}}" /></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                    <ul class="nav navbar-nav navbar-right">
                        @include('layouts/header')

                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
    </header>
    <!-- end header -->
    <!-- start Warning -->    
<div class="message-box message-box-warn">
  <i class="fa fa-warning fa-2x"></i>
  <span class="message-text"><strong></strong> يجب العلم انه في حالة التسجيل من هذا المتصفح يجب عليك استخدامه فيما بعد</span>
</div>
    <!-- end Warning -->
    <!-- start slider -->
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <div class="overlay">
                    <img src="images/1.jpg" alt="one">
                </div>
                <div class="carousel-caption wow bounceIn" data-wow-duration="3s" data-wow-offset="100">
                    <h1>الطريق إلي تحقيق حلمك</h1>
                </div>
            </div>
            <div class="item">
                <img src="images/1.jpg" alt="two">
                <div class="carousel-caption wow bounceIn" data-wow-duration="3s" data-wow-offset="100">
                    <h1>دليلك للتفوق</h1>
                </div>
            </div>  

        </div>

    </div>
    <!-- end slider -->
    <!--    start footer-->
    <div class="footer text-center">
            <div class="container">
                <p>Ostazy 2020 &copy; جميع الحقوق محفوظة  </p>
            </div>
        </div>
        <!--end footer-->
    <script src="js/html5shiv.min.js"></script>
    <script src="js/jquery-1.12.3.min.js"></script>
    <script src="js/bootstrap-arabic.min.js"></script>
    <script src="js/main.js"></script>
    <!--    wow.js file-->
    <script src="js/wow.min.js"></script>

</body>

</html>