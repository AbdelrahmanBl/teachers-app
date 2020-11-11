<?php
namespace app;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Students extends Authenticatable  
{
	protected $fillable = ['ID','Fullname','ACCESS_TOKEN','Phone','City','code'];

    
}




