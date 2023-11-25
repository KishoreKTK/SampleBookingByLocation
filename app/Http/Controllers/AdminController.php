<?php

namespace App\Http\Controllers;
use DB;
use Auth;
use Hash;
use Mail;
use Session;
use App\Roles;
use App\Admin;
use Validator;
use App\Booking;
use Carbon\Carbon;
use App\AdminRoles;
use App\SalonReviews;
use Illuminate\Http\Request;

class AdminController extends Controller
{
     
    public function get_login()
    {
        if(Auth::check())
        {
            return redirect(env("ADMIN_URL").'/dashboard');
        }
        else
        {   
            return view('admin.login');
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::forget('roles');
        Session::forget('booking');
        Session::forget('transactions');
        return redirect(env("ADMIN_URL").'/login');
    }

    public function test()
    {  
        // dd("am i here?");
         if(Auth::check())
        {
            return redirect(env("ADMIN_URL").'/dashboard');
        }
        else
        {
            return redirect(env("ADMIN_URL").'/login');
        }
    }

    public function test_email(Request $request)
    {
        $email="sinju@designfort.com";
          $data=["to"=>$email,
                "email"=>$email,
                ];
        if($request->type="cancel")
        {
             $mail=Mail::send('emails.booking_cancel', ["data"=>$data], function ($message) use ($data)
                {
                    $message->to($data['to'])->subject("Booking cancelled");
                });
        }
        elseif($request->type="reschedule")
        {
            $mail=Mail::send('emails.booking_reschedule', ["data"=>$data], function ($message) use ($data)
            {
                $message->to($data['to'])->subject("Booking rescheduled");
            }); 
        }
        else
        {
             $mail=Mail::send('emails.booking_success', ["data"=>$data], function ($message) use ($data)
            {
                $message->to($data['to'])->subject("Booking success");
            }); 
        }
        return "success";
    }

	public function login(Request $request,Admin $admin, Roles $role)
	{
        $rules=[
            "email"=>"required|exists:admin,email,deleted_at,NULL",
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
            if($remember==1)
            {
                if (Auth::attempt([ 'email' => $request->email, 
                                    'password' => $request->password,'suspend'=> '0'],$remember)) 
                {
                    $admin=Admin::where("email",$request->email)->first();
                }
                else
                {
                    return redirect()->back()->with("error", true)
                                    ->with("msg", "Invalid login credentials");
                }
            }
            else
            {
                if (Auth::attempt(['email' => $request->email,'password' => $request->password,'suspend'=> '0'])) 
                {
                    $admin=Admin::where("email",$request->email)->first();
                }
                else
                {
                    return redirect()->back()->with("error", true)
                                    ->with("msg", "Invalid login credentials");
                }
            }

            if (isset($admin))
            {
                // print("<pre>");
                // print($admin->suspend);
                // print("<br>");
                // print_r($admin);
                // die;
                // if($admin->suspend=="1")
                // {
                //     return redirect()->back()->with("error", true)->with("msg", "Your account is temporarily suspended.");
                // }
                // else
                // {
                //     return " I have permission";

                    // Authentication ...
                    $mail       =   $admin->email;
                    $token      =   md5(rand(10000,1000000).$mail);
                    $admin_id   =   $admin->id;
                    $time       =   Carbon::now();
                    $roles      =   AdminRoles::where("admin_id",$admin_id)->get();
                   
                    if(count($roles)==0)
                    {
                        return redirect()->back()->with("error", true)->with("msg", "You don't have any permission to view this page");
                    }
                    foreach($roles as $index=>$value)
                    {
                        $role_id[]=$value->role_id;
                    }

                    foreach ($role->whereIn("id",$role_id)->select('role')->get() as $value)
                    {
                        $arole[]=$value->role;
                    }

                    //booking
                    $booking        =   Booking::where("read_a",0)->count();
                    $transactions   =   Booking::where("read_ta",0)->count();
                    
                    Session::put('booking', $booking);
                    Session::put('transactions', $transactions);
                    Session::put('roles', $arole);

                    $api_token      =   $admin  ->where('id',$admin_id)
                                                ->where("email",$request->email)
                                                ->update(['api_token'=>$token,'updated_at'=>$time]);
                    
                    // print("<pre>");
                    // print_r(Session()->all());die;
                    return redirect(env("ADMIN_URL").'/dashboard')->with("error", false)
                    ->with("msg", "Successfully logged in");
                // }

            }
            else
            {
                return redirect()->back()->with("error", true)
                                        ->with("msg", "Invalid login credentials");
            }
        }
    }

    public function change_password(Request $Request)
    {
        return view("admin.change_password");
    }
    
    public function change_old_password(Request $request, Admin $admin)
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
            $take=$admin->where("email",Auth::user()->email)->first();
            $time=Carbon::now();

           if (Hash::check($request->old_password, $take->password))
           {
                
                $update=$admin->where("email",Auth::user()->email)->update([
                "password"=>bcrypt($request->password),
                "updated_at"=>$time,
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

    public function forgot_password(Request $Request)
    {
        return view("admin.forgot_password");
    }

    public function forgot_pwd_mail(Request $request, Admin $admin)
    {
         $rules=[
            "email"=>"required|exists:admin,email,deleted_at,NULL",
            ];
        $msg=[
            "email.required"=>"Email is required",
            "email.exists"=>"Sorry we can't find a user with that email address."
             ];
             
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {   
            $email=$request->email;
            $details=$admin->where("email",$request->email)->first();
            $token=md5(rand(10000,1000000).$email);
            $time=Carbon::now();
            $insert_token=DB::table("password_resets")->insertGetId(["email"=>$email,"token"=>$token,"created_at"=>$time,'updated_at'=>$time]);
            $to=$request->email;
                $data=["to"=>$to,
                "subject"=>"Reset password",
                "name"=>$details->first_name,
                "email"=>$request->email,
                "token"=>$token,
                ];
            $mail=Mail::send('emails.forgot_password', ["data"=>$data], function ($message) use ($data)
                {
                    $message->to($data['to'])->subject("Reset your password");
                }); 
           
                return redirect()->back()->with("error", false)->with("msg", "Please check your mail to reset your password");

        }
    }
     
    public function reset_password(Request $request, Admin $admin)
    {
        $token=$email='';
        $token=$request->token; $email=$request->email;
        return view("admin.reset_password",compact('email','token'));
    }

    public function reset_pwd(Request $request, Admin $admin)
    {
        $rules=[
            "email"=>"required|exists:admin,email,deleted_at,NULL",
            "token"=>"required",
            "password"=>"required|min:4",
            'password_confirmation'=>'required|same:password',
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
            $token=$request->token;
            $email=$request->email;

            $user=$admin->where("email",$request->email)->first();
            $check_token=DB::table("password_resets")->where("token",$token)->where("email",$email)->first();
            if(!isset($check_token))
            {
                return redirect()->back()->with("error", true)->with("msg", "This link has already used or expired.")->withInput();
            }
            else
            {
                $update=$admin->where("email",$request->email)->update([
                "password"=>bcrypt($request->password),"updated_at"=>$time
                ]);
                if($update)
                {
                    $del_token=DB::table("password_resets")->where("token",$token)->where("email",$email)->delete();
                    return redirect(env("ADMIN_URL").'/login')->with("error", false)->with("msg", "Password changed successfully");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
                }
                
            }
        }
    }
    
}
