<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Staffs;
use Carbon\Carbon;
use App\SalonStaffs;
use App\StaffHolidays;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonHolidaysController extends Controller
{
    public function index(Request $request)
    {
    	// $salon_id= Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }
        $activePage="Staffs";
        $rules=[
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            ];
        $msg=[
            "staff_id.required"=>"ID is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $staff_id=$request->staff_id;
            $staffs=SalonStaffs::where('salon_id',$salon_id)->where("id",$staff_id)->first();
            $staff=isset($staffs->staff)?$staffs->staff:'';

            $holidays=StaffHolidays::where("salon_id",$salon_id)->where("staff_id",$staff_id)->get();
           
            return view("salon.staffs.holidays", compact('staff_id','staff','activePage','holidays'));
        }
    }
      public function add(Request $request)
    {
        $activePage="Staffs";
        $rules=[
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "date"=>"required"
            ];
        $msg=[
            "staff_id.required"=>"ID is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
    		// $salon_id= Auth::guard('salon-web')->user()->id;
             $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }

            $staff_id=$request->staff_id;
            $dates =preg_split ("/\,/",$request->date); 
            $time=Carbon::now();
            if(isset($dates) && count($dates)>0)
            {
                foreach($dates as $date)
                {
                    for($i=0; $i<count($dates); $i=$i+1)
                    {
                        $check=StaffHolidays::where("staff_id",$staff_id)->where("salon_id",$salon_id)->where("date",$date)->first();
                        if(isset($check))
                        {

                        }
                        else
                        {
                            $add=StaffHolidays::insertGetId(["staff_id"=>$staff_id,"salon_id"=>$salon_id,"date"=>$date,"created_at"=>$time,"updated_at"=>$time]);
                          
                        }
                    }
                    // return $date;

                 }

                return redirect()->back()->with("error", false)->with("msg", "Staff holidays added successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    }
      public function edit(Request $request)
    {
        $activePage="Staffs";
        $rules=[
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "id"=>"required|exists:staff_holidays,id,deleted_at,NULL",
            ];
        $msg=[
            "staff_id.required"=>"ID is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $staff_id=$request->staff_id;
    		// $salon_id= Auth::guard('salon-web')->user()->id;
             $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $salon_id= Auth::guard('salon-web')->user()->id;
            }
            else
            {
                $salon_id= Auth::guard('salons-web')->user()->salon_id;
            }
            $staffs=SalonStaffs::where('salon_id',$salon_id)->where("id",$staff_id)->first();
            $staff=isset($staffs->staff)?$staffs->staff:'';
            $id=$request->id;
            $holiday=StaffHolidays::where("staff_id",$staff_id)->where("salon_id",$salon_id)->where("id",$id)->first();
            return view("salon.staffs.edit_holidays", compact('staff_id','staff','activePage','holiday','id'));
        }
    }
      public function update(Request $request)
    {
        $activePage="Staffs";
        $rules=[
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "id"=>"required|exists:staff_holidays,id,deleted_at,NULL",
            "date"=>"required"
            ];
        $msg=[
            "staff_id.required"=>"ID is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $staff_id=$request->staff_id;
            $id=$request->id;
            $date=$request->date;
    		// $salon_id= Auth::guard('salon-web')->user()->id;
             $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $salon_id= Auth::guard('salon-web')->user()->id;
            }
            else
            {
                $salon_id= Auth::guard('salons-web')->user()->salon_id;
            }

            $time=Carbon::now();
            $check=StaffHolidays::where("staff_id",$staff_id)->where("salon_id",$salon_id)->where("date",$date)->where("id", "!=", $id)->first();
            if(isset($check))
            {
                return redirect()->back()->with("error", true)->with("msg", "You already added this date as closed.");

            }
            else
            {
                 $update=StaffHolidays::where("staff_id",$staff_id)->where("salon_id",$salon_id)->where("id",$id)->update(["date"=>$date,"updated_at"=>$time]);
           
                if($update)
                {
                    return redirect()->back()->with("error", false)->with("msg", "Staff holidays added successfully");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }

            }
        }
           
    }
       public function delete(Request $request)
    {
        $activePage="Staffs";
        $rules=[
            "id"=>"required|exists:staff_holidays,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $id=$request->id;
            $delete=StaffHolidays::where("id",$id)->delete();
             if($delete)
                {
                    return redirect()->back()->with("error", false)->with("msg", "Staff closed date deleted successfully");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }
        }
    }
}
