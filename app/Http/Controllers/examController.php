<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Students ; // To Make Fillable Permissions
use App\Exams;
use App\Codes;
use App\Solves;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Cookie;

class examController extends Controller
{
	public function connectDB($DB){
    DB::disconnect('mysql');
    Config::set('database.connections.mysql.database', $DB);    
    }
	public function connectDBwithSession(){
    $DB = session('DB');
    DB::disconnect('mysql');
    Config::set('database.connections.mysql.database', $DB);    
    }
    
    public function backviewexams(Request $req){
        session()->forget('code');
        return json_encode(array('success'));
    }
	public function searchExam(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$examName = $req->examName;
		$search = DB::table('exams')->where('examName',$examName)->first();
		if($search)
			return json_encode(array('success',$search->ID));
		else return json_encode(array('Not'));
	}
	public function generateCode(Request $req){
	    $DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$Email = DB::table('users')->where('ACCESS_TOKEN',session('ENTRY_TOKEN'))->first()->email;
		$arr = explode('@',$Email);
		($arr[1] == 'ostazy.co')? $number = $arr[0] . $req->number : $number = $req->number ;
		$CHK = DB::table('codes')->where('banking',$number)->first();
		if($CHK)
		return json_encode(array("error"));
		//Generate Code
		$chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
    srand((double)microtime()*1000000); 
    $i = 0; 
    $code = '' ; 

    while ($i <= 7) { 
        $num = rand() % 33; 
        $tmp = substr($chars, $num, 1); 
        $code = $code . $tmp; 
        $i++; 
    } 
    $statue = false;
        while($statue != true){
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            if( !DB::table('codes')->where('ACCESS_TOKEN',$token)->first() ){
                 $statue = true;
            }
        }
		$Code = new Codes([
		    'banking'        =>  $number,
		    'code'           =>  $code,
		    'ACCESS_TOKEN'   =>  $token,
		    'Date'           =>  date("Y-m-d"),
		    'Time'           =>  date("h:i:sa"),
		    'Price'          =>  env("PRICEOFMONTH"),
		    'AddingBy'       =>  session('ENTRY_TOKEN')
		    ]);
		if($Code->save())
		return json_encode(array("success",$code));
	}
	public function chkExam(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		
		$examName = $req->examName;
		$Time     = $req->ETime;
		$Type     = $req->Type;
		$material = $req->material;
		$Year     = $req->Year;
		$validator = Validator::make($req->all(),[
			'examName' => 'required||unique:exams',
			'ETime'    => 'required||digits_between:1,3',
			'Type'     => 'required||integer',
			'material' => 'required||integer||min:1||max:11',
			'Year'     => 'required||integer||min:1||max:2',
			]);
		if($validator->passes()){
		$Exam = new Exams([
			'examName' => $examName,
			'ETime'	   => $Time,
			'Type'     => $Type,
			'material' => $material,
			'Year'     => $Year
		]);
		if($Exam->save()){
			$User = DB::table('exams')->where('examName',$examName )->first();
			$arr = array(
       		explode('&%', $User->QType ),	//0
       		explode('&%', $User->mainQ ),	//1
      		explode('&%', $User->Q ),		//2
      		explode('&%', $User->R ),		//3
       		explode('&%', $User->TR ),		//4
      		explode('&%', $User->QDegree ),	//5
      		$User->examName,				//6
        	$User->ETime ,					//7
        	$User->Type ,					//8
        );
        $Teacher = array(
            $material,                      //0    
            $Year,                          //1
        );
        session()->put('Teacher',$Teacher);
        session()->put('Edit',$arr);
			return json_encode(array("success"));
		}}
		return json_encode(array("error"));
	}

