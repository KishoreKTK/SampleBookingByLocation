<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Excel;
use Validator;
use App\Salons;
use App\Booking;
use Carbon\Carbon;
use App\Customers;
use App\SalonStaffs;
use App\BookingHold;
use App\WorkingHours;
use App\StaffServices;
use App\SalonServices;
use App\ServiceOffers;
use App\BookingAddress;
use App\BookingServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonBlockSlotController extends Controller
{

    public function list_block(Request $request)
    {
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
        $slots=DB::table("booking_services")
            ->join("booking", "booking.id","=","booking_services.booking_id")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
            ->join("salon_services", "salon_services.id","=","booking_services.service_id")
            ->whereNull('booking.deleted_at')
            ->where("booking.block",1)
            ->whereNull('booking_services.deleted_at')
            ->groupBy("booking_services.id")
            ->where('booking.salon_id',$salon_id)->orderBy("booking.id","desc")->select("booking_services.*","salon_staffs.staff","salon_services.service")->get();
        return view("salon.booking.slots",compact("slots","salon_id")); 

    }
     public function block(Request $request)
    {
        $salon_id=Auth::guard('salon-web')->user()->id;
        $staffs=[];
        $timeslot=[];
        $start_time=[];
        $end_time=[];
        $date=isset($request->date)?$request->date:'';
        $time=Carbon::now()->format("Y-m-d");

        if($request->date)
        {
            $r_date=new Carbon($request->date);
            $date=$r_date->format("d-m-Y");
            $n_date=$r_date->format("Y-m-d");
            if($n_date<=$time)
            {
              return redirect()->back()->with("error", true)->with("msg", "Choose future dates")->withInput();
            }

        }
        $service_id=$staff_id='';
       	$services=DB::table("salon_services")
            ->join("staff_services", "staff_services.service_id","=","salon_services.id")
            ->join("salon_staffs", "staff_services.staff_id","=","salon_staffs.id")
            ->join("categories", "categories.id","=","salon_services.category_id")
            ->where('salon_services.salon_id',$salon_id)
            ->where('salon_staffs.salon_id',$salon_id)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->pluck("salon_services.service","salon_services.id");
        return view("salon.booking.block_slot",compact("staffs","salon_id","timeslot","services","start_time","end_time","service_id","date","staff_id")); 
    }
      public function get_staff(Request $request)
    {
        $rules=[
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",

            ];
        $msg=[
            "service_id.required"=>"Service ID is required"
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

            $service_id=$request->service_id;
            $staffs=[];
            $staffs=DB::table("salon_staffs")
            ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
            ->join("salons", "salons.id","=","salon_staffs.salon_id")
            ->where("staff_services.service_id",$service_id)
            ->where("salons.id",$salon_id)
            ->whereNull("salon_staffs.deleted_at")
            ->whereNull("staff_services.deleted_at")
            ->whereNull("salons.deleted_at")
            ->groupBy("salon_staffs.id")
            ->pluck("salon_staffs.staff","salon_staffs.id");
            if(isset($staffs)&& count($staffs)>0)
            {
            return $staffs;

            }
            else
            {
            	return "no staffs found";
            }
            
        }
    }
    public function get_time(Request $request)
    {
        $rules=[
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "date"=>"required",

            ];
        $msg=[
            "id.required"=>"ID is required",
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else 
        {
            $salon_id=$request->salon_id;
           
            $service_id=$request->service_id;
            $staff_id=$request->staff_id;
            $timeframes=$timeslots=$books=$rtimeframes=[];
            $date=$request->date;
            $ndate=new Carbon($request->date);
            $time=Carbon::now();
            // if($ndate<=$time)
            // {
            //     $return['status']="failed";
            //     $return['msg']= "Please check the date.";
            //     return $return;                                                       
            // }
            $todate=Carbon::now()->format('d-m-Y');
            $f_date=$ndate->format('d-m-Y');
            $new_date=strtolower($ndate->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";
            $timeslot=WorkingHours::where("salon_id",$salon_id);
            $service=SalonServices::where("salon_id",$salon_id)->where("id",$service_id)->first();
            if($new_date=='monday')
            {
                $timeslot=$timeslot
                ->select('id','monday_start as start_time','monday_end as end_time')
                ->first();
            }
            elseif($new_date=='tuesday')
            {
                $timeslot=$timeslot
                ->select("id",'tuesday_start as start_time','tuesday_end as end_time')
                ->first();
            }
            elseif($new_date=='wednesday')
            {
                $timeslot=$timeslot
                ->select("id",'wednesday_start as start_time','wednesday_end as end_time')
                ->first();
            }
            elseif($new_date=='thursday')
            {
                $timeslot=$timeslot
                ->select("id",'thursday_start as start_time','thursday_end as end_time')
                ->first();
            }
            elseif($new_date=='friday')
            {
                $timeslot=$timeslot
                ->select("id",'friday_start as start_time','friday_end as end_time')
                ->first();
            }
            elseif($new_date=='saturday')
            {
                $timeslot=$timeslot
                ->select("id",'saturday_start as start_time','saturday_end as end_time')
                ->first();
            }
            else
            {
                $timeslot=$timeslot
                ->select("id",'sunday_start as start_time','sunday_end as end_time')
                ->first();
            }

            if(isset($timeslot)&& isset($service))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $duration=isset($service->time)?$service->time:30;
                for($i=$start_time; $i<$end_time;)
                {
                    $start=$i.":00";

                    $timestamp = strtotime($i) + $duration*60;
                    $end = date('H:i', $timestamp);
                    // return $end;
                    // $end=$end.":00";
                    if($end<=$end_time)
                    {
                        $booking=DB::table("booking")
                        ->join("booking_services", "booking_services.booking_id","=","booking.id")
                        ->where("booking_services.service_id",$service_id)
                        ->where("booking_services.staff_id",$staff_id)
                        ->where("start_time", $start)->where("booking_services.date",$f_date)
                        ->whereNull("booking.deleted_at")
                        ->whereNull("booking_services.deleted_at")
                        ->first(); 
                    
                        if(isset($booking))
                        {
                            $start_t=$booking->start_time;
                            $end_t=$booking->end_time;
                            for($j=$start_t; $j<$end_t;)
                            {
                                $j_start=$j;
                                $books[]=$j_start;
                                $j_timestamp = strtotime($j) + $duration*60;
                                $j = date('H:i', $j_timestamp);
                                $j=$j.":00";
                            }
                            
                        }

                        if(in_array($start,$books))
                        {
                            // $rtimeframes["start_time"]=$i;
                            // $rtimeframes["end_time"]=$end;
                            // $rtimeframes["duration"]=$duration;
                            // $rtimeframes["booking"]=true;
                            // $timeframes[]=$rtimeframes;

                            $timestamps = strtotime($i) + $duration*60;
                            $i = date('H:i', $timestamps);
                        }
                        else
                        {

                            if($f_date==$todate)
                            {
                                $now=Carbon::now()->format('H:i');
                                if($now>=$end || $now>=$i)
                                {
                                }
                                else
                                {
                                    $rtimeframes["start_time"]=$i;
                                    $rtimeframes["end_time"]=$end;
                                    $rtimeframes["duration"]=$duration;
                                    $timeframes[]=$rtimeframes;

                                }
                            }
                            else
                            {
                                $rtimeframes["start_time"]=$i;
                                $rtimeframes["end_time"]=$end;
                                $rtimeframes["duration"]=$duration;
                                $timeframes[]=$rtimeframes;

                                // $rtimeframes["booking"]=false;
                            }

                            $timestamps = strtotime($i) + $duration*60;
                            $i = date('H:i', $timestamps);
                        }
                    }
                    else
                    {
                        $timestamps = strtotime($i) + $duration*60;
                        $i = date('H:i', $timestamps);
                    }
                }
                  $services=DB::table("salon_services")
                    ->join("staff_services", "staff_services.service_id","=","salon_services.id")
                    ->join("salon_staffs", "staff_services.staff_id","=","salon_staffs.id")
                    ->join("categories", "categories.id","=","salon_services.category_id")
                    ->where('salon_services.salon_id',$salon_id)
                    ->where('salon_staffs.salon_id',$salon_id)
                    ->whereNull('salon_services.deleted_at')
                    ->groupBy("salon_services.id")
                    ->pluck("salon_services.service","salon_services.id");
                    $staffs=DB::table("salon_staffs")
                    ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                    ->join("salons", "salons.id","=","salon_staffs.salon_id")
                    ->where("staff_services.service_id",$service_id)
                    ->where("salons.id",$salon_id)
                    ->pluck('salon_staffs.staff', 'salon_staffs.id');
                // return view('salon.booking.add',compact('salon_id','service_id','services','staffs','activePage','date','staff_id','timeframes'));

            $return['status']='success';
            $return['timeframes']=$timeframes;
            $return['msg']="Timeslots listed successfully";
            }
            else
            {
            $return['status']='failed';
            $return['msg']="Error occured";
            }

        }
            return $return;
        
    }
    public function block_slot(Request $request)
    {
        $rules=[
            "timeslot"=>"required",
            "service_id"=>"required",
            "staff_id"=>"required",
            ];
        $msg=[
            "salon_id.required"=>"ID is required",
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['status']="failed";
            $return['msg']=  $validator->errors()->all();
            return $return;
        }
        else 
        {

            $time=Carbon::now();
            $id=Auth::guard('salon-web')->user()->id;
            $todate=Carbon::now()->format("Y-m-d");


            $service_id=$request->service_id;
            $date=new Carbon($request->date);
            $staff_id=$request->staff_id;
            $timeslot=$request->timeslot;
            $timeslot = explode("-", $timeslot);
            $request_start_time=$timeslot[0];
            $request_end_time=$timeslot[1];
            if(!isset($request_start_time) || !isset($request_end_time))
            {
              return redirect()->back()->with("error", true)->with("msg", "Choose timeslot")->withInput();
            }
			$user_id=0;
               
            $timeslot=WorkingHours::where("salon_id",$id);
            $time=Carbon::now();


            $f_date=$date->format('d-m-Y');
            $new_date=strtolower($date->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";

             if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $rstart_time=$request_start_time.":00";
                $rend_time=$request_end_time.":00";
                $from=new Carbon($f_date ." ".$rstart_time);
                $to=new Carbon($f_date ." ".$rend_time);
                // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
                // {
              		// return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();

                // }
                  $sstart_time=$start_time.":00";
                    $eend_time=$end_time.":00";

                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


                    if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();
                    }
                $check_staff=StaffServices::where("staff_id",$staff_id)->where("service_id",$service_id)->first();
                if(!isset($check_staff))
                {
              		return redirect()->back()->with("error", true)->with("msg", "Sorry staff is not available for this service.")->withInput();
                }

               
                //checking whether the given staff have the same appointment on the same date
                $check_booking_staffs=DB::table("booking")
                ->join("booking_services", "booking_services.booking_id","=","booking.id")
                ->where("booking_services.date",$f_date)
                ->whereNull("booking.deleted_at")
                ->whereNull("booking_services.deleted_at")
                 ->where("booking.active",1)
                ->where("booking_services.staff_id",$staff_id)
                ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                ->get();
                if(isset($check_booking_staffs)&&count($check_booking_staffs)>0)
                {
                    foreach($check_booking_staffs as $each)
                    {
                        $h_start=new Carbon($each->date ." ".$each->start_time);
                        $h_end=new Carbon($each->date ." ".$each->end_time);
                       if($h_start>=$to ||  $h_end <= $from)
                       {
                       }
                       else
                       {
              				return redirect()->back()->with("error", true)->with("msg", "Sorry the selected staff have already an appointment. Please try to change the staff or date.")->withInput();
                       }
                    }
                }
            }
             else
            {
				return redirect()->back()->with("error", true)->with("msg", "Sorry no timeslots found")->withInput();
            }
        
            $add_booking=Booking::insertGetId(["user_id"=>$user_id,"salon_id"=>$id,"active"=>1,"block"=>1,"status_code"=>0,"created_at"=>$time,"updated_at"=>$time]);
            if($add_booking)
            {
                   
                $date=new Carbon($request->date);
                $f_date=$date->format('d-m-Y');
                $rstart_time=$request_start_time.":00";
                $rend_time=$request_end_time.":00";
                $insert=BookingServices::insert([
                "booking_id"=>$add_booking,
                "date"=>$f_date,
                "service_id"=>$request->service_id,
                "staff_id"=>$request->staff_id,
                "start_time"=>$rstart_time,
                "end_time"=>$rend_time,
                'created_at'=> $time,
                "updated_at"=>$time
                ]);

				return redirect()->back()->with("error", false)->with("msg", "Slot blocked successfully");
                
            }
            else
            {
				return redirect()->back()->with("error", true)->with("msg", "Sorry Error occured during process.")->withInput();
            }
                
                // $return['timeslots']=$timeslots;
           
        }
    }
}
