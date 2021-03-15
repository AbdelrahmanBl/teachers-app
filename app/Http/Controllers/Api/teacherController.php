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
use App\Models\Solve;
use App\Models\Setting;
use App\Models\Notification;
use App\Models\Message;

use App\Helper;
use validate;
use DB;
use Hash;
class teacherController extends Controller
{
    protected $responds_sequence = '#S$S#';
    protected $userStorage       = 'storage/profiles';
    protected $degreeFrom        = 0.1;
    protected $degreeTo          = 1000;


    public function get_profile(Request $req)
    {try{
        $teacher_id      = $req->get('id');

        $model = new User();
        $model_select  = $model->where('id',$teacher_id);
        $model_data    = $model_select->first(['first_name','last_name','image','mobile','accept_register']);
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
    public function get_package(Request $req)
    {
      try{
        $teacher_id      = $req->get('id');
        $package_id      = $req->get('package_id');

        $model_data = Package::find($package_id)->makeHidden(['id','created_at','updated_at','status','price']);
        $model_data->image = $model_data->getImage;

        return Helper::return([
            'package'   => $model_data
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
        $teacher_id      = $req->get('id');
        $pagination      = $req->get('pagination');
        $year            = (int)$req->get('year');

        $where = array(
          'appointments.teacher_id' => $teacher_id
        );
        if($year)
          $where[] = ['appointments.year',$year];
        $model = new Appointment();
        $model_select  = $model->where($where);
        $select = ['appointments.id','days.day as days','appointments.time_from','appointments.time_to','appointments.year','appointments.status','appointments.max_class_no'];
        $model_data    = $model_select->join('days','appointments.days_id','days.id')->select($select)->orderBy('appointments.id','DESC')->paginate($pagination);
            
        $statistics = $model::where('teacher_id',$teacher_id)->get();
        return Helper::return([
            'all'        => $statistics->count(),
            'first'      => $statistics->where('year',1)->count(),
            'second'     => $statistics->where('year',2)->count(),
            'third'      => $statistics->where('year',3)->count(),
            'appointments'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_filter_appointments(Request $req)
    {try{
 
        $teacher_id      = $req->get('id');
        $year            = (int)$req->get('year');
        $exam_id         = (int)$req->get('exam_id');

        $where = array(
          'teacher_id' => $teacher_id,
          'year'       => $year
        );
        $model = new Appointment();
        $model_select  = $model->where($where);
        $select = ['appointments.id','days.day as days','appointments.time_from','appointments.max_class_no','appointments.current_class_no'];
        $model_data    = $model_select->join('days','appointments.days_id','days.id')->select($select)->get();
            
        if(count($model_data) == 0)
          return Helper::returnError(Lang::get('messages.no_appointments'));

        if($exam_id) {
          $students_data = Helper::getStudentsForPublish($req,$exam_id,$model_data->pluck('id'),$year); 
          $model_data->transform(function($appointment) use ($students_data){
            $appointment->count_published = $students_data->where('appointment_id',$appointment->id)->count();
            return $appointment->makeHidden(['max_class_no','current_class_no']);
          });
        }

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
    public function get_students(Request $req)
    {try{
        $teacher_id     = $req->get('id');
        $year           = (int)$req->get('year');
        $appointment_id = (int)$req->get('appointment_id');
        $pagination     = (int)$req->get('pagination');

        $model = new Subscrption();
        $where = array(
          'subscrptions.teacher_id'   => $teacher_id,
          'users.status'              => 'ON'
        );
        $model_select  = $model->where($where);
        if($year)
          $model_select->where('appointments.year',$year);
        if($appointment_id)
          $model_select->where('appointments.id',$appointment_id);
        
        $select = ['users.id','users.first_name','users.last_name','users.mobile','users.parent_mobile1','users.parent_mobile2','days.day as days','appointments.id as appointment_id','appointments.time_from','appointments.time_to','appointments.year','subscrptions.student_rate','subscrptions.status','users.created_at'];
        $model_data    = $model_select->join('users','users.id','subscrptions.student_id')->join('appointments','appointments.id','subscrptions.appointment_id')->orderBy('users.id','DESC')->join('days','appointments.days_id','days.id')->select($select)->paginate($pagination);
        $model_data->getCollection()->transform(function($item) {
          $item->fullname = "{$item['first_name']} {$item['last_name']}";
          return $item->makeHidden(['first_name','last_name']);
        });

        if($appointment_id)
          $where[]   = ['subscrptions.appointment_id',$appointment_id]; 
        $statistics  = $model::where($where)->join('users','users.id','subscrptions.student_id')->get();
        return Helper::return([
            'all'        => $statistics->count(),
            'first'      => $statistics->where('year',1)->count(),
            'second'     => $statistics->where('year',2)->count(),
            'third'      => $statistics->where('year',3)->count(),
            'students'   => $model_data
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
        $teacher_id     = $req->get('id');
        $year           = (int)$req->get('year');
        $pagination     = (int)$req->get('pagination');
        $appointment_id = (int)$req->get('appointment_id');

        $model = new TempStudent();
        $where = [
          ['teacher_id',$teacher_id],
          ['status','ON']
        ];
        $model_select  = $model->where($where);
        if($year)
          $model_select->whereHas('appointment',function($q) use ($year){
            $q->where('year',$year);
          });
        if($appointment_id)
          $model_select->whereHas('appointment',function($q) use ($appointment_id){
            $q->where('id',$appointment_id);
          });

        $model_data = $model_select->orderBy('id','DESC')->paginate($pagination);
        $model_data->getCollection()->transform(function($temp) {
          $temp->fullname       = $temp->student->fullname;
          $temp->mobile         = $temp->student->mobile;
          $temp->parent_mobile1 = $temp->student->parent_mobile1;
          $temp->time_from      = $temp->appointment->time_from;
          $temp->time_to        = $temp->appointment->time_to;
          $temp->year           = $temp->appointment->year;
          $temp->days           = $temp->appointment->day->day;

          return $temp->makeHidden(['appointment_id','teacher_id','status','updated_at','student','appointment']);
        });
        
        // $select = ['temp_students.id','temp_students.first_name','temp_students.last_name','temp_students.mobile','temp_students.parent_mobile1','temp_students.parent_mobile2','days.day as days','appointments.time_from','appointments.time_to','appointments.year'];
        // $model_data    = $model_select->join('appointments','appointments.id','temp_students.appointment_id')->join('days','appointments.days_id','days.id')->orderBy('temp_students.id','DESC')->select($select)->paginate($pagination);
  
        if($appointment_id)
          $where[]   = ['appointment_id',$appointment_id]; 
        $statistics  = $model::where($where)->get();

        return Helper::return([
            'all'        => $statistics->count(),
            'first'      => $statistics->where('appointment.year',1)->count(),
            'second'     => $statistics->where('appointment.year',2)->count(),
            'third'      => $statistics->where('appointment.year',3)->count(),
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
        
        $year               = (int)$req->get('year');
        $exam_id            = (int)$req->get('exam_id');

        $appointment_ids    = (array)$req->input('appointment_ids');

        if(count($appointment_ids) == 0)
          return Helper::returnError(Lang::get('messages.invalid_appointments'));

        $exam = Exam::where('id',$exam_id)->first();
        if($exam->year != $year)
          return Helper::returnError(Lang::get('messages.invalid_exam_year'));

        $model_data = Helper::getStudentsForPublish($req,$exam_id,$appointment_ids,$year); 
        
        if(count($model_data) == 0)
          return Helper::returnError(Lang::get('messages.no_published_students')); 
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
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'   => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}"
        ]);
        $exam_id = (int)$req->get('exam_id');

        $model       = Exam::where('id',$exam_id);
        $model_data  = $model->first(['id','exam_name','degree','duration','is_published','year','status','desc','is_rtl','created_at']);

        $question_model = Question::where('exam_id',$exam_id);
        $question_data  = $question_model->get(['_id','image','question_type','main_question','question','true_respond','responds','outside_counter','sub_outside_counter','inside_counter','degree','created_at']);

        $package_id     = $req->get('package_id');
        $exams_number   = $req->get('exams_number');
        $exams_limit    = Package::where('id',$package_id)->first()->exams_limit;
        return Helper::return([
            'exam'            => $model_data,
            'questions'       => $question_data,
            'remaining_exams' => ($exams_limit - $exams_number),
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
        $teacher_id      = $req->get('id');
        $req->validate([
          'request_id'   => "required|numeric|exists:exam_requests,id,teacher_id,{$teacher_id}"
        ]);
        $request_id = (int)$req->get('request_id');

        $exam_request = ExamRequest::where('id',$request_id)->first();
        $exam_id      = (int)$exam_request->exam_id;

        $model        = Exam::where('id',$exam_id);
        $model_data   = $model->first(['is_rtl']);

        $question_model = Question::where('exam_id',$exam_id);
        $question_data  = $question_model->get();

        $solve_model  = Solve::where(array('exam_id' => $exam_id , 'student_id' => (int)$exam_request->student_id));
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
          $map['sub_outside_counter'] = $value['sub_outside_counter'];
          $map['inside_counter']  = $value['inside_counter'];
          return $map;
        });
        return Helper::return([
            'exam'            => $model_data,
            'exam_data'       => $question_data,
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
        $teacher_id      = $req->get('id');
        $year            = (int)$req->get('year');
        $pagination      = $req->get('pagination');

        $where = array(
          'teacher_id'   => $teacher_id,
          'is_hide'      => 0
        );
        $model = new Exam();
        $model_select   = $model::where($where);
        if($year)
          $model_select->where('year',$year); 
        
        $model_data  = $model_select->select(['id','exam_name','question_no','degree','desc','duration','is_published','year','status','created_at'])->orderBy('id','DESC')->paginate($pagination);

        $statistics = $model::where($where)->get();
        return Helper::return([
            'all'        => $statistics->count(),
            'first'      => $statistics->where('year',1)->count(),
            'second'     => $statistics->where('year',2)->count(),
            'third'      => $statistics->where('year',3)->count(),
            'exams'      => $model_data,
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
        $teacher_id      = (int)$req->get('id');
        $req->validate([
          'exam_id'  => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'type'     => 'required|string|in:completed,not_marked,marked,not_exam,disconnected,in_exam',
        ]);
        $exam_id         = (int)$req->get('exam_id');
        $appointment_id  = (int)$req->get('appointment_id');
        $pagination      = (int)$req->get('pagination');
        $type            = $req->get('type');

        $where = array(
          'exam_requests.exam_id' => $exam_id,
          'subscrptions.teacher_id' => $teacher_id
        );
        if($appointment_id)
          $where[] = ['appointments.id',$appointment_id];

        $where[] = [ 'exam_requests.is_corrected' , ($type == 'completed')? 1 : 0 ];
        switch ($type) {
          case 'not_marked':
            $where[] = ['exam_requests.is_seen',0];
            $where[] = ['exam_requests.status','COMPLETED'];
            break;
          case 'marked':
            $where[] = ['exam_requests.is_seen',1];
            $where[] = ['exam_requests.status','COMPLETED'];
            break;
          case 'not_exam':
            $where[] = ['exam_requests.status','WAITING'];
            break;
          case 'disconnected':
            $where[] = ['exam_requests.status','DICONNECTED'];
            break;
          case 'in_exam':
            $where[] = ['exam_requests.status','IN_EXAM'];
            break;
        }

        $model = new ExamRequest();
        $model_select   = $model::where($where); 
        
        $model_data  = $model_select->join('exams','exam_requests.exam_id','exams.id')->join('users','exam_requests.student_id','users.id')->join('subscrptions','exam_requests.student_id','subscrptions.student_id')->join('appointments','subscrptions.appointment_id','appointments.id')->join('days','appointments.days_id','days.id')->select(['exam_requests.id','users.first_name','users.last_name','days.day','appointments.time_from','exam_requests.total_degree as student_degree','exam_requests.duration_solve','exam_requests.start_at','exam_requests.status'])->orderBy('exam_requests.id','DESC')->paginate($pagination);

        $where = array(
          'exam_requests.exam_id'   => $exam_id,
        );
        if($appointment_id)
          $where[] = ['appointments.id',$appointment_id];

        $statistics_select = $model::where($where);
        if($appointment_id){
          $statistics_select->where('subscrptions.teacher_id',$teacher_id);
          $statistics_select->join('subscrptions','exam_requests.student_id','subscrptions.student_id')->join('appointments','subscrptions.appointment_id','appointments.id')->select(['exam_requests.is_corrected','exam_requests.is_seen','exam_requests.status']);
        }
        
        $statistics = $statistics_select->get();
        return Helper::return([
            'completed'         => $statistics->where('is_corrected',1)->count(),
            'not_marked'        => $statistics->where('is_seen',0)->where('status','COMPLETED')->where('is_corrected',0)->count(),
            'marked'            => $statistics->where('is_seen',1)->where('status','COMPLETED')->where('is_corrected',0)->count(),
            'not_exam'          => $statistics->where('status','WAITING')->where('is_corrected',0)->count(),
            'disconnected'      => $statistics->where('status','DICONNECTED')->where('is_corrected',0)->count(),
            'in_exam'           => $statistics->where('status','IN_EXAM')->where('is_corrected',0)->count(),
            'exam_requests'     => $model_data,
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
        $teacher_id      = $req->get('id');

        $model = new Message();
        $where = array(
          'teacher_id'   => $teacher_id,
        );
        $model_select = $model::where($where);
        $model_data   = $model_select->orderBy('created_at','DESC')->paginate(10);     
        $appointments_data = Appointment::where('teacher_id',$teacher_id)->join('days','days.id','appointments.days_id')->select(['appointments.id','days.day as days','appointments.time_from'])->get();

        $model_collection = $model_data->transform(function ($value) use ($appointments_data,$req){
          $target = NULL;
          if($value['type'] == 'group'){
            $appointment = $appointments_data->where('id',(int)$value['target'])->first();
            $target = new Helper();
            $target->days      = $appointment->days;
            $target->time_from = $appointment->time_from;
          }
          else if($value['type'] == 'year')
            $target = $value['target'];

          $file = "{$this->userStorage}/{$req->get('image')}";
          $file = file_exists(public_path($file)) && $req->get('image') ? asset($file) : asset("default.jpg") ;
            
          $map['id']          = $value['_id'];
          $map['fullname']    = $req->get('first_name') .' '. $req->get('last_name');
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

    public function get_student_exams(Request $req)
    {try{
        $teacher_id      = $req->get('id');

        $student_id      = (int)$req->get('student_id');
        $paginate        = (int)$req->get('paginate');

        $model = new ExamRequest();
        $where = array(
          'exam_requests.teacher_id'   => $teacher_id,
          'exam_requests.student_id'   => $student_id,
          'exam_requests.is_corrected' => 1,
        );
        $select = array('exam_requests.id','exam_requests.status','exam_requests.duration_solve','exam_requests.total_degree','exams.exam_name','exams.degree','exams.duration','exam_requests.start_at');
        $model_select = $model::where($where);
        $model_data   = $model_select->join('exams','exams.id','exam_requests.exam_id')->select($select)->orderBy('exam_requests.created_at','DESC')->paginate($paginate);     
        
        return Helper::return([
          'exams'  => $model_data
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
    public function change_password(Request $req)
    {try{
        $teacher_id      = $req->get('id');
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
          'id'   => $teacher_id,
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
        $teacher_id      = $req->get('id');
        $req->validate([
          'first_name'    => 'required|string|max:15',
          'last_name'     => 'required|string|max:40',
          'email'         => "required|email|max:64|unique:users,email,{$teacher_id},id",
          'mobile'        => 'nullable|string|max:11',
        ]);
        

        $model = new User();
        $where = array(
          'id'   => $teacher_id,
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
        $teacher_id      = $req->get('id');
        $req->validate([
          'image'    => 'required|image|mimes:jpeg,png,jpg|max:2000',
        ]);
        $teacher_image = $req->get('image');
        $image         = $req->file('image');

        $model = new User();
        $where = array(
          'id'   => $teacher_id,
        );
        $model = $model::where($where);
        $url   = Helper::image($image,'add','profiles',NULL,$teacher_id);

        $model->update(['image' => $url]);

        Helper::delete_image($this->userStorage,$teacher_image);

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
    public function accept_students(Request $req)
    {try{
        $teacher_id      = $req->get('id');        
        $req->validate([
          'students'        => "required|array",
          'students.*'      => "required|numeric|exists:temp_students,id,teacher_id,{$teacher_id}",
        ]);
        $package_id      = $req->get('package_id');
        $ids = $req->input('students');
        $where = array(
          'teacher_id' => $teacher_id,
          // 'type'       => 'register'
        );
        $valid_ids = Subscrption::where($where)->whereIn('temp_id',$ids)->count();
        if($valid_ids != 0)
          return Helper::returnError(Lang::get('messages.process_completed'));
        
        $temp_students = TempStudent::where('status','ON')->whereIn('id',$ids);

        $students_limit  = Package::where('id',$package_id)->first()->students_limit;
        $students_number = $req->get('students_number') + $temp_students->count();
        if($students_number > $students_limit)
            return Helper::returnError(Lang::get('messages.package_limit'));
            
        $temp_students_data = $temp_students->get();

        $subscrptions = array();
        $notifications = array();
        $counter = 0;
        foreach($temp_students_data as $item){
            $student_id = (int)$item->student_id;
            $notify = new Notification();
            $notify->sender_id    = $teacher_id;
            $notify->reciever_id  = $student_id;
            $notify->event        = 'ASR';
            $notify->is_seen      = 0;
            $notify->seen_at      = NULL;
            $notify->created_at   = date('Y-m-d H:i:s');
            $notifications[] = $notify->toArray();

            $subscrptions[] = [
            'teacher_id'     => $teacher_id,
            'student_id'     => $student_id,
            'appointment_id' => $item->appointment_id,
            'temp_id'        => $item->id,
            'created_at'     => date('Y-m-d H:i:s'),
          ];
          $counter++;
        }
        Subscrption::insert($subscrptions);
        if(count($notifications) > 0)
          Notification::insert($notifications);
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
          'max_class_no'=> 'required|numeric',
        ]);
        $package_id    = $req->get('package_id');
        $days          = $req->input('days');
        $time_from     = $req->input('time_from');
        $time_to       = $req->input('time_to');
        $year          = $req->input('year');
        $max_class_no  = $req->input('max_class_no');

        $model = new Appointment();
        $count = $req->get('appointments_number');
        $teacher_appointment_limit = Package::where('id',$package_id)->first()->appointment_limit;
        if($count >= $teacher_appointment_limit)
          return Helper::returnError(Lang::get('messages.appointment_limit'));
        
        $where = array(
          "days_id"    => $days,      
          "time_from"  => $time_from, 
          "time_to"    => $time_to,   
          "year"       => $year,      
          "teacher_id" => $teacher_id,
        );
        $distinct = $model::where($where)->limit(1)->count();
        if($distinct > 0)
          return Helper::returnError(Lang::get('messages.distinct_appointment'));

        $model->days_id        = $days;      
        $model->time_from      = $time_from; 
        $model->time_to        = $time_to;   
        $model->year           = $year;      
        $model->teacher_id     = $teacher_id; 
        $model->max_class_no   = $max_class_no; 

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
          'max_class_no'=> 'required|numeric',
        ]);
        $id              = $req->input('id');
        $days            = $req->input('days');
        $time_from       = $req->input('time_from');
        $time_to         = $req->input('time_to');
        $year            = $req->input('year');
        $max_class_no    = $req->input('max_class_no');
        
        $appointments = Subscrption::where('appointment_id',$id)->limit(1)->count();
        if($appointments > 0)
          return Helper::returnError(Lang::get('messages.exist_appointments'));
        $model = new Appointment();
        $where = array(
          'id' => $id,
        );
        $model_select = $model::where($where);
        $my_arr = $req->all(['time_from','time_to','year','max_class_no']);
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
          'students'        => "required|array",
          'students.*'      => "required|numeric|exists:temp_students,id,teacher_id,{$teacher_id}",
        ]);
        $ids = $req->input('students');

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
          'duration'        => 'required|numeric|between:1,999',
          'desc'            => 'nullable|string|max:100', 
        ]); 
        $package_id     = $req->get('package_id');
        $exams_number   = $req->get('exams_number');
        $exams_limit = Package::where('id',$package_id)->first()->exams_limit;
        if($exams_number >= $exams_limit)
          return Helper::returnError(Lang::get('messages.exams_limit'));
        
        $my_arr = $req->all(['year','exam_name','duration','desc']);
        $my_arr['teacher_id']       = (int)$teacher_id;
        $my_arr['is_rtl']           = (bool)$req->get('is_rtl');
        $now = date('Y-m-d H:m:s');
        $model = new Exam($my_arr);
        $model->save();

        User::where('id',$teacher_id)->increment('exams_number');
        return Helper::return([
          'id' => $model->id,
          'created_at' => $now
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
        $exam_copy->status = "ON";
        $now = date('Y-m-d H:m:s');
        $model = new Exam($exam_copy->toArray());
        $model->save();

        $arr = Helper::merge_questions([$exam_id],$model->id);

        if($arr['length'] > 0)
        Question::insert($arr['data']);

        User::where('id',$teacher_id)->increment('exams_number');
        return Helper::return([
          'id' => $model->id,
          'created_at' => $now
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
          'students'   => "required|array",
          'students.*' => "required|exists:subscrptions,student_id,teacher_id,{$teacher_id}", 
          'exam_id'    => "required|exists:exams,id,teacher_id,{$teacher_id}",
          "publish_at" => "nullable|date_format:Y-m-d H:i:s"
        ]);
        $id      = $req->input('students');
        $exam_id = (int)$req->input('exam_id');
        $exam      = Exam::where('id',$exam_id);
        $exam_data = $exam->first();
        // if($exam_data->is_published == 1)
          // return Helper::returnError(Lang::get('messages.published_before'));
        if($exam_data->question_no == 0)
          return Helper::returnError(Lang::get('messages.empty_exam'));
        if($exam_data->status == 'OFF')
          return Helper::returnError(Lang::get('messages.closed_exam'));

        $ExamRequests = ExamRequest::where('exam_id',$exam_id)->whereIn('student_id',$id)->get(['student_id']);

        $not_allowed = array();
        foreach($ExamRequests as $ExamRequest){
          $search = array_search($ExamRequest->student_id, $id);
          array_splice($id, $search, 1); 
        }

        $where = array(
          'teacher_id'  => $teacher_id,
          'status'      => 'ON'
        );
        $subscrptions = Subscrption::whereIn('student_id',$id)->where($where)->get(['id','student_id']);
        $exam_requests = array();
        $notifications = array();
        $publish_at    = $req->input('publish_at');
        $now           = date('Y-m-d H:i:s');
        foreach($subscrptions as $subscrption){
          $Helper = new Helper();
          $Helper->exam_id        = (int)$exam_id;
          $Helper->student_id     = (int)$subscrption->student_id;
          $Helper->teacher_id     = (int)$teacher_id;
          $Helper->subscrption_id = (int)$subscrption->id;
          $Helper->created_at     = $publish_at ?? $now; 
          $exam_requests[] = $Helper->toArray();
          $notify = new Notification();
          $notify->sender_id    = (int)$teacher_id;
          $notify->reciever_id  = (int)$subscrption->student_id;
          $notify->event        = 'PE';
          $notify->is_seen      = 0;
          $notify->seen_at      = NULL;
          $notify->created_at   = $publish_at ?? $now; 
          $notifications[] = $notify->toArray();
        }
        
        ExamRequest::insert($exam_requests);
        Notification::insert($notifications);
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
          'question'        => "required|string|max:100000",
          'true_respond'    => "required|numeric|min:1|max:10",
          'degree'          => "required|numeric|between:{$this->degreeFrom},{$this->degreeTo}",
          'responds'        => "required|array|max:10",
          'responds.*'      => "required|string|max:100000|distinct",
          'outside_counter' => "nullable|string|max:255",
          'sub_outside_counter' => "nullable|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $id               = (int)$req->input('exam_id');
        $model = Exam::where('id',$id);
        $model_data = $model->first(['is_published','question_no']);
        if($model_data->is_published)
          return Helper::returnError(Lang::get('messages.published'));
        $questions_limit = Setting::where('key','questions_limit')->first()->value;
        if($model_data->question_no > $questions_limit)
          return Helper::returnError(Lang::get('messages.max_questions').$questions_limit);
        $responds         = $req->input('responds');
        $true_respond     = (int)$req->input('true_respond');
        if($true_respond > count($responds))
          return Helper::returnError(Lang::get('messages.not_in_responds'));

        $degree           =  (double)$req->input('degree');
        
        $my_arr = $req->all(['main_question','question','true_respond','outside_counter','sub_outside_counter','inside_counter']);
        $my_arr['responds']      = $responds;
        // $my_arr['image']         = NULL;
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
          'question'        => "required|string|max:100000",
          'true_respond'    => "nullable|string|max:100000",
          'degree'          => "required|numeric|between:{$this->degreeFrom},{$this->degreeTo}",
          'outside_counter' => "nullable|string|max:255",
          'sub_outside_counter' => "nullable|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $id               = (int)$req->input('exam_id');
        $model = Exam::where('id',$id);
        $model_data = $model->first(['is_published','question_no']);
        if($model_data->is_published)
          return Helper::returnError(Lang::get('messages.published'));
        $questions_limit = Setting::where('key','questions_limit')->first()->value;
        if($model_data->question_no > $questions_limit)
          return Helper::returnError(Lang::get('messages.max_questions').$questions_limit);
          
        $degree           =  (double)$req->input('degree');
        
        $my_arr = $req->all(['main_question','question','true_respond','outside_counter','sub_outside_counter','inside_counter']);
        // $my_arr['image']         = NULL;
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
    /*public function update_question_image(Request $req)
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
    }*/
    public function upload_image_url(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'image'           => 'required|image|mimes:jpeg,png,jpg|max:2000',
        ]);
        $image           = $req->file('image');
        
        $url = Helper::image($image,'add',"questions/{$teacher_id}",NULL,$teacher_id);
        return Helper::return([
          'url' => asset("storage/questions/{$teacher_id}/{$url}")
        ]);   
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
          'question'        => "required|string|max:100000",
          'true_respond'    => "required|numeric|min:1|max:10",
          'degree'          => "required|numeric|between:{$this->degreeFrom},{$this->degreeTo}",
          'responds'        => "required|array|max:10",
          'responds.*'      => "required|string|max:100000|distinct",
          'outside_counter' => "nullable|string|max:255",
          'sub_outside_counter' => "nullable|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $exam_id         = (int)$req->input('exam_id');
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
        $true_respond     = (int)$req->input('true_respond');
        if($true_respond > count($responds))
          return Helper::returnError(Lang::get('messages.not_in_responds'));

        $degree           =  (double)$req->input('degree');

        $my_arr = $req->all(['main_question','question','true_respond','outside_counter','sub_outside_counter','inside_counter']);
        $my_arr['responds']      = $responds;
        $my_arr['degree']        = $degree;

        $new_degree = $degree - $model_data->degree;    
        $question_model->update($my_arr);
        if($new_degree != 0)
        $model->increment('degree',$new_degree);
        return Helper::return([
          'new_degree'  => $new_degree
        ]);   
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
          'question_id'     => "required|string|exists:mongodb.questions,_id",
          'main_question'   => "nullable|string|max:100000",
          'question'        => "required|string|max:100000",
          'true_respond'    => "nullable|string|max:100000",
          'degree'          => "required|numeric|between:{$this->degreeFrom},{$this->degreeTo}",
          'outside_counter' => "nullable|string|max:255",
          'sub_outside_counter' => "nullable|string|max:255",
          'inside_counter'  => "required|string|max:255",
        ]);
        $exam_id         = (int)$req->input('exam_id');
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
        $my_arr = $req->all(['main_question','question','true_respond','outside_counter','sub_outside_counter','inside_counter']);
        $my_arr['degree']        = $degree;

        $new_degree = $degree - $model_data->degree;    
        $question_model->update($my_arr);
        if($new_degree != 0)
        $model->increment('degree',$new_degree);
        return Helper::return([
          'new_degree'  => $new_degree
        ]);   
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
        // if($model_data->image)
        //   Helper::delete_image('questions',$model_data->image);
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
          'duration'        => 'required|numeric|between:1,999',
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
    public function close_student(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|numeric|exists:subscrptions,student_id,teacher_id,{$teacher_id}",
        ]);
        $id         = $req->input('id');

        $model = new Subscrption();
        $where = array(
          'student_id' => $id,
          'teacher_id' => $teacher_id
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
    public function mark_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'request_id'     => "required|numeric|exists:exam_requests,id,teacher_id,{$teacher_id}",
          'marks'          => "required|array",
          'marks.*.id'     => "required|string|exists:mongodb.solves,_id",
          'marks.*.degree' => "required|numeric",
          'marks.*.question_id' => "required|string|exists:mongodb.questions,_id",
        ]);
        $request_id   = (int)$req->input('request_id');
        $marks        = $req->input('marks');
        $solve        = new Solve();
        $exam_request = ExamRequest::where('id',$request_id);
        $exam_request_data = $exam_request->first();
        $subscrption  = Subscrption::where(['student_id' => $exam_request_data->student_id ,'teacher_id' => $exam_request_data->teacher_id ]);

        $question_ids = array();
        foreach( $marks as $mark ){
          $question_ids[] = $mark['question_id'];
        }        
        $questions    = Question::whereIn('_id',$question_ids)->get();
        $solves       = Solve::whereIn('question_id',$question_ids)->get();

        $total_degree = 0;
        // $total_exam_degree = 0;
        foreach( $marks as $mark ){
          $teacher_degree = round((double)$mark['degree'],1,PHP_ROUND_HALF_DOWN);
          $question = $questions->where('_id',$mark['question_id'])->first();
          if($question->question_type != 'W')
            return Helper::returnError(Lang::get('messages.not_allowed'));
          if($teacher_degree > $question->degree)
            return Helper::returnError(Lang::get('messages.not_allowed'));

          $solve_item = $solves->where('_id',$mark['id']);
          $solve_item_data = $solve_item->first();
          if($solve_item_data->degree != NULL){
            $new_degree = $teacher_degree - $solve_item_data->degree;
            if($new_degree != 0)
              $solve::where('_id',$mark['id'])->increment('degree',$new_degree);
          }
          else{ 
            $new_degree    = $teacher_degree;
            $solve::where('_id',$mark['id'])->update(['degree' => $new_degree]);
          }

          $total_degree      += $new_degree;
          // $total_exam_degree += (double)$question->degree;
        }

        $exam_request->increment('total_degree',$total_degree);
        $exam_request->update(['is_seen' => 1]);
        // $subscrption->increment('student_degree',$total_degree);
        // if($exam_request_data->is_seen == 0)
        //   $subscrption->increment('exams_degree',$total_exam_degree);

        return Helper::return([
          'new_degree'  => $total_degree
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function send_exam_degree(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'request_ids'     => "required|array",
          'request_ids.*'   => "required|numeric|exists:exam_requests,id,teacher_id,{$teacher_id}",
        ]);
        $request_ids  = $req->input('request_ids');
        
        $exam_request = ExamRequest::where('is_corrected',0)->whereIn('id',$request_ids);
        $exam_request_data = $exam_request->get();

        $notifications = array();
        foreach($exam_request_data as $request){
          $notify = new Notification();
          $notify->sender_id    = (int)$teacher_id;
          $notify->reciever_id  = (int)$request->student_id;
          $notify->event        = 'ME';
          $notify->is_seen      = 0;
          $notify->seen_at      = NULL;
          $notify->created_at   = date('Y-m-d H:i:s');
          $notifications[] = $notify->toArray();

          $subscrption  = $request->subscrption;
          $student_rate = $request->total_degree / $request->exam->degree;
          if($subscrption->student_rate != NULL) {
            $student_rate = (($student_rate * 100) + $subscrption->student_rate) / 200;
          }
          $student_rate *= 100;
          $subscrption->student_rate = $student_rate;
          $subscrption->save();
        }

        $exam_request->update(['is_corrected' => 1]);
        if($notifications)
        Notification::insert($notifications);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function resend_exam(Request $req)
    {try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'request_ids'     => "required|array",
          'request_ids.*'   => "required|numeric|exists:exam_requests,id,teacher_id,{$teacher_id}",
        ]);
        $request_ids  = $req->input('request_ids');
        
        $exam_request = ExamRequest::where('status','DICONNECTED')->whereIn('id',$request_ids);
        $exam_request_data = $exam_request->get();

        $now = date('Y-m-d H:i:s');
        $notifications = array();
        foreach($exam_request_data as $request){
          $notify = new Notification();
          $notify->sender_id    = (int)$teacher_id;
          $notify->reciever_id  = (int)$request->student_id;
          $notify->event        = 'RS';
          $notify->is_seen      = 0;
          $notify->seen_at      = NULL;
          $notify->created_at   = $now;
          $notifications[] = $notify->toArray();
        }

        $exam_request->update(['status' => 'WAITING' , 'created_at' => $now]);
        if($notifications)
        Notification::insert($notifications);
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function send_message(Request $req)
    {
      try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'message'     => 'required|string|max:100000',
          'year_id'     => 'nullable|numeric|in:1,2,3',
          'group_id'    => "nullable|numeric|exists:appointments,id,teacher_id,{$teacher_id}"
        ]);
        $message  = $req->input('message');
        $year_id  = (int)$req->input('year_id');
        $group_id = (int)$req->input('group_id');

        $created_at = date('Y-m-d H:i:s');
        $model  = new Message();
        $model->teacher_id    = $teacher_id;
        $model->message       = $message;
        $model->created_at    = $created_at;
        
        $target = NULL;
        if($group_id > 0){
          $model->type     = 'group';
          $model->target   = $group_id;
          $target     = Appointment::where('appointments.id',$group_id)->join('days','days.id','appointments.days_id')->select(['days.day as days','appointments.time_from'])->first();
        }
        else if($year_id > 0){
          $model->type     = 'year';
          $model->target   = $year_id;
          $target          = $year_id;
        }
        else{
          $model->type     = 'all';
        }
        $model->save();
        return Helper::return([
          'id'           => $model->_id,
          'target'       => $target,
          'created_at'   => $model->created_at,
          'type'         => $model->type,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
      }
    }
    public function update_message(Request $req)
    {
      try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|string|exists:mongodb.messages,_id",
          'message'     => 'required|string|max:100000',
          // 'year_id'     => 'nullable|numeric|in:1,2,3',
          // 'group_id'    => "nullable|numeric|exists:appointments,id,teacher_id,{$teacher_id}"
        ]);
        $id       = $req->input('id');
        $message  = $req->input('message');
        // $year_id  = (int)$req->input('year_id');
        // $group_id = (int)$req->input('group_id');

        $model  = new Message();
        // $model->teacher_id    = $teacher_id;
        $model->message       = $message;
        
        // $target = NULL;
        // if($group_id > 0){
        //   $model->type     = 'group';
        //   $model->target   = $group_id;
        //   $target     = Appointment::where('appointments.id',$group_id)->join('days','days.id','appointments.days_id')->select(['days.day as days','appointments.time_from'])->first();
        // }
        // else if($year_id > 0){
        //   $model->type     = 'year';
        //   $model->target   = $year_id;
        //   $target          = $year_id;
        // }
        // else{
        //   $model->type     = 'all';
        // }
        
        $where = array(
            '_id'         => $id,
            'teacher_id'  => $teacher_id
        );
        Message::where($where)->update($model->toArray());
        return Helper::return([
          // 'target'       => $target,
          // 'type'         => $model->type,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
      }
    }
    public function delete_message(Request $req)
    {
      try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'          => "required|string|exists:mongodb.messages,_id",
        ]);
        $id       = $req->input('id');

        $model  = new Message();

        $where = array(
            '_id'         => $id,
            'teacher_id'  => $teacher_id
        );
        Message::where($where)->delete();
        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
      }
    }

    public function change_student_appointment(Request $req)
    {
      try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'id'             => "required|numeric",
          'appointment_id' => "required|numeric|exists:appointments,id,teacher_id,{$teacher_id}"
        ]);

        $id               = (int)$req->input('id');
        $appointment_id   = (int)$req->input('appointment_id');

        $where = array(
            'student_id'  => $id,
            'teacher_id'  => $teacher_id
        );
        $subscrption = Subscrption::where($where);

        $is_update = $subscrption->update(['appointment_id' => $appointment_id]);
        if(!$is_update)
          return Helper::returnError(__('messages.not_allowed'));

        return Helper::return([]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
      }
    }

    public function merge_exams(Request $req)
    {
      try{
        $teacher_id      = $req->get('id');
        $req->validate([
          'exam_id'         => "required|array",
          'exam_id.*'       => "required|numeric|exists:exams,id,teacher_id,{$teacher_id}",
          'year'            => 'required|in:1,2,3',
          'exam_name'       => 'required|string|max:100',
          'duration'        => 'required|numeric|between:1,999',
          'desc'            => 'nullable|string|max:100', 
        ]);

        $exam_ids         = $req->input('exam_id');
        
        $package_id     = $req->get('package_id');
        $exams_number   = $req->get('exams_number');
        $exams_limit = Package::where('id',$package_id)->first()->exams_limit;
        if($exams_number >= $exams_limit)
          return Helper::returnError(Lang::get('messages.exams_limit'));
        
        $exam_copy =  $req->all(['year','exam_name','duration','desc']);
        $exam_copy['teacher_id']   = $teacher_id;
        $exam_copy['is_rtl']       = (bool)$req->get('is_rtl');
        $now = date('Y-m-d H:m:s');
        $new_exam = new Exam($exam_copy);
        $new_exam->save();
        
 
        $arr = Helper::merge_questions($exam_ids,$new_exam->id);

        $questions_limit = Setting::where('key','questions_limit')->first()->value;
        if($arr['length'] > $questions_limit) {
          $new_exam->delete();
          return Helper::returnError(Lang::get('messages.max_questions').$questions_limit);
        }

        if($arr['length'] > 0) {
          Question::insert($arr['data']);
          Exam::where('id',$new_exam->id)->update([ 'degree' => $arr['degree'], 'question_no' => $arr['length'] ]);
        }

        User::where('id',$teacher_id)->increment('exams_number');
        
        return Helper::return([
          'id'           => $new_exam->id,
          'total_degree' => $arr['degree'],
          'total_length' => $arr['length'],
          'created_at'   => $now
        ]);  
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
      }
    }
}
