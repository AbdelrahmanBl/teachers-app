<?php

namespace App\Http\Middleware;

use Closure;
use Crypt;
use Exception;
use App\Helper; 
use App\Models\User; 

class AdminAuth
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
            'type'           => 'A'
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
        ]);
        return $next($request);
        }catch(Exception $e){
        return Helper::notFound('Not Authenicated !');
        }
    }
}
