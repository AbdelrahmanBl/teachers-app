@include('Auth/Student')
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{env('BRAND')}}</title>
    <link rel="icon" href="images/{{env('LOGO')}}">
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/iconfonts/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <script src="js/ajax.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Amiri&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        @media(max-width:414px){
            .card .card-body {
                padding : 0;
            }
            img{
                max-width: 240px;
            }
        }
    </style>
</head>
@if(session('time'))
<body onload="myFunction()">
    <!--      Start Spinner loading        -->
    <div id="loading" class="fixed" style="display:none">
            <div class="spinner">
            </div>
        </div>
        <style>
         @keyframes moving{
                form{transform: rotate(0deg)}
                to{transform: rotate(360deg)}
            }
        </style>
    <!--      End Spinner loading        --> 
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">

                <h2 class="brand"></h2>

            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center">

                <ul class="navbar-nav navbar-nav-right">
                    <div style="font-size: 20px" class="time" id="time">
                        
                    </div>
                    
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            @include('layouts/app')
            <!-- partial -->    
            <div class="main-panel" id="NAVCLOSE">
                <div class="content-wrapper">

                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body tx-right">
                                    <form class="e" id="FormEnd" action="end" method="POST">
                                        {{CSRF_field()}}
                                        @include('dataFile/getExams')
                                        <div class="col-md-3" style="margin-top: 40px">
                                            <button id="end-btn" onclick="chkEnd()" type="button" class="btn-primary btn-block" style="padding: 6px 5px">ارسل</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <input type="hidden" value="{{session('time')}}" id="getTime">
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <!-- End custom js for this page-->
<script>
    function chkEnd(){
        var questions = $("#Questions").val();
        var q = questions.split("&%");
        var counter   = 0 ;
        for(var i = 1 ; i < q.length ; i++){
            if(q[i] == 'M' ){
                if( $("input[name='R"+i+"']:checked").val() == undefined )
                    counter++;
            }
            else{
                if( $("#R"+i).val() == '' )
                    counter++;
            }
        }
        if(counter > 0)   
        var end = confirm('عدد الاسألة المتبقية : ' +counter+ ' هل تود انهاء الامتحان ؟' );
        else var end = confirm('هل تود انهاء الامتحان');
        if( end == true ){
        $("#time").hide('slow');
        $("#loading").show();    
        $("#FormEnd").submit();
        }
    }
    function myFunction() { 
        'use strict';
        var z = $("#getTime").val() ;
            var countDownDate = new Date(z).getTime(); 
                // Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
 
             var now = new Date().getTime()
  // Find the distance between now and the count down date
  var distance  =  countDownDate - now ;

  // Time calculations for days, hours, minutes and seconds
  var hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id
  document.getElementById("time").innerHTML = hours + "h "
  + minutes + "m " + seconds + "s ";
    //alert(countDownDate - now)
  // If the count down is over, write some text 
  if ( distance < 0) {
      clearInterval(x);
    $("#time").hide('slow');
    $("#loading").show();
    $("#FormEnd").submit();
  }

}, 1000);    
function end(){
    clearInterval(x);
}
}
</script>
<script>
    $(document).on('click touchstart', function () {
        $( "#NAVCLOSE" ).click(function() {
        $("#sidebar").removeClass('active');
});
    });
</script> 
</body>
@endif
</html>