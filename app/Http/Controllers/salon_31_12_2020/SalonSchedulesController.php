<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Booking;
use Carbon\Carbon;
use App\SalonStaffs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonSchedulesController extends Controller
{
    public function test() 
    {
        return view("salon.test");

    }
     public function index(Request $request)

    {
        $rules=[
            "staff_id"=>"nullable|exists:salon_staffs,id,deleted_at,NULL",
            ];
        $msg=[
            "salon_id.required"=>"Salon ID is required"
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['status']="failed";
            $return['msg']=implode( ", ",$validator->errors()->all());
            return $return;       
        }
        else 
        {   
            $time=Carbon::now();

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
            foreach (Booking::where('read_ss', '=',0)->where("salon_id",$salon_id)->get() as $data)
            {
                $read=Booking::where("id",$data->id)->update(["read_ss"=>1,"updated_at"=>$time]);
            }
            Session::put('schedules', 0);
            $staffs=[];$staff_id='';
            $staffs=SalonStaffs::where('salon_id',$salon_id)->pluck("staff","id");
        	$activePage="Schedules";
            $staff_id=$request->staff_id;

        }

    	return view("salon.schedules.list",compact('salon_id','staffs','staff_id'));
    }
        public function view(Request $request)
    {
         $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "staff_id"=>"nullable|exists:salon_staffs,id,deleted_at,NULL",
            ];
        $msg=[
            "salon_id.required"=>"Salon ID is required"
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['status']="failed";
            $return['msg']=implode( ", ",$validator->errors()->all());
            return $return;     
        }
        else 
        {   
            $salon_id=$request->salon_id;
    	    $day=Carbon::now();
            $dates=[];
            $bl_dates=$b_booking=[];

        	$dates=DB::table("booking_services")
                ->join("booking", "booking.id","=","booking_services.booking_id")
                ->join("salons", "booking.salon_id","=","salons.id")
                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                ->where('booking.active', 1)
                ->where("booking.salon_id",$salon_id)
                ->whereNull('booking_address.deleted_at')
                ->whereNull('booking_services.deleted_at')
                ->whereNull('booking.deleted_at')
                ->whereNull('salon_services.deleted_at')
                ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","salons.name as salon","booking_services.end_time","booking_address.first_name as user","booking.user_id as user_id","booking.id as booking_id");
                //block slots
            $slots=DB::table("booking_services")
            ->join("booking", "booking.id","=","booking_services.booking_id")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
            ->whereNull('booking.deleted_at')
            ->where("booking.block",1)
            ->whereNull('booking_services.deleted_at')
            ->groupBy("booking_services.id")
            ->where('booking.salon_id',$salon_id)->orderBy("booking_services.id","desc")->select("booking_services.*","salon_staffs.staff")->get();
          
                if($request->staff_id)
                {
                    $staff_id=$request->staff_id;
                    $dates=$dates->where("salon_staffs.id",$request->staff_id)->get();
                }
                else
                {
                    $staff_id='';
                    $dates=$dates->get();
                }
             if(isset($dates) && count($dates)>0)
            {
                foreach($dates as $date)
                {
                    $start_time=isset($date->start_time)?$date->start_time:'';
                    $end_time=isset($date->end_time)?$date->end_time:'';
                    $date->title=$date->user;
                    $new_date=$date->date;
                    
                    // $date->title=$date->first_name."(".$date->start_time."-".$date->end_time.")";
                    $new=new Carbon($new_date ." ".$start_time);
                    $date->start_date=$new->format('Y-m-d H:i:s');
                    $enew=new Carbon($new_date ." ".$end_time);
                    $date->end_date=$enew->format('Y-m-d H:i:s');
                    $booking[]=$date;
                }
            }
             if(isset($slots) && count($slots)>0)
            {
                foreach($slots as $date)
                {
                    $start_time=isset($date->start_time)?$date->start_time:'';
                    $end_time=isset($date->end_time)?$date->end_time:'';
                    $date->title=$date->staff;
                    $new_date=$date->date;
                    
                    // $date->title=$date->first_name."(".$date->start_time."-".$date->end_time.")";
                    $new=new Carbon($new_date ." ".$start_time);
                    $date->start_date=$new->format('Y-m-d H:i:s');
                    $enew=new Carbon($new_date ." ".$end_time);
                    $date->end_date=$enew->format('Y-m-d H:i:s');
                    $b_booking[]=$date;
                }
            }
            if(isset($b_booking))
            {
                $return["slots"]=$b_booking;

            }
            if(isset($booking))
            {
            	$return["error"]=false;
            	$return["msg"]="Booking listed successfully";
                $return["booking"]=$booking;
            	$return["staff_id"]=$staff_id;
            }
            else
            {
            	$return["error"]=true;
            	$return["msg"]="No bookings yet";
            }
        }
        return $return;
    }
     public function details(Request $request)
    {
          $rules=[
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
            ];
        $msg=[
            "email.required"=>"Email is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $activePage="Schedules";

            $booking_id=$request->booking_id;
            $booking=DB::table("booking")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
            ->whereNull('booking.deleted_at')
            // ->where('booking.active',1)
            ->where('booking.id',$booking_id)
            ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.pricing","booking_address.address")->first();
            if(isset($booking))
            {
                 $services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$booking_id)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                $booking->amount=$booking->amount+$booking->balance_amount;
                 if(isset($booking->pricing)&& $booking->pricing!=null)
                {
                    $booking->mood_commission=$booking->amount * ($booking->pricing/100);
                }
                else
                {
                    $booking->mood_commission="0.00";
                }
            }
            
            return view('salon.schedules.details',compact('booking','activePage','services'));

        }
    }
}
