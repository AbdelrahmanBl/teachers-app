<?php
$cookie_token = Cookie::get('ACCESS_TOKEN');
$cookie_year  = Cookie::get('YEAR');
($cookie_year == 1) ? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
if( $DB ){
DB::disconnect('mysql');
Config::set('database.connections.mysql.database', $DB);
$Exam    = session('Exam1');
/*       getExamDbByCode          */
		if(session('code') == 'ar'){
	        $table = "arabic";
	    }
	    else if(session('code') == 'en'){
	        $table = "english";
	    }
	    else if(session('code') == 'fr'){
	        $table = "french";
	    }
	    else if(session('code') == 'gr'){
	        $table = "germany";
	    }
	    else if(session('code') == 'ph'){
	        $table = "physics";
	    }
	    else if(session('code') == 'ch'){
	        $table = "chemistry";
	    }
	    else if(session('code') == 'ma'){
	        $table = "math";
	    }
	    else if(session('code') == 'bi'){
	        $table = "biology";
	    }
	    else if(session('code') == 'hi'){
	        $table = "history";
	    }
	    else if(session('code') == 'ge'){
	        $table = "geographic";
	    }
	    else if(session('code') == 'pl'){
	        $table = "philosophyandlogic";
	    }
	    /*--------------------------------*/
$User    = DB::table($table)->where('examName', $Exam->examName )->first();
if($User){
$arr     = array(
       	explode('&%', $User->QType ),	   				//0
       	explode('&%', $User->mainQ ),	   				//1
      	explode('&%', $User->Q ),		   				//2
      	explode('&%', $User->R ),		   				//3
       	explode('&%', $User->TR ),		   				//4
      	explode('&%', $User->QDegree ),	   				//5
        $User->examName ,					   			//6
        $User->ETime ,				   				    //7
        explode('&%', $Exam->Responds ) ,	            //8
        explode('&%', $Exam->studentDegree ),           //9
        $Exam->examDegree ,                             //10
        $Exam->Total  ,                                 //11
        $User->Type                                     //12
        );
session()->put('Edit1',$arr);
}
}
?>
@if(session('Edit1'))
<div id="examNameandTime" class="examNameandTime" >
<div>اسم الامتحان : {{session('Edit1')[6]}}</div>
<hr>
<div style="@if(session('Edit1')[11] == NULL) display: none; @endif">درجة الامتحان : {{session('Edit1')[10]}}/{{session('Edit1')[11]}}</div>
</div>
<form class="form" method="post" action="exitStudentView" id="exitStudentForm">
	{{CSRF_field()}}
	<button class="Bl11" onclick="exitStudentLoad()" type="button">خروج</button>
</form>

@for($i = 1 ; $i < count(session('Edit1')[0]) ; $i++)
    @if(session('Edit1')[0][$i] == 'W' )
<div class="article" id="Question{{$i}}" >
                        <article class="s_article" id="s_article0">
                            <span class="number" >{{$i}}</span>
                            
                            <div class="ql-editor ql-blank" data-gramm="false" contenteditable="false" id="myEditor{{$i}}">
                                <?php echo session('Edit1')[1][$i] ?></div></article>   
                                <textarea readonly class="txtArea">{{session('Edit1')[8][$i]}}</textarea>
                                <textarea readonly class="txtArea">الاجابة النموذجية :&#10;{{session('Edit1')[4][$i]}}</textarea>
                        <input type="hidden" id="R{{$i}}" value="{{session('Edit1')[4][$i]}}"/>
                        <input type="hidden" id="T{{$i}}" value="{{session('Edit1')[0][$i]}}"/>
                    <article class="s_article" id="s_article0">
                    <h3 class="E5">الدرجة : <input readonly class="E3" value="{{session('Edit1')[9][$i]}}/{{session('Edit1')[5][$i]}}" type="text" name="QDegree{{$i}}"></h3> 
                    </article>
                    </div>
                    
    @else       
                    <div class="mcq" id="Question{{$i}}" >
                       @if(session('Edit1')[1][$i] != "<p><br></p>" || session('Edit1')[1][$i] != '<p class="ql-direction-rtl ql-align-right"><br></p>' )
                        <article class="s_article" id="s_article0" @if(session('Edit1')[12] == 2) style="direction: ltr;" @endif>
                        	
                        	<div class="ql-editor ql-blank" data-gramm="false" contenteditable="false" id="myEditor{{$i}}"><?php echo session('Edit1')[1][$i] ?></div><span class="number" >{{$i}}</span></article>
                        @endif
                            <h3 class="heading_now alert alert-info" id="Q{{$i}}" @if(session('Edit1')[12] == 2) style="direction: ltr;text-align: left;" @endif >{{session('Edit1')[2][$i]}}</h3>
                        <div class="E2" @if(session('Edit1')[12] == 2) style="direction:ltr" @endif >
                            <?php $R = explode('%S', session('Edit1')[3][$i] ); ?>
                            @for($x = 1 ; $x < count($R) ; $x++)
                            
                            <div  class="here" style="@if(session('Edit1')[8][$i] == session('Edit1')[4][$i] && session('Edit1')[8][$i] == $R[$x] )background-color: #46da86;@elseif(session('Edit1')[8][$i] != session('Edit1')[4][$i] && session('Edit1')[8][$i] == $R[$x])background-color: #ff6e6e; @elseif(session('Edit1')[4][$i] == $R[$x] ) background-color: #46da86; @endif" >
                                <span class="word"  @if(session('Edit1')[12] == 2) style="order: 1;margin-right: 6px;" @endif></span>
                                {{$R[$x]}}
                            </div>
                            @endfor
                        </div>
                        <article class="s_article" id="s_article0">
                        <h3 @if(session('Edit1')[12] == 2) style="text-align:right" @endif class="E5">الدرجة : <input class="E3" value="{{session('Edit1')[9][$i]}}/{{session('Edit1')[5][$i]}}" readonly  type="text" name="QDegree{{$i}}"></h3>
                        </article>
                        <input type="hidden" id="T{{$i}}" value="{{session('Edit1')[0][$i]}}" >
                        <input type="hidden" id="TR{{$i}}" value="{{session('Edit1')[4][$i]}}" >
                        <input type="hidden" id="R{{$i}}" value="{{session('Edit1')[3][$i]}}" >
                    </div>
    @endif  
    <hr>
@endfor 
@endif        