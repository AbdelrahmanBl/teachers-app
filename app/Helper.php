<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use App\Models\Solve;
use App\Models\ExamRequest;
use App\Models\Subscrption;
use App\Models\Attendance;
use App\Models\Notification;
use App\Models\Question;

use Jenssegers\Date\Date;

use File;

class Helper extends Model
{
    public static function return($result)
    {
    return [
        'error_flag' => 0,
        'message' => 'success',
        'result'=> $result
      ];
    }
    public static function returnError($result)
    {
    return [
        'error_flag'    => 1,
        'message'       => $result,
        'result'        => NULL,
      ];
    }
    public static function returnException($e)
    {
    $Exception = Setting::where('key','EXCEPTION')->first();
    ($Exception->value)? $msg = $e->getMessage() : $msg = 'Something Went Wrong . Please Try Again' ;
    return $msg;
    }
    public static function notFound($message)
    {
      return response()->json([
            'error_flag' => 404,
            'message' => $message,
            'result'=> NULL
        ]);
    }
    public static function loginUsingId($model,$remember_token)
    {
            $token = ($remember_token)? $remember_token : str_random(64);
            $model->update([
            'failed_try'       => 0,
            'remember_token'   => $token,
            'last_login'       => date('Y-m-d H:i:s'),
            ]); 
            return $token;
    }
    
    public static function image($image,$mode,$destinationPath,$filepath = NULL,$person_id = 0,$counter = 0)
    {
        if( $mode == 'update'){
            $oldImageName = explode('/',$filepath);
            $oldImageName = $oldImageName[count($oldImageName)-1];
            $imageName    =  $oldImageName;
        }
        else 
        $imageName =  (time() . $counter . $person_id) .'.'.$image->getClientOriginalExtension();
        $destinationPath = 'storage/' . $destinationPath;
        // $url = $destinationPath . '/' . $imageName; 

        $image->move($destinationPath, $imageName);
        // $access_url =  /* env('APP_URL').'/' . */ $url;
        $access_url = $imageName;
        return $access_url;
    }
    public static function copy_image($filepath,$destinationPath,$counter = 0,$person_id = 0)
    {
        $oldImageName = explode('/',$filepath);
        $oldImageName = $oldImageName[count($oldImageName)-1];
        $imageName    =  $oldImageName;
        $newPath = $destinationPath . '/' . (time() . $counter . $person_id) . '.' . explode('.', $imageName)[1];
        $oldPath = $destinationPath . '/' . $imageName;
        $url =/* env('APP_URL').*/'/' . $newPath;
          
        File::copy($oldPath , $newPath);
        return $url;
    }
    public static function delete_image($destinationPath,$fileName)
    {
        // $oldImageName = explode('/',$filepath);
        // $oldImageName = $oldImageName[count($oldImageName)-1];
        // $imageName    =  $oldImageName;

        $image_path = $destinationPath . '/' . $fileName;  

        if(File::exists($image_path)) {
            File::delete($image_path);
            return true;
        }
        return false;
    }
    public static function directory_size($dirname)
    {
        $file_size = 0;
        if(!is_dir($dirname))
            return $file_size;
        foreach( File::allFiles(public_path($dirname)) as $file)
        {
            $file_size += $file->getSize();
        }


        $file_size = number_format($file_size / 1048576,2);
        return $file_size;
    }
    public static function disable($model,$col,$on,$off,$return_on,$return_off)
    {
        if($model->first()->$col == $on){
        $model->update([
            $col    => $off
        ]);
        $status = $return_off;
        }else{
        $model->update([
            $col    => $on
        ]);
        $status = $return_on;
        }
        return $status;
    }
    public static function handle_solves($solves,$questions,$student_id,$exam_id,$teacher_id)
    {
        $solves_arr  = array();
        $counter = 0;
        $total_degree = 0;
        foreach($solves as $solve){
          $question = $questions->where('_id',$solve['question_id'])->first();
          if(!$question)
            return 'question_not_found';
          if($question->question_type == 'M' && isset($solve['respond']) && (int)$solve['respond'] > count($question->responds)) /* !in_array($solve['respond'], $question->responds) */
            return 'distinct_respond';
            
          $solve_obj = new Solve();
          $solve_obj->student_id = $student_id;
          $solve_obj->exam_id = $exam_id;
          $solve_obj->question_id = $solve['question_id'];
          
          if(isset($solve['respond'])){
            if($question->question_type == 'M')
                $solve_obj->respond = (int)$solve['respond'];
            else $solve_obj->respond = $solve['respond'];
          } 
          else $solve_obj->respond = null;

          $images = array();
          if( isset($solves[$counter]['images']) && $question->question_type == 'W' ){
            $counter_skip = 1;
            foreach( $solves[$counter]['images'] as $index => $image ){
                if(!filesize($image) || $counter_skip > 2)
                  continue;
                $images[] = "{$teacher_id}/" . Helper::image($image,'add',"solves/{$teacher_id}",NULL,$student_id,$counter.($counter_skip - 1)/*$counter = $index*/);
                $counter_skip++;
            }
          }

          $solve_obj->images = $images;
          
          
          $solve_obj->degree = 0;
          if($question->question_type == 'M'){
            $trueRespond_index = $question->true_respond;
            if(isset($solve['respond']) && $trueRespond_index == (int)$solve['respond']){
                $solve_obj->degree = (double)$question->degree;
                $total_degree += (double)$question->degree;
            }
          }
          else $solve_obj->degree = NULL;
          

          $solves_arr[] = $solve_obj->toArray();
          $counter++;
        }
        return ['solves_arr' => $solves_arr , 'total_degree' => $total_degree];
    }

