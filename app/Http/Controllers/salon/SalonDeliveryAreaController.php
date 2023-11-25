<?php

namespace App\Http\Controllers\salon;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Notification\PushNotificationController;
use App\SalonsToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalonDeliveryAreaController extends Controller
{
    public function ViewDeliveryAreaPage()
    {
        $api_token  =   Session::get('salon_token');
        $take       =   SalonsToken::where("api_token",$api_token)->first();
        $salon_id   =   $take->salon_id;

        $salon = DB::table('salons')->where('id',$salon_id)->first();
        if(!$salon){
            return redirect()->back()->with("error", true)->with("msg", "Please Check Salon ID");
        }

        $result  = ['id'=>$salon->id,'lat'=>$salon->latitude,'lng'=>$salon->longitude,
                'name'=>$salon->name,'area'=>$salon->delivery_area_coords];
        return view('salon.delivery_area',compact('result'));
    }

    public function UpdateDeliveryArea(){
        // dd(request()->all());
        $salon_id   = request()->salon_id;
        $salon = DB::table('salons')->where('id',$salon_id)->first();
        if(!$salon){
            return redirect()->back()->with("error", true)->with("msg", "Please Check Salon ID");
        }
        $old_delivery_area = $salon->delivery_area_coords;

        $update_area = ['delivery_area_coords'=>request()->delivery_area_coords,
                        'updated_at'=>date('Y-m-d H:i:s')];
        // dd($update_area);
        $update = DB::table('salons')->where('id',$salon_id)->update($update_area);
        if($update)
        {
            if($old_delivery_area == null){
                $notification_data =    [
                                            'type'=>'1' ,
                                            "data"=> [
                                                "Shop_id"=>$salon_id,
                                                "ShopTitle"=>$salon->name,
                                                "ShopLocation"=>$salon->location
                                            ],
                                            'delivery_area' =>request()->delivery_area_coords
                                        ];
                PushNotificationController::sendWebNotification($notification_data);
            }
            return redirect()->back()->with("error", false)->with("msg", "Delivery Area Updated Successfully");
        }

    }

}
