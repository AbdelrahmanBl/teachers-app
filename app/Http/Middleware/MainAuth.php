<?php

namespace App\Http\Middleware;
use App\Models\Setting;
use Closure;
use Crypt;
use Exception;
use App\Helper; 
use App;

class MainAuth
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
        // $token1     = $request->header('header-key');
        // $token2     = /*Crypt::decrypt*/($request->header('oath-key'));
        // $header_key = Setting::where('key','HEADER_KEY')->first()->value;
        // $oath_key   = Setting::where('key','OATH_KEY')->first()->value;
        // if($token1 != $header_key || $token2 != $oath_key){
        //     return Helper::notFound('Not Authenicated !');
        // }
        $locale = $request->header('language');
        if(!in_array($locale, ['en','ar']))
            $locale = 'ar';
        App::setLocale($locale);
        return $next($request);
        }catch(Exception $e){
        return Helper::notFound('Not Authenicated !');
        }
    }
}