	public function addMCQ(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$degree 	=	 $req->degree;
		$Question	= 	 $req->Question;
		$TR			=	 $req->TR;
		$Main 		=  	 $req->Main;
		$Responds	=	 $req->Responds;
		if(!$Responds)
			return json_encode(array("error"));
		$validator = Validator::make($req->all(),[
			'degree'			=>	'required|numeric',
			'Question'			=>	'required',
		 	'TR'				=>	'required'
			]);
		if($validator->passes()){
			$RS = "";
			for( $i=0 ; $i < count($Responds) ; $i++ ){
				$RS .= '%S' . $Responds[$i];
			}
			
			$Data = DB::table('exams')->where('examName', session('Edit')[6] )->first();
			
			$Update = DB::table('exams')->where('examName', session('Edit')[6] )->update([
				'mainQ' 		=> $Data->mainQ.'&%'.$Main,
				'Q'				=> $Data->Q.'&%'.$Question,
				'R'				=> $Data->R.'&%'.$RS,
				'TR'			=> $Data->TR.'&%'.$TR,
				'QDegree'		=> $Data->QDegree.'&%'.$degree,
				'QType'			=> $Data->QType.'&%'.'M'
			]);
			if($Update){
			$User = DB::table('exams')->where('examName',session('Edit')[6])->first();
			$arr = array(
       		explode('&%', $User->QType ),	//0
       		explode('&%', $User->mainQ ),	//1
      		explode('&%', $User->Q ),		//2
      		explode('&%', $User->R ),		//3
       		explode('&%', $User->TR ),		//4
      		explode('&%', $User->QDegree ),	//5
      		$User->examName,				//6
        	$User->ETime ,					//7
        	$User->Type ,					//8
        	);
        	session()->put('Edit',$arr);
        	return json_encode(array("success"));	
			} 
		}
		return json_encode(array("error"));
	}
	public function getExamDbByCode($code){
	    /*       getExamDbByCode          */
		if($code == 'ar'){
	        return "arabic";
	    }
	    else if($code == 'en'){
	        return "english";
	    }
	    else if($code == 'fr'){
	        return "french";
	    }
	    else if($code == 'gr'){
	        return "germany";
	    }
	    else if($code == 'ph'){
	        return "physics";
	    }
	    else if($code == 'ch'){
	        return "chemistry";
	    }
	    else if($code == 'ma'){
	        return "math";
	    }
	    else if($code == 'bi'){
	        return "biology";
	    }
	    else if($code == 'hi'){
	        return "history";
	    }
	    else if($code == 'ge'){
	        return "geographic";
	    }
	    else if($code == 'pl'){
	        return "philosophyandlogic";
	    }
	    /*--------------------------------*/
	}
	public function findExam(Request $req){
	    ( Cookie::get('YEAR') == 1 )? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);
		$exam_code = $req->exam_code;

