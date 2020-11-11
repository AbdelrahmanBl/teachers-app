<?php
if(session('Teacher')){
	$DB = env('SERVER_CODE') ."main" ;
    DB::disconnect('mysql');
    Config::set('database.connections.mysql.database', $DB);
    $User1 = DB::table('exams')->where('material',session('Teacher')[0])->where('Year',1)->orderBy('ID', 'DESC')->get();
    $User2 = DB::table('exams')->where('material',session('Teacher')[0])->where('Year',2)->orderBy('ID', 'DESC')->get();
    session()->put('Exams1',$User1);
    session()->put('Exams2',$User2);
}
?>
@if(session('Exams1') && session('Exams2') )
<div class="links" id="links" >
    <h2 style="text-align:center">الصف الاول الثانوي</h2>
@foreach( session('Exams1') as $exam )
<div class="link" id="Div{{$exam->ID}}" >
	<div class="clickNow" >
		<span class="write-here" id="Name{{$exam->ID}}" >{{$exam->examName}}</span>
	</div>
	<div class="myOptions" id="myOptions" >
		<a role="button" onclick="deleteExam('{{$exam->ID}}');return false;" class="anchor btn btn-danger" >حذف</a>
		<a role="button" onclick="editExam('{{$exam->ID}}')" class="anchor btn btn-info" >تعديل ونشر</a>
		<a role="button" class="anchor_edit btn btn-success" >نسخ </a>
	</div>
	<div class="input_edit" >
			<input  id="exam{{$exam->ID}}" type="text" placeholder="قم بكتابة الاسم" class="enter_info "  >
			<input type="hidden" name="ID" value="{{$exam->ID}}">
			<button type="button" value="تم" onclick="copyExam({{$exam->ID}})" class="btn btn-warning" >تم</button>
			<span class="error" id="error{{$exam->ID}}" >من فضلك ادخل اسم غير مكرر</span>
	</div>
</div>
@endforeach
<h2 style="text-align:center;margin-top: 60px;">الصف الثاني الثانوي</h2>
@foreach( session('Exams2') as $exam )
<div class="link" id="Div{{$exam->ID}}" >
	<div class="clickNow" >
		<span class="write-here" id="Name{{$exam->ID}}" >{{$exam->examName}}</span>
	</div>
	<div class="myOptions" id="myOptions" >
		<a role="button" onclick="deleteExam('{{$exam->ID}}');return false;" class="anchor btn btn-danger" >حذف</a>
		<a role="button" onclick="editExam('{{$exam->ID}}')" class="anchor btn btn-info" >تعديل ونشر</a>
		<a role="button" class="anchor_edit btn btn-success" >نسخ </a>
	</div>
	<div class="input_edit" >
			<input  id="exam{{$exam->ID}}" type="text" placeholder="قم بكتابة الاسم" class="enter_info "  >
			<input type="hidden" name="ID" value="{{$exam->ID}}">
			<button type="button" value="تم" onclick="copyExam({{$exam->ID}})" class="btn btn-warning" >تم</button>
			<span class="error" id="error{{$exam->ID}}" >من فضلك ادخل اسم غير مكرر</span>
	</div>
</div>
@endforeach
</div>
@if(session('error'))
<script>alert('لقد حجثت مشكلة')</script>
@endif
<script>
	'use strict';
	$(".anchor_edit").on("click", function() {
		$(this).parent().next().fadeIn(250);
		$(".myInputNow").attr({
			id: "copy" + $(".link").attr("id")
		});
	});
</script>
<script>
	function editExam(ID){
		//var Name  = $('#Name'+ID).html();
		$.ajax({
            type: "POST",
            dataType: "json",
            url: "/editQuestion", 
            data: {_token:"{{csrf_token()}}", examID : ID  }
        }).done( function(data){
        	if(data[0] == 'success' ){
        		location.href = 'bank' ;
        	}
        	else alert("لقد حدثت مشكلة برجاء المجاولة مرة اخري")
        });
	}
	function deleteExam(ID){
		var r = confirm("اذا قمت بحذف هذا الامتحان سيتم حذفه عند باقي الطلبة اذا تم ارساله من قبل !");
		if(r == true ){
		$.ajax({
            type: "POST",
            dataType: "json",
            url: "/deleteExam", 
            data: {_token:"{{csrf_token()}}", examID : ID  }
        }).done( function(data){
        	if(data[0] == 'success' ){
        		$("#Div"+ID).hide("slow");
        	}
        	//else alert("لقد حدثت مشكلة برجاء المجاولة مرة اخري")
        });
      }
	}
	function copyExam(ID){
		var Name  = $('#exam'+ID).val();
		//var Time  = $('#Time'+ID).val();
		$.ajax({
            type: "POST",
            dataType: "json",
            url: "/copyExam", 
            data: {_token:"{{csrf_token()}}", examID : ID , examName : Name  }
        }).done( function(data){
        	if(data[0] == 'success' ){
        		location.href = 'bank';
        	}
        	else {$("#error"+ID).hide('slow');$("#error"+ID).show('slow')} 
        });
	}
</script>
@endif
