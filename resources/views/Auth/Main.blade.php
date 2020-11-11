<?php

  $cookie_token = Cookie::get('ACCESS_TOKEN');
  $cookie_year  = Cookie::get('YEAR');
  $statue = false;
  if(isset($cookie_token) && $cookie_year ){
    $statue = true;
  }
  else if( session('ENTRY_TOKEN') ){
    $statue = true;  
  }
?>
@if( $statue == true )
<script>
  location.href = 'home'
</script>
@endif