        $examName = $this->getExamDbByCode($exam_code);
		$exams    = DB::table($examName)->orderBy('ID','DESC');
		if($exams) return json_encode(array('success',$exams->pluck('examName'), $exams->pluck('ETime')  )  );
		else return json_encode(array("Not"));
	}
	public function findStudentExam(Request $req){
		$exam_code = $req->exam_code;
        session()->put('code',$exam_code);
		return json_encode(array('success') );
	}

	public function timer(Request $req){
	    ( Cookie::get('YEAR') == 1 )? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);
	    
	    $materialCode   =  $req->materialCode;
	    $material       =  $req->material;
	    $time           =  $req->time;
	    $examName       =  $this->getExamDbByCode($materialCode);
	    
	    $Exam = DB::table($examName)->where('examName',$material)->first();
	        session()->put('ExamData',$Exam);
			session()->put('time', $time );
			session()->put('materialCode',$materialCode);
			if( session('time')  ){
			return json_encode(array("success"));
			}
		
	return json_encode(array("NOT"));
	}
	public function end(Request $req){
	    if(!session('materialCode'))
	    return redirect('viewexams');
	    ( Cookie::get('YEAR') == 1 )? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);
		
		$QType 		= explode('&%', session('ExamData')->QType );
		$QDegree 	= explode('&%', session('ExamData')->QDegree );
		$TR 		= explode('&%', session('ExamData')->TR);
		$RS 		= "";
		$Total 		= 0 ;
		$Degree 	= "";
		$examDegree = 0;
		//$Marked 	= 1 ;
		for($i = 1 ; $i < count($QType) ; $i++ ){
			$R = 'R'.$i;
			$RS .= "&%" . $req->input("$R");
			$Total += (float)$QDegree[$i];
			if($QType[$i] == 'W' ){
				//$Marked = 0 ;
				if($req->input("$R") != "" ){
					$Degree .= "&%" . $QDegree[$i];
					$examDegree += (float)$QDegree[$i];
				}
				else {
					$Degree .= "&%" . '0';
					$examDegree += 0;
				}	
			}
			else{
				if($TR[$i] == $req->input("$R") ){
					$Degree .= "&%" . $QDegree[$i];
					$examDegree += (float)$QDegree[$i];
				}
				else {
					$Degree .= "&%" . "0";
					$examDegree += 0;
				}
			}	 
		} 
		
		$cookie_token = Cookie::get('ACCESS_TOKEN');
		if(!$cookie_token) return back();
		$EXID = session('ExamData')->examName;
		
		$CHKexamExist = DB::table('solves')->where('ACCESS_TOKEN','=',$cookie_token)->where('examName','=',$EXID)->where('materialCode','=',session('materialCode'));    
		if($CHKexamExist->first()){
		    $CHKexamExist->update([
		        'Responds' 		=> $RS,
				'studentDegree' => $Degree,
				'examDegree'	=> $examDegree,
		        ]);
		}
		else{
		    $Solves = new Solves([
		        'ACCESS_TOKEN'   => $cookie_token,
		        'examName'      => $EXID,
		        'Responds' 		=> $RS,
				'Total'			=> $Total,
				'studentDegree' => $Degree,
				'examDegree'	=> $examDegree,
				'materialCode'  => session('materialCode')
		    ]);
		    $Solves->save();
		}
		
		session()->put('code',session('materialCode'));
		$code  = session('materialCode');
        $Exam1 = DB::table('solves')->where('ACCESS_TOKEN','=',$cookie_token)->where('examName','=',$EXID)->where('materialCode','=',session('materialCode'))->first();

		session()->forget('time');
		session()->forget('materialCode');
		session()->forget('ExamData');
		return redirect('viewexams')->with([
		        'code'     => $code ,
		        'Exam1'    => $Exam1
		    ]);
		

		return redirect('home')->with('NotFound',$EXID);
	}

	public function editQuestion(Request $req){
		$DB = env('SERVER_CODE') ."main" ; 
		$this->connectDB($DB);
    	$User = DB::table('exams')->where('ID',$req->examID)->first();
        if($User){
        $arr = array(
       	explode('&%', $User->QType ),	//0
       	explode('&%', $User->mainQ ),	//1
      	explode('&%', $User->Q ),		//2
      	explode('&%', $User->R ),		//3
       	explode('&%', $User->TR ),		//4
      	explode('&%', $User->QDegree ),	//5
      	$User->examName,				//6
        $User->ETime ,					//7
        $User->Type ,					//8
        );
        $Teacher = array(
            $User->material,                      //0    
            $User->Year,                          //1
        );
        session()->put('Teacher',$Teacher);
        session()->put('Edit',$arr);
        return json_encode(array("success"));
    	}
        
    	else return json_encode(array("Error"));
	}
	public function endEdit(Request $req){
		session()->forget('Edit');
		return back();
	}
	public function deleteExam(Request $req){
	    $DB = env('SERVER_CODE') ."main" ; 
		$this->connectDB($DB);
    	$User = DB::table('exams')->where('ID',$req->examID);
    		
    	if($User->first() && $User->delete() ){
        return json_encode(array("success"));
        }
        else return json_encode(array("Error"));
	}
	public function copyExam(Request $req){
		$DB = env('SERVER_CODE') ."main" ; 
		$this->connectDB($DB);
		$User = DB::table('exams')->where('ID',$req->examID)->first();
        if($User){
        	$newName  = $req->examName;
        	$validator = Validator::make($req->all(),[
			'examName'  => 'required||unique:exams'
			]);	
			if($validator->passes()){
				$Exam = new Exams([
				'examName' => $newName,
				'ETime'	   => $User->ETime	,
				'mainQ'	   => $User->mainQ ,						
				'Q'		   => $User->Q ,				
				'R'		   => $User->R ,					
				'TR'	   => $User->TR ,						
				'QDegree'  => $User->QDegree ,							
				'QType'	   => $User->QType 	,
				'Type'     => $User->Type ,
				'material' => $User->material,
				'Year'     => $User->Year
			]);	
			if($Exam->save()){	
        	return json_encode(array("success"));}
			}	
        }
        return json_encode(array("Error"));
	}
	public function deleteQ(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
        $CHK = DB::table('exams')->where('examName',session('Edit')[6])->first();
  	
        if(session('Edit')){
        	$ID = $req->QID;
        	$Data = session('Edit');
        		$QType 		= ""; 
        		$mainQ 		= ""; 
        		$Q     		= ""; 	 
        		$R 	   		= ""; 	
        		$TR 		= ""; 	
        		$QDegree 	= ""; 	
        	for( $i = 1 ; $i < count($Data[0]) ; $i++ ){
        		if($i == $ID)
        			continue;
        		$QType 		.= "&%" . $Data[0][$i];
        		$mainQ 		.= "&%" . $Data[1][$i];
        		$Q     		.= "&%" . $Data[2][$i];	 
        		$R 	   		.= "&%" . $Data[3][$i];	
        		$TR 		.= "&%" . $Data[4][$i];	
        		$QDegree 	.= "&%" . $Data[5][$i];		
        	}
        	$Update = DB::table('exams')->where('examName',session('Edit')[6])->update([
        		    'QType'      	=>	$QType , 
					'mainQ'      	=>  $mainQ ,
					'Q'          	=>  $Q ,
					'R'          	=>  $R ,
					'TR'         	=>  $TR ,        
					'QDegree'  		=>	 $QDegree
        	]);
        	if($Update) {
        		$User = DB::table('exams')->where('examName',session('Edit')[6])->first();
        		$arr = array(
       			explode('&%', $User->QType ),	//0
       			explode('&%', $User->mainQ ),	//1
      			explode('&%', $User->Q ),		//2
      			explode('&%', $User->R ),		//3
       			explode('&%', $User->TR ),		//4
      			explode('&%', $User->QDegree ),	//5
      			$User->examName,				//6
        		$User->ETime ,					//7
        		$User->Type ,					//8
        		);
        		session()->put('Edit',$arr);
        		return json_encode(array("success"));
        	}
        }
        return json_encode(array("Error"));
	}

