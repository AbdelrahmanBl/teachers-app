@include('Auth/EntryAndAdmin')
@include('Auth/RefuseOrdinaryEntry')
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
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=Cairo" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Amiri&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/styleUI.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            @include('layouts/header2')
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
               @include('layouts/app')
            <!-- partial -->
            <div class="main-panel" id="NAVCLOSE">
                <div class="content-wrapper">
                       <!-------------------------- ADD Question ------------------------------>
                      @include('dataFile/AddQuestion')
                      <!------------------------------------------------------------------------>
                    <div id="DataContent" class="row purchace-popup">
                <h3 class="Bl1" >
                    وضع الامتحان الآن لكي يكون الطالب قادرًا على حل الامتحان</h3>
                <p class="Bl2">
                    قم باستخدام التطبيق التالي في كتابة الامتحان بطريقة مُنظمة مُنسقة ثم قم بعمل نشر للامتحانات حتى يصل
                    الامتحان للطلبة ويقوموا بحل الامتحان وارساله للمعلم</p>
                <p id="Allow" style="display: none;" class="Bl2">يسمح بنشر هذا الامتحان وتعديل الوقت فقط و غير مسموح بالتعديل علي هذا الامتحان برجاء التعديل علي نسخة اضافية</p> 
                    @if( !session('Edit') )
                    @if(!session('Teacher'))
                        <div class="card">
                        @include('student/materialsForBank')
                        </div>
                    @else
                    <div class="search_now" id="seach_now">
                        <div  method="post" class="E3">
                          {{CSRF_field()}}
                            <input class="E4"  onkeyup="smartSearch()" id="tags" type="text" placeholder="ادخل اسم الامتحان ">
                            <button type="button" onclick="searchExam()" class="btn-search" id="btn-search">بحث</button>
                        </div>
                    </div>
                    @if(session('Admin') == 1)
                    <form method="post" action="backEX" style="display:flex;justify-content:center">
                        {{CSRF_field()}}
                    <button type="submit" class="anchor btn btn-info" style="margin-bottom: 10px;">رجوع</button>
                    </form>
                    @endif
                     @include('dataFile/displayExams') 
                    @endif 
                @else
                <div class="groupTimePublish" >
                    <form class="form9" method="post" action="endEdit">
                        {{CSRF_field()}}
                      <button class="Bl3" type="submit" >رجوع</button>
                    </form>
                </div>
                   <div  class="Bl0">
                   <!--{{CSRF_field()}}--> 
                   <select id="Year" disabled class="mySelect">
                            <option @if(session('Teacher')[1] == 1) selected @endif value="1">الصف الاول الثانوي</option>
                            <option @if(session('Teacher')[1] == 2) selected @endif value="2">الصف الثاني الثانوي</option>
                    </select>
                    <select id="Material" disabled class="mySelect">
                    <option @if(session('Teacher')[0] == 1) selected @endif value="ar">اللغة العربية</option>
                    <option @if(session('Teacher')[0] == 2) selected @endif value="en">اللغة الانجليزية</option>
                    <option @if(session('Teacher')[0] == 3) selected @endif value="fr">اللغة الفرنسية</option>
                    <option @if(session('Teacher')[0] == 4) selected @endif value="gr">اللغة الالمانية</option>
                    <option @if(session('Teacher')[0] == 5) selected @endif value="ph">الفيزياء</option>
                    <option @if(session('Teacher')[0] == 6) selected @endif value="ch">الكيمياء</option>
                    <option @if(session('Teacher')[0] == 7) selected @endif value="ma">الرياضيات</option>
                    <option @if(session('Teacher')[0] == 8) selected @endif value="bi">الاحياء</option>
                    <option @if(session('Teacher')[0] == 9) selected @endif value="hi">التاريخ</option>
                    <option @if(session('Teacher')[0] == 10) selected @endif value="ge">الجغرافيا</option>
                    <option @if(session('Teacher')[0] == 11) selected @endif value="pl">الفلسفة والمنطق</option>
                    
                    </select>
                  <button onclick="publish()" id="publish" class="myEdit btn btn-primary" >نشر</button>

                        <input type="hidden" value="{{session('Edit')[6]}}" id="examName2">
                        <input type="hidden" value="{{count(session('Edit')[0])-1}}" id="QuestionNoAll">
                    </div>
                <div class='c_now' id="c_now" >@include('dataFile/editExam')</div>
               
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

