<?php
namespace app;
use Illuminate\Database\Eloquent\Model;
class Exams extends Model
{
	protected $fillable = ['examName','mainQ','Q','R','TR','QDegree','QType','ETime','Type','material','Year'];
	public $timestamps = false;
}


