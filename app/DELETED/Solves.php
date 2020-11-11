<?php
namespace app;
use Illuminate\Database\Eloquent\Model;
class Solves extends Model
{
	protected $fillable = ['ACCESS_TOKEN','examName','Responds','studentDegree','Total','examDegree','materialCode'];
	public $timestamps = false;
}