/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

    public function updateMCQ(Request $req){
        $DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		if(session('Edit')){
		$ID  		= 	 $req->ID;	
		$degree 	=	 $req->degree;
		$Question	= 	 $req->Question;
		$TrueR			=	 $req->TR;
		$Main 		=  	 $req->Main;
		$Responds	=	 $req->Responds;
		if(!$Responds)
			return json_encode(array("error"));
		$validator = Validator::make($req->all(),[
			'degree'			=>	'required|numeric',
			'Question'			=>	'required',
		 	'TR'				=>	'required'
			]);
		if($validator->passes()){
			$Data = session('Edit');
			$CHK = DB::table('exams')->where('examName',session('Edit')[6])->first();
  		
			$RS = "";
			for( $i=0 ; $i < count($Responds) ; $i++ ){
				$RS .= '%S' . $Responds[$i];
			}
			
				$QType 		= ""; 
        		$mainQ 		= ""; 
        		$Q     		= ""; 	 
        		$R 	   		= ""; 	
        		$TR 		= ""; 	
        		$QDegree 	= ""; 	
        	for( $i = 1 ; $i < count($Data[0]) ; $i++ ){
        		if($i == $ID){
        		$QType 		.= "&%" . $Data[0][$i];
        		$mainQ 		.= "&%" . $Main;
        		$Q     		.= "&%" . $Question;	 
        		$R 	   		.= "&%" . $RS;	
        		$TR 		.= "&%" . $TrueR;	
        		$QDegree 	.= "&%" . $degree;
        		}
       			else{
        		$QType 		.= "&%" . $Data[0][$i];
        		$mainQ 		.= "&%" . $Data[1][$i];
        		$Q     		.= "&%" . $Data[2][$i];	 
        		$R 	   		.= "&%" . $Data[3][$i];	
        		$TR 		.= "&%" . $Data[4][$i];	
        		$QDegree 	.= "&%" . $Data[5][$i];
        		}		
        	}
        	$Update = DB::table('exams')->where('examName',session('Edit')[6])->update([
        		    'QType'      	=>	$QType , 
					'mainQ'      	=>  $mainQ ,
					'Q'          	=>  $Q ,
					'R'          	=>  $R ,
					'TR'         	=>  $TR ,        
					'QDegree'  		=>	$QDegree
        	]);
        	if($Update) {
        		$User = DB::table('exams')->where('examName',session('Edit')[6])->first();
        		$arr = array(
       			explode('&%', $User->QType ),	//0
       			explode('&%', $User->mainQ ),	//1
      			explode('&%', $User->Q ),		//2
      			explode('&%', $User->R ),		//3
       			explode('&%', $User->TR ),		//4
      			explode('&%', $User->QDegree ),	//5
      			$User->examName,				//6
        		$User->ETime ,					//7
        		$User->Type ,					//8
        		);
        		session()->put('Edit',$arr);
        		return json_encode(array("success",$RS,$User->Type));
		}	
		return json_encode(array("SAME"));
     }
  }
  return json_encode(array("error"));
}
	public function viewExam2(Request $req){
		( Cookie::get('YEAR') == 1 )? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);
		
		$ID = $req->input('id');
		$Exam = DB::table('solves')->where('ID',$ID)->first();
		if($Exam){
			session()->put('Exam1',$Exam);
		}
		return back();
	}
    public function exitStudentView(Request $req){
	
		session()->forget('Solve1');
		session()->forget('Exam1');
		session()->forget('Edit1');
		return back();
}


