<?php

  $cookie_token = Cookie::get('ACCESS_TOKEN');
  $cookie_year  = Cookie::get('YEAR');
  $statue = false;
  if( $cookie_token && $cookie_year ){
    //CHK COOKIE IN DB
    if($cookie_year == 1)                $DB = env('SERVER_CODE') ."first";
    else if($cookie_year == 2)           $DB = env('SERVER_CODE') ."second";
    else                                 $DB = $cookie_year;
     //CHK DB EXIST
     $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $db = DB::select($query, [$DB]);
        if ( !empty($db) ) {
            DB::disconnect('mysql');
            Config::set('database.connections.mysql.database', $DB);
            $Student = DB::table('students')->where('ACCESS_TOKEN',$cookie_token)->first();
            if( $Student ){
                //Auth Success GO to Student Page
                if(!session('Firebase'))
                session(['Student' => $Student->Fullname,'Firebase'  => $Student->firebase ]);
                $statue   = true;    
            }
            else{
                Cookie::queue(Cookie::forget('ACCESS_TOKEN'));
                Cookie::queue(Cookie::forget('YEAR'));
                if(session('Student'))
                session()->flush();
            }
        }
        else{
                Cookie::queue(Cookie::forget('ACCESS_TOKEN'));
                Cookie::queue(Cookie::forget('YEAR'));
                if(session('Student'))
                session()->flush();
            }
  }
  
?>
@if( $statue == false )
<script>
  location.href = '/..'
</script>
@endif