<?php

namespace App\Http\Middleware;
use App\Models\Setting;
use Closure;
use Crypt;
use Exception;
use App\Helper; 
use App\Models\User; 
use App;
class TeacherAuth
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
        try{
        $email          = $request->header('email');
        $access_token   = /*Crypt::decrypt*/($request->header('access-token'));
        $where = array(
            'email'          => $email,
            'type'           => 'T'
        );
        $model       = User::where($where);
        $model_data  = $model->first();

        if(!$model_data){
            return Helper::notFound('Not Authenicated !');
        }
        if($model_data->failed_try >= 30)
            return Helper::notFound('Found But Closed');
        if($model_data->remember_token != $access_token){
            $model->increment('failed_try');
            return Helper::notFound('Not Authenicated !');
        }
        if($model_data->status == 'OFF')
            return Helper::notFound('admin closed !');
         
        $request->attributes->add([
            'id'             => $model_data->id,
            'first_name'     => $model_data->first_name,
            'last_name'      => $model_data->last_name,
            'image'          => $model_data->image,
            'package_id'     => $model_data->package_id,
            'students_number'=> $model_data->students_number,
            'appointments_number'=> $model_data->appointments_number,
            'exams_number'   => $model_data->exams_number,
            'is_rtl'         => $model_data->is_rtl,
            'password'       => $model_data->password,
        ]);
        return $next($request);
        }catch(Exception $e){
        return Helper::notFound('Not Authenicated !');
        }
    }
}
