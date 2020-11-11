<?php
  $statue = false;
  if( session('ENTRY_TOKEN') ){
    //CHK COOKIE IN DB
    $DB = env('SERVER_CODE') ."main" ;
            DB::disconnect('mysql');
            Config::set('database.connections.mysql.database', $DB);
            $User = DB::table('users')->where('ACCESS_TOKEN',session('ENTRY_TOKEN'))->first();
            if( $User->admin == 1 ){
                //Auth Success GO to Student Page
                if(!session('Admin'))
                session([ 'Admin' => $User->admin ]);
                $statue   = true;    
            }
        
  }
  
?>
@if( $statue == false )
<script>
  location.href = '/..'
</script>
@endif