public function setPublish(Request $req){
        $DB =  env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$examName = session('Edit')[6];
		$Copy = DB::table('exams')->where('examName',$examName)->first();
		
	    $Year     = $req->Year;
	    ($Year == 1)? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);
		
		$Material = $req->Material;
		$table = $this->getExamDbByCode($Material);
		$Exam = DB::table($table)->where('examName',$examName)->first();
		if(!$Exam){
        DB::table($table)->insert(array(
		        'examName'   => $Copy->examName, 
		        'mainQ'      => $Copy->mainQ, 
		        'Q'          => $Copy->Q, 
		        'R'          => $Copy->R, 
		        'TR'         => $Copy->TR, 
		        'QDegree'    => $Copy->QDegree, 
		        'QType'      => $Copy->QType, 
		        'ETime'      => $Copy->ETime,
		        'Type'       => $Copy->Type
		        ));

		    
		    return json_encode(array("success"));
		} 
		return json_encode(array("error"));
	}


	public function updateETime(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		
		$examName = $req->examName;
		$ETime    = $req->ETime;
		
		$validator = Validator::make($req->all(),[
			'examName' => 'exists:exams',
			'ETime'	   => 'digits_between:1,3||not_in:0'
			]);
		if($validator->passes()){
		$Update = DB::table('exams')->where('examName',$examName)->update([
			'ETime'		=>	$ETime	
		]);
		if($Update){
			$User = DB::table('exams')->where('examName',$examName )->first();
			$arr = array(
       		explode('&%', $User->QType ),	//0
       		explode('&%', $User->mainQ ),	//1 
      		explode('&%', $User->Q ),		//2
      		explode('&%', $User->R ),		//3
       		explode('&%', $User->TR ),		//4
      		explode('&%', $User->QDegree ),	//5
      		$User->examName,				//6
        	$User->ETime ,					//7
        	$User->Type ,					//8
        );
        session()->put('Edit',$arr);
		return json_encode(array("success"));
		}
		}
		return json_encode(array("error"));
	}
	function smartSearchBank(Request $req){
	    $DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
	    $search = $req->search;
	    $arr = array();
	    $GET = DB::table('exams')->where('examName','like',$search.'%')->limit(50)->orderBy('ID','DESC')->get();
	    foreach($GET as $row){
	        $arr[] = $row->examName;
	    }
	    return json_encode(array('success',$arr));
	}
    function getMaterialInfo(Request $req){
        $DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		
		$NO = $req->NO;
		$Teacher = array(
            $NO,                            //0    
        );
		session()->put('Teacher',$Teacher);
		return json_encode(array('success'));
    }
    function backEX(Request $req){
        session()->forget('Teacher');
        return back();
    }
    function addMark(Request $req){
        $ID = $req->input('mark');
        $fp = fopen('MARKUP.txt', 'w');
        fwrite($fp, $ID);
        fclose($fp);
        return back();
    }
/*	function copyToAnother(Request $req){
	    $this->connectDB();
	    $examName = $req->examName;
	    $Copy = DB::table('exams')->where('examName',$examName)->first();
	    $year     = $req->year;
        DB::disconnect('mysql');
        Config::set('database.connections.mysql.database', $year);
	    if( DB::table('exams')->where('examName',$examName)->first() )
	        return json_encode(array('error'));
	    $Exam = new Exams([
	       'examName'    => $examName, 
	       'mainQ'       => $Copy->mainQ,
	       'Q'           => $Copy->Q,
	       'R'           => $Copy->R,
	       'TR'          => $Copy->TR,
	       'QDegree'     => $Copy->QDegree,
	       'QType'       => $Copy->QType,
	       'ETime'       => $Copy->ETime,
	       'Day'         => NULL,
	       'Time'        => NULL,
	       'Edit'        => 0,
	       'Send'        => 0
	   ]);
	   if( $Exam->save() )    
	   return json_encode(array('success'));
	  
	}*/
}