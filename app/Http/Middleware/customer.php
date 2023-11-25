<?php

namespace App\Http\Middleware;
use DB;
use Closure;
use Carbon\Carbon;
class customer
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
        $api_token=request()->header('User-Token');

        if(isset($api_token)&& $api_token!='')
        {
          $query=DB::table("user_token")->where("api_token",$api_token)->whereNull('deleted_at')->first();

            if(empty($query))
            {
              return response()->json(["error" => true, "msg"=>"Session expired. Please login to continue.","errorCode"=>401,"errorType"=>"AuthenticationException"]);               
            }
            else
            {
              $time=Carbon::now();
              $update=DB::table('user_token')->where("api_token",$api_token)->update(["updated_at"=>$time]);

              return $next($request)
              ->header('Access-Control-Allow-Origin', isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'http://localhost')
              ->header('Access-Control-Allow-Headers', 'http://localhost:4200')
              ->header('Access-Control-Allow-Headers', 'http://localhost')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
              ->header('Access-Control-Allow-Credentials', 'true');
                    
            }
        }

        else
        {
              return response()->json(["error" => true, "msg"=>"No Api Token found.","errorCode"=>401,"errorType"=>"ApiTokenException"]);
        }
    }
}
