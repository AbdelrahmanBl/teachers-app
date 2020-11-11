<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Students ; // To Make Fillable Permissions
use App\Messages;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Cookie;

class studentController extends Controller
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
	public function signup(Request $req){
		$Fullname 		= $req->input('Fullname');
		$Year 			= $req->input('Year');
		$code           = $req->input('code');
		$Phone          = $req->input('Phone');
		$City           = $req->input('City');
		back()->with([
			'Fullname'	    =>	$Fullname,
			'Year'	        =>	$Year,
			'code'          =>  $code,
			'Phone'         =>  $Phone,
			'City'          =>  $City
		]);
        
		$this->validate($req,[
			'Fullname'		=>	'required||regex:/^[\s\p{Arabic}]+$/u||regex:/^[\pL\s\-]+$/u|max:30',
			'Year'	        =>	'required||in:1,2',
			'code'          =>  'required||exists:codes',
			'Phone'         =>  'required||numeric',
			'City'          =>  'required||digits_between:0,26'
			
		]);
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$token = DB::table('codes')->where('code',$code)->first()->ACCESS_TOKEN;
		
		($Year == 1) ? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);

        
		$Users = new Students([
            'Fullname'      =>  $Fullname,
            'ACCESS_TOKEN'  =>  $token,
            'Phone'         =>  $Phone,
			'City'          =>  $City ,
			'code'          =>  $code
        ]);
        if($Users->save()){
            //reset code to NULL
            $DB = env('SERVER_CODE') ."main";
            $this->connectDB($DB);
            DB::table('codes')->where('code',$code)->update([
                    'code'  => NULL
                ]);
        	session()->flush();
        	//COOKIES
        	$name     =  'ACCESS_TOKEN';
        	$value    =  $token;
        	$minutes  =  175200;   // 4 Months
        	Cookie::queue($name, $value, $minutes);
        	Cookie::queue( 'YEAR' , $Year, $minutes);
        	return redirect('home');
        }
        return back()->with('error','error');		
	}
	public function login(Request $req){
		$Email		=	$req->input('Email').$req->input('year');
		$Password   =	$req->input('Password');

		if( filter_var($Email, FILTER_VALIDATE_EMAIL) ){
		    $DB = env('SERVER_CODE') ."main" ;
		    $this->connectDB($DB);
			$Admin = DB::table('users')->where('email',$Email)->first();
			if($Admin && $Admin->password == $Password ){
			    $statue = false;
			    while($statue != true){
			        $ACCESS_TOKEN = bin2hex(openssl_random_pseudo_bytes(64));
			        if( !DB::table('users')->where('ACCESS_TOKEN',$ACCESS_TOKEN)->first() ){
			            DB::table('users')->where('email',$Email)->update([
			            'ACCESS_TOKEN'     =>  $ACCESS_TOKEN,
			        ]);
			         $statue = true;
			        }
			    }
			    
			    if($Admin->material){
			        $Teacher = array(
                    $Admin->material,                        //1
                    NULL
                );
			    session()->put('Teacher',$Teacher);
			    session()->put('ENTRY_TOKEN', $ACCESS_TOKEN );
				return redirect('bank');
			    }
			    else{
				session()->put('ENTRY_TOKEN', $ACCESS_TOKEN );
				return redirect('home');
			    }
			}
		}
		
		return back()->with('invalid','الايميل او الباسورد غير صحيحين');	
		
	}
	public function filterDate(Request $req){
	    $DB = env('SERVER_CODE') ."main";
	    $this->connectDB($DB);
	    $date  = $req->input('date');
	    $Codes = DB::table('codes')->where('Date',$date)->get();
	    return back()->with(['Codes'=> $Codes , 'date' => $date ]);
	    
	}
	public function filter(Request $req){
	    $Year   =   $req->input('Year');
	    $City   =   $req->input('City');
	    back()->with([
			'City'	        =>	$City,
			'Year'	        =>	$Year
		]);
	    if(empty($Year))
	         return back()->with('error','error');
	    ($Year == 1) ? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
	    $this->connectDB($DB);
	    if($City != ''){
	        $Users = DB::table('students')->where('City',$City)->get();    
	   }else{
	       $Users = DB::table('students')->get(); 
	   }  
	   return back()->with('Users',$Users); 
	         
	}
	public function deleteStudent(Request $req){
	    $ACCESS_TOKEN  = $req->input('ACCESS_TOKEN');
	    if(empty($ACCESS_TOKEN))
	    return back()->with('error','error');   

	    $DB = env('SERVER_CODE') ."first";
	    $this->connectDB($DB);
	    $Student = DB::table('students')->where('ACCESS_TOKEN',$ACCESS_TOKEN)->first();
	    if($Student){
	        return back()->with([
	            'StudentData'    =>  $Student ,
	            'Year'       => 'الصف الاول الثانوي',
	            'YearNo'     => 1
	            ]);
	    }
	        $Student = DB::table('students')->where('code',$ACCESS_TOKEN)->first();
	        if($Student){
	        return back()->with([
	            'StudentData'    =>  $Student ,
	            'Year'       => 'الصف الاول الثانوي',
	            'YearNo'     =>  1
	            ]);
	        }
	      $DB = env('SERVER_CODE') ."second";
	      $this->connectDB($DB);  
	      $Student = DB::table('students')->where('ACCESS_TOKEN',$ACCESS_TOKEN)->first();
	      if($Student){
	          return back()->with([
	            'StudentData'    =>  $Student ,
	            'Year'       => 'الصف الثاني الثانوي',
	            'YearNo'     =>  2
	            ]);
	      }
	      $Student = DB::table('students')->where('code',$ACCESS_TOKEN)->first();
	      if($Student){
	        return back()->with([
	            'StudentData'    =>  $Student ,
	            'Year'       => 'الصف الثاني الثانوي',
	            'YearNo'     =>  2
	            ]);
	    }
	   return back()->with('error','error');   
	}
	
 
	public function SignOut(){
		session()->flush();
		session_unset();
		return redirect('/signin');
	}



	public function publishMessage(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$Message = $req->Message ;
		$Year     = $req->Year ;
		$Link    =  $req->Link;
		$validator = Validator::make($req->all(),[
			'Message'    => 'max:255',
			'Year'        => 'required|integer|between:0,3' ,
			]);
		if ($validator->passes()) {
			$Message = new Messages([
				'Message'  =>  $Message,
				'Year'      =>  $Year,
				'Link'     =>  $Link
			]);
			/* $MTable = DB::table('messages');
			  if($MTable->count() >= 40 )
				$MTable->orderBy('ID')->limit(1)->delete();  */
			if($Message->save()){ 
				// $ID = DB::table('messages')->orderBy('ID','DESC')->first()->ID; 
				return json_encode(array("success"));
			}
		}
		return json_encode(array('error'));  	
	}
	public function DeleteMessage(Request $req){
		$DB = env('SERVER_CODE') ."main";
		$this->connectDB($DB);
		$ID = $req->ID;
		$validator = Validator::make($req->all(),[
			'ID'    => 'exists:messages'
		]);
		if ($validator->passes()) {
			DB::table('messages')->where('ID',$ID)->delete();
			return json_encode(array('success'));
		}
		return json_encode(array("error"));
	}
	public function DeletePerson(Request $req){
	    $Year     =  $req->YearNo;
	    ($Year == 1) ? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
		$this->connectDB($DB);
		
		$ID = $req->id;
		$validator = Validator::make($req->all(),[
			'id'    => 'exists:students'
		]);
		if ($validator->passes()) {
			$Student = DB::table('students')->where('ID',$ID);
			$token = $Student->first()->ACCESS_TOKEN;
			$Student->delete();
			
			$DB = env('SERVER_CODE') ."main";
		    $this->connectDB($DB);
		    
		    DB::table('codes')->where('ACCESS_TOKEN',$token)->delete();
		    
		    
			return json_encode(array('success'));
		}
		return json_encode(array("error"));
		
	}
	public function registerFCM(Request $req){
	    $cookie_year  = Cookie::get('YEAR');
	    ($cookie_year == 1)? $DB = env('SERVER_CODE') ."first" : $DB = env('SERVER_CODE') ."second" ;
	 $this->connectDB($DB);   
	 $firebase = $req->firebase;   
	 $cookie_token = Cookie::get('ACCESS_TOKEN');
	 $Student = DB::table('students')->where('ACCESS_TOKEN',$cookie_token)->first();
	 if( $Student ){
	     DB::table('students')->where('ACCESS_TOKEN',$cookie_token)->update([
	         'firebase'   => $firebase
	         ]);
	   session()->put('Firebase',$firebase);      
	 }
	}
	
}