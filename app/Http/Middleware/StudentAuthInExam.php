<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Helper; 
use App\Models\ExamRequest;
use App\Models\User;

class StudentAuthInExam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $student_id     = (int)$request->get('id');
        $student_status = $request->get('student_status');

        if($student_status == 'IN_EXAM'){
            $where = array(
                'student_id'      => $student_id,
                'status'          => 'IN_EXAM'
            );
            $exam_request      = ExamRequest::where($where)->limit(1)->orderBy('id','DESC');
            $exam_request_data = $exam_request->first();
            
            if(!$exam_request_data){
                User::where('id',$student_id)->update(['student_status' => 'WAITING']);
                return $next($request);
            }
            if($exam_request_data->end_at > date('Y-m-d H:i:s')){
                //Exam Exist
                return response()->json([
                    'error_flag'  => 22,
                    'message'     => 'in_exam',
                    'result'      => [
                        'request_id' => $exam_request_data->id
                    ],
                ], 200 );
            }
            
            $exam_request->update(['status' => 'DICONNECTED']); 
            User::where('id',$student_id)->update(['student_status' => 'WAITING']);
            
        }
        return $next($request);
    }
}
