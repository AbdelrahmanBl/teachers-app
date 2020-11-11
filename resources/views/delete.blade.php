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

                    <div class="row" >
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body tx-right">
                                    <form method="post" action="deleteStudent" >
                                        {{CSRF_field()}}
                                        <div id="exampleContainer" class="trips">

                                            <div class="input-group">
                                                <div class="form-group has-feedback has-clear" style="width: 100%">
                                                    <div class="row">
                                                        <div class="col-sm-12" >
                                                            <label for="">ادخل الACCESS_TOKEN  or Code </label>
                                <input name="ACCESS_TOKEN" placeholder="Enter ACCESS_TOKEN OR CODE" autocomplete="off" type="text" style="border: 1px solid#999;border-radius: 5px;padding: 4px;height: 32px; width: 100%;margin-bottom: 9px;">
                                 </div>
                                                        <div class="col-sm-2" style="display: inline-block">
                                                            <span class="input-group-btn">
                                                                <button   type="submit" class="btn btn-primary"
                                                                    id="exampleButton1">بحث</button>
                                                            </span>
                                                        </div>
                                                    </div>


                                                </div>

                                            </div>


                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div id="push"></div>
                        <!-- -----------------لما تعمل لوب اعملها من هنا ----------------- -->
                         @if(session('StudentData'))
                         <?php
                         $City = array('الإسكندرية','الإسماعيلية','أسوان','أسيوط','الأقصر','البحر الأحمر','البحيرة','بني سويف','بورسعيد','جنوب سيناء','الجيزة','الدقهلية','دمياط','سوهاج','السويس','الشرقية','شمال سيناء','الغربية','الفيوم','القاهرة','القليوبية','قنا','كفر الشيخ','مطروح','المنوفية','المنيا','الوادي الجديد');
                         ?>
                        <div id="DIV{{session('StudentData')->id}}" class="col-12 grid-margin">
                                <div class="card">
                                  <div class="card-body">
                                    <div class="fluid-container">
                                      <div class="row ticket-card mt-3 pb-2 mb-3">
                                        <div class="col-md-1">
                                          <img class="img-sm rounded-circle mb-4 mb-md-0" src="images/student.jpg">
                                        </div>
                                       
                                        <div class="ticket-details col-md-12">
                                          <div class="d-flex">
                                            <p class="text-dark font-weight-semibold mr-2 mb-0 no-wrap zag">{{session('StudentData')->Fullname}}</p>
                                            
                                          </div>
                                          <div class="col-12 tx-right">
                                                <p style="color: #1c65a5; margin-top: 20px;font-weight: bold;">{{session('Year')}}</p>
                                            </div>
                                          <p class="text-gray ellipsis mb-2" style="font-weight: bold;    font-size: 18px;">رقم الهاتف : {{session('StudentData')->Phone}}<br>المدينة : {{$City[session('StudentData')->City]}}<br>رقم الكود : {{session('StudentData')->code}}</p>
                                          <div class="row text-gray d-md-flex ">
                                            <?php $arrTimes = explode(' ',session('StudentData')->created_at); ?>
                                            <div class="col-12 ">
                                              <small class="mb-0 mr-2 text-muted text-muted">{{$arrTimes[1]}}</small>
                                            </div>
                                            <div class="col-12 ">
                                              <small class="mb-0 mr-2 text-muted text-muted">{{$arrTimes[0]}}</small>
                                            </div>
                                          </div>
                                          <button onclick="Delete({{session('StudentData')->id}},{{session('YearNo')}})" id="btnForDelete">حذف</button>
                                        </div>
                                        
                                      </div>
                              
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endif
                        <!-- ---------------------ونهاية اللوب هنا ---------------- -->
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
    <script type="text/javascript">
        function Delete(ID,YearNo){
            $("#btnForDelete").attr('disabled','disabled');
            $("#btnForDelete").html('يتم الحذف');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/DeletePerson", 
                data: {_token:"{{csrf_token()}}" ,id:ID , YearNo:YearNo}
                }).done( function(data){
            if(data[0] == "success" ){
                $("#DIV"+ID).hide('slow');
            }
         }); 
        }
        
    </script>
    @if(session('error'))
    <script>
        alert('لا يوجد هذا الطالب')
    </script>
    @endif
</body>

</html>