<div class="modal_box" id="modal_box" style="display: none;">
        <div class="catch-content" >
            <input class="Bl6" value="{{session('Edit')[7]}}" id="examMark" type="text" placeholder="درجة السؤال" />
            <input type="hidden" id="QuestionNo">
                <div id="toolbar-container">
                   <select class="ql-color">
                                  <option selected></option>
                                  <option value="red"></option>
                                  <option value="orange"></option>
                                  <option value="yellow"></option>
                                  <option value="green"></option>
                                  <option value="blue"></option>
                                  <option value="purple"></option>
                            </select>
                            <span class="ql-formats">
                                <select class="ql-size">
                                   <option value="10px">10px</option>
                                  <option value="12px">12px</option>
                                  <option value="14px">14px</option>
                                  <option value="16px">16px</option>
                                  <option value="18px">18px</option>
                                  <option value="20px">20px</option>
                                  <option value="22px">22px</option>
                                  <option value="24px">24px</option>
                                  <option value="26px">26px</option>
                                  <option value="28px">28px</option>
                                  <option value="30px">30px</option>
                                  <option value="32px">32px</option>
                                  <option value="34px">34px</option>
                                  <option value="36px">36px</option>
                                  <option value="38px">38px</option>
                                  <option value="40px">40px</option>
                                </select>
                              </span>
                           <select class="ql-font">
                            <option selected disabled>font</option>
                            <option value="amiri">Amiri</option>
                            <option value="cairo">Cairo</option>
                          </select>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-direction" value="rtl" type="button"></button>
                                <select class="ql-align">
                                    <option selected="selected"></option>
                                    <option value="center"></option>
                                    <option value="right"></option>
                                    <option value="justify"></option>
                                </select>
                            </span>
                        </div>
                       <div class="editor" id="editor" ></div>
                       <textarea class="txtArea" id="txtArea" placeholder= "اكتب الإجابة النموذجية" ></textarea>
                       <div class="myDiv-btn" >
                            <button type="button"  id="closeMD" class="btn btn-danger" >إغلاق</button>
                            <span class="error" style="display: none;" id="degreeErr" >برجاء مراجعة البيانات</span>
                            <button type="button" onclick="updateQuestion()" class="btn btn-primary" >حفظ</button>
                       </div>
                    </div>
