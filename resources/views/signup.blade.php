@include('Auth/Main')
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Colorlib Templates">
    <meta name="author" content="Colorlib">
    <meta name="keywords" content="Colorlib Templates">

    <!-- Title Page-->
    <title>{{env('BRAND')}}</title>
    <link rel="icon" href="images/{{env('LOGO')}}">

    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/style3.css" rel="stylesheet" media="all">
</head>

<body>  
    <div class="page-wrapper bg-gra-01 p-t-100 p-b-100 font-poppins">
            <h2 class="text-center zag"><a href="/.."> الرئيسية </a></h2>
        <div class="wrapper wrapper--w780">
            <div class="card card-3">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="title">معلومات التسجيل</h2>
                    <form action="signup" method="POST">
                        {{CSRF_field()}}
                    @if(session('error')) 
                         <span class="error" style="font-size: 20px">من فضلك حاول مرة اخري</span>
                    @endif   
                        <div class="input-group">
                                <span class="error">{{$errors->first('Fullname')}}</span>

                            <input value="{{session('Fullname')}}" class="input--style-3" type="text" placeholder="الاسم باللغة العربية" name="Fullname">
                        </div>
                        <div class="input-group">
                                <span class="error">{{$errors->first('Phone')}}</span>

                            <input value="{{session('Phone')}}" class="input--style-3" type="text" placeholder="رقم الهاتف" name="Phone">
                        </div>
                        <div class="input-group">
                          <span class="error">{{$errors->first('Year')}}</span>
                            <div class="rs-select2 js-select-simple select--no-search">
                                <select name="Year">
                                    <option disabled="disabled" selected="selected">المرحلة الدراسية</option>
                                    <option @if(session('Year') == 1 )selected @endif value="1">الصف الاول الثانوي</option>
                                    <option @if(session('Year') == 2 )selected @endif value="2">الصف الثاني الثانوي</option>
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                        </div>
                        <div class="input-group">
                          <span class="error">{{$errors->first('City')}}</span>
                            <div class="rs-select2 js-select-simple select--no-search">
                                <select name="City">
                                    <option disabled="disabled" selected="selected">اختر المدينة</option>
                                    <?php
                                    $towns = array('الإسكندرية','الإسماعيلية','أسوان','أسيوط','الأقصر','البحر الأحمر','البحيرة','بني سويف','بورسعيد','جنوب سيناء','الجيزة','الدقهلية','دمياط','سوهاج','السويس','الشرقية','شمال سيناء','الغربية','الفيوم','القاهرة','القليوبية','قنا','كفر الشيخ','مطروح','المنوفية','المنيا','الوادي الجديد');
                                    ?>
                                    @if($towns)
                                    @for( $i = 0 ; $i < count($towns) ; $i++ )
                                    <option @if( session('City') != '' && session('City') == $i )selected @endif value="{{$i}}">{{$towns[$i]}}</option>
                                    @endfor
                                    @endif
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                        </div>
                        <div class="input-group">
                                <span class="error">{{$errors->first('code')}}</span>

                            <input value="{{session('code')}}" class="input--style-3" type="text" placeholder="رقم الكود" name="code">
                        </div>  
                        <div class="p-t-10">
                            <button class="btn btn--pill btn--green" type="submit">تسجيل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS-->
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/datepicker/moment.min.js"></script>
    <script src="vendor/datepicker/daterangepicker.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>
</body>

</html>
<!-- end document-->