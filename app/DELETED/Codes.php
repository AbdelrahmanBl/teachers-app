<?php
namespace app;
use Illuminate\Database\Eloquent\Model;
class Codes extends Model
{
	protected $fillable = ['banking','code','ACCESS_TOKEN','Date','Time','Price','AddingBy'];
	public $timestamps = false;
}