</div>
<div class="modal_box_mcq" id="modal_box_mcq" style="display: none" >
        <div class="catch-content" >
            <input value="{{session('Edit')[7]}}" class="Bl7" id="mcqMark" type="text" placeholder="درجة السؤال" />
            <input type="hidden" id="MCQNo">
                <div id="toolbar-containerLast">
                   <select class="ql-color">
                                  <option selected></option>
                                  <option value="red"></option>
                                  <option value="orange"></option>
                                  <option value="yellow"></option>
                                  <option value="green"></option>
                                  <option value="blue"></option>
                                  <option value="purple"></option>
                            </select>
                            <span class="ql-formats">
                                <select class="ql-size">
                                   <option value="10px">10px</option>
                                  <option value="12px">12px</option>
                                  <option value="14px">14px</option>
                                  <option value="16px">16px</option>
                                  <option value="18px">18px</option>
                                  <option value="20px">20px</option>
                                  <option value="22px">22px</option>
                                  <option value="24px">24px</option>
                                  <option value="26px">26px</option>
                                  <option value="28px">28px</option>
                                  <option value="30px">30px</option>
                                  <option value="32px">32px</option>
                                  <option value="34px">34px</option>
                                  <option value="36px">36px</option>
                                  <option value="38px">38px</option>
                                  <option value="40px">40px</option>
                                </select>
                              </span>
                           <select class="ql-font">
                            <option selected disabled>font</option>
                            <option value="amiri">Amiri</option>
                            <option value="cairo">Cairo</option>
                          </select>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-direction" value="rtl" type="button"></button>
                                <select class="ql-align">
                                    <option selected="selected"></option>
                                    <option value="center"></option>
                                    <option value="right"></option>
                                    <option value="justify"></option>
                                </select>
                            </span>
                        </div>
                       <div id="editorLast" class="editorLast" ></div>
                       <div class="nocomplete" >
                        <div class="mcq_div" id="mcq_div" >
                            <div class="Bl8">
                                <button class="addAns" id="addAns" >+</button>
                                <input class="qN" id="qN" type="text" placeholder="اكتب سؤالًا" >
                                <input class="aN" id="aN" type="text" placeholder="اكتب الإجابة" >
                            </div>
                            <div class="answers" id="answers" ></div>
                        </div>
                    </div>
                       <div class="myDiv-btn" >
                            <button type="button" id="close_modal_box" class="btn btn-danger" >إغلاق</button>
                            <span class="error" style="display:none " id="degreeErr2" >برجاء مراجعة البيانات</span>
                            <button type="button" id="updateMCQ" class="btn btn-primary" >حفظ</button>
                       </div>
                    </div>
  </div>
    <!-- plugins:js -->
    <!--<script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>-->
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
   <!-- <script src="js/misc.js"></script>-->
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <!-- End custom js for this page-->
    <script>
        'use strict';
        $(".article").on("mousemove", function() {
            $(this).children(".options").css("display", "flex");
        });
        $(".article").on("mouseleave", function() {
            $(this).children(".options").css("display", "none");
        });
        $(".article").on("click", function(e) {
            if (e.target.className == "myDelete") {
                $(e.target).parent().parent().remove();
            }
        });
    </script>
    <script>
        'use strict';
        $(".mcq").on("mousemove", function() {
            $(this).children(".options").css("display", "flex");
        });
        $(".mcq").on("mouseleave", function() {
            $(this).children(".options").css("display", "none");
        });
        $(".mcq").on("click", function(e) {
            if (e.target.className == "myDelete") {
                $(e.target).parent().parent().remove();
            }
        });
    </script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            var Size = Quill.import('attributors/style/size');
            Size.whitelist = ['10px', '12px', '14px', '16px', '18px', '20px', '22px', '24px', '26px', '28px', '30px', '32px', '34px', '36px', '38px', '40px'];
            Quill.register(Size, true);
            
            var ColorClass = Quill.import('attributors/class/color');
            Quill.register(ColorClass, true);
            
            var Font = Quill.import('formats/font');
            Font.whitelist = ['amiri', 'cairo'];
            Quill.register(Font, true);

        var quill = new Quill('#editor', {
          modules: {
            toolbar: '#toolbar-container'            
          },
          theme: 'snow',
        });

        var quillLast = new Quill('#editorLast', {
          modules: {
            toolbar: '#toolbar-containerLast'            
          },
          theme: 'snow',
        });

        var quillLast2 = new Quill('#editorAdd', {
          modules: {
            toolbar: '#toolbar-containerAdd'            
          },
          theme: 'snow',
        });

       quillLast.format("direction", "rtl");
       quillLast.format("align", "right");
       quillLast2.format("direction", "rtl");
       quillLast2.format("align", "right");
       quill.format('direction', 'rtl');
       quill.format('align', 'right');
        </script>
        <script>
            function editQ(myID) {
                var type = $("#T" + myID).val();
                if(type == "W") {
                    $(".modal_box").css("display", "flex");
                    $("#examMark").val($("#examMark"+myID).val());
                    $('#editor .ql-editor').html($('#myEditor'+myID).html());
                    $('#txtArea').val($('#R'+myID).val());
                    $('#QuestionNo').val(myID);
                }
                else{
                    $('.modal_box_mcq').css("display", "flex");
                    $("#mcqMark").val($("#examMark"+myID).val());
                    $('#editorLast .ql-editor').html($('#myEditor'+myID).html());
                    $('#qN').val($('#Q'+myID).html())
                    $('#aN').val($('#TR'+myID).val())
                    $('#MCQNo').val(myID);
                    var r = $('#R'+myID).val().split('%S');
                    for(var i = 1 ; i < r.length ; i++ ){
                        $("#addAns").click();
                        $("#ans"+i).val(r[i]);
                    }

                }
            }
            $("#closeMD").on("click", function() {
                $(".modal_box").fadeOut("slow");
                $("#examMark").val("");
                $("#editor").find(".ql-editor").html("");
                $('#txtArea').val("");
                $('#degreeErr').hide();
            });

        </script>
        <script>
            var counter = 0;
            $(document).ready(function() {
                

            $("#close_modal_box").on("click", function() {
                $(".modal_box_mcq").fadeOut("slow");
                $("#mcqMark").val("");
                $("#editorLast").find(".ql-editor").html("");
                $('#qN').val('')
                $('#aN').val('')
                $("#answers").empty()
                $("#degreeErr2").hide()
                counter = 0;
                
            });
///////////////////////////////////////
              //var counter = 0;
            'use strict';
            $(document).ready(function() {
                $(".btnForClose").on("click", function() {
                    $("#examMarkAdd").val('');
                    $("#editorAdd").find(".ql-editor").html('');
                    if($("#writing").is(":checked")){
                    $('#degreeErr').html('');
                    $('#txtAreaAdd').val('');
                    }
                    else{
                        $('#degreeErr2').html('');
                        $('#qNAdd').val('');
                        $('#aNAdd').val('');
                        $('#answersAdd').empty();
                        counter = 0;
                        arr = [];
                    }
                    //alert());
                    $("#preview").css("display","none");
                    $("#DataContent").fadeIn(250);

                });
                $("#back").on("click", function() {
                    $("#DataContent").css("display", "none");
                    $("#preview").fadeIn(250);
                    $("#mcqAdd").click();
                });
                $("#mcqAdd").on("click", function() {
                    $(".nocompleteAdd").fadeIn("slow");
                    $(".WritingQ").find("#txtAreaAdd").fadeOut("slow");
                    $(".wea").fadeIn("slow");
                    $(".btnForWriting_div").fadeOut("slow");
                });
                $(".writing").on("click", function() {
                    $(".WritingQ").find("#txtAreaAdd").fadeIn("slow");
                    $(".nocompleteAdd").fadeOut("slow");
                    $(".wea").fadeOut("slow");
                    $(".btnForWriting_div").fadeIn("slow");
                });
            $("#addAnsAdd").on("click", function() {
                counter++;
                var div_input = $("<div>").attr({
                    class: "div" + counter,
                    id: "div" + counter,
                    style: "height: auto; width: 100%; position: relative; margin-top: 10px"
                });
                var myInput = $("<input>").attr({
                    class: "ans" + counter,
                    id: "ansAdd" + counter,
                    placeholder: "اكتب الإجابة ",
                    style: "text-align: right; font-family: Cairo; font-size: 16px; width: 100%; padding-right: 8px; border-right: 1px solid #DDD; height: 40px;  border: 0;outline: none; font-size: 14px; border: 1px solid #DDD; border-radius: 6px; box-shadow: inset 0 0 8px #efefef"
                });
                var myBBB_tn = $("<button>").attr({
                    class: "btn_ddd",
                    style: "position: absolute; top: 10px; left: 10px; height: 20px; width: 20px; border-radius: 50%; color: #FFF; border: none; outline: none; background-color: #ff7373; font-size: 18px; cursor: pointer; box-shadow: 0 5px 8px #888"
                }).text("-");
                div_input.append(myInput, myBBB_tn);
                $("#answersAdd").css("display", "block");
                $("#answersAdd").append(div_input);
                $("#answersAdd").click(function(e) {
                    if(e.target.className == "btn_ddd") {
                        $(e.target).parent().remove();
                    }
                });
            });
            $("#addAns").on("click", function() {
                counter++;
                var div_input = $("<div>").attr({
                    class: "div" + counter,
                    id: "div" + counter,
                    style: "height: auto; width: 100%; position: relative; margin-top: 10px"
                });
                var myInput = $("<input>").attr({
                    class: "ans" + counter,
                    id: "ans" + counter,
                    placeholder: "اكتب الإجابة ",
                    style: "text-align: right; font-family: Cairo; font-size: 16px; width: 100%; padding-right: 8px; border-right: 1px solid #DDD; height: 40px;  border: 0;outline: none; font-size: 14px; border: 1px solid #DDD; border-radius: 6px; box-shadow: inset 0 0 8px #efefef"
                });
                var myBBB_tn = $("<button>").attr({
                    class: "btn_ddd",
                    style: "position: absolute; top: 10px; left: 10px; height: 20px; width: 20px; border-radius: 50%; color: #FFF; border: none; outline: none; background-color: #ff7373; font-size: 18px; cursor: pointer; box-shadow: 0 5px 8px #888"
                }).text("-");
                div_input.append(myInput, myBBB_tn);
                $("#answers").css("display", "block");
                $("#answers").append(div_input);
                $("#answers").click(function(e) {
                    if(e.target.className == "btn_ddd") {
                        $(e.target).parent().remove();
                    }
                });
            });
            });
            $(".ql-editor").last().attr("id","myEditor");

            ///////////////////////////////////
            $("#updateMCQ").on("click", function() {

    /////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////

            var arr = [];
            var ID = $("#MCQNo").val();
            var degree = $('#mcqMark').val();
            var Question = $('#qN').val();
            var TR = $('#aN').val();
            var Main = $('#editorLast .ql-editor').html();
                if( Main == "<p><br></p>" || Main == '<p class="ql-direction-rtl ql-align-right"><br></p>' )
                Main = "";
            for(var i = 1 ; i <= counter ; i++){
            //alert(counter);
            if( typeof( $("#ans"+i).val() ) === 'undefined' || $("#ans"+i).val() == ""  )
            continue;
            arr.push($("#ans"+i).val())   
            }
            //console.log(arr);
            if(arr.length == 0 ){
                $('#degreeErr2').hide();
                $('#degreeErr2').show('slow');
                $('#degreeErr2').html('برجاء وضع الاجابات');
                return 0;
            }
            if(!arr.includes(TR)){
                $('#degreeErr2').hide();
                $('#degreeErr2').show('slow');
                $('#degreeErr2').html('لا توجد اجابة مشابهة للاجابة الصحيحة');
                arr = [];
                return 0;
            }


            $.ajax({
            type: "POST",
            dataType: "json",
            url: "/updateMCQ", 
            data: {_token:"{{csrf_token()}}", degree : degree , Question : Question , TR : TR , Main : Main , Responds : arr , ID : ID }
        }).done( function(data){
            if(data[0] == "success" ){
                $("#examMark"+ID).val(degree);
                $("#myEditor"+ID).html(Main);
                $("#Q"+ID).html(Question);
                $("#TR"+ID).val(TR);
                $("#R"+ID).val(data[1]);
                $("#ANSWERS"+ID).empty();
                for(var i = 0 ; i < arr.length ; i++ ){
                $("#ANSWERS"+ID).append('<div class="here" style="display: flex; align-items: center"><span class="word"></span><input style="margin-right:6px" type="radio" >'+arr[i]+'</div>');
                }
                if(data[2] == 2)
                $("#ANSWERS"+ID).css('direction','ltr');
                $("#close_modal_box").click();
            }
            else if(data[0] == 1)
                alert("غير مسموح");
            else if( data[0] == 'SAME' )
                $("#close_modal_box").click();
            else{
                arr = [];
                $('#degreeErr2').hide();
                $('#degreeErr2').show('slow');
                //$('#degreeErr2').html('برجاء مراجعة البيانات');
            }
        });

/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
            });
            

            });
        </script>
    <script>
      var GlobalDiv ;
        function publish(){
            var Year     = $('#Year').val();
            var Material = $('#Material').val();
            if(Material == null || Year == null ){
                alert('من فضلك تأكد من إدخال اسم المادة و السنة')
                return 0
            }
            $.ajax({
            type: "POST",
            dataType: "json",
            url: "/setPublish", 
            data: {_token:"{{csrf_token()}}", Year:Year , Material:Material  }
            }).done( function(data){
            if(data[0] == "success" ){
                alert('تم النشر بنجاح')
            }
            else{
                alert('تم نشر هذا الامتحان مسبقا . لإعادة نشره قم بنسخه')
            }
        });
        }
        function pause(){
            var examName = "{{session('Edit')[6]}}"
            $.ajax({
            type: "POST",
            dataType: "json",
            url: "/pause", 
            data: {_token:"{{csrf_token()}}", examName : examName  }
            }).done( function(data){
            if(data[0] == "success" ){
                $("#pause").hide('slow');
            }
        });
        }
      
       
    function searchExam(){
        var examName = $("#tags").val();
       $.ajax({
            type: "POST",
            dataType: "json",
            url: "/searchExam", 
            data: {_token:"{{csrf_token()}}", examName : examName}
        }).done( function(data){
            if(data[0] == "success" ){
              $(document).ready(function(){
                        var $box = $("#Div"+data[1]);
                        $('html, body').animate({
                        scrollTop: $box.offset().top - ($(window).height() - $box.outerHeight(true)) / 2}, 200);
                        $("#Div"+GlobalDiv).css("border","none");
                        $("#Div"+data[1]).css("border","5px solid blue");
                        GlobalDiv = data[1];
                    }); 
                $("Div"+data[1]);
            }
            else alert('لا يوجد هذا الامتحان')
        });
    }
    function smartSearch(){
      var examName = $("#tags").val();
      var availableTags = [] ;
      $.ajax({
            type: "POST",
            dataType: "json",
            url: "/smartSearchBank", 
            data: {_token:"{{csrf_token()}}", search : examName}
        }).done( function(data){
            if(data[0] == 'success' ){
                for(var i = 0 ; i < data[1].length ; i++){
                    availableTags.push(data[1][i]) ;
                }
            }
            $( "#tags" ).autocomplete({
      source: availableTags
    });
        });  
    }
    </script>  
    <script>
    function examsSection(NO){
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/getMaterialInfo", 
            data: {_token:"{{csrf_token()}}", NO : NO}
        }).done( function(data){
            if(data[0] == 'success' ){
                location.reload()
                return false;
            }
           
        });
    }
</script>
</body>

</html>