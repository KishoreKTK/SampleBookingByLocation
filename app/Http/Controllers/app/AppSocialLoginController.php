<?php

namespace App\Http\Controllers\app;
use DB;
use Auth;
use Mail;
use Hash;
use Validator;
use App\Currency;
use Carbon\Carbon;
use App\Customers;
use App\UserToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AppSocialLoginController extends Controller
{
    public function login_fb(Request $request)
    {
    	$rules=
        [
            "access_token"=>"required",
        ];

        $msg=
        [
            "access_token.required"=>"Access token field is required"
        ];

        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
            $return['error']=true;
            $return['msg']=$validator->errors()->all();
            return $return;

        }
        else
        {
        	$signup_type    =   0;
            $sn_id          =   1;
            $fcm            =   $request->fcm;
            $device         =   $request->device;
            $client_id      =   env('FB_CLIENT_ID');
            $secret         =   env('FB_CLIENT_SECRET');
            $time           =   Carbon::now();
            $access_token   =   $request->access_token;
            $login_type     =   $request->login_type;

            $curl           =   curl_init();
            curl_setopt_array($curl, array
            (
                CURLOPT_URL => 'https://graph.facebook.com/v2.10/me?fields=id,first_name,last_name,picture,email,link&access_token='.$access_token,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));

            $res = curl_exec($curl);
            $err = curl_error($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if($err)
            {
                $return["error"]=true;
                $return["msg"]="Failed to get account details";
                return $return;
            }
            elseif($status != 200)
            {
                $response=json_decode($res);
                $return['error']=true;
                $return["msg"]="Error occured";
                $return["response"]=$response;
                return $return;
            }
            else
            {
            	$res=json_decode($res);
            	$prof=[];
                $unique_id=isset($res->id)?$res->id:'';
                $first_name=isset($res->first_name)?$res->first_name:'';
                $email=isset($res->email)?$res->email:'';
                $last_name=isset($res->last_name)?$res->last_name:'';
                $image=isset($res->picture->data->url)?$res->picture->data->url:'';
                $sn_id=1;
                $profiles=[];
	            $api_token=md5(rand(10000,1000000).$email);

            	$fb_check=Customers::where("unique_id",$unique_id)->where("email",$email)->where("login_type",$sn_id)->first();

                if(isset($fb_check))
                {
                    $user_id=$fb_check->id;
                    $new_user=Customers::where("id",$user_id)->where("unique_id",$unique_id)->where("login_type",$sn_id)->update(['first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'image'=>$image,'updated_at'=>$time]);
                        // return $new_user;

                    if ($new_user)
                    {
		                $new_token=UserToken::insertGetId(['user_id'=>$user_id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
		                DB::commit();
		                 $user=DB::table("user")
					        ->leftJoin("countries", "countries.id","=","user.country_id")
					        ->where("user.id",$user_id)
			                ->whereNull('user.deleted_at')
					        ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
					        ->first();
		                $return['error']=false;
	                	$return['msg']="Successfully logged in as ".$user->first_name;
	                	$return['user_details']=$user;
	                	$return['api_token']=$api_token;
                    }
                    else
                	{
                		DB::rollback();
		        		$return['error']=true;
		        		$return['msg']="Error occured.";
                	}

                }
                else
                {
                	$check_exist=Customers::where("email",$email)->first();
                    if(isset($check_exist))
                    {
                    	 $user  =   DB::table("user")
                                    ->leftJoin("countries", "countries.id","=","user.country_id")
                                    ->where("email",$email)
                                    ->whereNull('user.deleted_at')
                                    ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                                    ->first();

                        $login_type=isset($user->login_type)?$user->login_type:0;
                        if($login_type==0)
                        {
                            if(isset($user->image)&& $user->image!='')
                            {
                                $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
                                $user->image=env('IMAGE_URL').'users/'.$user->image;
                            }
                            else
                            {
                                $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                            }
                        }
                        $new_token=UserToken::insertGetId(['user_id'=>$user->id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                        $return['error']=false;
	                	$return['msg']="Successfully logged in as ".$user->first_name;
	                	$return['user_details']=$user;
	                	$return['api_token']=$api_token;
                    }
                    else
                    {
                    	DB::beginTransaction();
                    	$new_user=Customers::insertGetId(['first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'country_id'=>0,'currency_id'=>0, 'gender_id'=>0, "active"=>1,"login_type"=>$sn_id,"unique_id"=>$unique_id,"suspend"=>0,'image'=>$image, 'created_at'=> $time,"updated_at"=>$time]);

			            if($new_user)
			            {
			                $new_token=UserToken::insertGetId(['user_id'=>$new_user,'fcm'=>$fcm,"device"=>$device,'api_token'=>$api_token, 'created_at'=> $time,"updated_at"=>$time]);
                			DB::commit();

			                $user=DB::table("user")
					        ->leftJoin("countries", "countries.id","=","user.country_id")
					        ->where("user.id",$new_user)
			                ->whereNull('user.deleted_at')
					        ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
					        ->first();

			                $return['error']=false;
		                	$return['msg']="Successfully logged in as ".$user->first_name;
		                	$return['user_details']=$user;
		                	$return['api_token']=$api_token;

                    	}
                    	else
                    	{
                    		DB::rollback();
			        		$return['error']=true;
			        		$return['msg']="Error occured.";
                    	}
                    }
                } //signup or already logged in using another platforms

            } //accesstoken authentications

        } //validator not failed
        return $return;
    }

    public function login_gp(Request $request)
    {
        $rules=
        [
            "access_token"=>"required",
        ];

        $msg=
        [
            "access_token.required"=>"Access token field is required"
        ];

        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
            $return['error']=true;
            $return['msg']=$validator->errors()->all();
            return $return;

        }
        else
        {
            $sn_id=2;
            $client_id=Config('google_credentials.GOOGLE_CLIENT_ID');
            // env('GP_CLIENT_ID');
            $secret=Config('google_credentials.GOOGLE_CLIENT_SECRET');
            // env('GP_CLIENT_SECRET');
            $time=Carbon::now();
            $access_token= $request->access_token;
            $login_type=$request->login_type;

            if($request->confirm_login)
            {
                $confirm_login=$request->confirm_login;
            }
            $curl = curl_init();
            curl_setopt_array($curl, array
            (
                CURLOPT_URL => 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$access_token,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));

            $resp = curl_exec($curl);
            $err = curl_error($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            // return $resp;

            if($err)
            {
                $return["error"]=true;
                $return["msg"]="Failed to get account details";
                return $return;
            }
            if ($status != 200)
            {
                $res=json_decode($resp);

                $return['error']=true;
                $return["msg"]="Error occured";
                $return["response"]=$res;

                return $return;
            }
            else
            {
                $res=json_decode($resp);
                $unique_id=$res->id;
                $fcm=$request->fcm;
                $device=$request->device;
                $first_name=$res->given_name;
                $email=$res->email;
                $last_name=$res->family_name;
                $image=$res->picture;
                $name=$res->name;
                $api_token=md5(rand(10000,1000000).$email);

                $gp_check=Customers::where("unique_id",$unique_id)->where("email",$email)->where("login_type",$sn_id)->first();

                if(isset($gp_check))
                {
                    $user_id    =   $gp_check->id;
                    $new_user=Customers::where("id",$user_id)->where("unique_id",$unique_id)->where("login_type",$sn_id)->update(['first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'image'=>$image,'updated_at'=>$time]);
                        // return $new_user;

                    if ($new_user)
                    {
                        $new_token=UserToken::insertGetId(['user_id'=>$user_id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                        DB::commit();
                         $user=DB::table("user")
                         ->leftJoin("countries", "countries.id","=","user.country_id")
                        ->where("user.id",$user_id)
                        ->whereNull('user.deleted_at')
                        ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                        ->first();
                        $return['error']=false;
                        $return['msg']="Successfully logged in as ".$user->first_name;
                        $return['user_details']=$user;
                        $return['api_token']=$api_token;
                    }
                    else
                    {
                        DB::rollback();
                        $return['error']=true;
                        $return['msg']="Error occured.";
                    }

                }
                else
                {
                    $check_exist=Customers::where("email",$email)->first();
                    if(isset($check_exist))
                    {
                         $user=DB::table("user")
                        ->leftJoin("countries", "countries.id","=","user.country_id")
                        ->where("email",$email)
                        ->whereNull('user.deleted_at')
                        ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                        ->first();
                        $login_type=isset($user->login_type)?$user->login_type:0;
                        if($login_type==0)
                        {
                            if(isset($user->image)&& $user->image!='')
                            {
                                $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
                                $user->image=env('IMAGE_URL').'users/'.$user->image;
                            }
                            else
                            {
                                $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                            }
                        }
                        $new_token=UserToken::insertGetId(['user_id'=>$user->id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                        $return['error']=false;
                        $return['msg']="Successfully logged in as ".$user->first_name;
                        $return['user_details']=$user;
                        $return['api_token']=$api_token;
                    }
                    else
                    {
                        DB::beginTransaction();
                        $new_user=Customers::insertGetId(['first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'country_id'=>0,'currency_id'=>0, 'gender_id'=>0, "active"=>1,"login_type"=>$sn_id,"unique_id"=>$unique_id,"suspend"=>0,'image'=>$image, 'created_at'=> $time,"updated_at"=>$time]);

                        if($new_user)
                        {
                            $new_token=UserToken::insertGetId(['user_id'=>$new_user,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                            DB::commit();

                            $user=DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->where("user.id",$new_user)
                            ->whereNull('user.deleted_at')
                            ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                            ->first();

                            $return['error']=false;
                            $return['msg']="Successfully logged in as ".$user->first_name;
                            $return['user_details']=$user;
                            $return['api_token']=$api_token;

                        }
                        else
                        {
                            DB::rollback();
                            $return['error']=true;
                            $return['msg']="Error occured.";
                        }
                    }
                } //signup or already logged in using another platforms
            } //accesstoken authentications
        } //validator not failed
        return $return;
    }

    public function login_apple(Request $request)
    {
        $rules=
        [
            "apple_id"=>"required",
        ];

        $msg=
        [
            "apple_id.required"=>"Apple Id is required"
        ];

        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
            $return['error']=true;
            $return['msg']=$validator->errors()->all();
            return $return;

        }
        else
        {
            $sn_id=3;

            $time=Carbon::now();
            $apple_id   =   $request->apple_id;
            $login_type =   $request->login_type;
            $unique_id  =   $apple_id;
            $fcm        =   $request->fcm;
            $device     =   $request->device;
            $first_name =   $apple_id;
            $email      =   $apple_id."@apple.com";
            $last_name='';
            $image='';
            $name=$apple_id;
            $api_token=md5(rand(10000,1000000).$email);

                $apple_check=Customers::where("unique_id",$unique_id)->where("email",$email)->where("login_type",$sn_id)->first();

                if(isset($apple_check))
                {
                    $user_id    =   $apple_check->id;
                    $new_user=Customers::where("id",$user_id)->where("unique_id",$unique_id)->where("login_type",$sn_id)->update(['first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'image'=>$image,'updated_at'=>$time]);
                        // return $new_user;

                    if ($new_user)
                    {
                        $new_token  =   UserToken::insertGetId(['user_id'=>$user_id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                        DB::commit();
                        $user=DB::table("user")
                         ->leftJoin("countries", "countries.id","=","user.country_id")
                        ->where("user.id",$user_id)
                        ->whereNull('user.deleted_at')
                        ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                        ->first();
                        $return['error']=false;
                        $return['msg']="Successfully logged in as ".$user->first_name;
                        $return['user_details']=$user;
                        $return['api_token']=$api_token;
                    }
                    else
                    {
                        DB::rollback();
                        $return['error']=true;
                        $return['msg']="Error occured.";
                    }

                }
                else
                {
                    $check_exist=Customers::where("email",$email)->first();
                    if(isset($check_exist))
                    {
                         $user=DB::table("user")
                        ->leftJoin("countries", "countries.id","=","user.country_id")
                        ->where("email",$email)
                        ->whereNull('user.deleted_at')
                        ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                        ->first();
                        $login_type=isset($user->login_type)?$user->login_type:0;
                        if($login_type==0)
                        {
                            if(isset($user->image)&& $user->image!='')
                            {
                                $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
                                $user->image=env('IMAGE_URL').'users/'.$user->image;
                            }
                            else
                            {
                                $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                            }
                        }
                        $new_token=UserToken::insertGetId(['user_id'=>$user->id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                        $return['error']=false;
                        $return['msg']="Successfully logged in as ".$user->first_name;
                        $return['user_details']=$user;
                        $return['api_token']=$api_token;
                    }
                    else
                    {
                        DB::beginTransaction();
                        $new_user=Customers::insertGetId(['first_name'=>$first_name,'last_name'=>$last_name,'email'=>$email,'country_id'=>0,'currency_id'=>0, 'gender_id'=>0, "active"=>1,"login_type"=>$sn_id,"unique_id"=>$unique_id,"suspend"=>0,'image'=>$image, 'created_at'=> $time,"updated_at"=>$time]);

                        if($new_user)
                        {
                            $new_token=UserToken::insertGetId(['user_id'=>$new_user,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
                            DB::commit();

                            $user=DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->where("user.id",$new_user)
                            ->whereNull('user.deleted_at')
                            ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
                            ->first();

                            $return['error']=false;
                            $return['msg']="Successfully logged in as ".$user->first_name;
                            $return['user_details']=$user;
                            $return['api_token']=$api_token;

                        }
                        else
                        {
                            DB::rollback();
                            $return['error']=true;
                            $return['msg']="Error occured.";
                        }
                    }
                } //signup or already logged in using another platforms
            // } //accesstoken authentications
        } //validator not failed
        return $return;
    }

    // public function login_apple(Request $request)
    // {
    //     $rules=
    //     [
    //         "apple_id"=>"required",
    //         // 'email'=>"required"
    //     ];

    //     $msg=
    //     [
    //         "apple_id.required"=>"Apple Id Required",
    //         // "email.required"=>"Email field is required"
    //     ];

    //     $validator=Validator::make($request->all(),$rules,$msg);
    //     if ($validator->fails())
    //     {
    //         $return['error']=true;
    //         $return['message']= implode( ", ",$validator->errors()->all());
    //         return $return;
    //     }
    //     else
    //     {
    //         $time=Carbon::now();
    //         $sn_id=3;

    //         $apple_id   =   $request->apple_id;
    //         $mailid     =   $apple_id."@apple.com";
    //         $name= isset($request->username)?$request->username:$request->apple_id;
    //         $email= isset($request->email)?$request->email:$mailid;

    //         $fcm=null;
    //         $device=null;
    //         $unique_id  =   $apple_id;
    //         $last_name='';
    //         $image=null;
    //         $api_token=md5(rand(10000,1000000).$email);

    //         $apple_check=Customers::where("unique_id",$apple_id)->where("email",$email)->where("login_type",$sn_id)->first();

    //         if(isset($apple_check))
    //         {
    //             $user_id    =   $apple_id;
    //             $new_user=Customers::where("id",$user_id)->where("unique_id",$apple_id)->where("login_type",$sn_id)->update(['first_name'=>$name,
    //                     'last_name'=>$last_name,'email'=>$email,'image'=>$image,'updated_at'=>$time]);
    //                 // return $new_user;

    //             if ($new_user)
    //             {
    //                 $new_token=UserToken::insertGetId(['user_id'=>$user_id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
    //                 DB::commit();
    //                  $user=DB::table("user")
    //                  ->leftJoin("countries", "countries.id","=","user.country_id")
    //                 ->where("user.id",$user_id)
    //                 ->whereNull('user.deleted_at')
    //                 ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
    //                 ->first();
    //                 $return['error']=false;
    //                 $return['msg']="Successfully logged in as ".$user->first_name;
    //                 $return['user_details']=$user;
    //                 $return['api_token']=$api_token;
    //             }
    //             else
    //             {
    //                 DB::rollback();
    //                 $return['error']=true;
    //                 $return['msg']="Error occured.";
    //             }

    //         }
    //         else
    //         {
    //             $check_exist=Customers::where("email",$email)->first();
    //             if(isset($check_exist))
    //             {
    //                  $user=DB::table("user")
    //                 ->leftJoin("countries", "countries.id","=","user.country_id")
    //                 ->where("email",$email)
    //                 ->whereNull('user.deleted_at')
    //                 ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
    //                 ->first();
    //                 $login_type=isset($user->login_type)?$user->login_type:0;
    //                 if($login_type==0)
    //                 {
    //                     if(isset($user->image)&& $user->image!='')
    //                     {
    //                         $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
    //                         $user->image=env('IMAGE_URL').'users/'.$user->image;
    //                     }
    //                     else
    //                     {
    //                         $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
    //                     }
    //                 }
    //                 $new_token=UserToken::insertGetId(['user_id'=>$user->id,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
    //                 $return['error']=false;
    //                 $return['msg']="Successfully logged in as ".$user->first_name;
    //                 $return['user_details']=$user;
    //                 $return['api_token']=$api_token;
    //             }
    //             else
    //             {
    //                 DB::beginTransaction();
    //                 $new_user=Customers::insertGetId(['first_name'=>$name,'last_name'=>$last_name,'email'=>$email,'country_id'=>0,'currency_id'=>0, 'gender_id'=>0, "active"=>1,"login_type"=>$sn_id,"unique_id"=>$unique_id,"suspend"=>0,'image'=>$image, 'created_at'=> $time,"updated_at"=>$time]);

    //                 if($new_user)
    //                 {
    //                     $new_token=UserToken::insertGetId(['user_id'=>$new_user,'api_token'=>$api_token,'fcm'=>$fcm,"device"=>$device, 'created_at'=> $time,"updated_at"=>$time]);
    //                     DB::commit();

    //                     $user=DB::table("user")
    //                     ->leftJoin("countries", "countries.id","=","user.country_id")
    //                     ->where("user.id",$new_user)
    //                     ->whereNull('user.deleted_at')
    //                     ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.image","user.email","countries.name as country")
    //                     ->first();

    //                     $return['error']=false;
    //                     $return['msg']="Successfully logged in as ".$user->first_name;
    //                     $return['user_details']=$user;
    //                     $return['api_token']=$api_token;

    //                 }
    //                 else
    //                 {
    //                     DB::rollback();
    //                     $return['error']=true;
    //                     $return['msg']="Error occured.";
    //                 }
    //             }
    //         } //signup or already logged in using another platforms
    //     } //accesstoken authentications

    //     return $return;
    // }
}
