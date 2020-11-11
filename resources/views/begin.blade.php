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
                $DB = env('SERVER_CODE') ."first" ;
                DB::disconnect('mysql');
                Config::set('database.connections.mysql.database', $DB);
                (session('Users'))? $Users = session('Users') : $Users = DB::table('students')->get(); ;
                $Year = 'الصف الاول الثانوي';
                $City = array('الإسكندرية','الإسماعيلية','أسوان','أسيوط','الأقصر','البحر الأحمر','البحيرة','بني سويف','بورسعيد','جنوب سيناء','الجيزة','الدقهلية','دمياط','سوهاج','السويس','الشرقية','شمال سيناء','الغربية','الفيوم','القاهرة','القليوبية','قنا','كفر الشيخ','مطروح','المنوفية','المنيا','الوادي الجديد');
                ?>
                <div class="content-wrapper">
                    <form action="filter" method="POST" >
                        {{CSRF_field()}}
                    <select class="specialBox" name="Year">
                        <option disabled >اختر الصف</option>
                        <option @if(session('Year') == 1 )selected @elsif(session('Year') == '') selected @endif value="1">الصف الاول الثانوي</option>
                        <option @if(session('Year') == 2 )selected @endif value="2">الصف الثاني الثانوي</option>
                    </select>
                    <select class="specialBox" name="City">
                        <option  value="">اختر المدينة</option>
                        @for( $i = 0 ; $i < count($City) ; $i++ )
                        <option @if( session('City') && session('City') == $i )selected @endif value="{{$i}}">{{$City[$i]}}</option>
                        @endfor
                    </select>
                    <button class="specialBtn">بحث</button>
                    </form>
                    <h1 class="center">{{$Users->count()}}</h1>
<table>
  <thead>
    <tr>
      <th>الرقم</th>    
      <th>اسم الطالب</th>
      <th>رقم الهاتف</th>
      <th>المدينة</th>
      <th>السنة الدراسية</th>
      <th>رقم الكود</th>
    </tr>
  </thead>
  <tbody>
      @foreach( $Users as $row )
    <tr>
      <td data-column="الرقم">{{$loop->iteration}}</td>
      <td data-column="اسم الطالب">{{$row->Fullname}}</td>
      <td data-column="رقم الهاتف">{{$row->Phone}}</td>
      <td data-column="المدينة">{{$City[$row->City]}}</td>
      <td data-column="السنة الدراسية">{{$Year}}</td>
      <td data-column="رقم الكود">{{$row->code}}</td>
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