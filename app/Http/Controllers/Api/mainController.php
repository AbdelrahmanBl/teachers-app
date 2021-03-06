<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Setting;
use App\Models\TempStudent;
use App\Models\Day;
 
use App\Helper;
use Hash;
use validate;

class mainController extends Controller
{
    public function connection(Request $req)
    {try{
        $header_key = Setting::where('key','HEADER_KEY')->first()->value;
        $oath_key   = Setting::where('key','OATH_KEY')->first()->value;
        return Helper::return([
            'key1'     => $header_key,
            'key2'     => $oath_key,
        ]);
    }catch(Exception $e){
      return Helper::returnError(Helper::returnException($e));
    }
    }
    public function get_teachers(Request $req)
    {try{
        $where = array(
            'type'   => 'T',
            'status' => 'ON',
        );
        $model = new User();
        $model_select  = $model->where($where);
        $select = ['id','first_name','last_name','accept_register'];
        $model_data    = $model_select->select($select)->get();
            
        return Helper::return([
            'teachers'   => $model_data
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function get_days(Request $req)
    {try{
        $model = Day::where('status','ON')->get(['id','day']);
        return Helper::return([
            'days'   => $model
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
        $teacher_id   = $req->get('teacher_id');
        $year         = $req->get('year');

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
    public function login(Request $req)
    {try{
        $req->validate([
            'email'         => 'required|email',
            'password'      => 'required',
        ]);
        $email      = $req->input('email');
        $password   = $req->input('password');

        $model = new User();
        $model_select  = $model->where('email',$email);
        $model_data    = $model_select->first();
            if(!$model_data) 
                return Helper::returnError(Lang::get('auth.failed'));
            if($model_data->status == 'OFF')
                return Helper::returnError(Lang::get('auth.deactivate'));
            if($model_data->failed_try >= 30)
                return Helper::returnError(Lang::get('auth.blocked'));
            if( !Hash::check($password, $model_data->password) ){
            $model_select->increment('failed_try');
            return Helper::returnError(Lang::get('auth.failed'));
            }
 
            $token    = Helper::loginUsingId($model_select,$model_data->remember_token);
        return Helper::return([
            'id'             => $model_data->id,
            'access_token'   => $token,//$model_data->remember_token,
            'type'           => $model_data->type,
            'first_name'     => $model_data->first_name,
            'last_name'      => $model_data->last_name,
            'image'          => $model_data->getImage,
            'mobile'         => $model_data->mobile,
            'parent_mobile1' => $model_data->parent_mobile1,
            // 'parent_mobile2' => $model_data->parent_mobile2,
            'accept_register'=> $model_data->accept_register,
            'is_rtl'         => $model_data->is_rtl,
        ]);   
       }catch(Exception $e){
          if($e instanceof ValidationException) {
             throw $e;
          }
         return Helper::returnError(Helper::returnException($e));
        }
    }
    public function register(Request $req)
    {try{
        
        $req->validate([
            'teacher_id'            => "required|numeric|exists:users,id,type,T",
            'year'                  => 'required|numeric|in:1,2,3',
            'appointment_id'        => "required|numeric|exists:appointments,id",
            'first_name'            => 'required|string|max:15',
            'last_name'             => 'required|string|max:40',
            'email'                 => 'required|email|max:64|unique:users',
            'password'              => 'required|string|min:6|max:16',
            'verify_password'       => 'required|string|same:password',
            'mobile'                => 'required|string|max:11',
            'parent_mobile1'        => 'required|string|max:11',
            'parent_mobile2'        => 'nullable|string|max:11',
        ]);
        $teacher_id         = $req->input('teacher_id');
        $year               = $req->input('year');
        $appointment_id     = $req->input('appointment_id');

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

        $my_arr = $req->all(['year','first_name','last_name','email','mobile','parent_mobile1','parent_mobile2']);
        $my_arr['password'] = Hash::make($req->input('password'));
        $my_arr['type']     = 'S';
        $my_arr['student_status'] = 'WAITING';

        $model = new User($my_arr);
        $model->save();
        
        $my_arr = $req->all(['teacher_id','appointment_id']);
        $my_arr['student_id'] = $model->id;

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
