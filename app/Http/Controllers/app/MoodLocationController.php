<?php

namespace App\Http\Controllers\app;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MoodLocationController extends Controller
{
    //
    public function CheckLocation()
    {
        try
        {
            if(request()->has('fcm_token') && request()->fcm_token != '')
            {
                $check_exists   =   DB::table('device_location')->where('fcm_token',request()->fcm_token)->first();
                if($check_exists){
                    $result =   ['status'=>true, 'data'=>$check_exists, 'message'=>"Location Already Added"];
                } else {
                    throw new Exception("Please Set Location to Continue");
                }
            } else {
                throw new Exception("Please Add FCM token to Continue");
            }
        } catch(Exception $e) {
            $result =   ['status'=>false, 'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }


    public function SetLocation()
    {
        try
        {
            $rules=[
                "fcm_token"=>"required",
                "device_name"=>"required",
                "location"=>"required",
                'latitude'=>'required',
                'longitude'=>"required"
            ];

            $validator=Validator::make(request()->all(), $rules);
            if($validator->fails())
            {
                throw new Exception("Please Set Location");
            }

            $input  =   request()->all();
            $check_already_added    =   DB::table('device_location')
                                        ->where('fcm_token',$input['fcm_token'])
                                        ->first();
            if($check_already_added){
                $device_id  =   $check_already_added->id;
                $input['updated_at']    =   date('Y-m-d H:i:s');
                $update_data = DB::table('device_location')->where('fcm_token', $input['fcm_token'])->update($input);
                if(!$update_data) {
                    throw new Exception("Error in Updating");
                }
            } else {

                $input['created_at']    =   date('Y-m-d H:i:s');
                $device_id            =   DB::table('device_location')->insertGetId($input);
                if(!$device_id){
                    throw new Exception("Error in Inserting");
                }
            }


            $result =   [
                            'status'=>true,
                            "data"=> DB::table('device_location')
                            ->where('id',$device_id)->first(),
                            'message'=>"Location Set Successfully"
                        ];
        } catch(Exception $e){
            $result =   ['status'=>false, 'message'=>"Please Set Location"];
        }
        return response()->json($result);
    }


}
