@include('Auth/EntryAndAdmin')
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
                        @if(session('Admin') == 1)
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body tx-right">
                                    <form action="">
                                        <div id="exampleContainer" class="trips">

                                            <div class="input-group">
                                                <div class="form-group has-feedback has-clear" style="width: 100%">
                                                    <div class="row">
                                                        <div class="col-sm-12" >
                                                            <label for="">ادخل رسالتك</label>
                                <input id="linkA" placeholder=" ادخل الرابط هنا" type="text" style="border: 1px solid#999;border-radius: 5px;padding: 4px;height: 32px; width: 100%;margin-bottom: 9px;">
                                <textarea id="Message" placeholder="ادخل رسالتك" class="btn-block" style="border: 1px solid #999;
                                                            border-radius: 5px;
                                                            padding: 4px;
                                                            height: 100px;
                                                            margin-bottom: 30px
                                                            "> </textarea> </div>
                                                        <div class="col-sm-3" style="display: inline-block">
                                                            <label for="">المرحلة الدراسية</label>
                                                            <select id="Year" name="" id="exampleInput1">
                                                                <option value="0" disabled selected >اختر المرحلة الدراسية</option>
                                                                <option value="1">الصف الاول الثانوي</option>
                                                                <option value="2">الصف الثاني الثانوي</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2" style="display: inline-block">
                                                            <span class="input-group-btn">
                                                                <button onclick="Publish()" id="publishbtn" type="button" class="btn btn-primary"
                                                                    id="exampleButton1">نشر</button>
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
                        @endif
                        <div id="push"></div>
                        <!-- -----------------لما تعمل لوب اعملها من هنا ----------------- -->
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
                                          <div class="d-flex">
                                            <p class="text-dark font-weight-semibold mr-2 mb-0 no-wrap zag">Ostazy</p>
                                            
                                          </div>
                                          <div class="col-12 tx-right">
                                                <p style="color: #1c65a5; margin-top: 20px;font-weight: bold;">مرسل الي : @if($row->year == 1) الصف الاول الثانوي @elseif($row->year == 2) الصف الثاني الثانوي @endif </p>
                                            </div>
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
                                          @if(session('Admin') == 1)
                                          <button onclick="Delete({{$row->ID}})" id="btnForDelete">حذف</button>
                                          @endif
                                        </div>
                                        
                                      </div>
                              
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endforeach
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
        function Delete(ID){
            $("#btnForDelete").attr('disabled','disabled');
            $("#btnForDelete").html('يتم الحذف');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/DeleteMessage", 
                data: {_token:"{{csrf_token()}}" ,ID:ID }
                }).done( function(data){
            if(data[0] == "success" ){
                $("#DIV"+ID).hide('slow');
            }
         }); 
        }
        function Publish(){
             $("#publishbtn").attr('disabled','disabled');
                $("#publishbtn").html('يتم النشر'); 
            var Message = $("#Message").val();
            var Year     = $("#Year").val();
            var link    = $('#linkA').val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/publishMessage", 
                data: {_token:"{{csrf_token()}}" , Message : Message , Year:Year , Link:link }
                }).done( function(data){
            if(data[0] == "success" ){ 
                location.reload()
            }
            else {
              alert(' لقد حدثت مشكلة برجاء المحاولة مرة اخري');
            }
            });
        }
    </script>
</body>

</html>