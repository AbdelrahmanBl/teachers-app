<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

use App\Models\ExamRequest;
use App\Models\User;

use Exception;
use App\Helper;
use validate;
use DB;
class studentController extends Controller
{
    public function get_profile(Request $req)
    {try{
        $id      = $req->get('id');

        $model 		   = new User();
        $model_select  = $model->where('id',$id);
        $model_data    = $model_select->first(['first_name','last_name','mobile','image','type']);
            
        return Helper::return([
            'profile'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_published(Request $req)
    {try{
        $id      = $req->get('id');

        $where = array(
        	'exam_requests.status' 		=> 'WAITING',
        	'exam_requests.student_id'	=>  $id
        );
        $select = ['users.image','users.first_name','users.last_name'
    			   ,'exams.exam_name','exams.desc','exams.degree','exams.question_no', 'exams.duration'];
        $model 		   = new ExamRequest();
        $model_select  = $model->where($where);  
        $model_data    = $model_select->join('users','exam_requests.teacher_id','users.id')->join('exams','exam_requests.exam_id','exams.id')->select($select)->paginate(10);
            
        return Helper::return([
            'exams'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    /*------------------------------------------------------------*/

}
