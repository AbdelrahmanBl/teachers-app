@if(session('ENTRY_TOKEN') )
<nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-profile">
                        <div class="nav-link">
                            <div class="">
                                <div class="profile-image">
                                    <img width="100" height="100" id="profilePic" src="images/profile.jpg" alt="Profile image">
                                </div>
                                <div class="">
                                    <p id="AdminName" class="profile-name">Ostazy</p>
                                    <p class="designation text-muted">مدرس</p>
                                </div>
                            </div>
                            <?php
                                $promotMaterial = "";
                                if(session('Teacher') != NULL)
                                    $promotMaterial = session('Teacher')[0];
                            ?>    
                            @if(session('Admin') == 1 || session('Teacher') != NULL )
                            <button onclick="putExam()" class="btn btn-success btn-block"> وضع امتحان جديد
                                <i class="mdi mdi-plus"></i>
                            </button>
                            @endif
                        </div>
                    </li>
                    
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'home') style="background-color: #c1c1c1;" @endif>
                        <a class="nav-link" href="/home">
                            <i class="menu-icon mdi mdi-television"></i>
                            <span class="menu-title">الرئيسية</span>
                        </a>
                    </li>
                    @if(!session('Teacher'))
                    <li class="nav-item">
                        <a class="nav-link"  href="javascript:;" onclick="approve()" >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title">الحصول علي كود</span>
                        </a>
                    </li>
                    @endif
                    @if(session('Admin') == 1)
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'control') style="background-color: #c1c1c1;" @endif> 
                        <a class="nav-link"  href="control"  >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title">لوحة التحكم الرئيسية</span>
                        </a>
                    </li>
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'numbers') style="background-color: #c1c1c1;" @endif> 
                        <a class="nav-link"  href="numbers"  >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title">ارقام الايصالات</span>
                        </a>
                    </li>
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'delete') style="background-color: #c1c1c1;" @endif> 
                        <a class="nav-link"  href="delete"  >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title">حذف طالب</span>
                        </a>
                    </li>
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'addings') style="background-color: #c1c1c1;" @endif> 
                        <a class="nav-link"  href="addings"  >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title">الامتحانات المضافة</span>
                        </a>
                    </li>
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'bank') style="background-color: #c1c1c1;" @endif>
                        <a class="nav-link"  href="bank" >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title"> بنك الاسئلة الخاص بي </span>
                        </a>
                    </li>
                    @endif
                    @if(session('Teacher') && session('Admin') != 1)
                    <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'bank') style="background-color: #c1c1c1;" @endif>
                        <a class="nav-link"  href="bank" >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title"> بنك الاسئلة الخاص بي </span>
                        </a>
                    </li>
                    @endif
                     <li class="nav-item">
                        <a class="nav-link"  href="SignOut" >
                            <i class="menu-icon mdi mdi-content-copy"></i>
                            <span class="menu-title">تسجيل الخروج</span>
                        </a>
                    </li>
                    <br><br><br><br>
                </ul>
            </nav>
<script>
  function approve(){
      var number = prompt("ادخل رقم الايصال للحصول علي كود");
    if(number == null)
    return 0;
    $.ajax({
            type: "POST",
            dataType: "json",
            url: "/generateCode", 
            data: {_token:"{{csrf_token()}}", number : number}
            }).done( function(data){
            if(data[0] == "success" ){
                alert(data[1])
                console.log(data[1])
            }else if(data[0] == "error" ) alert('الرقم الذي تم ادخاله موجود مسبقاً')
        }); 
  }
  function putExam(){
    var examName = prompt("من فضلك ادخل اسم الامتحان");
    if(examName == null)
    return 0;
   var ETime    = prompt("من فضلك ادخل زمن الامتحان");
   if(ETime == null)
    return 0;
    var Type    = prompt("لوضع الامتحان بالعربية : 1 || لوضع الامتحان باللغات : 2");
   if(Type == null)
    return 0;
    var prompot = "{{$promotMaterial}}"
    if(prompot == "")
    var material = prompt("اختر كود المادة : 1 - اللغة العربية || 2 - اللغة الانجليزية || 3 - اللغة الفرنسية || 4 - اللغة الالمانية || 5 - الفيزياء || 6 - الكيمياء || 7 - الرياضيات || 8 - الاحياء || 9 - التاريخ || 10 - الجغرافيا || 11 - الفلسفة والمنطق") ;
    else var material    = prompot;
   if(material == null)
    return 0;
    var Year    = prompt("اختر الصف الدراسي : 1- الصف الاول الثانوي || 2 - الصف الثاني الثانوي");
   if(Year == null)
    return 0;
            $.ajax({
            type: "POST",
            dataType: "json",
            url: "/chkExam", 
            data: {_token:"{{csrf_token()}}", examName : examName , ETime : ETime , Type:Type , material:material , Year:Year }
            }).done( function(data){
            if(data[0] == "success" ){
                location.href = 'bank';
            }else if(data[0] == "error" ) alert(' من فضلك تحقق من البيانات قد يكون الاسم مكرر أو الزمن غير صالح أو نوع اللغة ')
        }); 
  }
</script>
@elseif(session('Student'))  
<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item nav-profile">
            <div class="nav-link">
              <div class="">
                <div class="profile-image">
                    <img width="100" height="100" id="profilePic" src="images/profile.jpg" alt="Profile image">
                </div>
                <div class="">
                  <p class="profile-name">{{session('Student')}}</p>
                  <p class="designation text-muted">طالب</p>
                </div>
              </div>
              <button type="button" id="BtnExam" onclick="GoToExam()" style="display: none;" class="btn btn-success btn-block">
              </button>
            </div>
          </li>
          <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'home') style="background-color: #c1c1c1;" @endif>
            <a class="nav-link" href="home">
              <i class="menu-icon mdi mdi-television"></i>
              <span class="menu-title">الرئيسية</span>
            </a>
          </li>
          <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'exams' || str_replace('/', '', $_SERVER['REQUEST_URI']) == 'solveExam' ) style="background-color: #c1c1c1;" @endif>
            <a class="nav-link"  href="exams" >
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">الامتحانات </span>
            </a>
          </li>
          <li class="nav-item" @if(str_replace('/', '', $_SERVER['REQUEST_URI']) == 'viewexams') style="background-color: #c1c1c1;" @endif>
            <a class="nav-link"  href="viewexams" >
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">الامتحانات الممتحنة</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" target="_blank" href="https://www.facebook.com/Ostazy.co" >
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">الفيس بوك</span>
            </a>
          </li>
         
          <li class="nav-item">
            <a class="nav-link"  href="tel:+2001148953405" >
              <i class="menu-icon mdi mdi-content-copy"></i>
              <span class="menu-title">الاتصال بنا</span>
            </a>
          </li>
           
          <br><br><br><br>
        </ul>
      </nav>     

@endif            
