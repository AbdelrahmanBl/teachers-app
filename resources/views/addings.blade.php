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
                $Users = DB::table('exams')->orderBy('id','DESC')->limit(100)->get();
                    $filename = "MARKUP.txt";
                    $handle = fopen($filename, "r");
                    $contents = fread($handle, filesize($filename));
                    fclose($handle);
                ?>
                <div class="content-wrapper">
                    <form action="markup" method="POST">
                        {{csrf_field()}}
                    <input type="text" name="mark" style="width:25px" placeholder="ID" >
                    <button class="specialBtn">اضافة</button>
                    </form>
<table>
  <thead>
    <tr>
      <th>الرقم</th>    
      <th>الكود</th>    
      <th>اسم الامتحان</th>
      <th>اسم المادة</th>
      <th>الصف الدراسي</th>
      <th>عدد الاسألة</th>
    </tr>
  </thead>
  <tbody>
      @foreach( $Users as $row )
    <tr @if($row->ID == $contents) style="border-top:2px solid #11ff01" @endif>
      <td data-column="الرقم">{{$loop->iteration}}</td>
      <td data-column="الكود">{{$row->ID}}</td>
      <td data-column="اسم الامتحان">{{$row->examName}}</td>
      <td data-column="اسم المادة">@if($row->material == 1)امتحانات اللغة العربية @elseif($row->material == 2) امتحانات اللغة الانجليزية @elseif($row->material == 3) امتحانات اللغة الفرنسية @elseif($row->material == 4) امتحانات اللغة الالمانية @elseif($row->material == 5) امتحانات الفيزياء @elseif($row->material == 6) امتحانات الكيمياء @elseif($row->material == 7) امتحانات الرياضيات @elseif($row->material == 8) امتحانات الاحياء @elseif($row->material == 9) امتحانات التاريخ @elseif($row->material == 10) امتحانات الجغرافيا @elseif($row->material == 11) امتحانات الفلسفة والمنطق @endif</td>
      <td data-column="الصف الدراسي">@if($row->Year == 1)الصف الاول الثانوي @else الصف الثاني الثانوي @endif</td>
      <td data-column="عدد الاسألة">{{count(explode('&%',$row->mainQ)) - 1}}</td>
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