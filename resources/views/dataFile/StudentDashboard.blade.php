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
  <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
  <script src="js/ajax.js"></script>
</head>

<body>
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
         
            @include('dataFile/printMessages')
            @if(session('Messages'))
                         @foreach( session('Messages') as $row )
                        <div id="DIV{{$row->ID}}" class="col-12 grid-margin">
                                <div class="card">
                                  <div class="card-body">
                                    <div class="fluid-container">
                                      <div class="row ticket-card mt-3 pb-2 mb-3">
                                        <div class="col-md-1">
                                          <img class="img-sm rounded-circle mb-4 mb-md-0" src="images/profile.jpg">
                                        </div>
                                       
                                        <div class="ticket-details col-md-12">
                                          
                                          
                                          <p class="text-gray ellipsis mb-2" style="font-weight: bold;    font-size: 18px;">{{$row->message}}<br><a target="_blank" href="{{$row->link}}" >{{$row->link}}</a></p>
                                          <div class="row text-gray d-md-flex ">
                                            <?php $arrTimes = explode(' ',$row->created_at); ?>
                                            <div class="col-12 ">
                                              <small class="mb-0 mr-2 text-muted text-muted">{{$arrTimes[1]}}</small>
                                            </div>
                                            <div class="col-12 ">
                                              <small class="mb-0 mr-2 text-muted text-muted">{{$arrTimes[0]}}</small>
                                            </div>
                                            
                                          </div>
                                        </div>
                                        
                                      </div>
                              
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endforeach
                              @else 
                              <div class="col-12">
              <span  class="d-block d-md-flex align-items-center">
                <p id="blbl">لا توجد رسائل</p>
              </span>
                              @endif
          </div>
          
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
@if(!session('Firebase'))
<script src="https://www.gstatic.com/firebasejs/7.10.0/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.10.0/firebase-messaging.js"></script>

<script>
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyB-gc6I14nnbjwnJqLNvLz97rYPjFqEL4c",
    authDomain: "test-715d2.firebaseapp.com",
    databaseURL: "https://test-715d2.firebaseio.com",
    projectId: "test-715d2",
    storageBucket: "test-715d2.appspot.com",
    messagingSenderId: "885509427848",
    appId: "1:885509427848:web:2e78569d315a5dabec853d",
    measurementId: "G-L4DRJW1571"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  const messaging = firebase.messaging();
  const token =  messaging.getToken()
    token.then(function(result) {
     $.ajax({
            type: "POST",
            dataType: "json",
            url: "/registerFCM", 
            data: {_token:"{{csrf_token()}}", firebase : result}
            }).done( function(data){
        }); 
});
</script>
@endif
</body>

</html>