<?php
    $cookie_token = Cookie::get('ACCESS_TOKEN');
    $cookie_year  = Cookie::get('YEAR');
	($cookie_year == 1) ? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
	if($DB){
    DB::disconnect('mysql');
    Config::set('database.connections.mysql.database', $DB);

    $User = DB::table('solves')->where('ACCESS_TOKEN','=',$cookie_token)->where('materialCode','=',session('code'))->orderBy('ID','DESC')->get();
    session()->put('Exams',$User);
	}
?>
@if(session('Exams') )
<div class="card">
@foreach( session('Exams') as $exam )
<div class="card-body">
    <div class="row" id="row" >
        <div class="col-md-9">
            <p class="exam">اسم الامتحان : <span>{{$exam->examName}}</span></p>
        </div>
        <div class="col-md-9">
            @if( $exam->Total != NULL)
            <p class="exam">درجة الامتحان: <span>{{$exam->examDegree}}/{{$exam->Total}}</span></p>
            @else
            <p class="exam">درجة الامتحان: <span>لم يتم تأدية الامتحان</span></p>
            @endif
        </div>
        <div class="col-md-3">
        	<form method="post" action="viewExam2" id="viewForm{{$loop->iteration}}">
        		{{CSRF_field()}}
            <button type="button" onclick="viewLoading({{$loop->iteration}})" class="btn-primary btn-block">عرض</button>
            <input type="hidden" name="id" value="{{$exam->ID}}">
        	</form>
        </div>
    </div>  
</div>
@endforeach
</div>
@endif