@include('Auth/Admin')
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
    
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/table.css">
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
                <?php
                $DB = env('SERVER_CODE') ."main" ;
                DB::disconnect('mysql');
                Config::set('database.connections.mysql.database', $DB);
                (session('Codes'))? $Users = session('Codes') : $Users = DB::table('codes')->orderBy('id','DESC')->get(); 
                $Price = 0;
	            foreach($Users as $row){
	            $Price += $row->Price;
	            }
                ?>
                <div class="content-wrapper">
                    <form action="filterDate" method="POST" >
                        {{CSRF_field()}}
                    <input type="date" value="{{session('date')}}" name="date" />
                    <button class="specialBtn">بحث</button>
                    </form>
                    <h1 class="center">{{$Users->count()}} / <span style="border: 2px solid #737373;background-color:black;color:white">{{$Price}}EG</span></h1>
<table>
  <thead>
    <tr>
      <th>الرقم</th>    
      <th>رقم الايصال</th>
      <th>الكود</th>
      <th>ACCESS_TOKEN</th>
      <th>السعر</th>
      <th>التاريخ</th>
      <th>الوقت</th>
    </tr>
  </thead>
  <tbody>
      @foreach( $Users as $row )
    <tr>
      <td data-column="الرقم">{{$loop->iteration}}</td>
      <td data-column="رقم الايصال">{{$row->banking}}</td>
      <td data-column="الكود">{{$row->code}}</td>
      <td data-column="">{{$row->ACCESS_TOKEN}}</td>
      <td data-column="السعر">{{$row->Price}}</td>
      <td data-column="التاريخ">{{$row->Date}}</td>
      <td data-column="الوقت">{{$row->Time}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
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
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <!-- End custom js for this page-->
@if(session('error'))
    <script type="text/javascript">
alert('من فضلك اختر الصف الدراسي')
    </script>
@endif
</body>

</html>