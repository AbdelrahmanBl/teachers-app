<?php

namespace App\Http\Middleware;
use App\Models\Setting;
use Closure;
use Crypt;
use Exception;
use App\Helper; 
use App\Models\User; 
use App;
class StudentAuth
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
            'users.email'          => $email,
            'users.type'           => 'S'
        );
        $model       = User::where($where);
        $model_data  = $model->first();

        if(!$model_data){
            return Helper::notFound('Not Authenicated !');
        }
        if($model_data->failed_try >= 30)
            return Helper::notFound('Found But Closed');
        if($model_data->remember_token != $access_token ){
            $model->increment('failed_try');
            return Helper::notFound('Not Authenicated !');
        }
        if($model_data->status == 'OFF')
            return Helper::notFound('admin closed !');
        
        // if($model_data->subscrption_status == 'OFF')
        //     return Helper::notFound('teacher closed !');
          
        $request->attributes->add([
            'id'             => $model_data->id,
            'student_status' => $model_data->student_status,
            'year'           => $model_data->year,
            'password'       => $model_data->password,
            'image'          => $model_data->image,
            'first_name'     => $model_data->first_name,
            'last_name'      => $model_data->last_name,
            'mobile'         => $model_data->mobile,
            'parent_mobile1' => $model_data->parent_mobile1,
            'parent_mobile2' => $model_data->parent_mobile2,
        ]);
        return $next($request);
        }catch(Exception $e){
        return Helper::notFound('Not Authenicated !');
        }
    }
}

