<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\UserToken;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PushNotificationController extends Controller
{
    //
    public function index()
    {
        try
        {
            $api_token      =   request()->header('User-Token');
            $user_exists    =   UserToken::where("api_token",$api_token)->first();

            if(!$user_exists)
            {
                throw new Exception("Api Token Expired");
            }
            // dd($user_exists);
            $notification_list =    DB::table('notification')
                                    ->select('id','notify_type','redirect_id','data','read_at','created_at','updated_at','deleted_at')
                                    ->where('user_id',$user_exists->user_id)->orderBy('created_at', 'desc')->get();
            // dd($notification_list);
            $result     =   ["status"=>True,"notifications"=>$notification_list];
        }
        catch(Exception $e)
        {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }


    public static function sendWebNotification($notify_data)
    {
        $device_tokens  =   [];
        $notification_type = $notify_data["type"];
        $user_ids   =   [];

        if($notification_type == '1')
        {
            $fcm_tokens     =   UserToken::whereNotNull('fcm')->wherenull('deleted_at')
                                ->pluck('fcm')->all();

            if(count($fcm_tokens) > 0)
            {
                $delivery_area_location =   $notify_data["delivery_area"];
                $delivery_area_coords   =   json_decode($delivery_area_location);

                $vertices_x =   [];
                $vertices_y =   [];

                foreach($delivery_area_coords as $coords)
                {
                    $vertices_x[] = floatval($coords->lat);
                    $vertices_y[] = floatval($coords->lng);
                }

                $points_polygon         =   count($vertices_x) - 1;
                $users_in_location      =   DB::table('device_location')->whereIn('fcm_token',$fcm_tokens)->get();

                foreach($users_in_location as $users)
                {
                    if(PushNotificationController::is_in_polygon($points_polygon, $vertices_x, $vertices_y, $users->latitude, $users->longitude)){
                        $user_id[]    =     UserToken::where('fcm',$users->fcm_token)->wherenull('deleted_at')
                                            ->pluck('user_id')->toArray();
                        array_push($device_tokens,$users->fcm_token);
                    }
                }
                $notificationdata = [
                    "title" => PushNotificationController::NotificationTitle($notify_data["type"]),
                    "body"  => $notify_data["data"]["ShopTitle"].", ".$notify_data["data"]["ShopLocation"]
                ];
            }
        } else {
            $api_token      =   request()->header('User-Token');
            $booked_user    =   $notify_data['booked_user_id'];
            $fcm_tokens     =   UserToken::where('user_id',$booked_user)->whereNotNull('fcm')
                                ->groupby('fcm')->wherenull('deleted_at')->pluck('fcm')->all();
            // dd($fcm_tokens);
            $user           =   UserToken::where("api_token",$api_token)
                                ->whereNotNull('fcm')->wherenull('deleted_at')->first();
            // dd($user);
            if($user){
                $user_ids[]   = $user->user_id;
                array_push($device_tokens,$user->fcm);
            }
            $notificationdata = [
                "title" => PushNotificationController::NotificationTitle($notify_data["type"]),
                "body" => $notify_data["data"]["BookingDate"].", ".$notify_data["data"]["BookingTime"]
            ];
        }

        if(count($device_tokens) > 0)
        {
            $SERVER_API_KEY = env('NOTIFICATION_SERVER_KEY');
            $data   =   [
                            "registration_ids" => $device_tokens,
                            "notification" => $notificationdata
                        ];

            $dataString = json_encode($data);
            $headers =  [
                            'Authorization: key=' . $SERVER_API_KEY,
                            'Content-Type: application/json',
                        ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);

            if ($response === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
            // Close connection
            curl_close($ch);

            $res=json_decode($response);
            // if($res->success != 0)
            // {

            $notify_data["data"]['notify_title']    = PushNotificationController::NotificationTitle($notify_data["type"]);
            $insertdata     =   [];
            $cmn_user_ids = call_user_func('array_merge',$user_ids);
            $user_data = array_unique($cmn_user_ids);
            foreach($user_data as $key=>$userid){
                $insertdata[$key]   =
                [
                    'notify_type'=> $notify_data["type"],
                    'user_id'    => $userid,
                    'redirect_id'=> $notify_data['redirect_id'],
                    'data'      =>  json_encode($notify_data["data"]),
                    'read_at'   =>  Carbon::now(),
                    'created_at'=>  Carbon::now(),
                    "updated_at"=>  Carbon::now()
                ];
            }
            DB::table('notification')->insert($insertdata);
            $result = [
                        "status"=>true,
                        "success"=>$res->success,
                        "failure"=>$res->failure,
                        "message"=>"notification sent successfully."
                    ];
        } else {
            $result = [
                "status"=>false,
                "message"=>"Notification not Sent."
            ];
        }

        return response()->json($result);

    }

    public static function NotificationTitle($type)
    {
        if($type == '1'){ $title = "New Salon Added";}
        else if($type == '2'){ $title = "New Booking Added";}
        else if($type == '3'){ $title = "New PromoCode Added";}
        else{ $title = "New Notification for you"; }
        return $title;
    }


    public static function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
    {
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
            ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
            $c = !$c;
        }
        return $c;
    }

}