    public static function getStudentsForPublish($req,$exam_id,$appointment_ids,$year)
    {
      $teacher_id       = $req->get('id');

      $model = ExamRequest::where('exam_id',$exam_id)->get(['student_id']);
      $student_ids = $model->pluck('student_id');
      
      $model = Subscrption::whereNotIn('student_id',$student_ids)->whereIn('subscrptions.appointment_id',$appointment_ids);
      $where = array(
        'subscrptions.teacher_id'   => $teacher_id,
        'subscrptions.status'       => 'ON',
        // 'users.type'                => 'S',
        'users.year'                => $year
      );

      $model_select  = $model->where($where);
      $select = ['users.id','users.first_name','users.last_name','subscrptions.appointment_id'];
      $model_data    = $model_select->join('users','users.id','subscrptions.student_id')->select($select)->get();
      $model_data->transform(function($item) {
        $item->fullname = "{$item->first_name} {$item->last_name}";
        return $item->makeHidden(['first_name','last_name']);
      });


      return $model_data;
    }

    public static function getStudentStatistics($student_id,$teacher_id)
    {
      $where = ['student_id' => $student_id , 'teacher_id' => $teacher_id];
      $subscrption = Subscrption::where($where)->first();
      if(!$subscrption)
        return ['error' => 1];

      $main = [$subscrption->attend_no,$subscrption->missed_no];
      $attendances = Attendance::where($where)->orderBy('_id','DESC')->get(['month','status']);
      // dd($attendances->toArray());

      $attend  = [];
      $missed  = [];
      $months  = [];

      foreach($attendances as $attendance) {
        $month = $attendance->month;
        if(!in_array($month,$months)) {
          $months[] = $month;
          $attend[] = 0;
          $missed[] = 0;
        }
        $index = array_search($month,$months);
        if($attendance->status == true)
          $attend[$index]++;
        else $missed[$index]++; 
      }

      Date::setLocale('ar');
      $counter = 0;
      foreach($months as $month) {
        $months[$counter] = Date::create(0, $month + 1, 0, 0, 0, 0)->format('M');
        $counter++;
      }

      return [
        'main'     => $main,
        'months'   => $months,
        'attend'   => $attend,
        'missed'   => $missed,
      ];
    }

    public static function getNotificationCount($user_id)
    {
      $where = array(
        'student_id'  => $user_id,
        'status'      => 'ON'
      );
      $subscrptions     = Subscrption::where($where)->get();
      $subscrptions_arr = array();
      foreach($subscrptions as $subscrption){
        if(!in_array((int)$subscrption->teacher_id, $subscrptions_arr))
          $subscrptions_arr[] = (int)$subscrption->teacher_id;
      }

      $where = array(
        'reciever_id'   => $user_id,
        'is_seen'       => 0
      );
      $where[] = ['created_at','<=',date('Y-m-d H:i:s')];
    
      $new_count          = Notification::whereIn('sender_id',$subscrptions_arr)->where($where)->count(); 

      return $new_count;
    }

    public static function merge_questions($exam_ids,$new_exam_id)
    {
      $questions = Question::whereIn('exam_id',$exam_ids)->get();
      $counter      = 0; 
      $total_degree = 0;
      $arr = array();
      foreach( $questions as $question ){
        $question_copy = $question->replicate();
        $question_copy->exam_id = $new_exam_id;
        // $image = $question->image;
        // if($image)
        //   $question_copy->image = Helper::copy_image($image,'questions',$counter,$teacher_id);
        
        $arr[] = $question_copy->toArray();
        $total_degree += (double)$question->degree;
        $counter++;
      } 
      
      return [
        'length' => $counter,
        'degree' => $total_degree,
        'data'   => $arr
      ]; 
    }
    
}
