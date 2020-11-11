<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
 
use App\Models\User;
use App\Models\Appointment;
use App\Models\TempStudent;
use App\Models\Subscrption;
use App\Models\Package;
use App\Models\Exam;
use App\Models\ExamRequest;
use App\Models\Question;
use App\Models\Setting;

use Exception;
use App\Helper;
use validate;
use DB;
class teacherController extends Controller
{
    protected $responds_sequence = '#S$S#';
    public function get_profile(Request $req)
    {try{
        $id      = $req->get('id');

        $model = new User();
        $model_select  = $model->where('id',$id);
        $model_data    = $model_select->first(['first_name','last_name','mobile','image','type','accept_register','students_number','appointments_number','exams_number']);
            
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
    public function get_appointments(Request $req)
    {try{
        $id      = $req->get('id');

        $model = new Appointment();
        $model_select  = $model->where('teacher_id',$id);
        $select = ['appointments.id','days.day as days','appointments.time_from','appointments.time_to','appointments.year','appointments.status'];
        $model_data    = $model_select->join('days','appointments.days_id','days.id')->select($select)->paginate(10);
            
        return Helper::return([
            'appointments'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_registers(Request $req)
    {try{
        $id      = $req->get('id');
        $year    = (int)$req->get('year');

        $model = new TempStudent();
        $where = array(
          'temp_students.teacher_id'   => $id,
          'temp_students.status'       => 'ON'
        );
        $model_select  = $model->where($where);
        if($year)
          $model_select->where('appointments.year',$year);
        
        $select = ['temp_students.id','temp_students.first_name','temp_students.last_name','temp_students.mobile','temp_students.parent_mobile1','temp_students.parent_mobile2','days.day as days','appointments.time_from','appointments.time_to','appointments.year'];
        $model_data    = $model_select->join('appointments','appointments.id','temp_students.appointment_id')->join('days','appointments.days_id','days.id')->select($select)->paginate(10);
  
        return Helper::return([
            'all'        => $model::where($where)->count(),
            'first'      => $model::where($where)->where('year',1)->count(),
            'second'     => $model::where($where)->where('year',2)->count(),
            'third'      => $model::where($where)->where('year',3)->count(),
            'students'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_students_for_publish(Request $req)
    {try{
        $teacher_id       = $req->get('id');
        $req->validate([
          'exam_id'  => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'year'     => 'required|numeric|in:1,2,3',
          'appointment_id' => "nullable|numeric"
        ]);
        
        $year               = $req->get('year');
        $appointment_id     = $req->get('appointment_id');
        $exam_id            = $req->get('exam_id');

        $model = ExamRequest::where('exam_id',$exam_id)->get(['student_id']);
        $student_ids = array();
        foreach($model as $student){
          $student_ids[] = $student->student_id;
        }

        $model = Subscrption::whereNotIn('student_id',$student_ids);
        $where = array(
          'subscrptions.teacher_id'   => $teacher_id,
          'subscrptions.status'       => 'ON',
          'users.type'                => 'S',
        );
        $where[] = ['teacher_id',$teacher_id];
        // if($year)
          $where[] = ['users.year',$year];
        if($appointment_id)
          $where[] = ['users.appointment_id',$appointment_id];

        $model_select  = $model->where($where);
        $select = ['users.id','users.image','users.first_name','users.last_name','appointments.time_from','appointments.time_to','days.day','users.year'];
        $model_data    = $model_select->join('users','users.id','subscrptions.student_id')->join('appointments','appointments.id','users.appointment_id')->join('days','appointments.days_id','days.id')->select($select)->paginate(10);
            
        return Helper::return([
            'students'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_exam(Request $req)
    {try{
        $id      = $req->get('id');
        $req->validate([
          'exam_id'   => "required|numeric|exists:exams,id,teacher_id,{$id}"
        ]);
        $exam_id = (int)$req->get('exam_id');

        $model       = Exam::where('id',$exam_id);
        $model_data  = $model->first(['id','exam_name','degree','duration','is_published','year','status']);

        $question_model = Question::where('exam_id',$exam_id);
        $question_data  = $question_model->get(['_id','image','question_type','main_question','question','true_respond','responds','outside_counter','inside_counter','degree','created_at']);

        return Helper::return([
            'exam'      => $model_data,
            'questions' => $question_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_exams(Request $req)
    {try{
        $id      = $req->get('id');
        $year    = (int)$req->get('year');

        $where = array(
          'teacher_id'   => $id,
          'is_hide'      => 0
        );
        $model = new Exam();
        $model_select   = $model::where($where);
        if($year)
          $model_select->where('year',$year); 
        
        $model_data  = $model_select->select(['id','exam_name','question_no','degree','duration','is_published','year','status','created_at'])->paginate(10);

        return Helper::return([
            'all'        => $model::where($where)->count(),
            'first'      => $model::where($where)->where('year',1)->count(),
            'second'     => $model::where($where)->where('year',2)->count(),
            'third'      => $model::where($where)->where('year',3)->count(),
            'exams'      => $model_data,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
/*------------------------------------------------------------*/
    public function change_accept_register(Request $req)
    {try{
        $teacher_id      = $req->get('id');

        $model = new User();
        $where = array(
          'id'   => $teacher_id,
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'accept_register',1,0,true,false);
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function accept_students(Request $req)
    {try{
        $teacher_id      = $req->get('id');        
        $req->validate([
          'id'        => "required|array",
          'id.*'      => "required|numeric|exists:temp_students,id,teacher_id,{$teacher_id}",
        ]);
        $package_id      = $req->get('package_id');
        $ids = $req->input('id');
        $where = array(
          'teacher_id' => $teacher_id,
          'type'       => 'register'
        );
        $valid_ids = Subscrption::where($where)->whereIn('temp_id',$ids)->count();
        if($valid_ids != 0)
          return Helper::returnError(Lang::get('messages.process_completed'));
        
        $temp_students = TempStudent::where('status','ON')->whereIn('id',$ids);

        $students_limit  = Package::where('id',$package_id)->first()->students_limit;
        $students_number = $req->get('students_number') + $temp_students->count();
        if($students_number > $students_limit)
            return Helper::returnError(Lang::get('messages.package_limit'));

        $temp_students_data = $temp_students->get(['appointment_id','year','first_name','last_name','email','password','mobile','parent_mobile1','parent_mobile2','type']);
        $subscrptions = array();
        $counter = 0;
        foreach($temp_students_data as $item){
          $User = new User($item->toArray());
          $User->save();
          $subscrptions[] = [
            'teacher_id' => $teacher_id,
            'student_id' => $User->id,
            'temp_id'    => $ids[$counter],
            'type'       => 'register',
            'created_at' => date('Y-m-d H:i:s'),
          ];
          $counter++;
        }
        Subscrption::insert($subscrptions);
        $temp_students->update([
          'status'  => 'OFF'
        ]);

        User::where('id',$teacher_id)->increment('students_number',$counter);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function add_appointment(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'days'        => 'required|exists:days,id',
          'time_from'   => 'required|date_format:H:i',
          'time_to'     => 'required|date_format:H:i|after:time_from',
          'year'        => 'required|in:1,2,3',
        ]);
        $package_id = $req->get('package_id');
        $days       = $req->input('days');
        $time_from  = $req->input('time_from');
        $time_to    = $req->input('time_to');
        $year       = $req->input('year');

        $model = new Appointment();
        $count = $req->get('appointments_number');
        $teacher_appointment_limit = Package::where('id',$package_id)->first()->appointment_limit;
        if($count >= $teacher_appointment_limit)
          return Helper::returnError(Lang::get('messages.appointment_limit'));

        $model->days_id        = $days;
        $model->time_from      = $time_from;
        $model->time_to        = $time_to;
        $model->year           = $year; 
        $model->teacher_id     = $teacher_id;

        $model->save();
        User::where('id',$teacher_id)->increment('appointments_number');
        return Helper::return([
            'id'   => $model->id,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_appointment(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|exists:appointments,id,teacher_id,{$teacher_id}",
          'days'        => 'required|exists:days,id',
          'time_from'   => 'required|date_format:H:i',
          'time_to'     => 'required|date_format:H:i|after:time_from',
          'year'        => 'required|in:1,2,3',
        ]);
        $id         = $req->input('id');
        $days       = $req->input('days');
        $time_from  = $req->input('time_from');
        $time_to    = $req->input('time_to');
        $year       = $req->input('year');
        
        $appointments = User::where('appointment_id',$id)->limit(1)->count();
        if($appointments > 0)
          return Helper::returnError(Lang::get('messages.exist_appointments'));
        $model = new Appointment();
        $where = array(
          'id' => $id,
        );
        $model_select = $model::where($where);
        $my_arr = $req->all(['time_from','time_to','year']);
        $my_arr['days_id'] = $days;
        $model_select->update($my_arr);
        
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_appointment_status(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|numeric|exists:appointments,id,teacher_id,{$teacher_id}",
        ]);
        $id         = $req->input('id');

        $model = new Appointment();
        $where = array(
          'id' => $id
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'status','ON','OFF','ON','OFF');
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function delete_register(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'        => "required|array",
          'id.*'      => "required|numeric|exists:temp_students,id,teacher_id,{$teacher_id}",
        ]);
        $id         = $req->input('id');

        $ids = $req->input('id');
        $model = new TempStudent();
        $model = $model::where('status','ON')->whereIn('id',$ids);
        $model->delete();
        
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function add_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'year'            => 'required|in:1,2,3',
          'exam_name'       => 'required|string|max:100',
          'duration'        => 'required|numeric|between:1,360',
          'desc'            => 'nullable|string|max:100', 
        ]);
        $package_id     = $req->get('package_id');
        $exams_number   = $req->get('exams_number');
        $exams_limit = Package::where('id',$package_id)->first()->exams_limit;
        if($exams_number >= $exams_limit)
          return Helper::returnError(Lang::get('messages.exams_limit'));
        
        $my_arr = $req->all(['year','exam_name','duration','desc']);
        $my_arr['teacher_id']       = $teacher_id;
        $model = new Exam($my_arr);
        $model->save();
        
        User::where('id',$teacher_id)->increment('exams_number');
        return Helper::return([
          'id' => $model->id
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function copy_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
        ]);
        $exam_id        = $req->input('exam_id');
        $package_id     = $req->get('package_id');
        $exams_number   = $req->get('exams_number');
        $exams_limit = Package::where('id',$package_id)->first()->exams_limit;
        if($exams_number >= $exams_limit)
          return Helper::returnError(Lang::get('messages.exams_limit'));
        
        $model = Exam::where('id',$exam_id)->first();
        $exam_copy = $model->replicate();
        $exam_copy->is_published = 0;
        $model = new Exam($exam_copy->toArray());
        $model->save();

        $questions = Question::where('exam_id',$exam_id)->get();
        $counter = 0; 
        $arr = array();
        foreach( $questions as $question ){
          $question_copy = $question->replicate();
          $question_copy->exam_id = $model->id;
          $image = $question->image;
          if($image)
            $question_copy->image = Helper::copy_image($image,'questions',$counter,$teacher_id);
          $arr[] = $question_copy->toArray();
          $counter++;
        }
        if($arr)
        Question::insert($arr);

        User::where('id',$teacher_id)->increment('exams_number');
        return Helper::return([
          'id' => $model->id
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function publish_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'        => "required|array",
          'id.*'      => "required|exists:subscrptions,student_id,teacher_id,{$teacher_id}", 
          'exam_id'   => "required|exists:exams,id,teacher_id,{$teacher_id}"       
        ]);
        $id      = $req->input('id');
        $exam_id = $req->input('exam_id');
        $exam      = Exam::where('id',$exam_id);
        $exam_data = $exam->first();
        // if($exam_data->is_published == 1)
          // return Helper::returnError(Lang::get('messages.published_before'));
        if($exam_data->status == 'OFF')
          return Helper::returnError(Lang::get('messages.closed_exam'));

        $ExamRequests = ExamRequest::where('exam_id',$exam_id)->whereIn('student_id',$id)->get(['student_id']);

        $not_allowed = array();
        foreach($ExamRequests as $ExamRequest){
          $search = array_search($ExamRequest->student_id, $id);
          array_splice($id, $search, 1); 
        }

        $subscrptions = Subscrption::whereIn('student_id',$id)->where('status','ON')->get(['student_id']);
        $arr = array();
        foreach($subscrptions as $subscrption){
          $Helper = new Helper();
          $Helper->exam_id    =  $exam_id;
          $Helper->student_id =  $subscrption->student_id;
          $Helper->teacher_id =  $teacher_id;
          $arr[] = $Helper->toArray();
        }
        
        ExamRequest::insert($arr);
        if($exam_data->is_published == 0)
        $exam->update([ 'is_published' => 1 ]);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function add_mcq_question(Request $req)
    {try{ 
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'main_question'   => "nullable|string|max:100000",
          'question'        => "required|string|max:255",
          'true_respond'    => "required|string|max:100",
          'degree'          => "required|numeric|between:1,1000",
          'responds'        => "required|array|max:10",
          'responds.*'      => "required|string|max:100|distinct",
          'outside_counter' => "required|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $id               = $req->input('exam_id');
        $model = Exam::where('id',$id);
        $model_data = $model->first(['is_published','question_no']);
        if($model_data->is_published)
          return Helper::returnError(Lang::get('messages.published'));
        $questions_limit = Setting::where('key','questions_limit')->first()->value;
        if($model_data->question_no >= $questions_limit)
          return Helper::returnError(Lang::get('messages.max_questions').$questions_limit);
        $responds         = $req->input('responds');
        $true_respond     = $req->input('true_respond');
        if(!in_array($true_respond, $responds))
          return Helper::returnError(Lang::get('messages.not_in_responds'));

        $degree           =  (double)$req->input('degree');
        
        
        $my_arr = $req->all(['main_question','question','responds','true_respond','outside_counter','inside_counter']);
        $my_arr['image']         = NULL;
        $my_arr['question_type'] = 'M';
        $my_arr['degree']        = $degree;
        $my_arr['exam_id']       = $id;
        $question_model = new Question($my_arr);
        $question_model->save();
        
        $model->increment('degree',$degree);
        $model->increment('question_no');
        return Helper::return([
          'id' => $question_model->id
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function add_write_question(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'main_question'   => "nullable|string|max:100000",
          'question'        => "required|string|max:255",
          'degree'          => "required|numeric|between:1,1000",
          'outside_counter' => "required|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $id               = $req->input('exam_id');
        $model = Exam::where('id',$id);
        $model_data = $model->first(['is_published','question_no']);
        if($model_data->is_published)
          return Helper::returnError(Lang::get('messages.published'));
        $questions_limit = Setting::where('key','questions_limit')->first()->value;
        if($model_data->question_no >= $questions_limit)
          return Helper::returnError(Lang::get('messages.max_questions').$questions_limit);
          
        $degree           =  (double)$req->input('degree');
        
        $my_arr = $req->all(['main_question','question','outside_counter','inside_counter']);
        $my_arr['image']         = NULL;
        $my_arr['question_type'] = 'W';
        $my_arr['degree']        = $degree;
        $my_arr['exam_id']       = $id;
        $question_model = new Question($my_arr);
        $question_model->save();
        
        $model->increment('degree',$degree);
        $model->increment('question_no');
        return Helper::return([
          'id' => $question_model->id
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_question_image(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'image'           => 'required|image|mimes:jpeg,png,jpg|max:2000',
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'question_id'     => "required|string|exists:mongodb.questions,_id",
        ]);
        $image           = $req->file('image');
        $exam_id         = (int)$req->input('exam_id');
        $question_id     = $req->input('question_id');
        
        $where = array(
          'exam_id'     => $exam_id,
          '_id' => $question_id,
        );

        $question_model = Question::where($where);
        $model_data     = $question_model->first();
        if(!$model_data)
          return Helper::returnError(Lang::get('messages.not_allowed'));
        
        $model = Exam::where('id',$exam_id);
        $is_published = $model->first(['is_published'])->is_published;
        if($is_published)
          return Helper::returnError(Lang::get('messages.published'));
        
        if($model_data->image)
          Helper::image($image,'update','questions',$model_data->image,NULL,$teacher_id);
        else{
          $url = Helper::image($image,'add','questions');
          $question_model->update(['image' => $url]);
        }
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_mcq_question(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'question_id'     => "required|string|exists:mongodb.questions,_id",
          'main_question'   => "nullable|string|max:100000",
          'question'        => "required|string|max:255",
          'true_respond'    => "required|string|max:100",
          'degree'          => "required|numeric|between:1,1000",
          'responds'        => "required|array|max:10",
          'responds.*'      => "required|string|max:100|distinct",
          'outside_counter' => "required|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $exam_id         = $req->input('exam_id');
        $question_id     = $req->input('question_id');
        $where = array(
          'exam_id'     => $exam_id,
          '_id' => $question_id,
          'question_type' => 'M' 
        );

        $question_model = Question::where($where);
        $model_data     = $question_model->first();
        if(!$model_data)
          return Helper::returnError(Lang::get('messages.not_allowed'));
        
        $model = Exam::where('id',$exam_id);
        $is_published = $model->first(['is_published'])->is_published;
        if($is_published)
          return Helper::returnError(Lang::get('messages.published'));

        $responds         = $req->input('responds');
        $true_respond     = $req->input('true_respond');
        if(!in_array($true_respond, $responds))
          return Helper::returnError(Lang::get('messages.not_in_responds'));

        $degree           =  (double)$req->input('degree');

        
        $my_arr = $req->all(['main_question','question','true_respond','outside_counter','inside_counter']);
        $my_arr['responds']      = $responds;
        $my_arr['degree']        = $degree;

        $new_degree = $degree - $model_data->degree;    
        $question_model->update($my_arr);
        if($new_degree != 0)
        $model->increment('degree',$new_degree);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_write_question(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'question_id'     => "required|numeric|mongodb.questions,_id",
          'main_question'   => "nullable|string|max:100000",
          'question'        => "required|string|max:255",
          'degree'          => "required|numeric|between:1,1000",
          'outside_counter' => "required|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $exam_id         = $req->input('exam_id');
        $question_id     = $req->input('question_id');
        $where = array(
          'exam_id'     => $exam_id,
          '_id' => $question_id,
          'question_type' => 'W' 
        );

        $question_model = Question::where($where);
        $model_data     = $question_model->first();
        if(!$model_data)
          return Helper::returnError(Lang::get('messages.not_allowed'));
        
        $model = Exam::where('id',$exam_id);
        $is_published = $model->first(['is_published'])->is_published;
        if($is_published)
          return Helper::returnError(Lang::get('messages.published'));

        $degree           =  (double)$req->input('degree');
        $my_arr = $req->all(['main_question','question','outside_counter','inside_counter']);
        $my_arr['degree']        = $degree;

        $new_degree = $degree - $model_data->degree;    
        $question_model->update($my_arr);
        if($new_degree != 0)
        $model->increment('degree',$new_degree);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function delete_question(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'question_id'     => "required|string|exists:mongodb.questions,_id",
        ]);
        $exam_id         = $req->input('exam_id');
        $question_id     = $req->input('question_id');
        $where = array(
          'exam_id'     => $exam_id,
          '_id' => $question_id,
        );

        $question_model = Question::where($where);
        $model_data     = $question_model->first();
        if(!$model_data)
          return Helper::returnError(Lang::get('messages.not_allowed'));
        
        $model = Exam::where('id',$exam_id);
        $is_published = $model->first(['is_published'])->is_published;
        if($is_published)
          return Helper::returnError(Lang::get('messages.published'));

        $question_model->delete();
        $model->decrement( 'degree' , $model_data->degree );
        $model->decrement('question_no');
        if($model_data->image)
          Helper::delete_image('questions',$model_data->image);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'              => "required|exists:exams,id,teacher_id,{$teacher_id}",
          'year'            => 'required|in:1,2,3',
          'exam_name'       => 'required|string|max:100',
          'duration'        => 'required|numeric|between:1,360',
          'desc'            => 'nullable|string|max:100', 
        ]);
        $id = $req->input('id');

        $model = Exam::where('id',$id);
        $is_published = $model->first(['is_published'])->is_published;
        if($is_published)
          return Helper::returnError(Lang::get('messages.published'));

        $my_arr = $req->all(['year','exam_name','duration','desc']);

        $model = Exam::where('id',$id);
        $model->update($my_arr);
        
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function delete_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
        ]);
        $id         = $req->input('id');

        $model = new Exam();
        $where = array(
          'id' => $id
        );
        $model = $model::where($where);
        $model->update([
          'is_hide'  => 1
        ]);

        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function close_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
        ]);
        $id         = $req->input('id');

        $model = new Exam();
        $where = array(
          'id' => $id
        );
        $model = $model::where($where);
        $status = Helper::disable($model,'status','ON','OFF','ON','OFF');
        
        return Helper::return([
          'status' => $status
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
}
