<?php

namespace App\Http\Controllers\app;
use Illuminate\Support\Facades\Validator;
use App\Currency;
use Carbon\Carbon;
use App\Customers;
use App\UserToken;
use App\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Echo_;

class AppUserAddressController extends Controller
{
    public function index(Request $request)
    {
    	$rules=[
    	 	"id"=>"exists:user_address,id,deleted_at,NULL",
            ];
        $msg=[
            "rating.required"=>"Rating is required"
             ];
    	$api_token=request()->header('User-Token');
        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
        $user_email     =   DB::table('user')->where('id',$user_id)->first()->email;
        $checkuser = DB::table('user')->where('email',$user_email)->where('active',1)->where('suspend',0)->first();
        if(!$checkuser)
        {
        	$return['error']=true;
            $return['msg']="User is Suspended";
        } else {
            if($request->id)
            {
                $id=$request->id;
                $address=UserAddress::where("user_id",$user_id)->where("id",$id)->first();
            }
            else
            {
                $address=UserAddress::where("user_id",$user_id)->whereNotNull('latitude')->whereNotNull('longitude')->get();
            }

            if(request()->filled('salon_id')){
                $delivery_area     = DB::table('salons')->where('id',request()->salon_id)->whereNotNull('delivery_area_coords')->first()->delivery_area_coords;

                $delivery_area_coords   =   json_decode($delivery_area);

                $vertices_x =   [];
                $vertices_y =   [];

                foreach($delivery_area_coords as $coords){
                    $vertices_x[] = floatval($coords->lat);
                    $vertices_y[] = floatval($coords->lng);
                }

                $points_polygon         =   count($vertices_x) - 1;
                foreach($address as $addr)
                {
                    if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $addr->latitude, $addr->longitude)){
                        $addr['contains_location']   =   true;
                    } else {
                        $addr['contains_location']   =   false;
                    }
                }
            }

