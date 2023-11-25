<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Salons;
use Carbon\Carbon;
use App\SalonsToken;
use App\WorkingHours;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonTimeController extends Controller
{
    public function index(Request $request)
    {
        $activePage =   "Working Hours";
        // $salon_id=Auth::guard('salon-web')->user()->id;
        $who        =   Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id   =   Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id   =   Auth::guard('salons-web')->user()->salon_id;
        }
        $working_hours      =   WorkingHours::where("salon_id",$salon_id)->first();
        if(!isset($working_hours))
        {
            $working_hours=[];
            $working_hours['sunday_start']='';
            $working_hours['sunday_end']='';
            $working_hours['monday_start']='';
            $working_hours['monday_end']='';
            $working_hours['tuesday_start']='';
            $working_hours['tuesday_end']='';
            $working_hours['wednesday_start']='';
            $working_hours['wednesday_end']='';
            $working_hours['thursday_start']='';
            $working_hours['thursday_end']='';
            $working_hours['friday_start']='';
            $working_hours['friday_end']='';
            $working_hours['saturday_start']='';
            $working_hours['saturday_end']='';
        }
        return view("salon.working_hours.list", compact("salon_id",'working_hours','activePage'));

    }
    public function edit(Request $request)
    {
        $activePage="Working Hours";
        // $salon_id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $salon_id= Auth::guard('salon-web')->user()->id;
            }
            else
            {
                $salon_id= Auth::guard('salons-web')->user()->salon_id;
            }
        $time=[];
        $time=WorkingHours::where("salon_id",$salon_id)->first();
        if(!isset($time))
        {
        	$time['sunday_start']='09:00';
        	$time['sunday_end']='18:00';
        	$time['monday_start']='09:00';
        	$time['monday_end']='18:00';
        	$time['tuesday_start']='09:00';
        	$time['tuesday_end']='18:00';
        	$time['wednesday_start']='09:00';
        	$time['wednesday_end']='18:00';
        	$time['thursday_start']='09:00';
        	$time['thursday_end']='18:00';
        	$time['friday_start']='09:00';
        	$time['friday_end']='18:00';
        	$time['saturday_start']='09:00';
        	$time['saturday_end']='18:00';
        }
    	return view("salon.working_hours.edit", compact("salon_id",'time','activePage'));
    }
    public function edit_time(Request $request)
    {
         $rules=[
            "sunday_start"=>"required_with:sunday_end|nullable|date_format:H:i",
            "sunday_end"=>"required_with:sunday_start|nullable|date_format:H:i|after:sunday_start",
            "monday_start"=>"required_with:monday_end|nullable|date_format:H:i",
            "monday_end"=>"required_with:monday_start|nullable|date_format:H:i|after:monday_start",
            "tuesday_start"=>"required_with:tuesday_end|nullable|date_format:H:i",
            "tuesday_end"=>"required_with:tuesday_start|nullable|date_format:H:i|after:tuesday_start",
            "wednesday_start"=>"required_with:wednesday_end|nullable|date_format:H:i",
            "wednesday_end"=>"required_with:wednesday_start|nullable|date_format:H:i|after:wednesday_start",
            "thursday_start"=>"required_with:thursday_end|nullable|date_format:H:i",
            "thursday_end"=>"required_with:thursday_start|nullable|date_format:H:i|after:thursday_start",
            "friday_start"=>"required_with:friday_end|nullable|date_format:H:i",
            "friday_end"=>"required_with:friday_start|nullable|date_format:H:i|after:friday_start",
            "saturday_start"=>"required_with:saturday_end|nullable|date_format:H:i",
            "saturday_end"=>"required_with:saturday_start|nullable|date_format:H:i|after:saturday_start",
            ];
        $msg=[
            "sunday_start.required_with"=>"Sunday start time is required when sunday end time is present",
            "sunday_end.required_with"=>"Sunday end time is required when sunday start time is present",
            "monday_start.required_with"=>"Monday start time is required when monday end time is present",
            "monday_end.required_with"=>"Monday end time is required when monday start time is present",
            "tuesday_start.required_with"=>"Tuesday start time is required when tuesday end time is present",
            "tuesday_end.required_with"=>"Tuesday end time is required when tuesday start time is present",
            "wednesday_start.required_with"=>"Wednesday start time is required when wednesday end time is present",
            "wednesday_end.required_with"=>"Wednesday end time is required when wednesday start time is present",
            "thursday_start.required_with"=>"Thursday start time is required when thursday end time is present",
            "thursday_end.required_with"=>"Thursday end time is required when thursday start time is present",
             "friday_start.required_with"=>"Friday start time is required when friday end time is present",
            "friday_end.required_with"=>"Friday end time is required when friday start time is present",
             "saturday_start.required_with"=>"Saturday start time is required when saturday end time is present",
            "saturday_end.required_with"=>"Saturday end time is required when saturday start time is present",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            // $salon_id=Auth::guard('salon-web')->user()->id;
             // start time validation
            $sunday_start   =   $request->sunday_start;
            $monday_start   =   $request->monday_start;
            $tuesday_start  =   $request->tuesday_start;
            $wednesday_start=   $request->wednesday_start;
            $thursday_start =   $request->thursday_start;
            $friday_start   =   $request->friday_start;
            $saturday_start =   $request->saturday_start;

            if(isset($sunday_start)&& $sunday_start!='')
            {
                $sunday_start= Carbon::createFromFormat('H:i', $sunday_start)->format('i');
               if($sunday_start!=00 && $sunday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($monday_start)&& $monday_start!='')
            {
                $monday_start= Carbon::createFromFormat('H:i', $monday_start)->format('i');
               if($monday_start!=00 && $monday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($tuesday_start)&& $tuesday_start!='')
            {
                $tuesday_start= Carbon::createFromFormat('H:i', $tuesday_start)->format('i');
               if($tuesday_start!=00 && $tuesday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($wednesday_start)&& $wednesday_start!='')
            {
                $wednesday_start= Carbon::createFromFormat('H:i', $wednesday_start)->format('i');
               if($wednesday_start!=00 && $wednesday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($thursday_start)&& $thursday_start!='')
            {
                $thursday_start= Carbon::createFromFormat('H:i', $thursday_start)->format('i');
               if($thursday_start!=00 && $thursday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($friday_start)&& $friday_start!='')
            {
                $friday_start= Carbon::createFromFormat('H:i', $friday_start)->format('i');
               if($friday_start!=00 && $friday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($saturday_start)&& $saturday_start!='')
            {
                $saturday_start= Carbon::createFromFormat('H:i', $saturday_start)->format('i');
               if($saturday_start!=00 && $saturday_start !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }


            if(isset($sunday_end)&& $sunday_end!='')
            {
                $sunday_end= Carbon::createFromFormat('H:i', $sunday_end)->format('i');
               if($sunday_end!=00 && $sunday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }
            //end time validation

            if(isset($monday_end)&& $monday_end!='')
            {
                $monday_end= Carbon::createFromFormat('H:i', $monday_end)->format('i');
               if($monday_end!=00 && $monday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($tuesday_end)&& $tuesday_end!='')
            {
                $tuesday_end= Carbon::createFromFormat('H:i', $tuesday_end)->format('i');
               if($tuesday_end!=00 && $tuesday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($wednesday_end)&& $wednesday_end!='')
            {
                $wednesday_end= Carbon::createFromFormat('H:i', $wednesday_end)->format('i');
               if($wednesday_end!=00 && $wednesday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($thursday_end)&& $thursday_end!='')
            {
                $thursday_end= Carbon::createFromFormat('H:i', $thursday_end)->format('i');
               if($thursday_end!=00 && $thursday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($friday_end)&& $friday_end!='')
            {
                $friday_end= Carbon::createFromFormat('H:i', $friday_end)->format('i');
               if($friday_end!=00 && $friday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }

            if(isset($saturday_end)&& $saturday_end!='')
            {
                $saturday_end= Carbon::createFromFormat('H:i', $saturday_end)->format('i');
               if($saturday_end!=00 && $saturday_end !=30)
               {
                    return redirect()->back()->with("error", true)->with("msg", "Please enter a valid time with 30 minutes interval")->withInput();
               }
            }


            $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $salon_id= Auth::guard('salon-web')->user()->id;
            }
            else
            {
                $salon_id= Auth::guard('salons-web')->user()->salon_id;
            }
            $now=Carbon::now();
            $time=WorkingHours::where("salon_id",$salon_id)->first();
            if(isset($time))
            {
            	$new_time=WorkingHours::where("id",$time->id)->where("salon_id",$salon_id)->update(["sunday_start"=>$request->sunday_start,"sunday_end"=>$request->sunday_end,"monday_start"=>$request->monday_start,"monday_end"=>$request->monday_end,"tuesday_start"=>$request->tuesday_start,"tuesday_end"=>$request->tuesday_end,"wednesday_start"=>$request->wednesday_start,"wednesday_end"=>$request->wednesday_end,"thursday_start"=>$request->thursday_start,"thursday_end"=>$request->thursday_end,"friday_start"=>$request->friday_start,"friday_end"=>$request->friday_end,"saturday_start"=>$request->saturday_start,"saturday_end"=>$request->saturday_end,"updated_at"=>$now]);
            }
            else
            {
            	 $new_time=WorkingHours::where("salon_id",$salon_id)->insertGetId(["salon_id"=>$salon_id,"sunday_start"=>$request->sunday_start,"sunday_end"=>$request->sunday_end,"monday_start"=>$request->monday_start,"monday_end"=>$request->monday_end,"tuesday_start"=>$request->tuesday_start,"tuesday_end"=>$request->tuesday_end,"wednesday_start"=>$request->wednesday_start,"wednesday_end"=>$request->wednesday_end,"thursday_start"=>$request->thursday_start,"thursday_end"=>$request->thursday_end,"friday_start"=>$request->friday_start,"friday_end"=>$request->friday_end,"saturday_start"=>$request->saturday_start,"saturday_end"=>$request->saturday_end,"created_at"=>$now,"updated_at"=>$now]);
            }

            if($new_time)
            {
                return redirect(env("ADMIN_URL").'/salon/working_hours')->with("error", false)->with("msg", "Your working hours updated successfully")->withInput();
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    }
}
