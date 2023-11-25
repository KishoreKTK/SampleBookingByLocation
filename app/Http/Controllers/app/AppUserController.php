<?php

namespace App\Http\Controllers\app;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\Currency;
use Carbon\Carbon;
use App\Customers;
use App\UserToken;
use App\UserActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class AppUserController extends Controller
{
    public function signup(Request $request)
    {
    	$rules  =   [
                        "first_name"=>"required",
                        "email"=>"required|email|unique:user,email",
                        "password"=>"required|min:5",
                        // 'password_confirmation'=>'required|same:password',
                        // 'phone'=>"required|numeric"
                    ];

        $msg    =   [
                        "first_name.required"=>"First name is required",
                    ];

        $validator  =   Validator::make($request->all(), $rules, $msg);
        if($validator->fails())
        {
            $return['error']    =   true;
            $return['msg']      =   implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
        	$first_name     =   $request->first_name;
        	$last_name      =   $request->last_name;
            $email          =   $request->email;
            $phone          =   $request->phone;
            $country_code   =   $request->country_code;
            // $password       =   $request->password;
            // $fcm            =   $request->fcm;
            // $device         =   $request->device;
            $time           =   Carbon::now();
            $imageName      =   null;
            DB::beginTransaction();
            $b_password     =   bcrypt($request->password);
            $api_token      =   md5(rand(10000,1000000).$email);

            $new_user       =   Customers::insertGetId(['first_name'=>$first_name,
                                                'last_name'=>$last_name,
                                                'email'=>$email,
                                                'country_id'=>225,
                                                'currency_id'=>0,
                                                "password"=>$b_password,
                                                'login_type'=>0,
                                                'phone'=>$phone,
                                                'country_code'=>$country_code,
                                                'gender_id'=>0,
                                                "active"=>0,
                                                "suspend"=>0,
                                                'image'=>$imageName,
                                                'created_at'=> $time,
                                                "updated_at"=>$time
                                            ]);
            if($new_user)
            {
                $new_token  =   DB::table("email_token")->insertGetId(['email'=>$email,'token'=>$api_token, 'created_at'=> $time,"updated_at"=>$time]);

                $data       =   [
                                    "to"=>$email,
                                    "subject"=>"Verify your email",
                                    "name"=>$first_name,
                                    "email"=>$email,
                                    "token"=>$api_token,
                                ];

            	$mail       =   Mail::send('emails.email_verify', ["data"=>$data], function ($message) use ($data)
                {
                    $message->to($data['to'])->subject("Verify your email");
                });

                DB::commit();
                $return['error']    =   false;
        		$return['msg']      =   "Signup completed. Please check your email to verify.";
        	}
        	else
        	{
                DB::rollback();
        		$return['error']    =   true;
        		$return['msg']      =   "Error occured.";
        	}

        }
        return $return;
    }

    public function confirm_email(Request $request)
    {
        $rules=
        [
            "email"=>"required|email|exists:user,email,deleted_at,NULL",
            "token"=>"required",
        ];

        $msg=
        [
            "email.required"=>"email field is empty",
            "email.exists"=>"Invalid email id",
            "token.exists"=>"Invalid token",
        ];

        $validator  =   Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {
            $token=$request->token;
            $email=$request->email;
            $time=Carbon::now();
            $check_token=DB::table("email_token")->where("token",$token)->where("email",$email)->first();
            if(isset($check_token))
            {

            	$check_confirm=Customers::where("email",$email)->where("active",0)->first();
                if(isset($check_confirm))
                {
                    $update=Customers::where("email",$email)->update(["active"=>1,"updated_at"=>$time]);
                    if($update)
                    {
                        $del_token=DB::table("email_token")->where("token",$token)->delete();
                        return view("app.verified_email")->with("error", false)->with("msg", "Your email successfully verified. Please login to continue.");
                    }
                    else
                    {
                        return view("app.verified_email")->with("error", true)->with("msg", "Error occured");

                    }
                }
                else
                {
                    return view("app.verified_email")->with("error", true)->with("msg", "This link has already used or expired.");
                }

            }
            else
            {

                $check_confirm=Customers::where("email",$email)->where("active",1)->first();
                if(isset($check_confirm))
                {
                    return view("app.verified_email")->with("error", false)->with("msg", "You already confirmed your email address. Please login to continue.");
                }
                else
                {
                    return view("app.verified_email")->with("error", true)->with("msg", "This link has already used or expired.");
                }
            }
        }
        return $return;


    }

    public function login(Request $request)
	{
        $rules  =   [
                        "email"=>"required|exists:user,email,deleted_at,NULL",
                        "password"=>"required",
                    ];
        $msg    =   [
                        "email.required"=>"Email is required"
                    ];

        $validator  =   Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=   true;
		    $return['msg']  =   implode( ", ",$validator->errors()->all());
        }
        else
        {
             if($request->remember&&$request->remember==1)
            {
                $remember   =   1;
            }
            else
            {
                $remember   =   0;
            }

            $time       =   Carbon::now();
            $fcm        =   $request->fcm;
            $device     =   $request->device;

            if (Auth::guard('customer-web')->attempt(['email' => $request->email, 'password' => $request->password],$remember))
            {
                $customer=  DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->where("email",$request->email)
                            ->whereNull('user.deleted_at')
                            ->select("user.id","user.first_name","user.last_name","user.phone","user.active","user.suspend","user.image","user.country_code","user.email","countries.name as country")
                            ->first();
            }
            else
            {
            	$return['error']    =   true;
	    		$return['msg']      =   "Invalid login credentials";
                return $return;
            }


            if (isset($customer))
            {
        	 	if($customer->active==0)
	            {
	                $return['error']=true;
	                $return['msg']="Verify your email first";
	            }
                elseif($customer->suspend==1)
                {
                    $return['error']=true;
                    $return['msg']="Your account is temporarily suspended";
                }
	            else
	            {
	            	 if(isset($customer->image)&&$customer->image!='')
		            {

                        $customer->thumbnail=url($customer->image);
                        $customer->image=url($customer->image);

		                // $customer->thumbnail= env("IMAGE_URL")."users/thumbnails/".$customer->image;
		                // $customer->image= env("IMAGE_URL")."users/".$customer->image;
		            }
		            else
		            {
		                $customer->thumbnail= env("IMAGE_URL")."logo/no-profile.jpg";
		            }
	            	$mail=$customer->email;
	                $api_token=md5(rand(10000,1000000).$mail);
	                $user_id=$customer->id;
	                $time=Carbon::now();
	                $new_token=UserToken::insertGetId(['user_id'=>$user_id,'fcm'=>$fcm,"device"=>$device,'api_token'=>$api_token, 'created_at'=> $time,"updated_at"=>$time]);
	                if($new_token)
	                {
                        $user_activity=UserActivity::insertGetId(['activity_id'=>1,'action_id'=>3,"user_id"=>$user_id,'text'=>" logged into the account ",'date'=>$time,'time'=>$time, 'created_at'=> $time,"updated_at"=>$time]);
                        $details=UserToken::where("id",$new_token)->first();
	                	$return['error']=false;
	                	$return['msg']="Successfully logged in as ".$customer->first_name;
	                	$return['user_details']=$customer;
                        $return['api_token']=$api_token;
                        $return['device']=$details->device;
	                	$return['fcm']=$details->fcm;
	                }
	                else
	                {
	                	$return['error']=true;
	                	$return['msg']="Sorry error occured";
	                }
	            }

            }
            else
            {
                $return['error']=true;
	            $return['msg']="Invalid login credentials";
            }
        }
        return $return;
    }


    public function add_device(Request $request)
    {
     $rules=[
            "fcm"=>"required",
            "device"=>"required",
            ];
        $msg=[
            "fcm.required"=>"Fcm is required"
             ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
           $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {
            $api_token=request()->header('User-Token');
            $fcm=$request->fcm;
            $time=Carbon::now();
            $device=$request->device;
            $check_token=UserToken::where("api_token",$api_token)->first();
            if(isset($check_token))
            {
                $update=UserToken::where("api_token",$api_token)->update(['fcm'=>$fcm,"device"=>$device,"updated_at"=>$time]);
                if($update)
                {
                    $return['error']=false;
                    $return['msg']="Successfully updated login device";
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Sorry error occured";
                }
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry no records found";
            }
        }
        return $return;
    }


    public function logout(Request $request)
    {
        $time=Carbon::now();
        $api_token=request()->header('User-Token');

        $expiry=$time->subHours(24);
        $take=UserToken::where("api_token",$api_token)->first();
        $user_id=$take->user_id;
        $records=UserToken::where("user_id",$user_id)->where("updated_at", "<", $expiry)->get();
        $destroy=UserToken::where("id",$take->id)->delete();

        if($destroy)
        {
            // foreach (UserToken::where('user_id', '=',$user_id)->where("updated_at", "<", $expiry)->get() as $value)
            // {
            //     $delete=UserToken::where("id",$value->id)->delete();
            // }

            Auth::guard('customer-web')->logout();

            $return['error']=false;
	        $return['msg']="Successfully logged out";

        }
        else
        {
        	$return['error']=false;
	        $return['msg']="Sorry error occured";
        }
    	return $return;
    }


    public function forgot_password(Request $request, Customers $user)
    {
         $rules=[
            "email"=>"required|exists:user,email,deleted_at,NULL",
            ];
        $msg=[
            "email.required"=>"Email is required",
            "email.exists"=>"Sorry we can't find a user with that email address."
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {
            $email      =   $request->email;
            $details    =   $user->where("email",$request->email)->first();
            $token      =   md5(rand(10000,1000000).$email);
            $time       =   Carbon::now();
            $insert_token=DB::table("user_password_resets")->insertGetId([
                                    "email"=>$email,
                                    "token"=>$token,
                                    "created_at"=>$time,
                                    'updated_at'=>$time
                                ]);
            $to     =   $request->email;
            $data   =   [
                            "to"=>$to,
                            "subject"=>"Reset password",
                            "name"=>$details->first_name,
                            "email"=>$request->email,
                            "token"=>$token,
                        ];
            $mail   =   Mail::send('emails.user_forgot_password', ["data"=>$data], function ($message) use ($data)
            {
                $message->to($data['to'])->subject("Reset your password");
            });

            $return['error']=false;
            $return['msg']="Please check your email to change password.";
        }
        return $return;
    }

    public function reset_pwd(Request $request)
    {
        $token=$email='';
        $token=$request->token; $email=$request->email;
        return view("app.user_reset_password",compact('email','token'));
    }

    public function reset_password(Request $request,Customers $user)
    {
        $rules=[
            "email"=>"required|exists:user,email,deleted_at,NULL",
            "token"=>"required",
            "password"=>"required|min:5",
            'password_confirmation'=>'required|same:password',
            ];
        $msg=[
            "password.required"=>"New password is required",
            "token.exists"=>"Invalid or expired token"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            return redirect()->back()->with("error", false)->with("msg", implode( ", ",$validator->errors()->all()));
        }
        else
        {
            $time=Carbon::now();
            $token=$request->token;
            $email=$request->email;
            $user=$user->where("email",$request->email)->first();
             $check_token=DB::table("user_password_resets")->where("token",$token)->where("email",$email)->whereNull('user_password_resets.deleted_at')->first();
            if(!isset($check_token))
            {
                 return redirect()->back()->with("error", true)->with("msg", "This link has already used or expired");
            }
            else
            {
                $update=$user->where("email",$request->email)->update([
                "password"=>bcrypt($request->password),"updated_at"=>$time
                ]);
                if($update)
                {
                    $del_token=DB::table("user_password_resets")->whereNull('user_password_resets.deleted_at')->where("token",$token)->where("email",$email)->delete();
                    return redirect()->back()->with("error", false)->with("msg", "Password changed successfully.");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Error occured during process.");
                }
            }
        }
        return $return;
    }

    public function profile(Request $request)
    {
        $time       =   Carbon::now();
        $api_token  =   request()->header('User-Token');
        $expiry     =   $time->subHours(24);
        $take       =   UserToken::where("api_token",$api_token)->first();
        $user_id    =   $take->user_id;
        $user       =   DB::table("user")
                        ->leftJoin("countries", "countries.id","=","user.country_id")
                        ->where("user.id",$user_id)
                        ->whereNull('user.deleted_at')
                        ->select("user.id","user.first_name","user.last_name"
                            ,"user.phone","user.country_id","user.address","user.dob","user.city","user.login_type","user.active","user.image","user.country_code","user.email","countries.name as country")
                        ->first();
        if($user)
        {
            $login_type=isset($user->login_type)?$user->login_type:0;
            if($login_type==0)
            {
                if(isset($user->image)&& $user->image!='')
                {
                    $user->thumbnail=url($user->image);
                    $user->image=url($user->image);
                    // $user->thumbnail    =   env('IMAGE_URL').'users/thumbnails/'.$user->image;
                    // $user->image        =   env('IMAGE_URL').'users/'.$user->image;
                }
                else
                {
                    $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                }
            }
            $return['error']=false;
            $return['user']=$user;
            $return['msg']="User profile listed successfully";
        }
        else
        {
            $return['error']=false;
            $return['msg']="Sorry error occured";
        }
        return $return;
    }

    public function check_suspend(Request $request)
    {
        $time=Carbon::now();
        $api_token=request()->header('User-Token');


        $take=UserToken::where("api_token",$api_token)->first();
        $user_id=$take->user_id;
        $user=DB::table("user")
            ->leftJoin("countries", "countries.id","=","user.country_id")
            ->where("user.id",$user_id)
            ->whereNull('user.deleted_at')
            ->select("user.id","user.first_name","user.last_name"
                ,"user.phone","user.country_id","user.address","user.dob","user.city","user.login_type","user.active","user.image","user.suspend","user.country_code","user.email","countries.name as country")
            ->first();
        if(isset($user)&& isset($user->suspend) )
        {
           if($user->suspend==1)
           {
            $return['error']=false;
            $return['suspend']=true;
            $return['user']=$user;
            $return['msg']="User is suspended";

           }
           else
           {
             $return['error']=false;
             $return['suspend']=false;
            $return['user']=$user;
            $return['msg']="User is not suspended";
           }
        }
        else
        {
            $return['error']=false;
            $return['msg']="Sorry error occured";
        }
        return $return;
    }

    public function update_profile(Request $request)
    {
        $rules=[
            "first_name"=>"required",
            "image"=>"mimes:jpeg,jpg,png,gif|max:10000",
        ];
        $msg=[
            "name.required"=>"Company name is required",
            "image.mimes"=>"Please choose an image",
        ];

        $validator  =   Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {

            // $url            = public_path('img/users/');
            // $turl           = public_path('img/users/thumbnails/');

            // $url    =   env('UPLOAD_URL')."/users/";
            // $turl   =   env('UPLOAD_URL')."/users/thumbnails/";
            // dd($url);

            $api_token=request()->header('User-Token');
            $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            $first_name=$request->first_name;
            $last_name=$request->last_name;
            // $email=$request->email;
            $country_code=$request->country_code;

            // $image=$request->image;
            $dob=$request->dob;
            $country_id=$request->country;
            $address=$request->address;
            $city=$request->city;
            $phone=$request->phone;
            if($phone=="")
            {
                $phone=Customers::where('id', $user_id)->first()->phone;
            }
            $time=Carbon::now();

            if($request->country)
            {
                $currency_id=Currency::where("country_id",$country_id)->first()->id;
            }
            else
            {
                $currency_id=0;
                $country_id=0;
            }
            $update_data    =  ['first_name'=>$first_name,'last_name'=>$last_name,'country_id'=>$country_id, 'dob'=>$dob,'address'=>$address,'country_code'=>$country_code,'city'=>$city,'currency_id'=>$currency_id, 'phone'=>$phone,"updated_at"=>$time];

            if(request()->hasFile('image'))
            {
                $old_pic_path    = Customers::where('id', $user_id)->first()->image;

                if($old_pic_path)
                {
                    File::delete($old_pic_path);
                }
                $image_url      = 'img/users/';
                $image_name     = time() . '.'.request()->file('image')->getClientOriginalExtension();
                str_replace(' ', '_', $request->file('image')->getClientOriginalName());
                request()->image->move(public_path($image_url), $image_name);
                $update_data['image']   = $image_url.$image_name;
            }

            // dd($update_data);

            $new_user   =   Customers::where("id",$user_id)->update($update_data);

            if($new_user)
            {
                $user   =   DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->where("user.id",$user_id)
                            ->whereNull('user.deleted_at')
                            ->select("user.id","user.first_name","user.last_name","user.dob","user.phone","user.address","user.city","user.active","user.country_code","user.image","user.email","countries.name as country")
                            ->first();
                if(isset($user->image)&& $user->image!='')
                {
                    $user->thumbnail=url($user->image);
                    $user->image=url($user->image);
                    // env('IMAGE_URL').'users/'.$user->image;
                }
                else
                {
                    $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                }
                $return['error']=false;
                $return['user']=$user;
                $return['msg']="User profile updated successfully";
            }
            else
            {
                $return['error']=false;
                $return['msg']="Sorry error occured";
            }
        }
        return $return;
    }

    public function update_img(Request $request)
    {
        $rules=[
            "image"=>"required|mimes:jpeg,jpg,png,gif|max:10000",
            ];
        $msg=[
            "image.required"=>"Please choose an image",

             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {
            $url=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/users/";
            $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/users/thumbnails/";

            $api_token=request()->header('User-Token');
            $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            $image=$request->image;
            $time=Carbon::now();

            if ($image)
            {
                $file = Customers::where('id', $user_id)->first()->image;
                if ($file)
                {
                    if(file_exists($url.$file))
                    {
                        unlink($url.$file);
                    }
                     if(file_exists($turl.$file))
                    {
                        unlink($turl.$file);
                    }
                }
                $imageName = md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();
                $desti=$url.$imageName;
                $t_desti=$turl.$imageName;

                 $resize=Image::make($image->getRealPath())->resize(200, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                $resize->save($t_desti);
                $saveImage=$image->move($url, $imageName);
                $new_user=Customers::where("id",$user_id)->update(['image'=>$imageName,"updated_at"=>$time]);
            }

            if($new_user)
            {
                $user=DB::table("user")
                ->leftJoin("countries", "countries.id","=","user.country_id")
                ->where("user.id",$user_id)
                ->whereNull('user.deleted_at')
                ->select("user.id","user.first_name","user.last_name","user.dob","user.phone","user.address","user.city","user.country_code","user.active","user.image","user.email","countries.name as country")
                ->first();
                if(isset($user->image)&& $user->image!='')
                {
                    $user->thumbnail=url($user->image);
                    $user->image=url($user->image);

                    // $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
                    // $user->image=env('IMAGE_URL').'users/'.$user->image;
                }
                else
                {
                    $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                }
                $return['error']=false;
                $return['user']=$user;
                $return['msg']="Your profile image updated successfully";
            }
            else
            {
                $return['error']=false;
                $return['msg']="Sorry error occured";
            }
        }
        return $return;
    }

    public function change_password(Request $request,Customers $user)
    {
         $rules=[
            "old_password"=>"required",
            "password"=>"required|min:5",
            'password_confirmation'=>'required|same:password'
            ];
         $msg=[
            "old_password.required"=>"Current password is required",
            "password.required"=>"New password is required"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {
            $time=Carbon::now();
            $api_token=request()->header('User-Token');
            $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            $user=Customers::where("id",$user_id)->first();

            if(Hash::check($request->old_password, $user->password))
            {
                 $update=$user->where("id",$user_id)->update([
                "password"=>bcrypt($request->password),"updated_at"=>$time
                ]);
                if($update)
                {
                    $return['error']=false;
                    $return['msg']="Your password changed successfully.";
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Error occured during process";
                }
            }
            else
            {
                $return['error']=true;
                $return['msg']="Incorrect password";
            }
        }
        return $return;
    }

}
