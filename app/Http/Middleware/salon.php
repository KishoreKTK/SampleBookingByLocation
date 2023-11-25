<?php

namespace App\Http\Middleware;
use DB;
use Auth;
use Session;
use Closure;
use App\Booking;
use Carbon\Carbon;
use App\SalonReviews;

class salon
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

      $api_token=Session::get('salon_token');
      $time=Carbon::now();

        if(isset($api_token)&& $api_token!='')
        {
          $query=DB::table("salons_token")->where("api_token",$api_token)->first();
          $query1=DB::table("salon_users_token")
              ->leftJoin("salon_users", "salon_users.id","=","salon_users_token.salon_id")->where("api_token",$api_token)->select("salon_users.salon_id","salon_users_token.*")->first();
            if(empty($query) && empty($query1))
         {
            return redirect(env("ADMIN_URL").'/salon/login')->with("error", true)->with("msg", "Session expired. Please login to continue.");

         }

          if(isset($query) && $query->salon_id>0)
          {
            $salon_id=isset($query->salon_id)?$query->salon_id:0;
               $update=DB::table('salons_token')->where("api_token",$api_token)->update(["updated_at"=>$time]);

          }
           else if(isset($query1) && $query1->salon_id>0)
          {
            $salon_id=isset($query1->salon_id)?$query1->salon_id:0;
            $update=DB::table('salon_users_token')->where("api_token",$api_token)->update(["updated_at"=>$time]);

          }
          else
          {
            return redirect(env("ADMIN_URL").'/salon/login')->with("error", true)->with("msg", "Session expired. Please login to continue.");

          }

            $response = $next($request);

            $response->headers->set('Access-Control-Allow-Origin' , isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'http://localhost');
            $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');

            return $response;

            // return $next($request)
            // ->header('Access-Control-Allow-Origin', isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'http://localhost')
            // ->header('Access-Control-Allow-Headers', 'http://localhost:4200')
            // ->header('Access-Control-Allow-Headers', 'http://localhost')
            // ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            // ->header('Access-Control-Allow-Credentials', 'true');

        }

        else
        {
            return redirect(env("ADMIN_URL").'/salon/login')->with("error", true)->with("msg", "Session expired. Please login to continue.");
        }
    }
}
