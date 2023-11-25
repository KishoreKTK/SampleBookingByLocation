<?php

namespace App\Http\Controllers\app;
use DB;
use Mail;
use App\FAQ;
use Validator;
use App\Content;
use Carbon\Carbon;
use App\UserToken;
use App\Countries;
use App\ContactUs;
use App\WorkingHours;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppContentController extends Controller
{
    public function phpinfo()
    {
        echo phpinfo();
    }

    public function test(Request $request)
    {
        $id=$request->id;
        $working_hours=WorkingHours::where("salon_id",$id)->first();
        $working=[];
        if(isset($working_hours))
        {

            $sunday["day"]='sunday';
            $sunday["start_time"]=isset($working_hours->sunday_start)?$working_hours->sunday_start:'';
            $sunday["end_time"]=isset($working_hours->sunday_end)?$working_hours->sunday_end:'';
            $working[]=$sunday;

            $monday["day"]='monday';
            $monday["start_time"]=isset($working_hours->monday_start)?$working_hours->monday_start:'';
            $monday["end_time"]=isset($working_hours->monday_end)?$working_hours->monday_end:'';
            $working[]=$monday;

            $tuesday["day"]='tuesday';
            $tuesday["start_time"]=isset($working_hours->tuesday_start)?$working_hours->tuesday_start:'';
            $tuesday["end_time"]=isset($working_hours->tuesday_end)?$working_hours->tuesday_end:'';
            $working[]=$tuesday;

            $wednesday["day"]='wednesday';
            $wednesday["start_time"]=isset($working_hours->wednesday_start)?$working_hours->wednesday_start:'';
            $wednesday["end_time"]=isset($working_hours->wednesday_end)?$working_hours->wednesday_end:'';
            $working[]=$wednesday;

            $thursday["day"]='thursday';
            $thursday["start_time"]=isset($working_hours->thursday_start)?$working_hours->thursday_start:'';
            $thursday["end_time"]=isset($working_hours->thursday_end)?$working_hours->thursday_end:'';
            $working[]=$thursday;

            $friday["day"]='friday';
            $friday["start_time"]=isset($working_hours->friday_start)?$working_hours->friday_start:'';
            $friday["end_time"]=isset($working_hours->friday_end)?$working_hours->friday_end:'';
            $working[]=$friday;

            $saturday["day"]='saturday';
            $saturday["start_time"]=isset($working_hours->saturday_start)?$working_hours->saturday_start:'';
            $saturday["end_time"]=isset($working_hours->saturday_end)?$working_hours->saturday_end:'';
            $working[]=$saturday;

            return $working;
        }
    }

    public function privacy()
    {
        $privacy=Content::where("id",2)->select("id","title","description","created_at")->first();
        return view("app.privacy_policy",compact("privacy"));
    }

    public function about(Request $request)
    {
        $about=Content::where("id",1)->select("id","title","phone","email","address","website",
            "description","created_at")->first();
        return view("app.about_us",compact("about"));

        // $return['error']=false;
        // $return['msg']="About us listed successfully";
        // $return['about']=$about;
        // return $return;
    }

    public function privacy_policy(Request $request)
    {
        $privacy=Content::where("id",2)->select("id","title","description","created_at")->first();
        return view("app.privacy_policy",compact("privacy"));

        $return['error']=false;
        $return['msg']="Privacy policy listed successfully";
        $return['privacy']=$privacy;
        return $return;
    }

    public function terms_conditions(Request $request)
    {
        $terms=Content::where("id",5)->select("id","title","description","created_at")->first();
        return view("app.terms_conditions",compact("terms"));
        $return['error']=false;
        $return['msg']="Terms and conditions listed successfully";
        $return['terms']=$terms;
        return $return;
    }

    public function cancellation_policy(Request $request)
    {
        $cancel=Content::where("id",4)->select("id","title","description","created_at")->first();
        return view("app.cancellation",compact("cancel"));
        $return['error']=false;
        $return['msg']="Cancellation Policy listed successfully";
        $return['cancel']=$cancel;
        return $return;
    }

    public function faq(Request $request)
    {
        $faq=DB::table("faq")->join("faq_category","faq_category.id","=","faq.category_id")
            ->whereNull('faq.deleted_at')
            ->select("faq.id","faq.title","faq.description","faq_category.category as category")->get();
        $return['error']=false;
        $return['msg']="Faq listed successfully";
        $return['faq']=$faq;
        return $return;
    }

    public function countries(Request $request)
    {
        $countries=Countries::orderBy("name")->select("id","name")->get();
        $return['error']=false;
        $return['msg']="Countries listed successfully";
        $return['countries']=$countries;
        return $return;
    }

    public function contact_us(Request $request)
    {
        $rules=[
                "name"          =>  "required",
                "email"         =>  "required",
                // "subject"       =>  "required",
                "description"   =>  "required",
            ];

        $msg    =   [
                        "name.required"=>"Name is required",
                        "description.required"=>"Please type your enquiry",
                    ];

        $validator  =   Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']  = implode( ", ",$validator->errors()->all());
        }
        else
        {
            $name       =$request->name;
            $subject    = "Customer Enquiry";
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
                    $return['error']=true;
                    $return['msg']="API Token expired";
                    return $return;
                }
            }
            else
            {
                $user_id=0;
            }

            // $to="sinju@designfort.com";
            $to=env("MAIL_USERNAME");
            $new_contact=ContactUs::insertGetId(['name'=>$name,'subject'=>$subject,'phone'=>$phone,'user_id'=>$user_id,'email'=>$email,'description'=>$description,'read'=>0, 'created_at'=> $time,"updated_at"=>$time]);
            if($new_contact)
            {

                $data=["to"=>$to,
                "to"=>"kishore@designfort.in",
                "subject"=>"Customer Enquiry",
                "name"=>$name,
                "email"=>$email,
                "phone"=>$phone,
                "subject"=>$subject,
                "description"=>$description,
                ];

                $mail=Mail::send('emails.contact_us', ["data"=>$data], function ($message) use ($data)
                {
                    $message->to($data['to'])->subject("Customer Enquiry");
                });

                $return['error']=false;
                $return['msg']="Thank you. We will contact you soon";
            }
            else
            {
                $return['error']=true;
                $return['msg']="Error occured.";
            }

        }
        return $return;
    }
}