            if(isset($address))
            {
                $return['error']=false;
                $return['address']=$address;
                $return['msg']="Your addresses listed successfully";
            }
            else
            {
                $return['error']=false;
                $return['msg']="Sorry no records found ";
            }
        }
        return $return;
    }


    function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
    {
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
            ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
            $c = !$c;
        }
        return $c;
    }


    public function add(Request $request)
    {
        // dd(request->all());
        $rules=[
                    "adr_title"=>"required",
                    "first_name"=>"required",
                    "address"=>"required",
                    "phone"=>"required",
                    "emirate"=>"required",
                    "area"=>"required",
                    "latitude"=>"required",
                    "longitude"=>"required"
                ];
        $msg=[
                "address.required"=>"Address is required"
            ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
        	$return['error']=true;
		    $return['msg']=$validator->errors()->all();
        }
        else
        {
	    	$api_token     =    request()->header('User-Token');
	        $user_id       =    UserToken::where("api_token",$api_token)->first()->user_id;
            $user_email     =   DB::table('user')->where('id',$user_id)->first()->email;
            $checkuser = DB::table('user')->where('email',$user_email)->where('active',1)->where('suspend',0)->first();
            if($checkuser)
            {
                $address_title =    $request->adr_title;
                $first_name    =    $request->first_name;
                $last_name     =    $request->last_name;
                $phone         =    $request->phone;
                $address       =    $request->address;
                $location      =    $request->area;
                $city          =    $request->emirate;
                $default_addr  =    $request->has('default_addr')?$request->default_addr:'0';
                $billing_addr  =    $request->has('bill_addr')?$request->bill_addr:'0';
                $time          =    Carbon::now();

                $insert_array  =    [
                                    'address'=>$address,
                                    'user_id'=>$user_id,
                                    "addr_title"=>$address_title,
                                    'phone_code'=>$request->phone_code,
                                    'phone_num'=>$phone,
                                    'country_code'=>$request->country_code,
                                    'default_addr'=>$default_addr ,
                                    'billing_addr'=>$billing_addr,
                                    'city'=>$city,
                                    'first_name'=>$first_name,
                                    'last_name'=>$last_name,
                                    'location'=>$location,
                                    'latitude'=>$request->latitude,
                                    'longitude'=>$request->longitude,
                                    'created_at'=> $time,
                                    "updated_at"=>$time
                                ];
                if($request->default_addr== '1')
                {
                    $check     =    UserAddress::where("user_id",$user_id)->where('default_addr','1')
                                    ->select('id','default_addr')->get();
                    if(count($check) > 0)
                    {
                        foreach($check as $c)
                        {
                            UserAddress::where("id",$c->id)->update(["default_addr"=>'0',"updated_at"=>$time]);
                        }
                    }
                    $add_address    =   UserAddress::insertGetId($insert_array);
                }
                else{
                    $add_address    =   UserAddress::insertGetId($insert_array);
                }


                if($add_address)
                {
                    $c_address=UserAddress::where('id',$add_address)->get();

                    $return['error']  =       false;
                    $return['msg']    =       "Your address Added successfully";
                    $return['address']=       $c_address;
                }
                else
                {
                    $return['error']   = true;
                    $return['msg']     = "Sorry error occured";
                }
            } else {
                $return['error']=true;
                $return['msg']="User is Suspended";
            }
	    }
	    return $return;
    }

	public function update(Request $request)
	{
		$rules  =   [
                        "id"=>"required|exists:user_address,id,deleted_at,NULL",
                        "adr_title"=>"required",
                        "first_name"=>"required",
                        "address"=>"required",
                        "phone"=>"required",
                        "emirate"=>"required",
                        "area"=>"required",
                    ];
        $msg    =   [

                    ];
        $validator  =   Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
            $api_token      =   request()->header('User-Token');
            $take           =   UserToken::where("api_token",$api_token)->first();
            $id             =   $request->id;
            $user_id        =   $take->user_id;
            $user_addres    =   $request->address;
            $time           =   Carbon::now();
            $name           =   $request->first_name;
            $phone          =   $request->phone;
            $location       =    $request->area;
            $city           =    $request->emirate;
            $default_addr   =    $request->has('default_addr')?$request->default_addr:'0';
            $billing_addr   =    $request->has('bill_addr')?$request->bill_addr:'0';
           // $default  = $request->default_addr;

           $update_address_arr = [
                            'address'=>$user_addres,
                            'first_name'=>$name,
                            'last_name'=>$request->last_name,
                            "addr_title"=>$request->adr_title,
                            'phone_code'=>$request->phone_code,
                            'phone_num'=>$phone,
                            'country_code'=>$request->country_code,
                            'default_addr'=>$default_addr ,
                            'billing_addr'=>$billing_addr,
                            'city'=>$city,
                            'location'=>$location,
                            'latitude'=>$request->latitude,
                            'longitude'=>$request->longitude,
                            "updated_at"=>$time
                        ];
            if($request->default_addr=='1')
            {
                $address        =   UserAddress::where('user_id',$user_id)->where('default_addr','1')->get();
                if(isset($address) && count($address)>0)
                {
                    $update_address =   UserAddress::where("user_id",$user_id)
                                ->update(["default_addr"=>'0',"updated_at"=>$time]);
                }
                $update_address =   UserAddress::where("id",$id)
                                    ->update($update_address_arr);
            }
            else
            {
                $update_address =   UserAddress::where("id",$id)
                                    ->update($update_address_arr);

            }
            $c_address=UserAddress::where('id',$id)->get();
            if($update_address)
            {
                $return['error']=false;
                $return['msg']="user address has updated sucessfully";
                $return['address']=$c_address;
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry error occured";
            }
        }
        return $return;
	}

    public function UpdateDefaultAddress($id){
        $address_id = UserAddress::find($id);
        if ($address_id == null) {
            $return['error']=false;
            $return['msg']="Sorry no records found ";
        } else {
            $time           =    Carbon::now();
            $api_token      =   request()->header('User-Token');
	        $user_id        =   UserToken::where("api_token",$api_token)->first()->user_id;
            $address        =   UserAddress::where('user_id',$user_id)->where('default_addr','1')->get();
            if(isset($address) && count($address)>0)
            {
                UserAddress::where("user_id",$user_id)
                            ->update(["default_addr"=>'0',"updated_at"=>$time]);
            }

            UserAddress::where("id",$id)
                            ->update(["default_addr"=>'1',"updated_at"=>$time]);

            $address=UserAddress::where("user_id",$user_id)->get();
            if(isset($address))
            {
                $return['error']=false;
                $return['address']=$address;
                $return['msg']="Your addresses listed successfully";
            }
            else
            {
                $return['error']=false;
                $return['msg']="Sorry no records found ";
            }
            return $return;
        }
    }


    public function delete(Request $request)
    {
    	 $rules=[
    	 	"id"=>"required|exists:user_address,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required"
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
        	$return['error']=true;
		    $return['msg']=$validator->errors()->all();
        }
        else
        {
	    	$api_token=request()->header('User-Token');
	        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
	        $id=$request->id;
	        $delete=UserAddress::where("id",$id)->where("user_id",$user_id)->delete();
	        if($delete)
	        {
	        	$return['error']=false;
	        	$return['msg']="Your address deleted successfully";
	        }
	        else
	        {
	        	$return['error']=true;
	            $return['msg']="Sorry error occured";
	        }

	    }
	    return $return;
    }

    public function CheckMobileNum()
    {
        try
        {
            if(!request()->filled('mobile')){
                throw new Exception("Please Enter Mobile Number");
            }
            $mobile         =   request()->mobile;
            $otp            = rand(100000, 999999);
            // dd($otp);
            $response = Http::get('http://smpplive.com/api/send_sms/single_sms', [
                'to' => $mobile,
                'username' => 'Mood App',
                'password' => 'Mood2021',
                'from' => 'Mood App',
                'content' => 'Mood verification code is ' . $otp
            ]);
            $response_status = $response->getStatusCode();
            if($response_status == 200){
                $check_mobile_exists = DB::table('verify_otp')->where('mobile',$mobile)->first();
                if($check_mobile_exists){
                    DB::table('verify_otp')->where('mobile',$mobile)->delete();
                }
                $insert_data = [
                    'mobile' => $mobile,
                    'otp'=> $otp,
                    'created_at'=>date('Y-m-d H:i:s')
                ];

                $send_otp = DB::table('verify_otp')->insert($insert_data);
                if(!$send_otp)
                {
                    throw new Exception("Something Went Wrong. Please Verify again");
                }
                $result =   ['status'=>true, "message"=>"OTP Sent."];
            } else {
                throw new Exception($response->reasonPhrase);
            }
        }catch(Exception $e)
        {
            $result =   ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function VerifyOtp()
    {
        try
        {
            if(!request()->filled('mobile')){
                throw new Exception("Please Enter Mobile Number");
            }
            if(!request()->filled('otp')){
                throw new Exception("Please Enter otp Number");
            }
            $mobile         =   request()->mobile;
            $otp            =   request()->otp;

            $check_mobile_exists = DB::table('verify_otp')->where('mobile',$mobile)->whereNull('verified_at')->first();
            if(!$check_mobile_exists){
                throw new Exception("Please Send OTP to Continue");
            }
            $otp_send_time  =   $check_mobile_exists->created_at;
            $minutes_to_add = 5;
            $time = new DateTime($otp_send_time);
            $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
            $expiry_time  = $time->format('Y-m-d H:i:s');
            if( strtotime('now') > strtotime($expiry_time) ) {
                throw new Exception("OTP Expired");
            }
            if($otp != $check_mobile_exists->otp){
                throw new Exception("Please Check OTP");
            }
            $verified_otp = DB::table('verify_otp')->where('mobile',$mobile)->update(['verified_at'=>date('Y-m-d H:i:s')]);
            if(!$verified_otp)
            {
                throw new Exception("Something Went Wrong. Please Verify again");
            }
            $result =   ['status'=>true, "message"=>"Phone Number Verified"];
        }
        catch(Exception $e)
        {
            $result =   ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }

}
