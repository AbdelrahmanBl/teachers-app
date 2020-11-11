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
            if( DB::table('students')->where('ACCESS_TOKEN',$cookie_token)->first() ){
                //Auth Success GO to Student Page
                $statue = 'StudentDashboard';    
            } 
            else{
                Cookie::queue(Cookie::forget('ACCESS_TOKEN'));
                Cookie::queue(Cookie::forget('YEAR'));
                if(session('Student'))
                session('Student')->foregt();
            }
        }
        else{
                Cookie::queue(Cookie::forget('ACCESS_TOKEN'));
                Cookie::queue(Cookie::forget('YEAR'));
                if(session('Student'))
                session('Student')->foregt();
            }
  }
  else if( session('ENTRY_TOKEN') ){
    //CHK COOKIE IN DB
    $DB = env('SERVER_CODE') ."main" ;
            DB::disconnect('mysql');
            Config::set('database.connections.mysql.database', $DB);
            $User = DB::table('users')->where('ACCESS_TOKEN',session('ENTRY_TOKEN'))->first();
            if( $User ){
                //Auth Success GO to Student Page
                if(!session('Admin'))
                session([ 'Admin' => $User->admin ]);
                $statue = 'Admin'; 
            }
            else{
                if(session('ENTRY_TOKEN'))
                session()->forget('ENTRY_TOKEN');
            }
        
  }
?>
@if( $statue == 'StudentDashboard' )
    @include('dataFile/StudentDashboard')
@elseif( $statue == 'Admin' )
    @include('dataFile/AdminDashboard')    
@else
<script>
  location.href = '/..'
</script>
@endif
