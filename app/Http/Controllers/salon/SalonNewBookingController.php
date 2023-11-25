<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Excel;
use Session;
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

class SalonNewBookingController extends Controller
{

    public function add(Request $request)
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
        $staffs=[];

        return view("salon.booking.add",compact("staffs","salon_id"));
    }

    public function new(Request $request)
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
        $staffs=[];
         $services=DB::table("salon_services")
            ->join("staff_services", "staff_services.service_id","=","salon_services.id")
            ->join("salon_staffs", "staff_services.staff_id","=","salon_staffs.id")
            ->join("categories", "categories.id","=","salon_services.category_id")
            ->where('salon_services.salon_id',$salon_id)
            ->where('salon_staffs.salon_id',$salon_id)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*")->get();
             $users=DB::table("user")
                ->leftJoin("countries", "countries.id","=","user.country_id")
                ->whereNull('user.deleted_at')->select("user.first_name","user.id")->get();
        return view("salon.booking.new",compact("staffs","salon_id","services","users"));
    }

    public function services(Request $request)
    {
        $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            ];
        $msg=[
            "salon_id.required"=>"Salon ID is required"
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
                $return['status']="failed";
                $return['msg']=implode( ", ",$validator->errors()->all());
        }
        else
        {
            $salon_id=$request->salon_id;
            $date=$request->date;
            $staffs=[];
            $services=DB::table("salon_services")
            ->join("staff_services", "staff_services.service_id","=","salon_services.id")
            ->join("salon_staffs", "staff_services.staff_id","=","salon_staffs.id")
            ->join("categories", "categories.id","=","salon_services.category_id")
            ->where('salon_services.salon_id',$salon_id)
            ->where('salon_staffs.salon_id',$salon_id)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*")
            ->get();
             $users=DB::table("user")
                ->leftJoin("countries", "countries.id","=","user.country_id")
                ->whereNull('user.deleted_at')->select("user.first_name","user.id")->get();
            if(count($services)>0 && count($users)>0)
            {
            	$return['status']="success";
                $return['services']=$services;
                $return['users']=$users;
            }
            else
            {
            	$return['status']="failed";
                $return['msg']="No services or users found";
                $return['services']=[];
            }
        }
            return $return;

    }

    public function search_staffs(Request $request)
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
                $return['msg']=implode( ", ",$validator->errors()->all());;
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
            ->select('salon_staffs.*','staff_services.service_id')
            ->get();
             if(count($staffs)>0)
            {
                $return['status']="success";
                $return['msg']="Staffs listed successfully";
                $return['staffs']=$staffs;
            }
            else
            {
                $return['status']="failed";
                $return['msg']="No staffs found";
                $return['staffs']=[];
            }
        }
            return $return;
        // return view('admin.products.add', compact('categories','subcategories'));
    }

    public function search_time(Request $request)
    {
        $rules=[
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
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

    public function test_booking(Request $request)
    {
        return $request;
         $rules=[
            "date"=>"required",
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "start_time"=>"required",
            "end_time"=>"required",
            ];
        $msg=[
            "date.required"=>"Date is required",
             ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $date=$request->date;
            $salon_id=Auth::guard('salon-web')->user()->id;
            $staff_id=$request->staff_id;

            $service_id=$request->service_id;
            $start_time=$request->start_time;
            $end_time=$request->end_time;
            $users=DB::table("user")
            ->leftJoin("countries", "countries.id","=","user.country_id")
            ->whereNull('user.deleted_at')->pluck("user.first_name","user.id");
            return view("salon.booking.add_booking", compact("date","salon_id","service_id","start_time","staff_id","end_time","users"));

        }
    }

    public function complete_booking(Request $request)
    {
        $rules=[
            "date"=>"required",
            "amount"=>"required",
            "start_time"=>"required",
            "end_time"=>"required",
            "first_name"=>"required",
            "email"=>"required",
            "phone"=>"required",
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "staff_id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
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

            $offer_applied=isset($request->offer_applied)?$request->offer_applied:0;;
            $promocode=isset($request->promocode)?$request->promocode:'';
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
            $amount=$request->amount;
            if($request->user_id)
            {
                $user_id=$request->user_id;
                 $customer=Customers::where("id",$user_id)
                ->select("id","first_name","last_name","phone","address","email")
                ->first();
                $email=isset($customer->email)?$customer->email:'';
                $first_name=isset($customer->first_name)?$customer->first_name:'';
                $last_name=isset($customer->last_name)?$customer->last_name:'';
                $phone=isset($customer->phone)?$customer->phone:'';
                $address=isset($customer->address)?$customer->address:'';
            }
            else
            {
                $user_id=0;
                if($request->email=='' || $request->first_name=='' || $request->phone=='')
                {
                    $return['error']=true;
                    $return['msg']= "Please provide your details";
                    return $return;
                }
                $email=$request->email;
                $first_name=$request->first_name;
                $last_name=$request->last_name;
                $phone=$request->phone;
                $address=$request->address;

            }

             $fcm=$request->fcm;
            $device=$request->device;

            $date=new Carbon($request->date);
            $f_date=$date->format('d-m-Y');
            $new_date=strtolower($date->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";

            $timeslot=WorkingHours::where("salon_id",$id);
            $time=Carbon::now();
            if($date<=$time)
            {
            return redirect()->back()->with("error", true)->with("msg", "Please check the date.")->withInput();
            }

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
                    $service_id=$request->service_id;

                // $services=[1,2];
                $check_price=SalonServices::where('salon_id',$id)->where('id', $service_id)->sum("amount");
                $actual_amount=$check_price;
                if($amount>$actual_amount)
                {
                        return redirect()->back()->with("error", true)->with("msg", "Please check the price.")->withInput();
                }
                 $min_price=Salons::where("id",$id)->first()->min_price;
                if(isset($min_price)&& $min_price!=null)
                {
                    $min_amt=$actual_amount * ($min_price/100);
                    if(isset($min_amt) && $min_amt>0)
                    {
                        if($amount<$min_amt)
                        {
                        return redirect()->back()->with("error", true)->with("msg", "You have to pay a minimum amount of ".$min_amt." AED for this salon.")->withInput();

                        }
                    }
                }

                    $staff_id=$request->staff_id;
                    $start_time=$request->start_time;
                    $end_time=$request->end_time;

                    $rstart_time=$start_time.":00";
                    $rend_time=$end_time.":00";
                    $from=new Carbon($f_date ." ".$rstart_time);
                    $to=new Carbon($f_date ." ".$rend_time);
                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
                    // {
                    //     return redirect()->back()->with("error", true)->with("msg", "Please check the timeslot")->withInput();
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
                    $check_booking=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.service_id",$service_id)->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")->where("booking_services.date",$f_date)
                    ->where("booking_services.staff_id",$staff_id)
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();

                    if(isset($check_booking)&&count($check_booking)>0)
                    {
                        foreach($check_booking as $each)
                        {
                            $e_start=new Carbon($each->date ." ".$each->start_time);
                            $e_end=new Carbon($each->date ." ".$each->end_time);
                           if($e_start>=$to ||  $e_end <= $from)
                           {
                           }
                           else
                           {

                            return redirect()->back()->with("error", true)->with("msg", "Sorry this salon is already booked. Please try to change the time or date.")->withInput();
                           }
                        }
                    }
                    $check_booking_hold=DB::table("booking_hold")
                        ->join("booking_hold_services", "booking_hold_services.booking_hold_id","=","booking_hold.id")
                        ->where("booking_hold_services.service_id",$service_id)
                        ->where("booking_hold_services.staff_id",$staff_id)
                        ->where("booking_hold.user_id","!=",$user_id)->whereNull("booking_hold.deleted_at")
                        ->whereNull("booking_hold_services.deleted_at")
                        ->where("booking_hold_services.date",$f_date)
                        ->select("booking_hold.*","booking_hold_services.staff_id","booking_hold_services.service_id","booking_hold_services.start_time","booking_hold_services.end_time")->get();
                    // $check_booking_hold=BookingHold::where("salon_id",$id)->get();
                    if(isset($check_booking_hold)&&count($check_booking_hold)>0)
                    {
                        foreach($check_booking_hold as $each)
                        {
                            $h_start=new Carbon($each->date ." ".$each->start_time);
                            $h_end=new Carbon($each->date ." ".$each->end_time);
                           if($h_start>=$to ||  $h_end <= $from)
                           {
                           }
                           else
                           {
                            return redirect()->back()->with("error", true)->with("msg", "Sorry this salon is already booked. Please try to change the time or date.")->withInput();
                           }
                        }
                    }
                    $check_user_booking=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")->where("booking_services.date",$f_date)
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();
                    if(isset($check_user_booking)&&count($check_user_booking)>0)
                    {
                        foreach($check_user_booking as $each)
                        {
                            $h_start=new Carbon($each->date ." ".$each->start_time);
                            $h_end=new Carbon($each->date ." ".$each->end_time);
                           if($h_start>=$to ||  $h_end <= $from)
                           {
                           }
                           else
                           {
                            return redirect()->back()->with("error", true)->with("msg", "Sorry you already have an appointment. Please try to change the time or date.")->withInput();

                           }
                        }
                    }

                $add_booking=Booking::insertGetId(["user_id"=>$user_id,"salon_id"=>$id,"offer_applied"=>$offer_applied,"promocode"=>$promocode,"date"=>$f_date,"amount"=>$amount,"actual_amount"=>$actual_amount,"active"=>0,"status_code"=>0,"created_at"=>$time,"updated_at"=>$time]);
                if($add_booking)
                {
                    $add_booking_address=BookingAddress::insertGetId(["booking_id"=>$add_booking,"first_name"=>$first_name,"last_name"=>$last_name,"address"=>$address,"phone"=>$phone,"email"=>$email,"fcm"=>$fcm,"device"=>$device,"created_at"=>$time,"updated_at"=>$time]);


                    foreach (BookingHold::where("user_id", $user_id)->where("salon_id",$id)->get() as $expired)
                    {
                        $delete_record=BookingHold::where("id",$expired->id)->delete();
                    }

                         $rstart_time=$start_time.":00";
                        $rend_time=$end_time.":00";
                        $insert=BookingServices::insert([
                        "booking_id"=>$add_booking,
                        "service_id"=>$service_id,
                        "staff_id"=>$staff_id,
                        "start_time"=>$rstart_time,
                        "end_time"=>$rend_time,
                        'created_at'=> $time,
                        "updated_at"=>$time
                        ]);

                    $details=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->join("salons", "salons.id","=","booking.salon_id")
                    ->whereNull('booking.deleted_at')
                    ->whereNull("booking_services.deleted_at")
                    ->whereNull('salons.deleted_at')
                    ->where('booking.user_id',$user_id)
                    ->where('booking.id',$add_booking)
                    ->select("booking.id","salons.name as salon_name","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid")
                    ->first();

                     $details->services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$add_booking)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","booking_services.date","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                  return redirect()->back()->with("error", false)->with("msg", "New booking added successfully");
                }
                else
                {
                  return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
                }

                // $return['timeslots']=$timeslots;
            }
            else
            {
                  return redirect()->back()->with("error", true)->with("msg", "Sorry no timeslots found")->withInput();
            }

        }
    }


    public function add_booking(Request $request)
    {
        $rules=[
            "first_name"=>"required",
            "email"=>"required",
            "address"=>"required",
            "phone"=>"required",
            "amount"=>"required",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "services"=>"required",
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

            $offer_applied=isset($request->offer_applied)?$request->offer_applied:0;
            $price=0;

            $promocode=isset($request->promocode)?$request->promocode:'';
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
            $services=json_decode($request->services);
             foreach($services as $value)
            {
                $service_ids[]=$value->id;

                if(!isset($value->start_time)|| !isset($value->end_time)|| !isset($value->date))
                {
                      $return['error']=true;
                    $return['msg']="Please add date and time";
                    return $return;
                }
                 $rstart_time=$value->start_time.":00";

                $rend_time=$value->end_time.":00";
                $todate=Carbon::now()->format("Y-m-d");

                $to_date=new Carbon($value->date);
                $today=$to_date->format("Y-m-d");
                $s_date=$today. " " .$rstart_time;
                if($s_date<=$time)
                {
                    $return['error']=true;
                    $return['msg']="Please check the time";
                    return $return;
                }
                $serv_price=SalonServices::where('salon_id',$id)->where('id', $value->id)->first();
                if(isset($serv_price->amount) && $serv_price->amount>0)
                {
                    $ser_price=$serv_price->amount;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Please check the selected services";
                    return $return;
                }

                $discount=ServiceOffers::where("salon_id",$id)->where("service_id",$value->id)->get();
                if(isset($discount)&& count($discount)>0)
                {
                    $ids=[];
                    foreach($discount as $disc)
                    {
                        $start=new Carbon($disc->start_date);
                        $start_date=$start->format('Y-m-d');
                        $end=new Carbon($disc->end_date);
                        $end_date=$end->format('Y-m-d');

                        if($start_date<=$todate && $end_date>=$todate)
                        {
                            $ids[]=$disc->id;
                        }
                    }
                    if(isset($ids)&& count($ids)>0)
                    {
                        $service_price=ServiceOffers::where("salon_id",$id)->where("service_id",$value->id)->whereIn("id",$ids)->orderBy('discount_price', 'asc')->first()->discount_price;
                        $ser_price=isset($service_price)?$service_price:$ser_price;
                    }


                }
                    $price=$price+$ser_price;
            }

            $amount=$request->amount;
            if($request->user_id)
            {
                $user_id=$request->user_id;
                 $customer=Customers::where("id",$user_id)
                ->select("id","first_name","last_name","phone","address","email")
                ->first();
                $email=isset($customer->email)?$customer->email:'';
                $first_name=isset($customer->first_name)?$customer->first_name:'';
                $last_name=isset($customer->last_name)?$customer->last_name:'';
                $phone=isset($customer->phone)?$customer->phone:'';
                $address=isset($customer->address)?$customer->address:'';
            }
            else
            {
                $user_id=0;
                if($request->email=='' || $request->first_name=='')
                {
                    $return['status']="failed";
                    $return['msg']= "Please provide your details";
                    return $return;
                }
                $email=$request->email;
                $first_name=$request->first_name;
                $last_name=$request->last_name;
                $phone=$request->phone;
                $address=$request->address;

            }


            $timeslot=WorkingHours::where("salon_id",$id);
            $time=Carbon::now();


            foreach($services as $index=>$service)
            {
                $date=new Carbon($service->date);
                $f_date=$date->format('d-m-Y');
                $new_date=strtolower($date->format('l'));
                $start_date=$new_date."_start";
                $end_date=$new_date."_end";

                 if(isset($timeslot))
                {
                    $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                    $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';

                    // $services=[1,2];
                    // $check_price=SalonServices::where('salon_id',$id)->whereIn('id', $service_ids)->sum("amount");
                    $actual_amount=$price;
                    if($amount>$actual_amount)
                    {
                        $return['error']=true;
                        $return['msg']="Please check the price.";
                        return $return;
                    }
                     $min_price=Salons::where("id",$id)->first()->min_price;
                    if(isset($min_price)&& $min_price!=null)
                    {
                        $min_amt=$actual_amount * ($min_price/100);
                        if(isset($min_amt) && $min_amt>0)
                        {
                            if($amount<$min_amt)
                            {
                                $return['error']=true;
                                $return['msg']="You have to pay a minimum amount of ".$min_amt." AED for this salon.";
                                return $return;
                            }
                        }
                    }

                    $rstart_time=$service->start_time.":00";
                    $rend_time=$service->end_time.":00";
                    $from=new Carbon($f_date ." ".$rstart_time);
                    $to=new Carbon($f_date ." ".$rend_time);
                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
                    // {
                    //     $return['error']=true;
                    //     $return['msg']="Please check the timeslot";
                    //     return $return;
                    // }
                    $sstart_time=$start_time.":00";
                    $eend_time=$end_time.":00";

                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


                    if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
                    {
                        $return['error']=true;
                        $return['start_time']=$start_time;
                        $return['end_time']=$end_time;
                        $return['msg']="Please check the timeslot";
                        return $return;
                    }

                    $service_id=$service->id;
                    $ser["id"]=$service->id;
                    $ser["start_time"]=$service->start_time;
                    $ser["end_time"]=$service->end_time;
                    $ser["date"]=$service->date;

                    if(isset($service->staff_id) && $service->staff_id>0)
                    {
                        $staff_id=isset($service->staff_id)?$service->staff_id:'';
                    }
                    else
                    {

                        // if no staff is selected take available one
                         $staffs=DB::table("salon_staffs")
                        ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                        ->whereNull("staff_services.deleted_at")
                        ->where("salon_staffs.salon_id",$id)
                        ->where("staff_services.service_id",$service_id)->pluck("salon_staffs.id");
                        // return $staffs;
                        if(isset($staffs)&& count($staffs)>0)
                        {
                            foreach($staffs as $staff)
                            {
                                $check_booking_staffs=DB::table("booking")
                                ->join("booking_services", "booking_services.booking_id","=","booking.id")
                                ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
                                ->whereNull("booking_services.deleted_at")
                                ->where("booking_services.date",$f_date)
                                 ->where("booking.active",1)
                                ->where("booking_services.staff_id",$staff)
                                ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                                ->get();
                                 if(isset($check_booking_staffs)&&count($check_booking_staffs)>0)
                                {
                                    $staff_ids=[];
                                    foreach($check_booking_staffs as $each)
                                    {
                                        $h_start=new Carbon($each->date ." ".$each->start_time);
                                        $h_end=new Carbon($each->date ." ".$each->end_time);
                                       if($h_start>=$to ||  $h_end <= $from)
                                       {
                                        $staff_ids[]=$staff;
                                       }

                                    }
                                }
                                else
                                {
                                    $staff_ids[]=$staff;
                                }

                            }
                        }
                        else
                        {
                            $return['error']=true;
                            $return['msg']="Sorry no staffs are available at this time. Please try to change the date or time.";
                            return $return;
                        }
                        if(isset($staff_ids) && count($staff_ids)>0)
                        {
                            $staff_id=$staff_ids[0];
                        }
                         else
                        {
                            $return['error']=true;
                            $return['msg']="Sorry no staffs are available at this time. Please try to change the date or time.";
                            return $return;
                        }
                     $service->staff_id =$staff_id;

                    }

                    // end if staff not given


                    $ser["staff_id"]=$staff_id;
                    $booking_services[]=$ser;

                    $start_time=$service->start_time;
                    $end_time=$service->end_time;

                    $check_staff=StaffServices::where("staff_id",$staff_id)->where("service_id",$service_id)->first();
                    if(!isset($check_staff))
                    {
                        $return['error']=true;
                        $return['msg']="Sorry staff is not available for this service.";
                        return $return;
                    }

                    //checking whether any other users have the same booking time
                    $check_booking_hold=DB::table("booking_hold")
                        ->join("booking_hold_services", "booking_hold_services.booking_hold_id","=","booking_hold.id")
                        ->where("booking_hold_services.service_id",$service_id)
                        ->where("booking_hold_services.date",$f_date)
                        ->where("booking_hold_services.staff_id",$staff_id)
                        ->where("booking_hold.user_id","!=",$user_id)->whereNull("booking_hold.deleted_at")
                        ->whereNull("booking_hold_services.deleted_at")
                        ->select("booking_hold.*","booking_hold_services.staff_id","booking_hold_services.service_id","booking_hold_services.start_time","booking_hold_services.end_time")->get();
                    // $check_booking_hold=BookingHold::where("salon_id",$id)->get();
                    if(isset($check_booking_hold)&&count($check_booking_hold)>0)
                    {
                        foreach($check_booking_hold as $each)
                        {
                            $h_start=new Carbon($each->date ." ".$each->start_time);
                            $h_end=new Carbon($each->date ." ".$each->end_time);
                           if($h_start>=$to ||  $h_end <= $from)
                           {
                           }
                           else
                           {
                               $return['error']=true;
                                $return['msg']="Sorry this salon is already booked. Please try to change the time or date.";
                                return $return;
                           }
                        }
                    }
                    //checking whether the given user have already an appointment on the same date
                    $check_user_booking=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.date",$f_date)
                    ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
                    ->where("booking.active",1)
                    ->whereNull("booking_services.deleted_at")
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();

                    //checking whether the given staff have the same appointment on the same date
                    $check_booking_staffs=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.date",$f_date)
                    // ->where("booking.user_id",$user_id)
                    ->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")
                     ->where("booking.active",1)
                    ->where("booking_services.staff_id",$staff_id)
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();
                    if(isset($check_user_booking)&&count($check_user_booking)>0)
                    {
                        foreach($check_user_booking as $each)
                        {
                            $h_start=new Carbon($each->date ." ".$each->start_time);
                            $h_end=new Carbon($each->date ." ".$each->end_time);
                           if($h_start>=$to ||  $h_end <= $from)
                           {
                           }
                           else
                           {
                               $return['error']=true;
                                $return['msg']="Sorry you already have an appointment. Please try to change the time or date.";
                                return $return;
                           }
                        }
                    }
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
                               $return['error']=true;
                                $return['msg']="Sorry the selected staff have already an appointment. Please try to change the staff or date.";
                                return $return;
                           }
                        }
                    }
                }
                 else
                {
                    $return['error']=true;
                    $return['msg']="Sorry no timeslots found";
                    return $return;

                }
            }
                $add_booking=Booking::insertGetId(["user_id"=>$user_id,"salon_id"=>$id,"offer_applied"=>$offer_applied,"promocode"=>$promocode,"amount"=>$amount,"actual_amount"=>$actual_amount,"active"=>1,"status_code"=>0,"created_at"=>$time,"updated_at"=>$time]);
                if($add_booking)
                {
                   $add_booking_address=BookingAddress::insertGetId(["booking_id"=>$add_booking,"first_name"=>$first_name,"last_name"=>$last_name,"address"=>$address,"phone"=>$phone,"email"=>$email,"device"=>"web","created_at"=>$time,"updated_at"=>$time]);

                    foreach (BookingHold::where("user_id", $user_id)->where("salon_id",$id)->get() as $expired)
                    {
                        $delete_record=BookingHold::where("id",$expired->id)->delete();
                    }

                    foreach($services as $each)
                    {
                         // adding service amount
                        $ser_amount=SalonServices::where("id",$each->id)->first();
                        $service_amount=isset($ser_amount->amount)?$ser_amount->amount:0;


                        //adding service discount price
                        $discount=ServiceOffers::where("salon_id",$id)->where("service_id",$each->id)->get();
                            // dd(DB::getQueryog());
                        $disc_amount=$service_amount;
                        if(isset($discount)&& count($discount)>0)
                        {
                            $ids=[];
                            foreach($discount as $disc)
                            {
                                $start=new Carbon($disc->start_date);
                                $start_date=$start->format('Y-m-d');
                                $end=new Carbon($disc->end_date);
                                $end_date=$end->format('Y-m-d');

                                if($start_date<=$todate && $end_date>=$todate)
                                {
                                    $ids[]=$disc->id;
                                }
                            }
                            if(isset($ids)&& count($ids)>0)
                            {
                                $offer=ServiceOffers::where("salon_id",$id)->where("service_id",$each->id)->whereIn("id",$ids)->orderBy('discount_price', 'asc')->first();
                                $disc_amount=$offer->discount_price;

                            }

                        }
                        $date=new Carbon($each->date);
                        $f_date=$date->format('d-m-Y');
                        $rstart_time=$each->start_time.":00";
                        $rend_time=$each->end_time.":00";
                        $insert=BookingServices::insert([
                        "booking_id"=>$add_booking,
                        "date"=>$f_date,
                        "amount"=>$service_amount,
                        "discount_price"=>$disc_amount,
                        "service_id"=>$each->id,
                        "staff_id"=>$each->staff_id,
                        "start_time"=>$rstart_time,
                        "end_time"=>$rend_time,
                        'created_at'=> $time,
                        "updated_at"=>$time
                        ]);

                    }

                    $details=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->join("salons", "salons.id","=","booking.salon_id")
                    ->whereNull('booking.deleted_at')
                    ->whereNull("booking_services.deleted_at")
                    ->whereNull('salons.deleted_at')
                    ->where('booking.user_id',$user_id)
                    ->where('booking.id',$add_booking)
                    ->select("booking.id","salons.name as salon_name","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid")
                    ->first();

                     $details->services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$add_booking)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","booking_services.date","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                    $return['status']="success";
                    $return['msg']="Your booking completed successfully";
                    $return['booking_details']=$details;
                  return $return;

                }
                else
                {
                    $return['status']="failed";
                    $return['msg']="Sorry Error occured during process.";
                    return $return;
                }

                // $return['timeslots']=$timeslots;

        }
        return $return;
    }

    public function add_booking_test(Request $request)
    {
        $rules=[
            // "date"=>"required",
            "amount"=>"required",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "services"=>"required",
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

            $offer_applied=isset($request->offer_applied)?$request->offer_applied:0;;
            $promocode=isset($request->promocode)?$request->promocode:'';
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
            $services=json_decode($request->services);
             foreach($services as $val)
            {
                $service_ids[]=$val->id;
            }

            $amount=$request->amount;
            if($request->user_id)
            {
                $user_id=$request->user_id;
                 $customer=Customers::where("id",$user_id)
                ->select("id","first_name","last_name","phone","address","email")
                ->first();
                $email=isset($customer->email)?$customer->email:'';
                $first_name=isset($customer->first_name)?$customer->first_name:'';
                $last_name=isset($customer->last_name)?$customer->last_name:'';
                $phone=isset($customer->phone)?$customer->phone:'';
                $address=isset($customer->address)?$customer->address:'';
            }
            else
            {
                $user_id=0;
                if($request->email=='' || $request->first_name=='')
                {
                    $return['status']="failed";
                    $return['msg']= "Please provide your details";
                    return $return;
                }
                $email=$request->email;
                $first_name=$request->first_name;
                $last_name=$request->last_name;
                $phone=$request->phone;
                $address=$request->address;

            }

            $fcm=$request->fcm;
            $device=$request->device;

            $date=new Carbon ($request->date);
            $f_date=$date->format('d-m-Y');
            $new_date=strtolower($date->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";

            $timeslot=WorkingHours::where("salon_id",$id);
            $time=Carbon::now();
            if($date<=$time)
            {
                $return['status']="failed";
                $return['msg']= "Please check the date.";
                return $return;
            }

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
                    $service_id=$request->service_id;

                // $services=[1,2];
                $check_price=SalonServices::where('salon_id',$id)->whereIN('id', $service_ids)->sum("amount");
                $actual_amount=$check_price;
                if($amount>$actual_amount)
                {
                    $return['status']="failed";
                    $return['msg']= "Please check the price.";
                    return $return;
                }
                 $min_price=Salons::where("id",$id)->first()->min_price;
                if(isset($min_price)&& $min_price!=null)
                {
                    $min_amt=$actual_amount * ($min_price/100);
                    if(isset($min_amt) && $min_amt>0)
                    {
                        if($amount<$min_amt)
                        {
                            $return['status']="failed";
                            $return['msg']= "You have to pay a minimum amount of ".$min_amt." AED for this salon.";
                            return $return;
                        }
                    }
                }

                foreach($services as $service)
                {
                    $staff_id=$service->staff_id;
                    $start_time=$service->start_time;
                    $end_time=$service->end_time;
                    $service_id=$service->id;

                    $rstart_time=$start_time.":00";
                    $rend_time=$end_time.":00";
                    $from=new Carbon($f_date ." ".$rstart_time);
                    $to=new Carbon($f_date ." ".$rend_time);
                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
                    // {
                    //      $return['status']="failed";
                    //     $return['msg']="Please check the timeslot";
                    //     return $return;
                    // }
                    $sstart_time=$start_time.":00";
                    $eend_time=$end_time.":00";

                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


                    if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
                    {
                        $return['error']=true;
                        $return['start_time']=$start_time;
                         $return['status']="failed";
                        $return['end_time']=$end_time;
                        $return['msg']="Please check the timeslot";
                        return $return;
                    }

                    $check_staff=StaffServices::where("staff_id",$staff_id)->where("service_id",$service_id)->first();
                    if(!isset($check_staff))
                    {
                         $return['status']="failed";
                        $return['msg']="Sorry staff is not available for this service.";
                        return $return;
                    }
                    $check_booking=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.service_id",$service_id)->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")->where("booking_services.date",$f_date)
                    ->where("booking.active",1)
                    ->where("booking_services.staff_id",$staff_id)
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();

                    if(isset($check_booking)&&count($check_booking)>0)
                    {
                        foreach($check_booking as $each)
                        {
                            $e_start=new Carbon($each->date ." ".$each->start_time);
                            $e_end=new Carbon($each->date ." ".$each->end_time);
                           if($e_start>=$to ||  $e_end <= $from)
                           {
                           }
                           else
                           {
                                $return['status']="failed";
                                $return['msg']="Sorry this salon is already booked. Please try to change the time or date.";
                                return $return;
                           }
                        }
                    }
                    $check_booking_hold=DB::table("booking_hold")
                        ->join("booking_hold_services", "booking_hold_services.booking_hold_id","=","booking_hold.id")
                        ->where("booking_hold_services.service_id",$service_id)
                        ->where("booking_hold_services.staff_id",$staff_id)
                        ->where("booking_hold.user_id","!=",$user_id)->whereNull("booking_hold.deleted_at")
                        ->whereNull("booking_hold_services.deleted_at")->where("booking_hold_services.date",$f_date)
                        ->select("booking_hold.*","booking_hold_services.staff_id","booking_hold_services.service_id","booking_hold_services.start_time","booking_hold_services.end_time")->get();
                    // $check_booking_hold=BookingHold::where("salon_id",$id)->get();
                    if(isset($check_booking_hold)&&count($check_booking_hold)>0)
                    {
                        foreach($check_booking_hold as $each)
                        {
                            $h_start=new Carbon($each->date ." ".$each->start_time);
                            $h_end=new Carbon($each->date ." ".$each->end_time);
                           if($h_start>=$to ||  $h_end <= $from)
                           {
                           }
                           else
                           {
                                $return['status']="failed";
                                $return['msg']="Sorry this salon is already booked. Please try to change the time or date.";
                                return $return;
                           }
                        }
                    }
                    $check_user_booking=DB::table("booking")->where("date",$f_date)
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
                    ->where("booking.active",1)
                    ->whereNull("booking_services.deleted_at")->where("booking_services.date",$f_date)
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();
                    if(isset($check_user_booking)&&count($check_user_booking)>0)
                    {
                        foreach($check_user_booking as $each)
                        {
                            $h_start=new Carbon($each->date ." ".$each->start_time);
                            $h_end=new Carbon($each->date ." ".$each->end_time);
                           if($h_start>=$to ||  $h_end <= $from)
                           {
                           }
                           else
                           {
                                $return['status']="failed";
                                $return['msg']="Sorry this user have already an appointment. Please try to change the time or date.";
                                return $return;

                           }
                        }
                    }
                }
                $add_booking=Booking::insertGetId(["user_id"=>$user_id,"salon_id"=>$id,"offer_applied"=>$offer_applied,"promocode"=>$promocode,"amount"=>$amount,"actual_amount"=>$actual_amount,"active"=>1,"status_code"=>0,"created_at"=>$time,"updated_at"=>$time]);
                if($add_booking)
                {
                   $add_booking_address=BookingAddress::insertGetId(["booking_id"=>$add_booking,"first_name"=>$first_name,"last_name"=>$last_name,"address"=>$address,"phone"=>$phone,"email"=>$email,"fcm"=>$fcm,"device"=>$device,"created_at"=>$time,"updated_at"=>$time]);

                    foreach (BookingHold::where("user_id", $user_id)->where("salon_id",$id)->get() as $expired)
                    {
                        $delete_record=BookingHold::where("id",$expired->id)->delete();
                    }

                    foreach($services as $each)
                    {
                         // adding service amount
                        $ser_amount=SalonServices::where("id",$each->id)->first();
                        $service_amount=isset($ser_amount->amount)?$ser_amount->amount:0;


                        //adding service discount price
                        $discount=ServiceOffers::where("salon_id",$id)->where("service_id",$each->id)->get();
                            // dd(DB::getQueryog());
                        $disc_amount=$service_amount;
                        if(isset($discount)&& count($discount)>0)
                        {
                            $ids=[];
                            foreach($discount as $disc)
                            {
                                $start=new Carbon($disc->start_date);
                                $start_date=$start->format('Y-m-d');
                                $end=new Carbon($disc->end_date);
                                $end_date=$end->format('Y-m-d');

                                if($start_date<=$todate && $end_date>=$todate)
                                {
                                    $ids[]=$disc->id;
                                }
                            }
                            if(isset($ids)&& count($ids)>0)
                            {
                                $offer=ServiceOffers::where("salon_id",$id)->where("service_id",$each->id)->whereIn("id",$ids)->orderBy('discount_price', 'asc')->first();
                                $disc_amount=$offer->discount_price;

                            }

                        }
                        $date=new Carbon($each->date);
                        $f_date=$date->format('d-m-Y');
                        $rstart_time=$each->start_time.":00";
                        $rend_time=$each->end_time.":00";
                        $insert=BookingServices::insert([
                        "booking_id"=>$add_booking,
                        "date"=>$f_date,
                        "amount"=>$service_amount,
                        "discount_price"=>$disc_amount,
                        "service_id"=>$each->id,
                        "staff_id"=>$each->staff_id,
                        "start_time"=>$rstart_time,
                        "end_time"=>$rend_time,
                        'created_at'=> $time,
                        "updated_at"=>$time
                        ]);

                    }

                    $details=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->join("salons", "salons.id","=","booking.salon_id")
                    ->whereNull('booking.deleted_at')
                    ->whereNull("booking_services.deleted_at")
                    ->whereNull('salons.deleted_at')
                    ->where('booking.user_id',$user_id)
                    ->where('booking.id',$add_booking)
                    ->select("booking.id","salons.name as salon_name","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid")
                    ->first();

                     $details->services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$add_booking)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","booking_services.date","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                    $return['status']="success";
                    $return['msg']="Your booking completed successfully";
                    $return['booking_details']=$details;
                  return $return;

                }
                else
                {
                    $return['status']="failed";
                    $return['msg']="Sorry Error occured during process.";
                    return $return;
                }

                // $return['timeslots']=$timeslots;
            }
            else
            {
                    $return['status']="failed";
                    $return['msg']="Sorry no timeslots found.";
                    return $return;
            }

        }
        return $return;
    }
}
