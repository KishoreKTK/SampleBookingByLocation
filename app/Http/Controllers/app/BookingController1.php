<?php

namespace App\Http\Controllers\app;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PDF;
use Mail;
use Excel;
use Validator;
use App\Salons;
use App\Booking;
use App\Content;
use Carbon\Carbon;
use App\Customers;
use App\UserToken;
use App\SalonStaffs;
use App\BookingHold;
use App\PaymentToken;
use App\WorkingHours;
use App\StaffHolidays;
use App\StaffServices;
use App\SalonServices;
use App\ServiceOffers;
use App\BookingAddress;
use App\BookingServices;
use App\BookingHoldServices;
use Exception;

class BookingController extends Controller
{
    //
    // public function WorkingTimes(){
    //     // $salonid='5';
    //     $timeslot=WorkingHours::where("salon_id",$id);
    //     return $timeslot;
    // }

    function is_valid_time($date)
    {
        $rstart_time    =   $value->start_time.":00";
        $rend_time      =   $value->end_time.":00";
        $todate         =   Carbon::now()->format("Y-m-d");
        $to_date        =   new Carbon($value->date);
        $today          =   $to_date->format("Y-m-d");
        $s_date         =   $today. " " .$rstart_time;
        // return $s_date;
        if($s_date<=$time)
        {
            $return['error']=true;
            $return['msg']="Please check the time";
            return $return;
        }
       if( count($reasons) == 0 )
            return TRUE;
        else
            return FALSE;
    }

    function Check_Available_staff(){}

    function is_already_booked_slot($date,$time){}

    function is_user_has_already_booked_n_same_date_n_time(){}


    public function getBookedSlots(Request $request)
    {
        try
        {
            $result = ['status'=> true, 'data'=>$request->all(), 'message'=>"Check for Available Booking"];
        }
        catch(\Exception $e){
            $result = ['status'=> true, 'message'=>"Error in Booking", 'e_msg'=> $e->getMessage()];

        }
        return $result;
    }

    public function SaveCardDetails(){
        try
        {
            $result = ['status'=> true, 'data'=>$request->all(), 'message'=>"Check for Available Booking"];
        }
        catch(\Exception $e){
            $result = ['status'=> true, 'message'=>"Error in Booking", 'e_msg'=> $e->getMessage()];

        }
        return $result;
    }

    function is_valid_date($date,$start_time,$end_time)
    {
        $today_dt_n_time    =   Carbon::now();
        $today_dt           =   $today_dt_n_time->format("Y-m-d");
        $date               =   new Carbon($date);
        $date               =   $date->format("Y-m-d");
        $bk_dt_time         =   $date. " " .$start_time;
        if($bk_dt_time<=$today_dt_n_time)
        {
            $result = ['status' => false, 'message'=>"Please Check Date and Time" ];
        }
        else{
            $result = ['status' => true ];
        }

        return $result;
    }


    function get_service_det($salon_id,$services){
        try{
            $price = 0;
            foreach ($services as $key => $service) {
                $serv_price     =   SalonServices::where('salon_id',$salon_id)
                                    ->where('id', $service->id)->first();
                if(isset($serv_price->amount) && $serv_price->amount>0)
                {
                    $ser_price  =   $serv_price->amount;
                }
                else{
                    throw new \Exception("Please check the selected services");
                }
                $price=$price+$ser_price;
            }
            $result = ['status'=> true, "price"=> $price];
        }
        catch(\Exception $e){ $result = ['status'=> false, "message"=>$e->getMessage()];}
        return $result;
    }

    function get_booked_user_det($request){
        try{
            if(request()->header('User-Token'))
            {
                $api_token=request()->header('User-Token');
                $user=UserToken::where("api_token",$api_token)->first();
                if(isset($user)&& isset($user->user_id))
                {
                    $result['user_id']=$user->user_id;
                }
                else
                {
                    throw new \Exception("API Token expired");
                }
                $customer   =   Customers::where("id",$user_id)
                                ->select("id","first_name","last_name","phone","address","email")
                                ->first();
                $result['email']      =   isset($customer->email)?$customer->email:'';
                $result['first_name'] =   isset($customer->first_name)?$customer->first_name:'';
                $result['last_name']  =   isset($customer->last_name)?$customer->last_name:'';
                $result['phone']      =   isset($customer->phone)?$customer->phone:'';
                $result['address']    =   isset($customer->address)?$customer->address:'';
            }
            else
            {
                $user_id=0;
                if($request->email=='' || $request->first_name=='' || $request->phone=='')
                {
                    throw new \Exception("Please provide your email, first_name and phone number");
                }
                $result['email']          =   $request->email;
                $result['first_name']     =   $request->first_name;
                $result['last_name']      =   $request->last_name;
                $result['country_code']   =   $request->country_code;
                $result['phone']          =   $request->phone;
                $result['address']        =   $request->address;
            }
        }
        catch(\Exception $e){ $result = ['status'=> false, "message"=>$e->getMessage()];}
        return $result;
    }


