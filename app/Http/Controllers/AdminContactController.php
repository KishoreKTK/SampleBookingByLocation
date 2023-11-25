<?php

namespace App\Http\Controllers;
use DB;
use Mail;
use Session;
use Validator;
use App\Partner;
use App\ContactUs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    public function contact_us(Request $request)
    {
    	$activePage="Contact Us";
        $time=Carbon::now();
        
         foreach (ContactUs::where('read_a', '=',0)->get() as $data)
        {
            $read=ContactUs::where("id",$data->id)->update(["read_a"=>1,"updated_at"=>$time]);
        }
            Session::put('contact', 0);
    	if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
	    	$contact_us=ContactUs::where(function ($q) use ($keyword) {
            $q->where("name","like",'%'.$keyword.'%')
        	->orWhere("email","like",'%'.$keyword.'%')
        	->orWhere("subject","like",'%'.$keyword.'%')
        	->orWhere("description","like",'%'.$keyword.'%');})
        	->orderBy("created_at","desc")
    		->paginate(20);
        }
        else
        {
        	$keyword="";
	    	$contact_us=ContactUs::orderBy("created_at","desc")->paginate(20);

        }
		return view('admin.contact_us.list',compact('activePage','contact_us','keyword'));
    }
    public function penquiries(Request $request)
    {
        $activePage="Partner Enquiries";
        $time=Carbon::now();
        
        if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
            $enquiries=Partner::where(function ($q) use ($keyword) {
            $q->where("name","like",'%'.$keyword.'%')
            ->orWhere("email","like",'%'.$keyword.'%')
            ->orWhere("company","like",'%'.$keyword.'%')
            ->orWhere("message","like",'%'.$keyword.'%');})
            ->orderBy("created_at","desc")
            ->paginate(20);
        }
        else
        {
            $keyword="";
            $enquiries=Partner::orderBy("created_at","desc")->paginate(20);

        }
        return view('admin.partner_enquiries.list',compact('activePage','enquiries','keyword'));
    }

    public function details(Request $request)
    {
    	 $rules=
        [
            "id"=>"required|exists:contact_us,id,deleted_at,NULL",
        ];
        
        $msg=
        [
            "id.required"=>"Id field is empty",
        ];
    	$activePage="Contact Us";
        
        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
          $id=$request->id;
          $time=Carbon::now();
          $contact_us=ContactUs::where("id",$id)->first();
          $update_read=ContactUs::where("id",$id)->update(["read"=>1,"updated_at"=>$time]);
          return view('admin.contact_us.details',compact('contact_us','id','activePage'));
        }
    }
     public function reply(Request $request)
    {
    	 $rules=
        [
            "id"=>"required|exists:contact_us,id,deleted_at,NULL",
            "reply"=>"required",
        ];
        
        $msg=
        [
            "id.required"=>"Id field is empty",
        ];
    	$activePage="Contact Us";
        
        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $id=$request->id;
            $time=Carbon::now();
            $reply=$request->reply;

            $contact_us=ContactUs::where("id",$id)->update(["reply"=>$reply,"updated_at"=>$time]);
            if($contact_us)
            {
                $check=ContactUs::where("id",$id)->first();
               
                    $name=isset($check->name)?$check->name:'';
                    $email=isset($check->email)?$check->email:'';
                    if($email!='')
                    {
                        $data=[
                        "name"=>$name,
                        "email"=>$email,
                        "reply"=>$reply,
                    ];
                     $mail=Mail::send('emails.contact_reply', ["data"=>$data], function ($message) use ($data)
                    {
                        $message->to($data['email'])->subject("Your enquiry reply | Mood");
                    }); 
                    }
                return redirect()->back()->with("error", false)->with("msg", "You replied to this query successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    }
}
