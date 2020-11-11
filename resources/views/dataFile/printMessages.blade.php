<?php 
$DB = env('SERVER_CODE') ."main" ;
if($DB){
DB::disconnect('mysql');
Config::set('database.connections.mysql.database', $DB);
if( session('ENTRY_TOKEN') ){ 
$User = DB::table('messages')->orderBy('ID','DESC')->Limit(10)->get();
session()->put('Messages',$User);
}
else if( Cookie::has('ACCESS_TOKEN')  ){
$User = DB::table('messages')->orderBy('ID','DESC')->where( 'Year',Cookie::get('YEAR') )->Limit(8)->get();
session()->put('Messages',$User);        
}
}
