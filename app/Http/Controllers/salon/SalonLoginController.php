<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Mail;
use Hash;
use Session;
use Validator;
use App\SRoles;
use App\Salons;
use App\Booking;
use App\Currency;
use App\Countries;
use Carbon\Carbon;
use App\Categories;
use App\SalonRoles;
use App\SalonUsers;
use App\SalonStaffs;
use App\SalonsToken;
use App\SalonImages;
use App\SalonReviews;
use App\SalonServices;
use App\SalonUsersToken;
use App\SalonCategories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session as SessionSession;

class SalonLoginController extends Controller
{
    public function test()
    {
        if(Auth::guard('salon-web')->check() && Auth::guard('salons-web')->check())
        {
            return redirect(env("ADMIN_URL").'/salon/dashboard');
        }
        else
        {
            return redirect(env("ADMIN_URL").'/salon/login');
        }
    }

    public function get_login()
    {
        if(Auth::guard('salon-web')->check() && Auth::guard('salons-web')->check())
        {
            return redirect(env("ADMIN_URL").'/salon/dashboard');
        }
        else
        {
            return view('salon.login');
        }
    }

    public function login(Request $request)
	{
     $rules=[
            "email"=>"required",
            // "email"=>"required|exists:salons,email,deleted_at,NULL",
            "password"=>"required",
            ];
        $msg=[
            "email.required"=>"Email is required"
             ];

        if($request->remember&&$request->remember==1)
        {
            $remember=1;
        }
        else
        {
            $remember=0;
        }

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $time=Carbon::now();
            $roles=[];
            $who='';
            //check email

            $check=Salons::where("email",$request->email)->first();
            if(isset($check))
            {
                if($remember==1)
                {
                    if (Auth::guard('salon-web')->attempt(['email' => $request->email, 'password' => $request->password],$remember))
                    {
                        $salon_id=$salon->id;

                        $salon=Salons::where("email",$request->email)->first();
                        $roles=SRoles::pluck("role")->toArray();
                        $who="salon";

                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Invalid login credentials");
                    }
                }
                else
                {
                     if (Auth::guard('salon-web')->attempt(['email' => $request->email, 'password' => $request->password]))
                    {
                        $salon=Salons::where("email",$request->email)->first();
                        $salon_id=$salon->id;

                        $roles=SRoles::pluck("role")->toArray();
                        $who="salon";
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Invalid login credentials");
                    }
                }
            }
            else
            {
                $check_user=SalonUsers::where("email",$request->email)->first();
                if(isset($check_user))
                {
                    if($remember==1)
                    {
                        if (Auth::guard('salons-web')->attempt(['email' => $request->email, 'password' => $request->password],$remember))
                        {
                            $salon=SalonUsers::where("email",$request->email)->first();
                            $who="user";
                            $salon_id=$salon->salon_id;
                            $roles=DB::table("salon_roles")
                            ->join("sroles", "sroles.id","=","salon_roles.role_id")
                            ->whereNull("salon_roles.deleted_at")
                            ->whereNull("sroles.deleted_at")
                            ->where("salon_roles.salon_id",$salon->id)->pluck("role")->toArray();

                        }
                        else
                        {
                            return redirect()->back()->with("error", true)->with("msg", "Invalid login credentials");
                        }
                    }
                    else
                    {
                         if (Auth::guard('salons-web')->attempt(['email' => $request->email, 'password' => $request->password]))
                        {
                            $salon=SalonUsers::where("email",$request->email)->first();
                            $who="user";
                            $salon_id=$salon->salon_id;
                             $roles=DB::table("salon_roles")
                            ->join("sroles", "sroles.id","=","salon_roles.role_id")
                            ->whereNull("salon_roles.deleted_at")
                            ->whereNull("sroles.deleted_at")
                            ->where("salon_roles.salon_id",$salon->id)->pluck("role")->toArray();

                        }
                        else
                        {
                            return redirect()->back()->with("error", true)->with("msg", "Invalid login credentials");
                        }
                    }
                }
                else
                {
                        return redirect()->back()->with("error", true)->with("msg", "Invalid email");

                }

            }

            if (isset($salon))
            {
                // Authentication ...
                $mail=$salon->email;
                $api_token=md5(rand(10000,1000000).$mail);
                $time=Carbon::now();

                if($salon->suspend==1)
                {
                	return redirect()->back()->with("error", true)->with("msg", "Your account is temporarily suspended.");
                }
                 if(isset($check))
                {
                    $new_token=SalonsToken::insertGetId(['salon_id'=>$salon_id,'api_token'=>$api_token, 'created_at'=> $time,"updated_at"=>$time]);
                }

                 else if(isset($check_user))
                {
                     $new_token=SalonUsersToken::insertGetId(['salon_id'=>$salon_id,'api_token'=>$api_token, 'created_at'=> $time,"updated_at"=>$time]);
                }
                else
                {
                        return redirect()->back()->with("error", true)->with("msg", "Invalid credentials");

                }

                $booking=Booking::where("read_s",0)->where("salon_id",$salon_id)->count();
                $reviews=SalonReviews::where("read",0)->where("salon_id",$salon_id)->count();

                $transactions=Booking::where("read_ts",0)->where("salon_id",$salon_id)->count();
                Session::put('sreviews', $reviews);
                Session::put('sroles', $roles);
                Session::put('user', $who);
                // Session::put('salon_id', $salon_id);
                Session::put('sbooking', $booking);
                Session::put('stransactions', $transactions);
                Session::put('salon_token', $api_token);
                // return $salon;

            return redirect(env("ADMIN_URL").'/salon/dashboard')->with("error", false)->with("msg", "Successfully logged in");

            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Invalid login credentials");
            }
        }
    }

    public function logout(Request $request)
    {
        $api_token=Session::get('salon_token');
        $time=Carbon::now();

        $expiry=$time->subHours(24);
        // $api_token=$request->api_token;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $take=SalonsToken::where("api_token",$api_token)->first();
            $salon_id=$take->salon_id;
            $records=SalonsToken::where("salon_id",$salon_id)->where("updated_at", "<", $expiry)->get();
            $destroy=SalonsToken::where("id",$take->id)->delete();
             foreach($records as $value)
            {
               $delete=SalonsToken::where("id",$value->id)->delete();
            }
            Auth::guard('salon-web')->logout();

        }
        else
        {
             $take=SalonUsersToken::where("api_token",$api_token)->first();
            $salon_id=$take->salon_id;
            $records=SalonUsersToken::where("salon_id",$salon_id)->where("updated_at", "<", $expiry)->get();
            $destroy=SalonUsersToken::where("id",$take->id)->delete();
             foreach($records as $value)
            {
               $delete=SalonUsersToken::where("id",$value->id)->delete();
            }
            Auth::guard('salons-web')->logout();

        }

        if($destroy)
        {


            Session::forget('salon_token');
            Session::forget('sreviews');
            Session::forget('user');
            Session::forget('stransactions');
            Session::forget('sbooking');
            return redirect(env("ADMIN_URL").'/salon/login')->with("error", false)->with("msg", "Successfully logged out");
        }
        else
        {
            return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
        }
    }

    public function change_password(Request $Request)
    {
        return view("salon.change_password");
    }

    public function change_old_password(Request $request)
    {
        $rules=[
            "old_password"=>"required",
            "password"=>"required|min:6",
            'password_confirmation'=>'required|same:password'
            ];
        $msg=[
            "old_password.required"=>"Current password is required",
            "password.required"=>"New password is required"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $time=Carbon::now();

             $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $email= Auth::guard('salon-web')->user()->email;
                  $take=Salons::where("email",$email)->first();

               if (Hash::check($request->old_password, $take->password))
               {
                    $update=Salons::where("email",$email)->update([
                    "password"=>bcrypt($request->password),
                    "updated_at"=>$time
                    ]);
                    if($update)
                    {
                        return redirect()->back()->with("error", false)->with("msg", "Password changed successfully");
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
                    }

                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Incorrect password")->withInput();

                }
            }
            else
            {
                $email= Auth::guard('salons-web')->user()->email;
                  $take=SalonUsers::where("email",$email)->first();

               if (Hash::check($request->old_password, $take->password))
               {
                    $update=SalonUsers::where("email",$email)->update([
                    "password"=>bcrypt($request->password),
                    "updated_at"=>$time
                    ]);
                    if($update)
                    {
                        return redirect()->back()->with("error", false)->with("msg", "Password changed successfully");
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
                    }

                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Incorrect password")->withInput();

                }
            }


        }
    }

    public function forgot_password(Request $Request)
    {
        return view("salon.forgot_password");
    }

    public function forgot_pwd_mail(Request $request)
    {
        $email=$request->email;
        $details=Salons::where("email",$request->email)->first();
        if(!isset($details))
        {
            $details=SalonUsers::where("email",$request->email)->first();
            if(!isset($details))
            {
            return redirect()->back()->with("error", false)->with("msg", "Invalid email");

            }
        }
        $token=md5(rand(10000,1000000).$email);
        $time=Carbon::now();
        $insert_token=DB::table("salon_password_resets")->insertGetId(["email"=>$email,"token"=>$token,"created_at"=>$time,'updated_at'=>$time]);
        $to=$request->email;
            $data=["to"=>$to,
            "subject"=>"Reset password",
            "name"=>$details->name,
            "email"=>$request->email,
            "token"=>$token,
            ];
        $mail=Mail::send('emails.salon.forgot_password', ["data"=>$data], function ($message) use ($data)
            {
                $message->to($data['to'])->subject("Reset your password");
            });

            return redirect()->back()->with("error", false)->with("msg", "Please check your mail to reset your password");
    }

    public function view(Request $request)
    {
        // $id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $id= Auth::guard('salons-web')->user()->salon_id;
        }
        $salon=DB::table("salon")
            ->leftJoin("countries", "countries.id","=","salon.country_id")->whereNull('salon.deleted_at')->where("salon.id",$id)->select("salon.*","countries.name as country")->first();
        if(isset($salon->image)&&$salon->image!='')
        {
            $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;

        }
        else
        {
            $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
        }

        return view('salon.profile.view',compact('activePage','salon','id'));
    }

    public function edit(Request $request)
    {
        // $id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $id= Auth::guard('salons-web')->user()->salon_id;
        }
        $categories=Categories::where('active_status',1)->whereNull('deleted_at')->get();
        $c_categories=$subs=[];
         $sub_images=salonImages::where("salon_id",$id)->select("id","image")->get();
        if(isset($sub_images)&&count($sub_images)>0)
        {
            foreach($sub_images as $one)
            {
                $sub_image=$one->image;
                $img_id=$one->id;

                $one->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$sub_image;
                $one->image= env("IMAGE_URL")."salons/".$sub_image;
                $sb_image= env("IMAGE_URL")."salons/".$sub_image;

                $new["id"]=$img_id;
                $new["image"]=$sb_image;
                $subs[]=$new;
            }
        }
        foreach (saloncategories::where('salon_id', '=',$id)->get() as $each)
        {
            $c_categories[]=$each->facility_id;
        }
        $countries=Countries::orderBy("name")->pluck("name","id");
        $salon=Salons::where("id",$id)->first();

        return view('salon.profile.edit',compact('activePage','countries','salon','subs','id','categories','c_facilities'));
    }

    public function update(Request $request)
    {
        $rules=[
            "name"=>"required",
            "image"=>"mimes:jpeg,jpg,png,gif|max:10000",
            "country"=>"required",
            "location"=>"required",
            ];
        $msg=[
            "name.required"=>"salon name is required",
            "image.required"=>"Please choose an image",
            "country.required"=>"Select a company",

             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            // $id=Auth::guard('salon-web')->user()->id;
             $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $id= Auth::guard('salon-web')->user()->id;
            }
            else
            {
                $id= Auth::guard('salons-web')->user()->salon_id;
            }
            $name=$request->name;
            $description=$request->description;
            $image=$request->image;
            $country=$request->country;
            $city=$request->city;
            $location=$request->location;
            $phone=$request->phone;
            $files = $request->file('sub_images');
            $time=Carbon::now();
            $currency_id=Currency::where("country_id",$country)->first()->id;
            $url=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/salons/";
            $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/salons/thumbnails/";

            $latitude=isset($request->latitude)?$request->latitude:0;
            $longitude=isset($request->longitude)?$request->longitude:0;
            if ($image)
            {
                $file = Salons::where('id', $id)->first()->image;
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

                 $resize=Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                $resize->save($t_desti);
                $saveImage=$image->move($url, $imageName);
                $new_salon=Salons::where("id",$id)->update(['name'=>$name,'description'=>$description,'latitude'=>$latitude,'longitude'=>$longitude,'location'=>$location,'city'=>$city,'country_id'=>$country,'currency_id'=>$currency_id, 'phone'=>$phone,'image'=>$imageName,"updated_at"=>$time]);
            }
            else
            {
                $new_salon=Salons::where("id",$id)->update(['name'=>$name,'description'=>$description,'latitude'=>$latitude,'longitude'=>$longitude,'location'=>$location,'city'=>$city,'country_id'=>$country,'currency_id'=>$currency_id, 'phone'=>$phone,"updated_at"=>$time]);
            }


            if($new_salon)
            {
                if($request->hasFile('sub_images'))
                {
                    foreach ($files as $file)
                    {
                        $fileName = md5(rand(1000,9078787878)).'.'.$file->getClientOriginalExtension();
                        $desti=$url.$fileName;
                        $t_desti=$turl.$fileName;

                         $resize=Image::make($file->getRealPath())->resize(300, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        $resize->save($t_desti);
                        $savefile=$file->move($url, $fileName);

                        $new_file=SalonImages::insertGetId(['salon_id'=>$id, 'approved'=>1,'image'=>$fileName, 'created_at'=> $time,"updated_at"=>$time]);
                    }
                }
                foreach (SalonFacilities::where('salon_id', '=',$id)->get() as $facility)
                {
                    $delete_facilities=SalonFacilities::where("id",$facility->id)->delete();
                }

                $start=$end=0;
                  if(isset($request->facilities))
                {
                     foreach($request->facilities as $facility)
                    {
                        $start=$start++;
                        $insert=SalonFacilities::insert([
                            "salon_id"=>$id,
                            "facility_id"=>$facility,
                            'created_at'=> $time,
                            "updated_at"=>$time
                            ]);
                        if($insert)
                        {
                            $end=$end++;
                        }
                    }
                }
                if($start==$end)
                {
                    return redirect()->back()->with("error", false)->with("msg", "Your profile updated successfully");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }

        }
    }

    public function reset_password(Request $request)
    {
        $token=$email='';
        $token=$request->token; $email=$request->email;
        return view("salon.reset_password",compact('email','token'));
    }

    public function reset_pwd(Request $request)
    {
     $rules=[
            // "email"=>"required|exists:salons,email,deleted_at,NULL",
            "token"=>"required",
            "password"=>"required|min:4",
            'password_confirmation'=>'required',
            ];
        $msg=[
            "password.required"=>"New password is required",
            "token.exists"=>"Invalid or expired token"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $time=Carbon::now();
            $user=Salons::where("email",$request->email)->first();
            if(!isset($user))
            {
                $suser=SalonUsers::where("email",$request->email)->first();
               if(!isset($suser))
               {
                return redirect()->back()->with("error", true)->with("msg", "Invalid emai.")->withInput();

               }
            }
            $token=$request->token;
            $email=$request->email;

             $check_token=DB::table("salon_password_resets")->whereNull('salon_password_resets.deleted_at')->where("token",$token)->where("email",$email)->first();
            if(empty($check_token))
            {
                return redirect()->back()->with("error", true)->with("msg", "This link has already used or expired.")->withInput();
            }
            else
            {

                if($request->password!=$request->password_confirmation)
                {
                    return redirect()->back()->with("error", true)->with("msg", "The password confirmation does not match")->withInput();
                }
                else
                {
                    if(isset($user))
                    {
                        $update=Salons::where("email",$request->email)->update(["password"=>bcrypt($request->password),"updated_at"=>$time]);
                    }
                    else if(isset($suser))
                    {
                        $update=SalonUsers::where("email",$request->email)->update(["password"=>bcrypt($request->password),"updated_at"=>$time]);
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Invalid emai.")->withInput();

                    }


                    if($update)
                    {
                        $del_token=DB::table("salon_password_resets")->where("token",$token)->where("email",$email)->delete();

                        return redirect(env("ADMIN_URL").'/salon/login')->with("error", false)->with("msg", "Password changed successfully");
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
                    }

                }
            }
        }
    }

    public function delete_img(Request $request)
    {
         $rules=
        [
            "id"=>"required|exists:salon_images,id,deleted_at,NULL",
        ];

        $msg=
        [
            "id.required"=>"Id field is empty",
        ];

        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
           $id=$request->id;

            $delete=SalonImages::where("id",$id)->delete();
            if($delete)
            {
              return redirect()->back()->with("error", false)->with("msg", "Image deleted successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process");

            }
        }
    }

}
