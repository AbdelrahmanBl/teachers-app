<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

use App\Models\ExamRequest;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Solve;
use App\Models\Subscrption;
use App\Models\Notification;
use App\Models\Message;
use App\Models\Appointment;
use App\Models\TempStudent;

use App\Helper;
use validate;
use DB;
use Carbon\Carbon;
use Hash;

class studentController extends Controller
{
    protected $userStorage = 'storage/profiles';


    public function get_profile(Request $req)
    {try{
        $student_id      = $req->get('id');

        $model       = new User();
        $model_select  = $model->where('id',$student_id);
        $model_data    = $model_select->first(['first_name','last_name','mobile','image']);
        $model_data->image = $model_data->getImage;

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
        $student_id      = $req->get('id');

        $req->validate([
          'teacher_id'   => "nullable|numeric|exists:subscrptions,teacher_id,student_id,{$student_id}"
        ]);
        $paginate        = (int)$req->get('paginate');
        $teacher_id      = (int)$req->get('teacher_id');

        $where = array(
          'exam_requests.status'      => 'WAITING',
          'exam_requests.student_id'  => $student_id,
          'exams.status'              => 'ON',
          'subscrptions.student_id'   => $student_id,
          'subscrptions.status'       => 'ON'
        );
        

        $select = ['exam_requests.id as request_id','exam_requests.created_at','exam_requests.teacher_id','users.image','users.first_name','users.last_name'
             ,'exams.exam_name','exams.desc','exams.degree','exams.question_no', 'exams.duration'];
        $model       = new ExamRequest();
        $model_select  = $model->where($where); 
        if($teacher_id)
          $model_select->where('exam_requests.teacher_id',$teacher_id);//$where[] = ['exam_requests.teacher_id',$teacher_id]; 
        $model_data    = $model_select->join('users','exam_requests.teacher_id','users.id')->join('subscrptions','exam_requests.teacher_id','subscrptions.teacher_id')->join('exams','exam_requests.exam_id','exams.id')->select($select)->orderBy('exam_requests.created_at','DESC')->paginate($paginate);

        $model_data->getCollection()->transform(function($item) {
          $file = "{$this->userStorage}/{$item->image}";
          $file = file_exists(public_path($file)) && $item->image ? asset($file) : asset("default.jpg") ;
          $item->image = $file;
          return $item;
        });

        // $where = array(
        //   'subscrptions.student_id'  => $student_id,
        //   'subscrptions.status'      => 'ON',
        // );
        $select = array('users.id','users.first_name','users.last_name');
        $model_select  = $model->where($where);  
        $teachers = $model_select->join('users','exam_requests.teacher_id','users.id')->join('subscrptions','exam_requests.teacher_id','subscrptions.teacher_id')->join('exams','exam_requests.exam_id','exams.id')->select($select)->get();
        if(count($teachers) > 1){
        $teacher_arr = array();
        foreach($teachers as $teacher){
          $teacher_arr[] = $teacher->id;
        }
        $where = array(
          'student_id'      => $student_id,
          'status'          => 'WAITING',

        );
        // $teachers_data = $model::where($where)->whereIn('teacher_id',$teacher_arr)->get();
        $added_teachers = array();
        $teachers_all   = array();
        foreach( $teachers as $teacher ){
          if(in_array($teacher->id, $added_teachers))
            continue;
          $teacher->exams_number = $teachers->where('id',(int)$teacher->id)->count();
          $teachers_all[]        = $teacher;
          $added_teachers[]      = $teacher->id;
        }
        }

        return Helper::return([
            'exams'   => $model_data,
            'teachers' => (count($teachers) > 1)? $teachers_all : $teachers 
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
        $student_id      = $req->get('id');
        $req->validate([
          'request_id'   => "required|numeric|exists:exam_requests,id,student_id,{$student_id}"
        ]);
        $request_id = (int)$req->get('request_id');
        $exam_request = ExamRequest::where('id',$request_id);
        $exam_request_data = $exam_request->first();
        if($exam_request_data->status == 'WAITING')
          return Helper::returnError(Lang::get('messages.must_start_exam'));
        if($exam_request_data->status != 'IN_EXAM')
          return Helper::returnError(Lang::get('messages.not_in_exam'));
        if($exam_request_data->end_at < date('Y-m-d H:i:s')){
          $exam_request->update(['status' => 'DICONNECTED']);
          return Helper::returnError(Lang::get('messages.not_in_exam'));
        }

        $exam_id     = (int)$exam_request_data->exam_id;
        $exam_model  = Exam::where('id',$exam_id);
        $exam_data   = $exam_model->first(['exam_name','desc','degree','question_no','duration','is_rtl']);

        $question_model = Question::where('exam_id',$exam_id);
        $question_data  = $question_model->get(['_id','image','question_type','main_question','question','responds','outside_counter','inside_counter','degree']);

        return Helper::return([
            'now'       => date('Y-m-d H:i:s'),
            'end_at'    => $exam_request_data->end_at,
            'exam'      => $exam_data,
            'questions' => $question_data,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_exams_mark(Request $req)
    {try{
        $student_id      = $req->get('id');
        
        $req->validate([
          'teacher_id'   => "nullable|numeric|exists:subscrptions,teacher_id,student_id,{$student_id}"
        ]);
        $paginate        = (int)$req->get('paginate');
        $teacher_id      = (int)$req->get('teacher_id');

        $where = array(
          'exam_requests.student_id'   => $student_id,
          'exam_requests.is_corrected' => 1,
          'subscrptions.student_id'    => $student_id,
          'subscrptions.status'        => 'ON'
        );
        if($teacher_id)
          $where[] = ['exam_requests.teacher_id',$teacher_id];
        $select = ['exam_requests.id as request_id','exam_requests.start_at as created_at','exam_requests.teacher_id','exam_requests.total_degree','exam_requests.duration_solve','users.image','users.first_name','users.last_name'
             ,'exams.exam_name','exams.desc','exams.degree','exams.question_no', 'exams.duration'];
        
        $model       = new ExamRequest();
        $model_select  = $model->where($where);  
        $model_data    = $model_select->join('users','exam_requests.teacher_id','users.id')->join('subscrptions','exam_requests.teacher_id','subscrptions.teacher_id')->join('exams','exam_requests.exam_id','exams.id')->select($select)->orderBy('exam_requests.created_at','DESC')->paginate($paginate);

        $model_data->getCollection()->transform(function($item) {
          $file = "{$this->userStorage}/{$item->image}";
          $file = file_exists(public_path($file)) && $item->image ? asset($file) : asset("default.jpg") ;
          $item->image = $file;
          return $item;
        }); 
        
        $where = array(
          'subscrptions.student_id'  => $student_id,
          'subscrptions.status'      => 'ON',
        );
        $select = array('users.id','users.first_name','users.last_name');
        $teachers = Subscrption::where($where)->join('users','subscrptions.teacher_id','users.id')->select($select)->get();
        
        
        if(count($teachers) > 1){
        $teacher_arr = array();
        foreach($teachers as $teacher){
          $teacher_arr[] = $teacher->id;
        }
        $where = array(
          'student_id'   => $student_id,
          'is_corrected' => 1
        );
        $teachers_data = $model::where($where)->whereIn('teacher_id',$teacher_arr)->get();
        $teachers_all  = array();
        foreach( $teachers as $teacher ){
          $teacher->exams_number = $teachers_data->where('teacher_id',$teacher->id)->count();
          $teachers_all[] = $teacher;
        }
        }
        return Helper::return([
            'exams'   => $model_data,
            'teachers' => (count($teachers) > 1)? $teachers_all : $teachers 
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_exam_mark(Request $req)
    {try{
        $student_id      = $req->get('id');
        $req->validate([
          'request_id'   => "required|numeric|exists:exam_requests,id,student_id,{$student_id}"
        ]);
        $request_id = (int)$req->get('request_id');
        $exam_request = ExamRequest::where('id',$request_id);
        $selected_attr = ['duration_solve','total_degree','start_at','status','is_corrected','student_id','exam_id'];
        $exam_request_data = $exam_request->first($selected_attr);

        if($exam_request_data->is_corrected == 0)
          return Helper::returnError(Lang::get('messages.not_allowed'));
  
        $exam_id     = (int)$exam_request_data->exam_id;

        $model        = Exam::where('id',$exam_id);
        $model_data   = $model->first(['is_rtl','exam_name','desc','duration','degree']);

        $question_model = Question::where('exam_id',$exam_id);
        $question_data  = $question_model->get();

        $solve_model  = Solve::where(array('exam_id' => $exam_id , 'student_id' => (int)$exam_request_data->student_id));
        $solve_data   = $solve_model->get();

        $exam_data   = $question_data->transform(function ($value) use ($solve_data){
          $solve  =  $solve_data->where('question_id',$value['_id'])->first();

          $map['id']              = $solve->_id;
          $map['question_id']     = $value['_id'];
          $map['question_type']   = $value['question_type'];
          $map['main_question']   = $value['main_question'];
          $map['question']        = $value['question'];
          $map['true_respond']    = $value['true_respond'];
          $map['responds']        = $value['responds'];
          $map['degree']          = $value['degree'];
          $map['student_respond'] = $solve->respond;
          $map['student_images']  = $solve->getImages;
          $map['student_degree']  = $solve->degree;
          $map['outside_counter'] = $value['outside_counter'];
          $map['inside_counter']  = $value['inside_counter'];
          return $map;
        });
        
        return Helper::return([
            'exam_request_data' => $exam_request_data,
            'exam'              => $model_data,
            'exam_data'         => $question_data,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_notifications_count(Request $req)
    {try{
        $student_id      = $req->get('id');
        
        $where = array(
          'student_id'  => $student_id,
          'status'      => 'ON'
        );
        $subscrptions     = Subscrption::where($where)->get();
        $subscrptions_arr = array();
        foreach($subscrptions as $subscrption){
          if(!in_array($subscrption->teacher_id, $subscrptions_arr))
            $subscrptions_arr[] = $subscrption->teacher_id;
        }

        $where = array(
          'reciever_id'   => $student_id,
          'is_seen'       => 0
        );
      
        $new_count          = Notification::whereIn('sender_id',$subscrptions_arr)->where($where)->count(); 

        return Helper::return([
          'unseen_count'   => $new_count
        ]);
      }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_notifications(Request $req)
    {try{
        $student_id      = (int)$req->get('id');
        $new_count       = (int)$req->get('unseen_count'); 

        $where = array(
          'student_id'  => $student_id,
          'status'      => 'ON'
        );
        $subscrptions     = Subscrption::where($where)->get();
        $subscrptions_arr = array();
        foreach($subscrptions as $subscrption){
          if(!in_array($subscrption->teacher_id, $subscrptions_arr))
            $subscrptions_arr[] = (int)$subscrption->teacher_id;
        }

        $where = array(
          'reciever_id'   => $student_id,
        );
        $notifications_seen = Notification::whereIn('sender_id',$subscrptions_arr)->where($where)->where('is_seen',1)->orderBy('created_at','DESC')->paginate(4);
        $notifications_not_seen  = Notification::whereIn('sender_id',$subscrptions_arr)->where($where)->where('is_seen',0)->limit($new_count);
        $notifications_not_seen_data = $notifications_not_seen->get();

        $senders           = array();
        $unseen_arr        = array();
        foreach($notifications_seen as $notification){
          if(!in_array($notification->sender_id, $senders))
            $senders[] = (int)$notification->sender_id;
        }
        foreach($notifications_not_seen_data as $notification){
          if(!in_array($notification->sender_id, $senders))
            $senders[] = (int)$notification->sender_id;
          
          $unseen_arr[]  = $notification->_id;
        }
        $senders_data = User::whereIn('id',$senders)->get();

        $notifications_old = $notifications_seen->transform(function ($value) use ($senders_data){
          $sender = $senders_data->where('id',(int)$value['sender_id'])->first();

          $map['image']       = $sender->getImage;
          $map['first_name']  = $sender->first_name;
          $map['last_name']   = $sender->last_name;
          $map['event']       = $value['event'];
          $map['created_at']  = $value['created_at'];

          return $map;
        });

        $notifications_new_collection = $notifications_not_seen_data->transform(function ($value) use ($senders_data){
          $sender = $senders_data->where('id',(int)$value['sender_id'])->first();

          $map['image']       = $sender->getImage;
          $map['first_name']  = $sender->first_name;
          $map['last_name']   = $sender->last_name;
          $map['event']       = $value['event'];
          $map['created_at']  = $value['created_at'];

          return $map;
        });

        $notifications_new = array();
        foreach($notifications_new_collection as $notify){
          array_unshift($notifications_new, $notify);
        }

        Notification::whereIn('_id',$unseen_arr)->update(['is_seen' => 1 , 'seen_at' => date('Y-m-d H:i:s')]);
        return Helper::return([
          'total'       => $notifications_seen->total() + $notifications_not_seen_data->count() ,
          'current_page'=> $notifications_seen->currentPage(),
          'per_page'    => $notifications_seen->perPage(),
          'seen'        => $notifications_old,
          'unseen'      => $notifications_new,
        ]);
      }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_messages(Request $req)
    {try{
        $student_id      = $req->get('id');

        $where = array(
          'student_id'    => $student_id,
          'status'        => 'ON'
        );
        $subscrptions    = Subscrption::where($where)->get();

        $subscrptions_arr = array();
        $appointments_arr = array();
        foreach($subscrptions as $subscrption){
          $subscrptions_arr[] = (int)$subscrption->teacher_id;
          $appointments_arr[] = (int)$subscrption->appointment_id;
        }
        $year = (int)$req->get('year');

        $where = array('target' => NULL);
        $model = new Message();
        $model_select = $model::orWhere($where)->orWhere(function($query) use ($year) {
          $query->where('type','year')->where('target',$year); 
          })->orWhere(function($query) use ($appointments_arr) {
          $query->where('type','group')->whereIn('target',$appointments_arr); 
          })->whereIn('teacher_id',$subscrptions_arr);
        $model_data   = $model_select->orderBy('created_at','DESC')->paginate(10);     

        $appointments_data = Appointment::whereIn('appointments.id',$appointments_arr)->join('days','days.id','appointments.days_id')->join('users','users.id','appointments.teacher_id')->select(['appointments.id','days.day as days','appointments.time_from','users.first_name','users.last_name','users.image','users.id as teacher_id'])->get(); //whereIn('appointments.teacher_id',$subscrptions_arr)->

        $model_collection = $model_data->transform(function ($value) use ($appointments_data){
          $teacher = $appointments_data->where('teacher_id',(int)$value['teacher_id'])->first();
          $target = NULL;
          if($value['type'] == 'group'){
            $appointment = $appointments_data->where('id',(int)$value['target'])->first();
            $target = new Helper();
            $target->days      = $appointment->days;
            $target->time_from = $appointment->time_from;
          }
          else if($value['type'] == 'year')
            $target = $value['target'];

          $file = "{$this->userStorage}/{$teacher->image}";
          $file = file_exists(public_path($file)) && $teacher->image ? asset($file) : asset("default.jpg") ;
            
          $map['id']          = $value['_id'];
          $map['fullname']    = $teacher->first_name .' '. $teacher->last_name;
          $map['image']       = $file;
          $map['type']        = $value['type'];
          $map['target']      = $target;
          $map['message']     = $value['message'];
          $map['created_at']  = $value['created_at'];

          return $map;
        });
        return Helper::return([
          'total'       => $model_data->total(),
          'current_page'=> $model_data->currentPage(),
          'per_page'    => $model_data->perPage(),
          'messages'    => $model_collection
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
 
    public function get_teachers_unsubscribed(Request $req)
    {try{
        $student_id      = $req->get('id');

        $where = array(
          'subscrptions.student_id'  => $student_id
        );
        $subscribed = Subscrption::where($where)->join('temp_students','temp_students.id','subscrptions.temp_id')->select('subscrptions.teacher_id')->distinct()->get();
        $subscribed_arr = array();
        foreach($subscribed as $subscribe){
          $subscribed_arr[] = $subscribe->teacher_id;
        }
        $teachers = User::where('type','T')->whereNotIn('id',$subscribed_arr)->get(['id','first_name','last_name','accept_register']);
        return Helper::return([
          'teachers'    => $teachers
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
        $teacher_id      = (int)$req->get('teacher_id');

        $student_id      = $req->get('id');
        $year            = $req->get('year');

        $where = array(
            'appointments.teacher_id' => $teacher_id,
            'appointments.year'       => $year,
        );

        $model = new Appointment();
        $model_select  = $model->where($where);
        $select = ['appointments.id','days.day as days','appointments.time_from','appointments.time_to','appointments.status'];
        $model_data    = $model_select->join('days','appointments.days_id','days.id')->select($select)->get();
            
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
    /*------------------------------------------------------------*/
    public function start_exam(Request $req)
    {try{
        $student_id      = $req->get('id');
        $student_status  = $req->get('student_status');
        if($student_status != 'WAITING' && $student_status != NULL)
          return Helper::returnError(Lang::get('messages.exam_once'));
        $req->validate([
          'request_id'   => "required|numeric|exists:exam_requests,id,student_id,{$student_id}"
        ]);
        $request_id = (int)$req->input('request_id');
        $exam_request = ExamRequest::where('id',$request_id);
        $exam_request_data = $exam_request->first();
        if($exam_request_data->status != 'WAITING')
          return Helper::returnError(Lang::get('messages.not_waiting'));
        
        $exam_id     = $exam_request_data->exam_id;
        $exam_model  = Exam::where('id',$exam_id);
        $exam_data   = $exam_model->first();
        if($exam_data->status == 'OFF')
          return Helper::returnError(Lang::get('messages.exam_is_off'));

        $start_at = date('Y-m-d H:i:s');
        $end_time = strtotime("+{$exam_data->duration} minutes", strtotime($start_at));
        $end_at = date('Y-m-d H:i:s',$end_time);

        $exam_request->update(['status' => 'IN_EXAM','start_at' => $start_at,'end_at' => $end_at]);
        User::where('id',$student_id)->update(['student_status' => 'IN_EXAM']);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function end_solve_exam(Request $req)
    {try{
        $now    = $req->input('solve_time');//date('Y-m-d H:i:s');
        $student_id      = $req->get('id');
        $req->validate([
          'request_id'   => "required|numeric|exists:exam_requests,id,student_id,{$student_id}",
          'solves'       => 'nullable|array',
          'solves.*.question_id' => 'required|string|exists:mongodb.questions,_id|distinct',
          'solves.*.respond'     => 'nullable|string|max:500',
          'solves.*.images'      => 'nullable|array|max:2',
          'solves.*.images.*'    => 'nullable|image|mimes:jpeg,png,jpg|max:2000',

        ]);
        $request_id = (int)$req->input('request_id');
        $solves = $req->all('solves')['solves'];
        $exam_request = ExamRequest::where('id',$request_id);
        $exam_request_data = $exam_request->first();
        if($exam_request_data->status != 'IN_EXAM')
          return Helper::returnError(Lang::get('messages.not_in_exam'));
        // Check Layout For Long Requests
        $delay_time = 5; // In minutes
        $layout = round((strtotime($now) - strtotime(  $exam_request_data->end_at  )) / 60,2);
        if($layout > $delay_time){
          $exam_request->update(['status' => 'DICONNECTED']);
          User::where('id',$student_id)->update(['student_status' => 'WAITING']);
          return Helper::returnError(Lang::get('messages.not_in_exam'));
        }
        
        $exam_id     = (int)$exam_request_data->exam_id;
        $questions   = Question::where('exam_id',$exam_id)->get(['question_type','responds','degree','true_respond']);

        if(count($solves) != count($questions))
          return Helper::returnError(Lang::get('messages.wrong_questions_no'));
        
        $solves_arr  = Helper::handle_solves($solves,$questions,$student_id,$exam_id,$exam_request_data->teacher_id);
        if($solves_arr == 'distinct_respond')
          return Helper::returnError(Lang::get('messages.distinct_respond'));
        else if($solves_arr == 'question_not_found')
          return Helper::returnError(Lang::get('messages.question_not_found'));
        $startTime     = Carbon::parse($exam_request_data->start_at);
        $endTime       = Carbon::parse($now);

        $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S');
        Solve::insert($solves_arr['solves_arr']);
        $exam_request->update(['status' => 'COMPLETED','duration_solve' => $totalDuration , 'total_degree' => $solves_arr['total_degree'] ]);
        User::where('id',$student_id)->update(['student_status' => 'WAITING']);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function change_password(Request $req)
    {try{
        $student_id      = $req->get('id');
        $req->validate([
          'old_password'     => 'required|string|min:6|max:16',
          'new_password'     => 'required|string|min:6|max:16',
          'verify_password'  => 'required|string|same:new_password',
        ]);
        $password         = $req->get('password');
        $old_password     = $req->input('old_password');
        $new_password     = $req->input('new_password');

        $model = new User();
        $where = array(
          'id'   => $student_id,
        );
        $model = $model::where($where);

        if(!Hash::check($old_password, $password)){
          $model->increment('failed_try');
          return Helper::returnError(Lang::get('auth.password'));
        }
        $update_password = Hash::make($new_password);
        $model->update(['password' => $update_password]);
        
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_profile(Request $req)
    {try{
        $student_id      = $req->get('id');
        $temp_id         = Subscrption::where('student_id',$student_id)->first()->temp_id;
        $req->validate([
          'first_name'    => 'required|string|max:15',
          'last_name'     => 'required|string|max:40',
          'email'         => "required|email|max:64|unique:users,email,{$student_id},id|unique:temp_students,email,{$temp_id},id",
          'mobile'        => 'required|string|max:11',
        ]);
        

        $model = new User();
        $where = array(
          'id'   => $student_id,
        );
        $model = $model::where($where);
        $model->update($req->all(['first_name','last_name','email','mobile']));
        
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function update_profile_image(Request $req)
    {try{
        $student_id      = $req->get('id');
        $req->validate([
          'image'    => 'required|image|mimes:jpeg,png,jpg|max:2000',
        ]);
        $student_image = $req->get('image');
        $image         = $req->file('image');

        $model = new User();
        $where = array(
          'id'   => $student_id,
        );
        $model = $model::where($where);
        $url   = Helper::image($image,'add','profiles',NULL,$student_id);

        $model->update(['image' => $url]);
        
        Helper::delete_image($this->userStorage,$student_image);
        
        return Helper::return([
          'url'   => asset("{$this->userStorage}/{$url}")
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function subscribe(Request $req)
    {try{
        $student_id         = $req->get('id');
        $req->validate([
          'teacher_id'        => "required|numeric|exists:users,id,type,T",
          'appointment_id'    => "required|numeric|exists:appointments,id",
        ]);
        $teacher_id     = (int)$req->input('teacher_id');
        $appointment_id = (int)$req->input('appointment_id');
        $year           = (int)$req->get('year');

        $where = array(
          'teacher_id'   => $teacher_id,
          'student_id'   => $student_id,
        );
        $chk_subscribe = TempStudent::where($where)->count();
        if($chk_subscribe > 0)
          return Helper::returnError(Lang::get('messages.student_subscribed'));

        $where = array(
            'id'           => $appointment_id,
            'teacher_id'   => $teacher_id,
            'year'         => $year,
        );

        $accept_register = User::where('id',$teacher_id)->first()->accept_register;
        if($accept_register == false)
            return Helper::returnError(Lang::get('messages.closed_teacher'));
        $chk_appointment = Appointment::where($where)->first();
        if(!$chk_appointment)
            return Helper::returnError(Lang::get('messages.invalid_appointment'));
        if($chk_appointment->status == 'OFF')
            return Helper::returnError(Lang::get('messages.closed_appointment'));

        $my_arr['teacher_id']     = $teacher_id;
        $my_arr['appointment_id'] = $appointment_id;
        $my_arr['student_id']     = $student_id;
        $my_arr['year']           = $year;
        $my_arr['first_name']     = $req->get('first_name');
        $my_arr['last_name']      = $req->get('last_name');
        $my_arr['mobile']         = $req->get('mobile');
        $my_arr['parent_mobile1'] = $req->get('parent_mobile1');
        $my_arr['parent_mobile2'] = $req->get('parent_mobile2');
        $my_arr['process']        = 'subscrption';
        $my_arr['type']           = 'S';

        $model = new TempStudent($my_arr);
        $model->save();
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
}
