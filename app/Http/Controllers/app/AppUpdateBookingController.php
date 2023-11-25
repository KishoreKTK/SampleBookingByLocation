<?php

namespace App\Http\Controllers\app;
use DB;
use Mail;
use Validator;
use App\Salons;
use App\Booking;
use Carbon\Carbon;
use App\Content;
use App\Customers;
use App\UserToken;
use App\SalonStaffs;
use App\BookingHold;
use App\WorkingHours;
use App\StaffServices;
use App\SalonServices;
use App\BookingAddress;
use App\BookingServices;
use App\BookingHoldServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppUpdateBookingController extends Controller
{

    public function review_status(Request $request)
    {
        $api_token=request()->header('User-Token');
        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
        $today=Carbon::now();
        $service_id=0;
        $reviews=[];

        $booking=DB::table("booking_services")
        ->join("booking", "booking_services.booking_id","=","booking.id")
        ->join("booking_address", "booking_address.booking_id","=","booking.id")
        ->join("salons", "salons.id","=","booking.salon_id")
        ->join("user", "user.id","=","booking.user_id")
        ->where('booking.user_id',$user_id)
        ->where('booking.cancel_review', "!=",1)
        ->whereNull('booking.deleted_at')
        ->whereNull("booking_services.deleted_at")
        ->whereNull('salons.deleted_at')
        ->orderBy("booking_services.id","asc")
         ->select("booking_services.*")
        ->get();

        if(isset($booking) && count($booking)>0)
        {
            foreach ($booking as $key => $each)
            {
                $date=new Carbon($each->date);
                if($date< $today)
                {
                    $service_id=$each->id;
                }
                # code...
            }
            if($service_id>0)
            {
                $salon=DB::table("booking_services")
                ->join("booking", "booking_services.booking_id","=","booking.id")
                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->join("user", "user.id","=","booking.user_id")
                ->where('booking.user_id',$user_id)
                ->where('booking_services.id',$service_id)
                ->whereNull('booking.deleted_at')
                ->whereNull("booking_services.deleted_at")
                ->whereNull('salons.deleted_at')
                ->orderBy("booking_services.id","asc")
                 ->select("salons.id","booking.id as booking_id","salons.name as salon_name","salons.id as salon_id","booking.user_id","booking.balance_amount","user.first_name","user.last_name","user.email","booking.active","booking.actual_amount as amount_total","booking.amount as amount_paid","booking_address.email","booking_address.first_name","booking_address.last_name","booking_address.address","booking_address.phone","booking_services.*")
                ->first();
                //check reviews
                $salon_id=isset($salon->salon_id)?$salon->salon_id:0;

                 $reviews=DB::table("salon_reviews")
                ->join("salons", "salons.id","=","salon_reviews.salon_id")
                ->whereNull('salon_reviews.deleted_at')
                ->select("salon_reviews.id","salon_reviews.salon_id","salon_reviews.rating","salon_reviews.reviews","salons.name","salons.image","salon_reviews.created_at")
                ->where("salon_reviews.user_id",$user_id)
                ->where("salon_reviews.salon_id",$salon_id)
                ->get();
                // return $reviews;
                if(count($reviews)==0)
                {
                    $return['error']=false;
                    $return['review']=true;
                    $return['msg']="Please tell us about your experience";
                    $return['booking']=$salon;
                }
                else
                {
                    $return['error']=false;
                    $return['msg']="You already added review";
                    // $return['booking']=$salon;
                    $return['review']=false;
                }
            }
            else
            {
                $return['error']=false;
                $return['msg']="You already added review";
                $return['review']=false;
            }

        }
        else
        {
            $return['error']=false;
            $return['msg']="You already added review";
            $return['review']=false;
        }

        return $return;
    }

    public function cancel_review(Request $request)
    {
        $rules=[
            "booking_id"=>"required|exists:booking_services,id,deleted_at,NULL",
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
            $time=Carbon::now();

            $services=$request->services;
            $booking_id=$request->booking_id;
            $api_token=request()->header('User-Token');
            $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            $today=Carbon::now();
            $service_id=0;
            $reviews=[];

            $booking=Booking::where("id",$booking_id)->where('user_id',$user_id)->first();

            if(isset($booking))
            {
                $update=Booking::where("id",$booking_id)->where('user_id',$user_id)->update(["cancel_review"=>1,"updated_at"=>$today]);
                if($update)
                {
                    $return['error']=false;
                    $return['msg']="Your review status updated";
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Error occured";
                }
            }
            else
            {
                $return['error']=true;
                $return['msg']="You don't have the permission to perform this task";
            }

        return $return;
        }
    }

    public function reschedule_booking(Request $request)
    {
        $rules=[
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
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
            $time=Carbon::now();

            $services=$request->services;
            $booking_id=$request->booking_id;
            $salon_id=$request->salon_id;
            foreach($services as $val)
            {
                $value=json_decode($val);
                $service_ids[]=$value->id;
                $rstart_time=$value->start_time.":00";
                $rend_time=$value->end_time.":00";

                $to_date=new Carbon($value->date);
                $today=$to_date->format("Y-m-d");
                $s_date=$today. " " .$rstart_time;
                if($s_date<=$time)
                {
                    $return['error']=true;
                    $return['msg']="Please check the time";
                    return $return;
                }
            }
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
              $check=Booking::where("id",$booking_id)->where("user_id",$user_id)->first();
            if(empty($check))
            {
                 $return['error']=true;
                $return['msg']="Sorry you have no permission to do this task";
                return $return;
            }

            $staff_ids=[];

            $timeslot=WorkingHours::where("salon_id",$salon_id);

            $booking_services=[]; $ser=[];
            foreach($services as $index=>$val)
            {
                $ser=[];
                $service=json_decode($val);

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
                    $check_price=SalonServices::where('salon_id',$salon_id)->whereIn('id', $service_ids)->sum("amount");


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
                        ->where("salon_staffs.salon_id",$salon_id)
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
                            $return['msg']="Sorry no staffs are available at this time. Please try to change the staff or date.";
                            return $return;
                        }
                        if(isset($staff_ids) && count($staff_ids)>0)
                        {
                            $staff_id=$staff_ids[0];
                        }
                         else
                        {
                            $return['error']=true;
                            $return['msg']="Sorry no staffs are available at this time. Please try to change the staff or date.";
                            return $return;
                        }
                     $service->staff_id =$staff_id;

                    }
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
                    $check_booking=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.service_id",$service_id)->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")->where("booking_services.date",$f_date)
                    ->where("booking_services.staff_id",$staff_id)
                    ->where("booking.id", "!=",$booking_id)
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
                                $return['error']=true;
                                $return['msg']="Sorry this salon is already booked. Please try to change the time or date.";
                                return $return;
                           }
                        }
                    }
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
                    $check_user_booking=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.date",$f_date)
                    ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")
                    ->where("booking.active",1)
                    ->select("booking.*","booking_services.staff_id","booking_services.date","booking_services.service_id","booking_services.start_time","booking_services.end_time")
                    ->get();
                    $check_booking_staffs=DB::table("booking")
                    ->join("booking_services", "booking_services.booking_id","=","booking.id")
                    ->where("booking_services.date",$f_date)
                    ->where("booking.user_id",$user_id)->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")
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

            $up_booking=Booking::where("id",$booking_id)->update(["updated_at"=>$time]);
            if($up_booking)
            {

                foreach($booking_services as $each)
                {
                    // $each=json_decode($val);
                     $take=BookingServices::where("booking_id",$booking_id)->where("service_id",$each['id'])->first();

                    if(isset($take))
                    {
                        $to_date=new Carbon($take->date);
                        $today=$to_date->format("Y-m-d");
                        $s_date=$today. " " .$take->start_time;
                        if($s_date>$time)
                        {
                             $date=new Carbon($each['date']);
                            $f_date=$date->format('d-m-Y');
                             $rstart_time=$each['start_time'].":00";
                            $rend_time=$each['end_time'].":00";
                            $update=BookingServices::where("booking_id",$booking_id)->where("service_id",$each['id'])->update([
                            "date"=>$f_date,
                            "staff_id"=>$each['staff_id'],
                            "start_time"=>$rstart_time,
                            "end_time"=>$rend_time,
                            "updated_at"=>$time
                            ]);
                        }
                    }
                }


                $details=DB::table("booking")
                ->join("booking_services", "booking_services.booking_id","=","booking.id")
                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->whereNull('booking.deleted_at')
                ->whereNull("booking_services.deleted_at")
                ->whereNull('salons.deleted_at')
                ->where('booking.user_id',$user_id)
                ->where('booking.id',$booking_id)
                ->select("booking.id","salons.name as salon_name","salons.pricing","booking_address.first_name","booking_address.last_name","booking_address.email","booking_address.address","booking_address.phone","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid","salons.email as salon_email")
                ->first();

                if(isset($details))
                {
                     $o_id=10000+$booking_id;
                        $orderId="PM".$o_id;
                        $details->orderId=$orderId;
                     $book_services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$booking_id)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                     if(isset($book_services) && count($book_services)>0)
                    {
                        foreach($book_services as $ser)
                        {
                            $ser->start_time=substr($ser->start_time, 0, -3);
                            $ser->end_time=substr($ser->end_time, 0, -3);
                        }
                    }

                    $terms_c=Content::where("id",3)->select("id","title","description","created_at")->first();
                    $terms=isset($terms_c->description)?$terms_c->description:'';
                     $todate=Carbon::now()->format("d-m-Y");
                             $data = ['details' => $details,'today'=>$todate,"book_services"=>$book_services,"terms"=>$terms];
                    $details->services=$book_services;

                    //send emails
                    $datas=[
                    "name"=>$details->first_name. " " .$details->last_name,
                    "email"=>$details->email,
                    "address"=>$details->address,
                    "country"=>'United Arab Emirates',
                    "phone"=>$details->phone,
                    "orderId"=>$details->orderId,
                    "salon_name"=>$details->salon_name,
                    'billing_id'=>$booking_id,
                    'amount'=>$details->amount_paid,
                    'actual_amount'=>$details->amount_total,

                     ];
                     $email=$details->email;
                     $salon_email=$details->salon_email;
                 $mail=Mail::send('emails.booking_updated', ["data"=>$datas], function ($message) use ($data,$email)

                  {
                    $message->to($email)->subject("Your booking has been updated | Mood");
                    });
                  $mail2=Mail::send('emails.booking_updated_salon', ["data"=>$datas], function ($message) use ($data,$salon_email)

                  {
                    $message->to($salon_email)->subject("Your booking has been updated | Mood");
                    });

                    //send emails
                }

                $return['error']=false;
                $return['msg']="Your booking updated successfully";
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

    public function cancel_booking(Request $request)
    {
        $rules=[
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
            ];
        $msg=[
            "booking_id.required"=>"ID is required",
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
            $time=Carbon::now();
            $booking_id=$request->booking_id;
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
            $check=Booking::where("id",$booking_id)->where("user_id",$user_id)->first();
            if(empty($check))
            {
                 $return['error']=true;
                $return['msg']="Sorry you have no permission to do this task";
                return $return;
            }
             $take=BookingServices::where("booking_id",$booking_id)->get();

            if(isset($take) && count($take)>0)
            {
               foreach($take as $ser)
                {
                    //checking whether the booking  is past
                    $to_date=new Carbon($ser->date);
                    $today=$to_date->format("Y-m-d");
                    $s_date=$today. " " .$ser->start_time;
                    if($s_date<=$time)
                    {
                        $return['error']=true;
                        $return['msg']="Sorry you couldn't cancel this order since this is already completed";
                        return $return;
                    }
                }

            }
            //check booking_status
            $check_book=Booking::where("id",$booking_id)->where("active",2)->first();
            if(isset($check_book))
            {
                $return['error']=true;
                $return['msg']="You already cancelled this booking";
                return $return;
            }
            $can_booking=Booking::where("id",$booking_id)->update(["active"=>2,"updated_at"=>$time]);

            if($can_booking)
            {

                $details=DB::table("booking")
                ->join("booking_services", "booking_services.booking_id","=","booking.id")
                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->whereNull('booking.deleted_at')
                ->whereNull("booking_services.deleted_at")
                ->whereNull('salons.deleted_at')
                ->where('booking.user_id',$user_id)
                ->where('booking.id',$booking_id)
                  // ->select("booking.*","salons.name as salon_name","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid")
                   ->select("booking.id","salons.name as salon_name","salons.pricing","booking_address.first_name","booking_address.last_name","booking_address.email","booking_address.address","booking_address.phone","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid","salons.email as salon_email","booking.transaction_id")
                ->first();
                 $o_id=10000+$booking_id;
                        $orderId="PM".$o_id;
                        $details->orderId=$orderId;
                 $book_services= DB::table("booking_services")
                ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                ->where("booking_services.booking_id",$booking_id)
                ->whereNull('booking_services.deleted_at')
                ->whereNull('salon_services.deleted_at')
                ->whereNull('salon_staffs.deleted_at')
                ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                 if(isset($book_services) && count($book_services)>0)
                {
                    foreach($book_services as $ser)
                    {
                        $ser->start_time=substr($ser->start_time, 0, -3);
                        $ser->end_time=substr($ser->end_time, 0, -3);
                    }
                }
                $reason=isset($request->reason)?$request->reason:'Request for Cancellation';
                $details->services=$book_services;
                $transaction_id=isset($details->transaction_id)?$details->transaction_id:'';
                if(isset($transaction_id)&& $transaction_id!='')
                {
                     $merchant_email=env('PAYTABS_MERCHANT_EMAIL');
                    $merchant_id=env('PAYTABS_MERCHANT_ID');
                    //check the status of the booking
                     $curl = curl_init();
                       $params = array(
                        'merchant_email'  => $merchant_email,
                        'merchant_id'   => $merchant_id,
                        'secret_key' => env('PAYTABS_SECRET_KEY'),
                        'merchant_id'    => $merchant_id,
                        'transaction_id'    => $transaction_id,
                        'refund_amount'    => $details->amount_paid,
                        'refund_reason'    => $reason,
                        'order_id'    => $details->id,
                    );
                    curl_setopt_array($curl, array
                    (
                        CURLOPT_URL => 'https://www.paytabs.com/apiv2/refund_process',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_POSTFIELDS => $params,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                    ));

                    $resp = curl_exec($curl);
                        $result_res=$resp;

                    $err = curl_error($curl);
                    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                     if($err)
                    {
                        $return["error"]=true;
                        $return["response"]=$err;
                        $return["msg"]="Sorry error occured";
                        return $return;
                    }

                    else
                    {
                        $res=json_decode($resp);
                        $result_res='';
                        $terms_c=Content::where("id",3)->select("id","title","description","created_at")->first();
                    $terms=isset($terms_c->description)?$terms_c->description:'';
                     $todate=Carbon::now()->format("d-m-Y");
                             $data = ['details' => $details,'today'=>$todate,"book_services"=>$book_services,"terms"=>$terms];
                        $datas=[
                                "name"=>$details->first_name. " " .$details->last_name,
                                "email"=>$details->email,
                                "address"=>$details->address,
                                "country"=>'United Arab Emirates',
                                "phone"=>$details->phone,
                                "salon_name"=>$details->salon_name,
                                'billing_id'=>$booking_id,
                                "orderId"=>$orderId,
                                'amount'=>$details->amount_paid,
                                'actual_amount'=>$details->amount_total,

                                 ];
                                 $email=$details->email;
                                 $salon_email=$details->salon_email;
                             $mail=Mail::send('emails.booking_cancel', ["data"=>$datas], function ($message) use ($data,$email)

                              {
                                $message->to($email)->subject("Your booking cancelled | Mood");
                                });
                              $mail2=Mail::send('emails.booking_cancel_salon', ["data"=>$datas], function ($message) use ($data,$salon_email)

                              {
                                $message->to($salon_email)->subject("Your booking cancelled | Mood");
                                });

                        if(isset($res->refund_request_id)&& $res->refund_request_id!='')
                        {
                            $result_res=isset($res->result)?$res->result:'No response from gateway';
                            $up_booking=Booking::where("id",$booking_id)->update(["refund_response"=>$result_res,"refund_id"=>$res->refund_request_id,"updated_at"=>$time]);
                              $return['error']=false;
                            $return['msg']="Your booking cancelled and refund initiated";
                            $return['booking_details']=$details;
                            $return['response']=$resp;
                            return $return;
                        }
                        else
                        {
                             $up_booking=Booking::where("id",$booking_id)->update(["refund_response"=>$result_res,"updated_at"=>$time]);
                             $return['error']=false;
                            $return['msg']="Your booking cancelled successfully";
                            $return['booking_details']=$details;
                            $return['response']=$resp;
                            return $return;
                        }
                            $return['error']=false;
                            $return['response']=$resp;
                            return $return;
                    }
                }


                $return['error']=false;
                $return['msg']="Your booking cancelled successfully";
                $return['booking_details']=$details;
                            return $return;


            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry error occured";
            }

        }
        return $return;
    }
}
