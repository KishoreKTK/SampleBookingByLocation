<?php

namespace App\Http\Controllers\salon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
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
use App\Http\Traits\BookingCheckTrait;
class SalonBlockSlotController extends Controller
{
    use BookingCheckTrait;

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
        $activePage="Block Slot";
        $todate=Carbon::now()->format('Y-m-d');
        $slots=DB::table("booking_services")
            ->join("booking", "booking.id","=","booking_services.booking_id")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
            ->whereNull('booking.deleted_at')
            ->where("booking.block",1)
            ->whereNull('booking_services.deleted_at')
            ->groupBy("booking_services.id")
            ->where('booking.salon_id',$salon_id)->orderBy("booking_services.id","desc")->select("booking_services.*","salon_staffs.staff")->get();
            if(isset($slots) && count($slots)>0)
            {
                foreach($slots as $slot)
                {
                    $date=new Carbon($slot->date);
                    $date=$date->format('Y-m-d');
                    if($date<=$todate)
                    {
                        $slot->delete=0;
                    }
                    else
                    {
                        $slot->delete=1;
                    }

                }
            }
            // return $slots;
        return view("salon.booking.slots",compact("slots","salon_id","activePage"));

    }

    public function block(Request $request)
    {
        $timeframes=$timeslots=$books=$rtimeframes=[];
        $activePage="Block Slot";

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
        $staffs=[];
        $timeslot=[];
        $date=isset($request->date)?$request->date:'';
        $time=Carbon::now()->format("Y-m-d");

        if($request->date)
        {
            $r_date         =   new Carbon($request->date);
            $date           =   $r_date->format("d-m-Y");
            $n_date         =   $r_date->format("Y-m-d");
            if($n_date<$time)
            {
              return redirect()->back()->with("error", true)->with("msg", "Choose future dates")->withInput();
            }
        }
        else
        {
            $r_date         =   Carbon::now();
            $date           =   $r_date->format("d-m-Y");
        }
        $starting           =   "08:00";
        $closing            =   "22:00";
        $staff_id           =   '';
        $staffs_all         =   DB::table("salon_staffs")
                                ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                                ->join("salons", "salons.id","=","salon_staffs.salon_id")
                                ->where("salons.id",$salon_id)
                                ->whereNull("salon_staffs.deleted_at")
                                ->whereNull("staff_services.deleted_at")
                                ->whereNull("salons.deleted_at")
                                ->groupBy("salon_staffs.id");
        $staffs             =   $staffs_all->pluck("salon_staffs.staff","salon_staffs.id");
        $staff              =   $staffs_all->select("salon_staffs.staff","salon_staffs.id")->first();
        if(isset($staff) && isset($staff->id))
        {
            $staff_id=$staff->id;
        }
        else
        {
            return redirect()->back()->with("error", true)->with("msg", "Sorry no staffs")->withInput();
        }


        $times      =   [];
        $ndate      =   new Carbon($date);
        $todate     =   Carbon::now()->format('d-m-Y');
        $f_date     =   $ndate->format('d-m-Y');
        $new_date   =   strtolower($ndate->format('l'));
        $start_date=$new_date."_start";
        $end_date=$new_date."_end";
            $timeslot=WorkingHours::where("salon_id",$salon_id);

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

            if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $duration=30;
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
                                    if($starting<=$i && $i<$closing)
                                    {
                                        $rtimeframes["start_time"]=$i;
                                        $times[]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                    }
                                }
                            }
                            else
                            {
                                if($starting<=$i && $i<$closing)
                                {
                                    $times[]=$i;
                                    $rtimeframes["start_time"]=$i;
                                    $rtimeframes["end_time"]=$end;
                                    $rtimeframes["duration"]=$duration;
                                    $timeframes[]=$rtimeframes;
                                }


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
            }
            //get todays timeslot

        return view("salon.booking.block_slot",compact("activePage","staffs","salon_id","timeslot","date","staff_id",'times'));
    }

    public function default_time(Request $request)
    {
        $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",

            ];
        $msg=[
            "salon_id.required"=>"ID is required",
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
            $activePage="Block Slot";

            $salon_id=$request->salon_id;
            $timeframes=$timeslots=$books=$rtimeframes=$times=[];
            $time=Carbon::now()->format("Y-m-d");
            $starting="08:00";
            $closing="22:00";
            if($request->date)
            {
                $r_date=new Carbon($request->date);
                $date=$r_date->format("d-m-Y");
                $n_date=$r_date->format("Y-m-d");
                if($n_date<$time)
                {
                    $return['error']=true;
                    $return['msg']="Choose future dates";
                    return $return;
                }

            }
            else
            {
                $r_date=Carbon::now();
                $date=$r_date->format("d-m-Y");
            }
            $staffs_all=DB::table("salon_staffs")
            ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
            ->join("salons", "salons.id","=","salon_staffs.salon_id")
            ->where("salons.id",$salon_id)
            ->whereNull("salon_staffs.deleted_at")
            ->whereNull("staff_services.deleted_at")
            ->whereNull("salons.deleted_at")
            ->groupBy("salon_staffs.id");
                $staffs=$staffs_all->pluck("salon_staffs.staff","salon_staffs.id");
                $staff=$staffs_all->select("salon_staffs.staff","salon_staffs.id")->first();
                if(isset($staff) && isset($staff->id))
                {
                    $staff_id=$staff->id;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Sorry not staffs";
                    return $return;
                }
             //get default todays timeslot;

            $ndate=new Carbon($date);

            $todate=Carbon::now()->format('d-m-Y');
            $f_date=$ndate->format('d-m-Y');
            $new_date=strtolower($ndate->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";
            $timeslot=WorkingHours::where("salon_id",$salon_id);
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
            if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $duration=30;
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

                                    if($starting<=$i && $i<$closing)
                                    {
                                         $rtimeframes["start_time"]=$i;
                                        $times[]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                    }

                                }
                            }
                            else
                            {
                                    if($starting<=$i && $i<$closing)
                                    {

                                        $times[]=$i;
                                        $rtimeframes["start_time"]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                    }


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
                }

            $return['status']='success';
            $return['timeframes']=$timeframes;
            $return['times']=$times;
            $return['msg']="Timeslots listed successfully";

            return $return;
        }

    }

    public function get_time(Request $request)
    {
        $rules=[
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            // "date"=>"required",

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
            $activePage="Block Slot";

            $staff_id=$request->staff_id;
            $timeframes=$timeslots=$books=$rtimeframes=$times=[];
            $time=Carbon::now()->format("Y-m-d");
            $starting="08:00";
            $closing="22:00";
            $date=$request->date;
             if($request->date)
            {
                $r_date=new Carbon($request->date);
                $date=$r_date->format("d-m-Y");
                $n_date=$r_date->format("Y-m-d");

                if($n_date<$time)
                {
                    $return['error']=true;
                    $return['msg']="Choose future datess";
                    return $return;
                }

            }
            else
            {
                $r_date=Carbon::now();
                $date=$r_date->format("d-m-Y");
            }
            $ndate=new Carbon($date);

            $todate=Carbon::now()->format('d-m-Y');
            $f_date=$ndate->format('d-m-Y');
            $new_date=strtolower($ndate->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";
            $timeslot=WorkingHours::where("salon_id",$salon_id);
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

            if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $duration=30;
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
                                    if($starting<=$i && $i<$closing)
                                    {
                                          $rtimeframes["start_time"]=$i;
                                        $times[]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;

                                    }
                                }
                            }
                            else
                            {
                                    if($starting<=$i && $i<$closing)
                                    {
                                        $rtimeframes["start_time"]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                        $times[]=$i;
                                    }



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

                    $staffs=DB::table("salon_staffs")
                    ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                    ->join("salons", "salons.id","=","salon_staffs.salon_id")
                    ->where("salons.id",$salon_id)
                    ->pluck('salon_staffs.staff', 'salon_staffs.id');
                // return view('salon.booking.add',compact('salon_id','staffs','activePage','date','staff_id','timeframes'));

            $return['status']='success';
            $return['timeframes']=$timeframes;
            $return['times']=$times;
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
            "start_time"=>"required",
            "staff_id"=>"required",
            ];
        $msg=[
            "staff_id.required"=>"Staff is required",
            "start_time.required"=>"Time is required",
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            if(request()->has('date') && request()->date != ''){
                $check_date = $this-> GreaterThanToday(request()->date);
                if($check_date['status'] != true){
                    return redirect()->back()->with("error", true)->with("msg", "Please Check Booking Date")->withInput();
                }

                $staffs_on_leave =  DB::table('staff_holidays')
                                    ->join('salon_staffs','staff_holidays.staff_id','salon_staffs.id')
                                    ->where('staff_holidays.staff_id',request()->staff_id)
                                    ->where('staff_holidays.date',request()->date)
                                    ->whereNull('staff_holidays.deleted_at')
                                    ->whereNull('salon_staffs.deleted_at')
                                    ->first();
                if($staffs_on_leave){
                    return redirect()->back()->with("error", true)->with("msg", "Selected Staff is Leave on that day")->withInput();
                }
            }
            $time=Carbon::now();
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
            $todate=Carbon::now()->format("Y-m-d");


            if($request->date==null|| $request->date=='')
            {
                $date=Carbon::now();
            }
            else
            {
                $date=new Carbon($request->date);
            }
            $staff_id=$request->staff_id;

			$user_id=0;

            $timeslot=WorkingHours::where("salon_id",$id);

            $time=Carbon::now();


            $f_date=$date->format('d-m-Y');
            $n_date=$date->format('Y-m-d');
            $new_date=strtolower($date->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";

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

            if(isset($timeslot))
            {
                if($request->start_time&& count($request->start_time)>0)
                {
                    foreach($request->start_time as $val)
                    {
                        $request_start_time=$val;
                        $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                        $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                        $rstart_time=$request_start_time.":00";
                        $from=new Carbon($n_date ." ".$rstart_time);
                        $to=$from->addMinutes(30);

                        $request_end_time=$to->format('H:i');

                        // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
                        // {
                        //     return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();

                        // }
                        $sstart_time=$start_time.":00";
                        $eend_time=$end_time.":00";
                        $rend_time=$request_end_time.":00";
                        // return $rstart_time;
                        // return $rend_time;

                        // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


                        if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
                        {
                            return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();
                        }

                        $check_booking_staffs=[];

                        //checking whether the given staff have the same appointment on the same date
                        $check_booking_staffs=  DB::table("booking")
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
                }

            }
            else
            {
				return redirect()->back()->with("error", true)->with("msg", "Sorry no timeslots found")->withInput();
            }

            $add_booking=Booking::insertGetId(["user_id"=>$user_id, "bookdate"=>request()->date,
                        "salon_id"=>$id,"active"=>1,"read_s"=>1,"read_ts"=>1,"block"=>1,
                        "status_code"=>0,"created_at"=>$time,"updated_at"=>$time
            ]);

            if($add_booking)
            {
                if($request->start_time&& count($request->start_time)>0)
                {
                    foreach($request->start_time as $val)
                    {
                        $request_start_time=$val;
                        $rstart_time=$request_start_time.":00";
                        $from=new Carbon($n_date ." ".$rstart_time);
                        $to=$from->addMinutes(30);
                        $request_end_time=$to->format('H:i');

                        $date=new Carbon($request->date);
                        $f_date=$date->format('d-m-Y');
                        $rstart_time=$request_start_time.":00";
                        $rend_time=$request_end_time.":00";
                        $insert=BookingServices::insert(["booking_id"=>$add_booking,"date"=>$f_date,"service_id"=>0,"staff_id"=>$request->staff_id,"start_time"=>$rstart_time,"end_time"=>$rend_time,'created_at'=> $time,"updated_at"=>$time
                        ]);
                    }

                }



				return redirect()->back()->with("error", false)->with("msg", "Slot blocked successfully");

            }
            else
            {
				return redirect()->back()->with("error", true)->with("msg", "Sorry Error occured during process.")->withInput();
            }

                // $return['timeslots']=$timeslots;

        }
    }

    public function edit(Request $request)
    {
         $rules=[
            "id"=>"required|exists:booking_services,id,deleted_at,NULL",
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
            $activePage="Block Slot";

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
            $timeframes=$timeslots=$books=$times=$rtimeframes=[];
            $staffs=[];
            $timeslot=[];
            $slot=DB::table("booking_services")
                ->join("booking", "booking.id","=","booking_services.booking_id")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                ->whereNull('booking.deleted_at')
                ->where("booking.block",1)
                ->where("booking_services.id",$id)
                ->whereNull('booking_services.deleted_at')
                ->groupBy("booking_services.id")
                ->where('booking.salon_id',$salon_id)->orderBy("booking_services.id","desc")->select("booking_services.*","salon_staffs.staff")->first();
                // return $slots;

                 $staff_id='';
            $staffs_all=DB::table("salon_staffs")
                ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                ->join("salons", "salons.id","=","salon_staffs.salon_id")
                ->where("salons.id",$salon_id)
                ->whereNull("salon_staffs.deleted_at")
                ->whereNull("staff_services.deleted_at")
                ->whereNull("salons.deleted_at")
                ->groupBy("salon_staffs.id");
            $staffs=$staffs_all->pluck("salon_staffs.staff","salon_staffs.id");


            if(isset($slot) && isset($slot->staff_id))
            {
                $staff_id=$slot->staff_id;
                $date=$slot->date;
                $start=$slot->start_time;
                $start_time=substr($start, 0, -3);
            }

            $ne_date=new Carbon($date);
            $today=Carbon::now();
            if($ne_date<=$today)
            {
             return redirect()->back()->with("error", true)->with("msg", "Please choose future dates to edit")->withInput();

            }
            return view("salon.booking.slot_edit",compact("staffs","staff_id","id","start_time","date","times","salon_id"));

        }
    }

    public function default_times(Request $request)
    {
        $rules=[
            "id"=>"required|exists:booking_services,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",

            ];
        $msg=[
            "salon_id.required"=>"ID is required",
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
            $id=$request->id;
            $timeframes=$timeslots=$books=$rtimeframes=$times=[];
            $time=Carbon::now()->format("Y-m-d");
            $starting="08:00";
            $closing="22:00";
            $activePage="Block Slot";

            if($request->date)
            {
                $r_date=new Carbon($request->date);
                $date=$r_date->format("d-m-Y");
                $n_date=$r_date->format("Y-m-d");
                if($n_date<$time)
                {
                    $return['error']=true;
                    $return['msg']="Choose future dates";
                    return $return;
                }

            }
            else
            {
                $r_date=Carbon::now();
                $date=$r_date->format("d-m-Y");
            }
            $staffs_all=DB::table("salon_staffs")
            ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
            ->join("salons", "salons.id","=","salon_staffs.salon_id")
            ->where("salons.id",$salon_id)
            ->whereNull("salon_staffs.deleted_at")
            ->whereNull("staff_services.deleted_at")
            ->whereNull("salons.deleted_at")
            ->groupBy("salon_staffs.id");
                $staffs=$staffs_all->pluck("salon_staffs.staff","salon_staffs.id");
                $staff=$staffs_all->select("salon_staffs.staff","salon_staffs.id")->first();
                if(isset($staff) && isset($staff->id))
                {
                    $staff_id=$staff->id;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Sorry not staffs";
                    return $return;
                }
             //get default todays timeslot;

            $ndate=new Carbon($date);

            $todate=Carbon::now()->format('d-m-Y');
            $f_date=$ndate->format('d-m-Y');
            $new_date=strtolower($ndate->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";
            $timeslot=WorkingHours::where("salon_id",$salon_id);
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
            if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $duration=30;
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
                        ->where("booking_services.id","!=", $id)
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

                                    if($starting<=$i && $i<$closing)
                                    {
                                         $rtimeframes["start_time"]=$i;
                                        $times[]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                    }

                                }
                            }
                            else
                            {
                                    if($starting<=$i && $i<$closing)
                                    {

                                        $times[]=$i;
                                        $rtimeframes["start_time"]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                    }


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
                }

            $return['status']='success';
            $return['timeframes']=$timeframes;
            $return['times']=$times;
            $return['msg']="Timeslots listed successfully";

            return $return;
        }

    }

    public function get_times(Request $request)
    {
        $rules=[
            "id"=>"required|exists:booking_services,id,deleted_at,NULL",
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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

            $staff_id=$request->staff_id;
            $id=$request->id;
            $timeframes=$timeslots=$books=$rtimeframes=$times=[];
            $time=Carbon::now()->format("Y-m-d");
            $starting="08:00";
            $closing="22:00";
            $date=$request->date;
             if($request->date)
            {
                $r_date=new Carbon($request->date);
                $date=$r_date->format("d-m-Y");
                $n_date=$r_date->format("Y-m-d");

                if($n_date<$time)
                {
                    $return['error']=true;
                    $return['msg']="Choose future datess";
                    return $return;
                }

            }
            else
            {
                $r_date=Carbon::now();
                $date=$r_date->format("d-m-Y");
            }
            $ndate=new Carbon($date);

            $todate=Carbon::now()->format('d-m-Y');
            $f_date=$ndate->format('d-m-Y');
            $new_date=strtolower($ndate->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";
            $timeslot=WorkingHours::where("salon_id",$salon_id);
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

            if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                $duration=30;
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
                        ->where("booking_services.staff_id",$staff_id)
                        ->where("booking_services.id","!=", $slot_id)
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
                                    if($starting<=$i && $i<$closing)
                                    {
                                          $rtimeframes["start_time"]=$i;
                                        $times[]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;

                                    }
                                }
                            }
                            else
                            {
                                    if($starting<=$i && $i<$closing)
                                    {
                                        $rtimeframes["start_time"]=$i;
                                        $rtimeframes["end_time"]=$end;
                                        $rtimeframes["duration"]=$duration;
                                        $timeframes[]=$rtimeframes;
                                        $times[]=$i;
                                    }



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

                    $staffs=DB::table("salon_staffs")
                    ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                    ->join("salons", "salons.id","=","salon_staffs.salon_id")
                    ->where("salons.id",$salon_id)
                    ->pluck('salon_staffs.staff', 'salon_staffs.id');
                // return view('salon.booking.add',compact('salon_id','staffs','activePage','date','staff_id','timeframes'));

            $return['status']='success';
            $return['timeframes']=$timeframes;
            $return['times']=$times;
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

    public function update(Request $request)
    {
        // return $request;
        $rules=[
            "slot_id"=>"required|exists:booking_services,id,deleted_at,NULL",
            "start_time"=>"required",
            "staff_id"=>"required",
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

            $time=Carbon::now();
            // $id=Auth::guard('salon-web')->user()->id;
             $who=Session::get('user');
            if(isset($who) && $who == 'salon')
            {
                $salon_id= Auth::guard('salon-web')->user()->id;
            }
            else
            {
                $salon_id= Auth::guard('salons-web')->user()->salon_id;
            }
            $todate=Carbon::now()->format("Y-m-d");


            if($request->date==null|| $request->date=='')
            {
                $date=Carbon::now();
            }
            else
            {
                $date=new Carbon($request->date);
            }
            $staff_id=$request->staff_id;

            $user_id=0;

            $timeslot=WorkingHours::where("salon_id",$salon_id);

            $time=Carbon::now();

            $slot_id=$request->slot_id;
            $f_date=$date->format('d-m-Y');
            $n_date=$date->format('Y-m-d');
            $new_date=strtolower($date->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";
            $slot=DB::table("booking_services")
                ->join("booking", "booking.id","=","booking_services.booking_id")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                ->whereNull('booking.deleted_at')
                ->where("booking.block",1)
                ->where("booking_services.id",$slot_id)
                ->whereNull('booking_services.deleted_at')
                ->groupBy("booking_services.id")
                ->where('booking.salon_id',$salon_id)->orderBy("booking_services.id","desc")->select("booking_services.*","salon_staffs.staff")->first();
                $booking_id=$slot->booking_id;
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

             if(isset($timeslot))
            {
                if($request->start_time)
                {

                    $request_start_time=$request->start_time;
                    $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                    $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';
                    $rstart_time=$request_start_time.":00";
                    $from=new Carbon($n_date ." ".$rstart_time);
                    $to=$from->addMinutes(30);
                    $request_end_time=$to->format('H:i');

                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
                    // {
                    //     return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();

                    // }
                      $sstart_time=$start_time.":00";
                    $eend_time=$end_time.":00";
                    $rend_time=$request_end_time.":00";

                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


                    if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();
                    }

                   $check_booking_staffs=[];
                    //checking whether the given staff have the same appointment on the same date
                    $check_booking_staffs=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.date",$f_date)
                    ->whereNull("booking.deleted_at")
                    ->where("booking_services.id","!=", $slot_id)
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

            }
             else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry no timeslots found")->withInput();
            }

            $add_booking=Booking::where("id",$booking_id)->update(["updated_at"=>$time]);
            if($add_booking)
            {
                if($request->start_time)
                {
                     $request_start_time=$request->start_time;
                    $rstart_time=$request_start_time.":00";
                    $from=new Carbon($n_date ." ".$rstart_time);
                    $to=$from->addMinutes(30);
                    $request_end_time=$to->format('H:i');


                     $date=new Carbon($request->date);
                    $f_date=$date->format('d-m-Y');
                    $rstart_time=$request_start_time.":00";
                    $rend_time=$request_end_time.":00";
                    $insert=BookingServices::where("id",$slot_id)->update(["date"=>$f_date,"staff_id"=>$request->staff_id,"start_time"=>$rstart_time,"end_time"=>$rend_time,'created_at'=> $time,"updated_at"=>$time
                    ]);

                }

                return redirect(env("ADMIN_URL").'/salon/list_block')->with("error", false)->with("msg", "Slot edited successfully");

            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry Error occured during process.")->withInput();
            }

                // $return['timeslots']=$timeslots;

        }
    }

    public function delete(Request $request)
    {
        $rules=[
            "id"=>"required|exists:booking_services,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required",
             ];
              $validator    =   Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $id=$request->id;
            $service=BookingServices::where("id",$id)->first();
            $booking_id=isset($service->booking_id)?$service->booking_id:'';

            $count=BookingServices::where("booking_id",$booking_id)->count();

            $delete=BookingServices::where("id",$id)->delete();
             if($delete)
                {
                    if($count==1)
                    {
                        $del_book=Booking::where("id",$booking_id)->delete();
                    }
                    return redirect()->back()->with("error", false)->with("msg", "Slot deleted successfully");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }
        }
    }
}
