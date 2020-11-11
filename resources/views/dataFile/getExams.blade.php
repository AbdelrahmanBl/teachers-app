<?php
if( Cookie::get('YEAR') && session('ExamData') ){
    
    $arr = array(
	explode('&%', session('ExamData')->QType ),     //0
	explode('&%', session('ExamData')->mainQ ),		//1	
	explode('&%', session('ExamData')->Q ),			//2
	explode('&%', session('ExamData')->R ),			//3
	explode('&%', session('ExamData')->TR ),		//4
	explode('&%', session('ExamData')->QDegree ),	//5
	session('ExamData')->Type ,	                    //6
	);
	session()->put('EData',$arr);
}	
?>
@if(session('EData') && Cookie::get('YEAR') )
@for($i = 1 ; $i < count(session('EData')[0]) ; $i++ )
@if(session('EData')[0][$i] == 'W' )
               <div class="article" style="padding: 3px">
                   <article class="s_article" id="s_article0"><span class="number">{{$i}}</span>
                       <div class="ql-editor ql-blank" data-gramm="false" contenteditable="false" id="myEditor{{$i}}"><? echo session('EData')[1][$i]; ?></div>
                    </article>
                </div>
        <textarea class="textarea" placeholder="ضع إجابتك هنا ..." name="R{{$i}}" id="R{{$i}}" ></textarea>
@else             <!-- ================================ -->
<div>
    <div class="article" style="padding: 3px; margin-top: 50px; border-top: 1px solid #ccc">
        <article class="s_article" id="s_article0" @if(session('EData')[6] == 2) style="direction: ltr;" @endif>
            @if(session('EData')[1][$i] != '<p><br></p>' && session('EData')[1][$i] != '<p class="ql-direction-rtl ql-align-right"><br></p>' )
            <div class="ql-editor ql-blank" data-gramm="false" contenteditable="false" id="myEditor{{$i}}"><? echo session('EData')[1][$i]; ?></div>
            @endif
            <span class="number">{{$i}}</span>
                <div class="mcq" style="margin-top: 8px">
                    <h3 class="heading_mcq" id="heading_mcq" @if(session('EData')[6] == 2) style="text-align:left" @endif >{{session('EData')[2][$i]}}</h3>
                </div>
                <?php
                	$R = explode('%S', session('EData')[3][$i] );
                ?>
                @for( $x = 1 ; $x < count($R) ; $x++ )
        		<div class="E" style="padding: 8px;">
                   <div class="here" >
                       <input class="input" id="R{{$i}}" value="{{$R[$x]}}" name="R{{$i}}" type="radio" @if(session('EData')[6] == 2) style="margin-right: 6px;" @endif > <span style="padding-right: 10px" class="word">{{$R[$x]}}</span>
                   </div>
        		</div>
        		@endfor
        </article>
    </div>
</div>
@endif
<hr>                                                
@endfor
<input type="hidden" id="Questions" value="{{session('ExamData')->QType}}">
@endif
