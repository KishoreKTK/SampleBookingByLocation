<?php

namespace App\Http\Controllers\web;
use DB;
use Mail;
use Validator;
use App\Partner;
use App\Content;
use App\ContactUs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home()
    {
    	$title="Mood";
       return view('web.home',compact('title'));
    }
     public function about()
    {
    	$title="About Us - Mood";
        $about=Content::where("id",1)->select("id","title","phone","email","address","website","description","created_at")->first();

       return view('web.about',compact('title','about'));
    }
     public function terms()
    {
    	$title="Terms & conditions - Mood";
        $terms=Content::where("id",3)->select("id","title","description","created_at")->first();
       return view('web.terms',compact('title','terms'));
    }
     public function privacy()
    {
        $privacy=Content::where("id",2)->select("id","title","description","created_at")->first();
    	$title="Privacy policy - Mood";
    	
       return view('web.privacy',compact('title','privacy'));
    }
    
 	public function partner()
    {
    	$title="Partner with us - Mood";
       return view('web.partner',compact('title'));
    } 
    public function contact()
    {
    	$title="Contact Us - Mood";
       return view('web.contact',compact('title'));
    }
     public function contact_us(Request $request)
    {
        $rules=[
            "name"=>"required|regex:/^[\pL\s\-]+$/u",
            "email"=>"required|email",
            "subject"=>"required",
            "description"=>"required",
            'phone' => "regex:/^([0-9\s\-\+\(\)]*)$/|min:10"
            ];
        $msg=[
            "name.required"=>"Name is required",
            "description.required"=>"Please type your message",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $name=$request->name;
            $subject=$request->subject;
            $email=$request->email;
            $phone=$request->phone;
            $description=$request->description;
            $time=Carbon::now();
            if(request()->header('User-Token'))
            {
                $api_token=request()->header('User-Token');
                // $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
                 $user=UserToken::where("api_token",$api_token)->first();
                if(isset($user)&& isset($user->user_id))
                {
                    $user_id=$user->user_id;             
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "API Token invalid")->withInput();
                }
            }
            else
            {
                $user_id=0;
            }

            // $to="sruthy@designfort.com";
            $to=env("MAIL_USERNAME");
            $new_contact=ContactUs::insertGetId(['name'=>$name,'subject'=>$subject,'phone'=>$phone,'user_id'=>$user_id,'email'=>$email,'description'=>$description,'read'=>0, 'created_at'=> $time,"updated_at"=>$time]);
            if($new_contact)
            {

                $data=["to"=>$to,
                "subject"=>"New Contact Us Submission",
                "name"=>$name,
                "email"=>$email,
                "phone"=>$phone,
                "subject"=>$subject,
                "description"=>$description,
                ];

                $mail=Mail::send('emails.contact_us', ["data"=>$data], function ($message) use ($data)
                {
                    $message->to($data['to'])->subject("New contact us submission");
                }); 

                    return redirect()->back()->with("error", false)->with("msg", "Thank you. We will contact you soon");

            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry. Something wrong")->withInput();

            }
      
        }
        return $return;
    }
    public function partner_add(Request $request)
    {
        $rules=[
             "name"=>"required|regex:/^[\pL\s\-]+$/u",
            "email"=>"required|email",
            'phone' => "regex:/^([0-9\s\-\+\(\)]*)$/|min:10",
            "company"=>"required",
            "message"=>"required",
            ];
        $msg=[
            "name.required"=>"Name is required",
            "message.required"=>"Please type your message",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            // return $request;
            $name=$request->name;
            $company=$request->company;
            $email=$request->email;
            $phone=$request->phone;
            $message=$request->message;
            $time=Carbon::now();
            
            $new_contact=Partner::insertGetId(['name'=>$name,'company'=>$company,'phone'=>$phone,'email'=>$email,'message'=>$message, 'created_at'=> $time,"updated_at"=>$time]);
            if($new_contact)
            {

                return redirect()->back()->with("error", false)->with("msg", "Thank you. We will contact you soon");

            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry. Something wrong")->withInput();

            }
      
        }
        return $return;
    }
}