    public function Booking(Request $request)
    {
        try
        {
            $rules=[
                    "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
                    "date"=>"required",
                    "amount"=>"required",
                    "services"=>"required",
                ];
            $msg=[
                "id.required"=>"ID is required",
            ];

            $validator  =   Validator::make($request->all(), $rules, $msg);
            if(!$validator->fails())
            {
                throw new \Exception(implode( ", ",$validator->errors()->all()));
            }
            $salon_id       =   $request->salon_id;
            $bookingdate    =   new Carbon($request->date);
            $start_time     =   $request->start_time.":00";
            $end_time       =   $request->end_time.":00";
            $services       =   $request->services;

            // Checks Date and Time of Booking Slot
            $checkdate      =   $this->is_valid_date($bookingdate,$start_time,$end_time);
            if($checkdate['status'] != true) {
                throw new \Exception($checkdate['message']);
            }

            // Checks Service Details of
            $get_service_details    =  $this->get_service_det($salon_id,$services);
            if($get_service_details['status'] != true) {
                throw new \Exception($get_service_details['message']);
            }
            $service_price = $get_service_details['price'];

            // Checks User Registered or Not and Gets User Details
            $user_det = $this->get_booked_user_det($request->all());
            if($user_det['status'] != true) {
                throw new \Exception($user_det['message']);
            }

            $user_id        =   $user_det['user_id'];
            $email          =   $user_det['email'];
            $first_name     =   $user_det['first_name'];
            $last_name      =   $user_det['last_name'];
            $country_code   =   $user_det['country_code'];
            $phone          =   $user_det['phone'];
            $address        =   $user_det['address'];
            $fcm            =   $request->fcm;
            $device         =   $request->device;

            // if($get_service_details['status'] != true) {
            //     throw new \Exception($get_service_details['message']);
            // }
            // $service_price = $get_service_details['price'];

            $result = [
                    'status'    =>  true,
                    'data'      =>  $request->all(),
                    'message'   =>  "Check for Available Booking"
            ];
        }
        catch(\Exception $e){
            $result = ['status'=> false, 'message'=>$e->getMessage()];
        }
        return $result;
    }

