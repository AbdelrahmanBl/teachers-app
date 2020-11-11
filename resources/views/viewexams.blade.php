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
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
  <script src="js/ajax.js"></script>
  <link href="https://fonts.googleapis.com/css?family=Amiri&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        <div class="content-wrapper" >
         @if(session('code'))
          <div class="row"  id="row">
            <div class="col-12 grid-margin">
              @if(!session('Exam1'))
              <div class="backButton">
              <button class="btn" id="backbtn"  onclick="back()">رجوع</button>
          </div> 
                @include('student/displayExams')
              </div>
              @else
                @include('student/viewExams')
              @endif
          </div>
    @endif      
    <!-----------------------------------         Materals          --------------------------------->           
<div class="col-12 grid-margin" id="examsSection" style="display:none">
  <div class="card">
  @include('student/materials')
  </div>
</div>
<!------------------------------------------------------------------------->       


        </div>
     
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
       function examsSection(materialCode){
           setTimeout(function(){ $("#loading").show(); $("#body").css('overflow', 'hidden') },300)
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/findStudentExam", 
            data: {_token:"{{csrf_token()}}", exam_code : materialCode }
        }).done( function(data){
            
            if(data[0] == 'success' ){
                location.reload();
                return false;
            }
            else{
                $('#ExamNot').show('slow');
            }
        });
    }  
    function back(){
        $("#loading").show(); 
        $("#body").css('overflow', 'hidden')
         $.ajax({
            type: "POST",
            dataType: "json",
            url: "/backviewexams", 
            data: {_token:"{{csrf_token()}}" }
        }).done( function(data){
            
            if(data[0] == 'success' ){
                //location.reload();
                //return false;
                $('#row').hide()
                $('#examsSection').fadeIn('slow')
                $("#loading").fadeOut(); 
                $("#body").css('overflow', 'visible')
            }
            else{
                $('#ExamNot').show('slow');
            }
        });
        
    }
    function viewLoading(ID){
        setTimeout(function(){ $("#loading").show(); $("#body").css('overflow', 'hidden');$("#viewForm"+ID).submit(); },300)
    }
    function exitStudentLoad(){
        setTimeout(function(){ $("#loading").show(); $("#body").css('overflow', 'hidden');$("#exitStudentForm").submit(); },300)
    }
  </script>
 @if(!session('code'))
<script>
    $("#examsSection").show();
</script>
@endif
</body>

</html>