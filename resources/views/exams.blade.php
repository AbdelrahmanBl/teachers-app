@include('Auth/Student')
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Font Awesome -->    
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">    
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
  <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
  <script src="js/ajax.js"></script>
</head>

<body id="body">
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
    @include('layouts/header2')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      @include('layouts/app')
      <!-- partial -->
      <div class="main-panel" id="NAVCLOSE">
        <div class="content-wrapper">
          <div  class="row purchace-popup">
            
            <div id="ExamNot" style="display: none;" style="display: none;" class="col-12">
              <span  class="d-block d-md-flex align-items-center">
                 <p id="blbl">لا يوجد امتحانات ف الوقت الحالي</p>
              </span>
            </div>
 </div>
          <div class="backButton">
              <button class="btn" id="backbtn" style="display:none" onclick="back()">رجوع</button>
          </div>            
<!-----------------------------------         Materals          --------------------------------->           
<div class="col-12 grid-margin" id="examsSection">
  <div class="card">
  @include('student/materials')
  </div>
</div>
<!------------------------------------------------------------------------->              
<div class="col-12 grid-margin" id="exams">

</div>
<!----------------------------------------------------------------->
      </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
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
      
    function examsSection(materialCode){
         $("#examsSection").hide('fast');
        $("#loading").show(); 
        $("#body").css('overflow', 'hidden')         
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/findExam", 
            data: {_token:"{{csrf_token()}}", exam_code : materialCode }
        }).done( function(data){
            if(data[0] == 'success' ){
                var elements = '<div class="card">';
                for(var i = 0 ; i < data[1].length ; i++){
                    elements  += '<div class="card-body"><div class="row" id="row"><div class="col-md-9"><p class="exam"><span><i class="fas fa-external-link-square-alt"></i> '+data[1][i]+'</span></p></div><div class="col-md-3"><div method="post" ><button type="button" onclick="GoToExam('+"'"+materialCode+"',"+data[2][i]+",'"+data[1][i]+"'"+')" class="btn-primary btn-block">أود الامتحان</button></div></div></div></div>';
                }
                elements += '</div>';
                $('#backbtn').fadeIn('slow');
                 $("#exams").empty();
                $("#exams").append(elements);
                $("#exams").fadeIn('slow');
            }
            else{
                $('#ExamNot').show('slow');
            }
            $("#loading").hide(); 
            $("#body").css('overflow', 'visible')
        });
    }    
    function GoToExam(materialCode,ETime,material){
        $("#exams").hide('fast');
        $("#loading").show(); 
        $("#body").css('overflow', 'hidden')         
        var d1 = new Date (),
        time = new Date ( d1 );
        time.setMinutes ( d1.getMinutes() + ETime );
        
         $.ajax({
                type: "POST",
                dataType: "json",
                url: "/timer", 
                data: {_token:"{{csrf_token()}}" , materialCode:materialCode , material:material , time:time }
                }).done( function(data){
            if(data[0] == "success" ){
                location.href = 'solveExam';
            }
            else{
                location.reload();
            }
            }); 
    }   
    function back(){
        $("#exams").hide();
        $("#examsSection").fadeIn('slow');
        $('#backbtn').hide();
    }
  </script>
</body>

</html>