    public function moodbooking(Request $request)
    {

        $data = $request->all();
        $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "date"=>"required",
            "amount"=>"required",
            "services"=>"required",
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
            $time       =   Carbon::now();
            $services   =   $request->services;
            $id         =   $request->salon_id;
            $price      =   0;
            $bookeddate =   $request->date;
            $amount     =   $request->amount;


            foreach($services as $val)
            {
                $value          =   $val;
                $service_ids[]  =   $value->id;
                $rstart_time    =   $value->start_time.":00";
                $rend_time      =   $value->end_time.":00";
                $todate         =   Carbon::now()->format("Y-m-d");
                $to_date        =   new Carbon($bookeddate);
                $today          =   $to_date->format("Y-m-d");
                $s_date         =   $today. " " .$rstart_time;

                if($s_date<=$time)
                {
                    $return['error']    =   true;
                    $return['msg']      =   "Please check the time";
                    return $return;
                }

                $serv_price     =   SalonServices::where('salon_id',$id)
                                    ->where('id', $value->id)->first();

                if(isset($serv_price->amount) && $serv_price->amount>0)
                {
                    $ser_price  =   $serv_price->amount;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Please check the selected services";
                    return $return;
                }

                $price=$price+$ser_price;
            }

            $promocode=isset($request->promocode)?$request->promocode:'';

            if(request()->header('User-Token'))
            {
                $api_token  =   request()->header('User-Token');
                $user       =   UserToken::where("api_token",$api_token)->first();
                if(isset($user)&& isset($user->user_id))
                {
                    $user_id=   $user->user_id;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="API Token expired";
                    return $return;
                }
                $customer   =   Customers::where("id",$user_id)
                                ->select("id","first_name","last_name","phone","address","email")
                                ->first();
                $email      =   isset($customer->email)?$customer->email:'';
                $first_name =   isset($customer->first_name)?$customer->first_name:'';
                $last_name  =   isset($customer->last_name)?$customer->last_name:'';
                $phone      =   isset($customer->phone)?$customer->phone:'';
                $address    =   isset($customer->address)?$customer->address:'';
            }
            else
            {
                $user_id=0;
                if($request->email=='' || $request->first_name=='' || $request->phone=='')
                {
                    $return['error']=true;
                    $return['msg']= "Please provide your email, first_name and phone number";
                    return $return;
                }
                $email          =   $request->email;
                $first_name     =   $request->first_name;
                $last_name      =   $request->last_name;
                $country_code   =   $request->country_code;
                $phone          =   $request->phone;
                $address        =   $request->address;
            }

            $fcm                =   $request->fcm;
            $device             =   $request->device;
            $staff_ids          =   [];

            $timeslot           =   WorkingHours::where("salon_id",$id);
            $booking_services   =   []; $ser    =   [];

            //checking if staff is selected for same time
            foreach($services as $val)
            {
                $value      =   json_decode($val);
                $date       =   new Carbon($bookeddate);
                $rstart_time=   $value->start_time.":00";
                $rend_time  =   $value->end_time.":00" ;
                $f_date     =   $date->format('d-m-Y');
                $from       =   new Carbon($f_date ." ".$rstart_time);
                $to         =   new Carbon($f_date ." ".$rend_time);

                //checking whether the user have selected the same time twice
                foreach($services as $index=>$servi)
                {
                    $ser    =   [];
                    $service=   json_decode($servi);
                    $h_start=   new Carbon($bookeddate ." ".$service->start_time);
                    $h_end  =   new Carbon($bookeddate ." ".$service->end_time);
                    if($service->id != $value->id)
                    {
                        if($h_start>=$to ||  $h_end <= $from)
                        {

                        }
                        else
                        {
                            if($value->staff_id == $service->staff_id)
                            {
                                $return['error']=true;
                                $return['msg']="Sorry you already this staff. Please try to change the staff or time.";
                                return $return;
                            }
                        }
                    }
                }
            }

            //
            foreach($services as $index=>$val)
            {
                $ser        =   [];
                $service    =   json_decode($val);
                $date       =   new Carbon($service->date);
                $f_date     =   $date->format('d-m-Y');
                $new_date   =   strtolower($date->format('l'));
                $start_date =   $new_date."_start";
                $end_date   =   $new_date."_end";
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
                        $timeslot   =   $timeslot
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

                    // $services=[1,2];
                    // $check_price=SalonServices::where('salon_id',$id)->whereIn('id', $service_ids)->sum("amount");
                    $actual_amount=$price;
                    if($amount>$actual_amount)
                    {
                        $return['error']=true;
                        $return['amount']=$amount;
                        $return['actual_amount']=$actual_amount;
                        $return['msg']="Please check the price.";
                        return $return;
                    }
                    $min_price  =   Salons::where("id",$id)->first()->min_price;
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
                    $sstart_time    =   $start_time.":00";
                    $eend_time      =   $end_time.":00";

                    // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


                    if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
                    {
                        $return['error']=true;
                        $return['start_time']=$start_time;
                        $return['end_time']=$end_time;
                        $return['msg']="Please check the timeslot";
                        return $return;
                    }

                    $service_id         =   $service->id;
                    $ser["id"]          =   $service->id;
                    $ser["start_time"]  =   $service->start_time;
                    $ser["end_time"]    =   $service->end_time;
                    $ser["date"]        =   $service->date;

                    if(isset($service->staff_id) && $service->staff_id>0)
                    {
                        $staff_id       =   isset($service->staff_id)?$service->staff_id:'';
                    }
                    else
                    {
                        // if no staff is selected take available one
                        $check  =   [];
                        $check  =   StaffHolidays::where("date",$service->date)->pluck("staff_id")->toArray();
                        $staffs =   DB::table("salon_staffs")
                                    ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                                    ->whereNotIn("staffs.id",$check)
                                    ->whereNull("staff_services.deleted_at")
                                    ->where("salon_staffs.salon_id",$id)
                                    ->where("staff_services.service_id",$service_id)
                                    ->pluck("salon_staffs.id");
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
                                        $h_start    =   new Carbon($each->date ." ".$each->start_time);
                                        $h_end      =   new Carbon($each->date ." ".$each->end_time);
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
                    $check  =   StaffHolidays::where("staff_id",$staff_id)->where("date",$service->date)->first();
                    if(isset($check))
                    {
                        $return['error']=true;
                        $return['msg']="Sorry this staff is not available for the given date. Please change the staff or date";
                        return $return;
                    }

                    // end if staff not given
                    $ser["staff_id"]        =   $staff_id;
                    $booking_services[]     =   $ser;
                    $start_time             =   $service->start_time;
                    $end_time               =   $service->end_time;
                    $check_staff            =   StaffServices::where("staff_id",$staff_id)->where("service_id",$service_id)->first();
                    if(!isset($check_staff))
                    {
                        $return['error']=true;
                        $return['msg']="Sorry staff is not available for this service.";
                        return $return;
                    }

                    //checking whether any other users have the same booking time
                    $check_booking_hold =DB::table("booking_hold")
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
                    //checking whether the given user have already an appointment on the same date and another salon
                    $check_user_booking =   DB::table("booking")
                                            ->join("booking_services", "booking_services.booking_id","=","booking.id")
                                            ->where("booking_services.date",$f_date)
                                            ->where("booking.user_id",$user_id)
                                            ->where("booking.salon_id","!=",$id)
                                            ->whereNull("booking.deleted_at")
                                            ->where("booking.active",1)
                                            ->whereNull("booking_services.deleted_at")
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

                    //checking whether the given staff have the same appointment on the same date
                    $check_booking_staffs=  DB::table("booking")
                                            ->join("booking_services", "booking_services.booking_id","=","booking.id")
                                            ->where("booking_services.date",$f_date)
                                            // ->where("booking.user_id",$user_id)
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

            $add_booking=Booking::insertGetId([
                            "user_id"=>$user_id,
                            "salon_id"=>$id,
                            "offer_applied"=>$offer_applied,
                            "promocode"=>$promocode,
                            "amount"=>$amount,
                            "actual_amount"=>$actual_amount,
                            "active"=>0,
                            "status_code"=>0,
                            "created_at"=>$time,
                            "updated_at"=>$time
                        ]);
            if($add_booking)
            {
                BookingAddress::insertGetId([
                            "booking_id"=>$add_booking,
                            "first_name"=>$first_name,
                            "last_name"=>$last_name,
                            "address"=>$address,
                            "phone"=>$phone,
                            "email"=>$email,
                            "fcm"=>$fcm,
                            "device"=>$device,
                            "created_at"=>$time,
                            "updated_at"=>$time
                        ]);

                foreach (BookingHold::where("user_id", $user_id)->where("salon_id",$id)->get() as $expired)
                {
                    $delete_record=BookingHold::where("id",$expired->id)->delete();
                }
                foreach($booking_services as $each)
                {
                    // adding service amount
                    $ser_amount=SalonServices::where("id",$each['id'])->first();
                    $service_amount=isset($ser_amount->amount)?$ser_amount->amount:0;

                    //adding service discount price
                    $discount   =   ServiceOffers::where("salon_id",$id)
                                    ->where("approved",1)
                                    ->where("service_id",$each['id'])
                                    ->get();
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
                            $offer=ServiceOffers::where("salon_id",$id)->where("service_id",$each['id'])->whereIn("id",$ids)->where("approved",1)->orderBy('discount_price', 'asc')->first();
                            $disc_amount=$offer->discount_price;

                        }

                    }

                    // $each=json_decode($val);
                     $date=new Carbon($each['date']);
                    $f_date=$date->format('d-m-Y');
                     $rstart_time=$each['start_time'].":00";
                    $rend_time=$each['end_time'].":00";
                    $insert=BookingServices::insert([
                                                    "booking_id"=>$add_booking,
                                                    "date"=>$f_date,
                                                    "amount"=>$service_amount,
                                                    "discount_price"=>$disc_amount,
                                                    "service_id"=>$each['id'],
                                                    "staff_id"=>$each['staff_id'],
                                                    "start_time"=>$rstart_time,
                                                    "end_time"=>$rend_time,
                                                    'created_at'=> $time,
                                                    "updated_at"=>$time
                                                ]);
                }

                $details=DB::table("booking")
                                ->join("booking_services", "booking_services.booking_id","=","booking.id")
                                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                                ->join("salons", "salons.id","=","booking.salon_id")
                                ->whereNull('booking.deleted_at')
                                ->whereNull("booking_services.deleted_at")
                                ->whereNull('salons.deleted_at')
                                ->where('booking.user_id',$user_id)
                                ->where('booking.id',$add_booking)
                                ->select("booking.id","salons.name as salon_name","salons.pricing","booking_address.first_name","booking_address.last_name","booking_address.email","booking_address.address","booking_address.phone","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid","salons.email as salon_email")
                                ->first();

                $o_id=10000+$add_booking;
                $orderId="PM".$o_id;
                $details->orderId=$orderId;

                if(isset($details->pricing)&& $details->pricing!=null)
                {
                    $details->mood_commission=$details->amount_paid * ($details->pricing/100);
                }
                else
                {
                    $details->mood_commission="0.00";
                }


                $book_services= DB::table("booking_services")
                ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                ->where("booking_services.booking_id",$add_booking)
                ->whereNull('booking_services.deleted_at')
                ->whereNull('salon_services.deleted_at')
                ->whereNull('salon_staffs.deleted_at')
                ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","booking_services.amount","booking_services.discount_price","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();

                if(isset($book_services) && count($book_services)>0)
                {
                    foreach($book_services as $ser)
                    {
                        $ser->start_time=substr($ser->start_time, 0, -3);
                        $ser->end_time=substr($ser->end_time, 0, -3);
                    }
                }

                $details->services=$book_services;
                if(request()->header('User-Token'))
                {
                    $api_token=request()->header('User-Token');
                      if(isset($request->new_card) && $request->new_card=='true')
                    {
                        $return['error']=false;
                        $return['msg']="Your booking completed successfully";
                        $return['booking_details']=$details;
                        $return['is_token']=false;
                    }
                    else
                    {
                        return $this->complete_booking($details,$api_token,$first_name,$last_name, $add_booking,$email,$phone,$amount,$address,$actual_amount,$book_services);
                    }
                }
                else
                {
                    $return['error']=false;
                    $return['msg']="Your booking completed successfully";
                    $return['booking_details']=$details;
                    $return['is_token']=false;
                }

                // $return['error']=false;
                // $return['msg']="Your booking completed successfully";
                $return['booking_details']=$details;
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry error occured";
            }

            // $return['timeslots']=$timeslots;


        }
        return $return;
    }




    // public function bookings(Request $request)
    // {

    //         $time=Carbon::now();
    //         $price=0;

    //         $services=$request->services;
    //         $id=$request->salon_id;

    //         foreach($services as $val)
    //         {
    //             $value=json_decode($val);

    //             $service_ids[]=$value->id;

    //             $rstart_time=   $value->start_time.":00";
    //             $rend_time  =   $value->end_time.":00";

    //             $todate     =   Carbon::now()->format("Y-m-d");
    //             $to_date    =   new Carbon($value->date);
    //             $today      =   $to_date->format("Y-m-d");
    //             $s_date     =   $today. " " .$rstart_time;

    //             if($s_date<=$time)
    //             {
    //                 $return['error']=true;

    //                 $return['msg']="Please check the time";
    //                 return $return;
    //             }

    //             $serv_price =   SalonServices::where('salon_id',$id)->where('id', $value->id)->first();

    //             if(isset($serv_price->amount) && $serv_price->amount>0)
    //             {
    //                 $ser_price=$serv_price->amount;
    //             }
    //             else
    //             {
    //                 $return['error']=true;
    //                 $return['msg']="Please check the selected services";
    //                 return $return;
    //             }

    //         }

    //         $offer_applied  =   isset($request->offer_applied)?$request->offer_applied:0;;
    //         $promocode      =   isset($request->promocode)?$request->promocode:'';
    //         $amount         =   $request->amount;

    //         if(request()->header('User-Token'))
    //         {
    //             $api_token  =   request()->header('User-Token');
    //             $user       =   UserToken::where("api_token",$api_token)->first();
    //             if(isset($user)&& isset($user->user_id))
    //             {
    //                 $user_id=$user->user_id;
    //             }
    //             else
    //             {
    //                 $return['error']=true;
    //                 $return['msg']="API Token expired";
    //                 return $return;
    //             }
    //             $customer   =   Customers::where("id",$user_id)
    //                             ->select("id","first_name","last_name","phone","address","email")
    //                             ->first();
    //             $email      =   isset($customer->email)?$customer->email:'';
    //             $first_name =   isset($customer->first_name)?$customer->first_name:'';
    //             $last_name  =   isset($customer->last_name)?$customer->last_name:'';
    //             $phone      =   isset($customer->phone)?$customer->phone:'';
    //             $address    =   isset($customer->address)?$customer->address:'';
    //         }
    //         else
    //         {
    //             $user_id    =   0;
    //             if($request->email=='' || $request->first_name=='' || $request->phone=='')
    //             {
    //                 $return['error']=true;
    //                 $return['msg']= "Please provide your email, first_name and phone number";
    //                 return $return;
    //             }
    //             $email          =   $request->email;
    //             $first_name     =   $request->first_name;
    //             $last_name      =   $request->last_name;
    //             $country_code   =   $request->country_code;
    //             $phone          =   $request->phone;
    //             $address        =   $request->address;
    //         }

    //         $fcm                =   $request->fcm;
    //         $device             =   $request->device;
    //         $staff_ids          =   [];

    //         $timeslot           =   WorkingHours::where("salon_id",$id);

    //         $booking_services=[]; $ser=[];

    //         //checking if staff is selected for same time
    //         foreach($services as $val)
    //         {
    //             $value          =   json_decode($val);
    //             $date           =   new Carbon($value->date);
    //             $rstart_time    =   $value->start_time.":00";
    //             $rend_time      =   $value->end_time.":00" ;
    //             $f_date         =   $date->format('d-m-Y');
    //             $from           =   new Carbon($f_date ." ".$rstart_time);
    //             $to             =   new Carbon($f_date ." ".$rend_time);

    //              //checking whether the user have selected the same time twice
    //             foreach($services as $index=>$servi)
    //             {
    //                 $ser    =   [];
    //                 $service=   json_decode($servi);
    //                 $h_start=   new Carbon($service->date ." ".$service->start_time);
    //                 $h_end  =   new Carbon($service->date ." ".$service->end_time);

    //                 if($service->id != $value->id)
    //                 {
    //                     if($h_start>=$to ||  $h_end <= $from)
    //                     {
    //                     }
    //                     else
    //                     {
    //                         if($value->staff_id == $service->staff_id)
    //                         {
    //                             $return['error']=true;
    //                             $return['msg']="Sorry you have already this staff. Please try to change the staff or time.";
    //                             return $return;
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         foreach($services as $index=>$val)
    //         {
    //             $ser        =   [];
    //             $service    =   json_decode($val);
    //             $date       =   new Carbon($service->date);
    //             $f_date     =   $date->format('d-m-Y');
    //             $new_date   =   strtolower($date->format('l'));
    //             $start_date =   $new_date."_start";
    //             $end_date   =   $new_date."_end";
    //             if($new_date=='monday')
    //             {
    //                     $timeslot=$timeslot
    //                     ->select('id','monday_start as start_time','monday_end as end_time')
    //                     ->first();
    //                 }
    //                 elseif($new_date=='tuesday')
    //                 {
    //                     $timeslot=$timeslot
    //                     ->select("id",'tuesday_start as start_time','tuesday_end as end_time')
    //                     ->first();
    //                 }
    //                 elseif($new_date=='wednesday')
    //                 {
    //                     $timeslot=$timeslot
    //                     ->select("id",'wednesday_start as start_time','wednesday_end as end_time')
    //                     ->first();
    //                 }
    //                 elseif($new_date=='thursday')
    //                 {
    //                     $timeslot=$timeslot
    //                     ->select("id",'thursday_start as start_time','thursday_end as end_time')
    //                     ->first();
    //                 }
    //                 elseif($new_date=='friday')
    //                 {
    //                     $timeslot   =   $timeslot
    //                                     ->select("id",'friday_start as start_time','friday_end as end_time')
    //                                     ->first();
    //                 }
    //                 elseif($new_date=='saturday')
    //                 {
    //                     $timeslot=$timeslot
    //                     ->select("id",'saturday_start as start_time','saturday_end as end_time')
    //                     ->first();
    //                 }
    //                 else
    //                 {
    //                     $timeslot=$timeslot
    //                                 ->select("id",'sunday_start as start_time','sunday_end as end_time')
    //                                 ->first();
    //                 }

    //             if(isset($timeslot))
    //             {
    //                 $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
    //                 $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';

    //                 // $services=[1,2];
    //                 // $check_price=SalonServices::where('salon_id',$id)->whereIn('id', $service_ids)->sum("amount");
    //                 $actual_amount=$price;
    //                 if($amount>$actual_amount)
    //                 {
    //                     $return['error']=true;
    //                     $return['amount']=$amount;
    //                     $return['actual_amount']=$actual_amount;
    //                     $return['msg']="Please check the price.";
    //                     return $return;
    //                 }
    //                 $min_price  =   Salons::where("id",$id)->first()->min_price;
    //                 if(isset($min_price)&& $min_price!=null)
    //                 {
    //                     $min_amt=$actual_amount * ($min_price/100);
    //                     if(isset($min_amt) && $min_amt>0)
    //                     {
    //                         if($amount<$min_amt)
    //                         {
    //                             $return['error']=true;
    //                             $return['msg']="You have to pay a minimum amount of ".$min_amt." AED for this salon.";
    //                             return $return;
    //                         }
    //                     }
    //                 }

    //                 $rstart_time=$service->start_time.":00";
    //                 $rend_time=$service->end_time.":00";
    //                 $from=new Carbon($f_date ." ".$rstart_time);
    //                 $to=new Carbon($f_date ." ".$rend_time);
    //                 // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)
    //                 // {
    //                 //     $return['error']=true;
    //                 //     $return['msg']="Please check the timeslot";
    //                 //     return $return;
    //                 // }
    //                 $sstart_time=$start_time.":00";
    //                 $eend_time=$end_time.":00";

    //                 // if($start_time>=$rstart_time && $rstart_time<$end_time && $start_time<$end_time && $rend_time<=$end_time)


    //                 if($sstart_time>$rstart_time || $rstart_time>=$eend_time || $rend_time>$eend_time)
    //                 {
    //                     $return['error']=true;
    //                     $return['start_time']=$start_time;
    //                     $return['end_time']=$end_time;
    //                     $return['msg']="Please check the timeslot";
    //                     return $return;
    //                 }

    //                 $service_id         =   $service->id;
    //                 $ser["id"]          =   $service->id;
    //                 $ser["start_time"]  =   $service->start_time;
    //                 $ser["end_time"]    =   $service->end_time;
    //                 $ser["date"]        =   $service->date;

    //                 if(isset($service->staff_id) && $service->staff_id>0)
    //                 {
    //                     $staff_id       =   isset($service->staff_id)?$service->staff_id:'';
    //                 }
    //                 else
    //                 {
    //                     // if no staff is selected take available one
    //                     $check  =   [];
    //                     $check  =   StaffHolidays::where("date",$service->date)->pluck("staff_id")->toArray();
    //                     $staffs =   DB::table("salon_staffs")
    //                                     ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
    //                                     ->whereNotIn("staffs.id",$check)
    //                                     ->whereNull("staff_services.deleted_at")
    //                                     ->where("salon_staffs.salon_id",$id)
    //                                     ->where("staff_services.service_id",$service_id)
    //                                     ->pluck("salon_staffs.id");
    //                     // return $staffs;

    //                     if(isset($staffs)&& count($staffs)>0)
    //                     {
    //                         foreach($staffs as $staff)
    //                         {
    //                             $check_booking_staffs=DB::table("booking")
    //                                                     ->join("booking_services", "booking_services.booking_id","=","booking.id")
    //                                                     ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
    //                                                     ->whereNull("booking_services.deleted_at")
    //                                                     ->where("booking_services.date",$f_date)
    //                                                     ->where("booking.active",1)
    //                                                     ->where("booking_services.staff_id",$staff)
    //                                                     ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
    //                                                     ->get();
    //                             if(isset($check_booking_staffs)&&count($check_booking_staffs)>0)
    //                             {
    //                                 $staff_ids  =   [];
    //                                 foreach($check_booking_staffs as $each)
    //                                 {
    //                                     $h_start    =   new Carbon($each->date ." ".$each->start_time);
    //                                     $h_end      =   new Carbon($each->date ." ".$each->end_time);
    //                                    if($h_start>=$to ||  $h_end <= $from)
    //                                    {
    //                                     $staff_ids[]=$staff;
    //                                    }
    //                                 }
    //                             }
    //                             else
    //                             {
    //                                 $staff_ids[]=$staff;
    //                             }
    //                         }
    //                     }
    //                     else
    //                     {
    //                         $return['error']=true;
    //                         $return['msg']="Sorry no staffs are available at this time. Please try to change the date or time.";
    //                         return $return;
    //                     }
    //                     if(isset($staff_ids) && count($staff_ids)>0)
    //                     {
    //                         $staff_id=$staff_ids[0];
    //                     }
    //                      else
    //                     {
    //                         $return['error']=true;
    //                         $return['msg']="Sorry no staffs are available at this time. Please try to change the date or time.";
    //                         return $return;
    //                     }
    //                     $service->staff_id =$staff_id;
    //                 }
    //                 $check  =   StaffHolidays::where("staff_id",$staff_id)->where("date",$service->date)->first();
    //                 if(isset($check))
    //                 {
    //                     $return['error']=true;
    //                     $return['msg']="Sorry this staff is not available for the given date. Please change the staff or date";
    //                     return $return;
    //                 }

    //                 // end if staff not given
    //                 $ser["staff_id"]        =   $staff_id;
    //                 $booking_services[]     =   $ser;
    //                 $start_time             =   $service->start_time;
    //                 $end_time               =   $service->end_time;
    //                 $check_staff=StaffServices::where("staff_id",$staff_id)->where("service_id",$service_id)->first();
    //                 if(!isset($check_staff))
    //                 {
    //                     $return['error']=true;
    //                     $return['msg']="Sorry staff is not available for this service.";
    //                     return $return;
    //                 }

    //                 //checking whether any other users have the same booking time
    //                 $check_booking_hold =   DB::table("booking_hold")
    //                                         ->join("booking_hold_services", "booking_hold_services.booking_hold_id","=","booking_hold.id")
    //                                         ->where("booking_hold_services.service_id",$service_id)
    //                                         ->where("booking_hold_services.date",$f_date)
    //                                         ->where("booking_hold_services.staff_id",$staff_id)
    //                                         ->where("booking_hold.user_id","!=",$user_id)->whereNull("booking_hold.deleted_at")
    //                                         ->whereNull("booking_hold_services.deleted_at")
    //                                         ->select("booking_hold.*","booking_hold_services.staff_id","booking_hold_services.service_id","booking_hold_services.start_time","booking_hold_services.end_time")->get();
    //                 // $check_booking_hold=BookingHold::where("salon_id",$id)->get();
    //                 if(isset($check_booking_hold)&&count($check_booking_hold)>0)
    //                 {
    //                     foreach($check_booking_hold as $each)
    //                     {
    //                         $h_start=new Carbon($each->date ." ".$each->start_time);
    //                         $h_end=new Carbon($each->date ." ".$each->end_time);
    //                         if($h_start>=$to ||  $h_end <= $from)
    //                         {

    //                         }
    //                         else
    //                         {
    //                             $return['error']    =   true;
    //                             $return['msg']      =   "Sorry this salon is already booked. Please try to change the time or date.";
    //                             return $return;
    //                         }
    //                     }
    //                 }


    //                 //checking whether the given user have already an appointment on the same date and another salon
    //                 $check_user_booking =   DB::table("booking")
    //                                         ->join("booking_services", "booking_services.booking_id","=","booking.id")
    //                                         ->where("booking_services.date",$f_date)
    //                                         ->where("booking.user_id",$user_id)
    //                                         ->where("booking.salon_id","!=",$id)
    //                                         ->whereNull("booking.deleted_at")
    //                                         ->where("booking.active",1)
    //                                         ->whereNull("booking_services.deleted_at")
    //                                         ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
    //                                         ->get();

    //                 //checking whether the given staff have the same appointment on the same date
    //                 $check_booking_staffs=DB::table("booking")
    //                                         ->join("booking_services", "booking_services.booking_id","=","booking.id")
    //                                         ->where("booking_services.date",$f_date)
    //                                         // ->where("booking.user_id",$user_id)
    //                                         ->whereNull("booking.deleted_at")
    //                                         ->whereNull("booking_services.deleted_at")
    //                                         ->where("booking.active",1)
    //                                         ->where("booking_services.staff_id",$staff_id)
    //                                         ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
    //                                         ->get();

    //                 if(isset($check_user_booking)&&count($check_user_booking)>0)
    //                 {
    //                     foreach($check_user_booking as $each)
    //                     {
    //                         $h_start=new Carbon($each->date ." ".$each->start_time);
    //                         $h_end=new Carbon($each->date ." ".$each->end_time);
    //                        if($h_start>=$to ||  $h_end <= $from)
    //                        {
    //                        }
    //                        else
    //                        {
    //                            $return['error']=true;
    //                             $return['msg']="Sorry you already have an appointment. Please try to change the time or date.";
    //                             return $return;
    //                        }
    //                     }
    //                 }
    //                 if(isset($check_booking_staffs)&&count($check_booking_staffs)>0)
    //                 {
    //                     foreach($check_booking_staffs as $each)
    //                     {
    //                         $h_start=new Carbon($each->date ." ".$each->start_time);
    //                         $h_end=new Carbon($each->date ." ".$each->end_time);
    //                        if($h_start>=$to ||  $h_end <= $from)
    //                        {
    //                        }
    //                        else
    //                        {
    //                            $return['error']=true;
    //                             $return['msg']="Sorry the selected staff have already an appointment. Please try to change the staff or date.";
    //                             return $return;
    //                        }
    //                     }
    //                 }
    //             }
    //             else
    //             {
    //                 $return['error']=true;
    //                 $return['msg']="Sorry no timeslots found";
    //                 return $return;
    //             }
    //         }

    //         $add_booking=Booking::insertGetId([
    //                         "user_id"=>$user_id,
    //                         "salon_id"=>$id,
    //                         "offer_applied"=>$offer_applied,
    //                         "promocode"=>$promocode,
    //                         "amount"=>$amount,
    //                         "actual_amount"=>$actual_amount,
    //                         "active"=>0,
    //                         "status_code"=>0,
    //                         "created_at"=>$time,
    //                         "updated_at"=>$time
    //                     ]);
    //         if($add_booking)
    //         {
    //             $add_booking_address=BookingAddress::insertGetId([
    //                         "booking_id"=>$add_booking,
    //                         "first_name"=>$first_name,
    //                         "last_name"=>$last_name,
    //                         "address"=>$address,
    //                         "phone"=>$phone,
    //                         "email"=>$email,
    //                         "fcm"=>$fcm,
    //                         "device"=>$device,
    //                         "created_at"=>$time,
    //                         "updated_at"=>$time
    //                     ]);

    //             foreach (BookingHold::where("user_id", $user_id)->where("salon_id",$id)->get() as $expired)
    //             {
    //                 $delete_record=BookingHold::where("id",$expired->id)->delete();
    //             }
    //             foreach($booking_services as $each)
    //             {
    //                 // adding service amount
    //                 $ser_amount=SalonServices::where("id",$each['id'])->first();
    //                 $service_amount=isset($ser_amount->amount)?$ser_amount->amount:0;

    //                 //adding service discount price
    //                 $discount   =   ServiceOffers::where("salon_id",$id)
    //                                 ->where("approved",1)
    //                                 ->where("service_id",$each['id'])
    //                                 ->get();
    //                     // dd(DB::getQueryog());
    //                 $disc_amount=$service_amount;
    //                 if(isset($discount)&& count($discount)>0)
    //                 {
    //                     $ids=[];
    //                     foreach($discount as $disc)
    //                     {
    //                         $start=new Carbon($disc->start_date);
    //                         $start_date=$start->format('Y-m-d');
    //                         $end=new Carbon($disc->end_date);
    //                         $end_date=$end->format('Y-m-d');

    //                         if($start_date<=$todate && $end_date>=$todate)
    //                         {
    //                             $ids[]=$disc->id;
    //                         }
    //                     }
    //                     if(isset($ids)&& count($ids)>0)
    //                     {
    //                         $offer=ServiceOffers::where("salon_id",$id)->where("service_id",$each['id'])->whereIn("id",$ids)->where("approved",1)->orderBy('discount_price', 'asc')->first();
    //                         $disc_amount=$offer->discount_price;

    //                     }

    //                 }

    //                 // $each=json_decode($val);
    //                  $date=new Carbon($each['date']);
    //                 $f_date=$date->format('d-m-Y');
    //                  $rstart_time=$each['start_time'].":00";
    //                 $rend_time=$each['end_time'].":00";
    //                 $insert=BookingServices::insert([
    //                                                 "booking_id"=>$add_booking,
    //                                                 "date"=>$f_date,
    //                                                 "amount"=>$service_amount,
    //                                                 "discount_price"=>$disc_amount,
    //                                                 "service_id"=>$each['id'],
    //                                                 "staff_id"=>$each['staff_id'],
    //                                                 "start_time"=>$rstart_time,
    //                                                 "end_time"=>$rend_time,
    //                                                 'created_at'=> $time,
    //                                                 "updated_at"=>$time
    //                                             ]);
    //             }

    //             $details=DB::table("booking")
    //                             ->join("booking_services", "booking_services.booking_id","=","booking.id")
    //                             ->join("booking_address", "booking_address.booking_id","=","booking.id")
    //                             ->join("salons", "salons.id","=","booking.salon_id")
    //                             ->whereNull('booking.deleted_at')
    //                             ->whereNull("booking_services.deleted_at")
    //                             ->whereNull('salons.deleted_at')
    //                             ->where('booking.user_id',$user_id)
    //                             ->where('booking.id',$add_booking)
    //                             ->select("booking.id","salons.name as salon_name","salons.pricing","booking_address.first_name","booking_address.last_name","booking_address.email","booking_address.address","booking_address.phone","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid","salons.email as salon_email")
    //                             ->first();
    //              $o_id=10000+$add_booking;
    //             $orderId="PM".$o_id;
    //             $details->orderId=$orderId;
    //              if(isset($details->pricing)&& $details->pricing!=null)
    //             {
    //                 $details->mood_commission=$details->amount_paid * ($details->pricing/100);
    //             }
    //             else
    //             {
    //                 $details->mood_commission="0.00";
    //             }

    //              $book_services= DB::table("booking_services")
    //             ->join("salon_services", "salon_services.id","=","booking_services.service_id")
    //             ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
    //             ->where("booking_services.booking_id",$add_booking)
    //             ->whereNull('booking_services.deleted_at')
    //             ->whereNull('salon_services.deleted_at')
    //             ->whereNull('salon_staffs.deleted_at')
    //             ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","booking_services.amount","booking_services.discount_price","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
    //             if(isset($book_services) && count($book_services)>0)
    //             {
    //                 foreach($book_services as $ser)
    //                 {
    //                     $ser->start_time=substr($ser->start_time, 0, -3);
    //                     $ser->end_time=substr($ser->end_time, 0, -3);
    //                 }
    //             }
    //              $details->services=$book_services;
    //              if(request()->header('User-Token'))
    //             {
    //                 $api_token=request()->header('User-Token');
    //                   if(isset($request->new_card) && $request->new_card=='true')
    //                 {
    //                     $return['error']=false;
    //                     $return['msg']="Your booking completed successfully";
    //                     $return['booking_details']=$details;
    //                     $return['is_token']=false;
    //                 }
    //                 else
    //                 {
    //                     return $this->complete_booking($details,$api_token,$first_name,$last_name, $add_booking,$email,$phone,$amount,$address,$actual_amount,$book_services);
    //                 }
    //             }
    //             else
    //             {
    //                 $return['error']=false;
    //                 $return['msg']="Your booking completed successfully";
    //                 $return['booking_details']=$details;
    //                 $return['is_token']=false;
    //             }

    //             // $return['error']=false;
    //             // $return['msg']="Your booking completed successfully";
    //             $return['booking_details']=$details;

    //         }
    //         else
    //         {
    //             $return['error']=true;
    //             $return['msg']="Sorry error occured";
    //         }
    //         // $return['timeslots']=$timeslots;
    //     }
    //     return $return;
    // }

    public function complete_booking($details,$api_token,$first_name,$last_name, $add_booking,$email,$phone,$amount,$address,$actual_amount,$book_services)
    {
        $booking_id=$add_booking;
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
         $token=PaymentToken::where("user_id",$user_id)->where("default",1)->first();
         if(!isset($token) && empty($token))
         {
            $token=PaymentToken::where("user_id",$user_id)->latest()->first();
         }

        $pt_token=isset($token->token)?$token->token:'';
        $today=Carbon::now()->format("d-m-Y");

        if($token && isset($pt_token)&& $pt_token!='')
        {
            $cs_email=isset($token->email)?$token->email:'';
            // $e_mail='abc@accept.com';
            $e_mail=$email;
            $cs_password=isset($token->password)?$token->password:'';
            $currency='AED';
            $address="Mood Dubai";
            $merchant_email=env('PAYTABS_MERCHANT_EMAIL');
            $merchant_id=env('PAYTABS_MERCHANT_ID');
            $secret_key=env('PAYTABS_SECRET_KEY');
            //check the status of the booking
             $curl = curl_init();
               $params = array(
                'merchant_email'  => $merchant_email,
                'merchant_id'   => $merchant_id,
                'secret_key' => $secret_key,
                'title'=>'Salon Booking',
                'cc_first_name'    => $first_name,
                'cc_last_name'    => $last_name,
                'order_id'    => $add_booking,
                'product_name'    => 'Salon',
                'customer_email'    => $e_mail,
                'phone_number'    => $phone,
                'amount'    => $amount,
                'currency'    => $currency,
                'address_billing'    => $address,
                'state_billing'    => 'Dubai',
                'city_billing'    => 'Dubai',
                'postal_code_shipping'    => '00971',
                'country_shipping'    => 'ARE',
                'pt_token'    => $pt_token,
                'pt_customer_email'    => $cs_email,
                'pt_customer_password'    => $cs_password,
                'billing_shipping_details'    => 'no',

            );
            curl_setopt_array($curl, array
            (
                CURLOPT_URL => 'https://www.paytabs.com/apiv3/tokenized_transaction_prepare',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_POSTFIELDS => $params,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
            ));

            $res = curl_exec($curl);
            $err = curl_error($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
             if($err)
            {
                $return["error"]=true;
                $return['is_token']=false;
                $return["msg"]="Sorry error occured";
                return $return;
            }
            else
            {
                $res=json_decode($res);
                $response=isset($res->result)?$res->result:'';
                $transaction_id=isset($res->transaction_id)?$res->transaction_id:'';
                $status_code=isset($res->response_code)?$res->response_code:'';
                $time=Carbon::now();
                $update_booking=Booking::where("id",$booking_id)->update(["status_code"=>$status_code,"response"=>$response,"transaction_id"=>$transaction_id,"updated_at"=>$time]);

                if($update_booking && $transaction_id!='')
                {
                    $codes=[112,113,115,116];
                    if($status_code==100)
                    {
                        $update_booking=Booking::where("id",$booking_id)->update(["active"=>1,"updated_at"=>$time]);
                        $terms_c=Content::where("id",3)->select("id","title","description","created_at")->first();
                        $terms=isset($terms_c->description)?$terms_c->description:'';
                             $data = ['details' => $details,'today'=>$today,"book_services"=>$book_services,"terms"=>$terms];


                         $pdf = PDF::loadView('emails.invoice', $data)->setPaper('a4');;
                         $datas=[
                                "name"=>$first_name. " " .$last_name,
                                "email"=>$email,
                                "address"=>$address,
                                "country"=>'United Arab Emirates',
                                "phone"=>$phone,
                                "salon_name"=>$details->salon_name,
                                "orderId"=>$details->orderId,
                                'billing_id'=>$add_booking,
                                'amount'=>$amount,
                                'actual_amount'=>$actual_amount,

                                 ];
                                 $salon_email=$details->salon_email;

                             $mail=Mail::send('emails.booking_invoice', ["data"=>$datas], function ($message) use ($data,$pdf,$email)
                              {
                                $message->to($email)->subject("Your invoice | Mood")
                                ->attachData($pdf->output(), "invoice.pdf");
                                });
                              $mail2=Mail::send('emails.booking_invoice_salon', ["data"=>$datas], function ($message) use ($data,$pdf,$salon_email)

                              {
                                $message->to($salon_email)->subject("You have a new booking | Mood");
                                });

                        $return['error']=false;
                        $return['msg']="Your booking is completed and updated successfully";
                        $return['is_token']=true;

                    }
                    elseif(in_array($status_code,$codes))
                    {
                        $update_booking=Booking::where("id",$booking_id)->update(["active"=>3,"updated_at"=>$time]);
                        $return['error']=false;
                        $return['msg']="Your booking is under review.";
                        $return['is_token']=true;
                    }
                    else
                    {
                        $update_booking=Booking::where("id",$booking_id)->update(["active"=>4,"updated_at"=>$time]);
                        $return['error']=true;
                        $return['msg']="Your booking is rejected. Please try again later.";
                        $return['is_token']=false;
                    }


                    $return['booking_details']=$details;
                    $return['status_code']=$status_code;
                    $return['response']=$response;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']=$response;
                    $return['booking_details']=$details;
                    $return['is_token']=false;
                }

            }
        }
        else
        {
           $return['error']=false;
            $return['msg']="Your booking completed successfully";
            $return['booking_details']=$details;
            $return['is_token']=false;
        }

       return $return;
    }

}
