<div id="examNameandTime" class="examNameandTime E5" >
<div>اسم الامتحان : {{session('Edit')[6]}}</div>
<div class="E6">وقت الامتحان : <input id="inputETime" onfocus="$(this).css('border','1px solid #DDD');" onfocusout="updateETime()" class="Bl10" type="text" value="{{session('Edit')[7]}}"></div>
</div>
<div class="examNameandTime EA" >
<button id="back" class="btn">اضافة سؤال</button>
</div>
@for($i = 1 ; $i < count(session('Edit')[0]) ; $i++)
    @if(session('Edit')[0][$i] == 'W' )
<div class="article" id="Question{{$i}}" >
                        <article class="s_article" id="s_article0">
                        <span class="number" >{{$i}}</span>
                        <input type="hidden" value="{{session('Edit')[5][$i]}}" readonly  id="examMark{{$i}}">
                        <div class="ql-editor ql-blank" style="margin-right: 0px;" data-gramm="false" contenteditable="false" id="myEditor{{$i}}"><?php echo session('Edit')[1][$i] ?></div></article>   
                        <input type="hidden" id="R{{$i}}" value="{{session('Edit')[4][$i]}}"/>
                        <input type="hidden" id="T{{$i}}" value="{{session('Edit')[0][$i]}}"/>
                        <div class="options" >
                                <button class="btn20"   class="myEdit btn btn-primary" id="D{{$i}}" onclick = "editQ({{$i}})" >تعديل</button>
                                <button class="btn2" onclick = "deleteQ({{$i}})" id="D{{$i}}">حذف</button>
                        </div>
                    </div>
    @else       
                    <div class="mcq" id="Question{{$i}}" >
                       @if(session('Edit')[1][$i] != "<p><br></p>" || session('Edit')[1][$i] != '<p class="ql-direction-rtl ql-align-right"><br></p>' )
                        <article class="s_article" id="s_article0" @if(session('Edit')[8] == 2) style="direction: ltr;" @endif>
                            <input type="hidden" value="{{session('Edit')[5][$i]}}" readonly  id="examMark{{$i}}">
                            <div  class="ql-editor ql-blank" style="margin-right: 0px;" data-gramm="false" contenteditable="false" id="myEditor{{$i}}"><?php echo session('Edit')[1][$i] ?></div><span class="number" >{{$i}}</span></article>
                        @endif
                            <h3 class="heading_now alert alert-info" id="Q{{$i}}" @if(session('Edit')[8] == 2) style="direction: ltr;text-align: left;" @endif >{{session('Edit')[2][$i]}}</h3>
                        <div class="E2" id="ANSWERS{{$i}}" >
                            <?php $R = explode('%S', session('Edit')[3][$i] ); ?>
                            @for($x = 1 ; $x < count($R) ; $x++)
                            <div class="here" @if(session('Edit')[8] == 2) style="justify-content: flex-end;" @endif ><span class="word"></span><input class="Bl14"  @if(session('Edit')[8] == 2) style="order: 1;margin-right: 6px;" @endif value="س" name="R1" type="radio">{{$R[$x]}}</div>
                            @endfor
                        </div>
                        <div class="options" >
                        <button class="myEdit" id="D{{$i}}" id="myEdit" onclick="editQ({{$i}})">تعديل</button>
                            <button   class="myDelete" id="D{{$i}}" id="myDelete" onclick = "deleteQ({{$i}})">حذف</button>
                        </div>
                        <input type="hidden" id="T{{$i}}" value="{{session('Edit')[0][$i]}}" >
                        <input type="hidden" id="TR{{$i}}" value="{{session('Edit')[4][$i]}}" >
                        <input type="hidden" id="R{{$i}}" value="{{session('Edit')[3][$i]}}" >
                    </div>
    @endif                
@endfor         
<script>
    function updateETime(){
        var  examName = $("#examName2").val();
        var  ETime    = $("#inputETime").val();   
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/updateETime", 
            data: {_token:"{{csrf_token()}}", examName : examName , ETime : ETime }
        }).done( function(data){
            if(data[0] == 'success' ){
                $("#inputETime").css("border","3px solid #47e22b");
            }else $("#inputETime").css("border","3px solid red");
        });
    }
    function deleteQ(ID){
        $("#D"+ID).attr("disabled","disabled");
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "/deleteQ", 
            data: {_token:"{{csrf_token()}}", QID : ID  }
        }).done( function(data){
            if(data[0] == 'success' ){
                //$('#Question'+ID).hide('slow');
                location.href = 'bank';
            }
            else if(data[0] == 1)
                alert("غير مسموح");
            else {alert("لقد حدثت مشكلة من فضلك حاول مرة اخري");
            location.href='bank';
            } 
        });
    }
</script>           