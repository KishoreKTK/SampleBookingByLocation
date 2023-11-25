<?php

namespace App\Http\Controllers;

use App\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Datetime;
use Exception;
use App\UserToken;
use App\Customers;
use App\Http\Controllers\Notification\PushNotificationController;
use App\Http\Traits\BookingTrait;
use App\Http\Traits\PaymentTrait;
use App\SalonReviews;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{

    use BookingTrait, PaymentTrait;

//=========================================================================================//
//======================= Major Functionalities in Web ====================================//
//=========================================================================================//

    public function SlotsBookedList(){
        $activePage="Block Slot";
        return view('admin.BookSlots.list',compact('activePage'));
    }

    public function CreateNewBookingSlotPage() {
        $categorylist       =   DB::table('categories')->select('id','category')->get();
        $customers          =   DB::table('user')->select('id','first_name')->where('active',1)->get();
        //get todays timeslot
        // $salon_list     =   DB::table('salons')->select('id','name')
        //                     ->where('approved',1)->where('active',1)->get();
        return view('admin.BookSlots.AddBooking',compact('categorylist','customers'));
    }

    // Does Booking Here
    // public function MakeBooking()
    // {
    //     try
    //     {
    //         $input          =   request()->all();
    //         $booking_date   =   $input["selected_date"];
    //         $salon_id       =   $input["selected_salon"];
    //         $start_time     =   $input["booking_start_time"];
    //         $end_time       =   $input["booking_end_time"];
    //         $services       =   $input['selected_services'];
    //         $customer_id    =   $input['customer_id'];
    //         $address_id     =   $input['address_id'];
    //         $selected_staff =   $input['selected_staffs'];
    //         $promo_code     =   10;
    //         $service_ids    =   [];
    //         $ServiceNeedsMultipleStaffs = [];

    //         // Gets All Service ID as Array
    //         foreach ($services as $key => $service)
    //         {
    //             // Passing Service Id as New Array
    //             $service_ids[]  =   $service["service_id"];
    //             if($service["service_type"]  == 2 )
    //             {
    //                 $ServiceNeedsMultipleStaffs[] = $service["service_id"];
    //             }
    //         }




    //         // Checks Customer has Another Booking on Same Date and Time
    //         $check_customer =   $this->CheckCustomerAvailability($customer_id,$booking_date,$start_time,$end_time);
    //         if($check_customer['status'] == false){
    //             throw new \Exception($check_customer['message']);
    //         }


    //         // Check Booking Amount & Promocode & Mood Comission
    //         // $check_booking_cost         =   $this->CheckCostofBooking($salon_id,$service_ids,$ServiceNeedsMultipleStaffs,$total_amount,$promo_code);
    //         // if($check_booking_cost['status'] == false){
    //         //     throw new \Exception($check_booking_cost['message']);
    //         // }

    //         // Insert In Booking and Booking Related Tables
    //         $make_booking       =   $this->InsertNewBookingRecord($input);


    //         if($make_booking['status'] == false)
    //         {
    //             throw new Exception($make_booking['message']);
    //         }

    //         // $payment_amount     =   $this->checkPaymentAmount($input);
    //         $payment_amount     =   140;
    //         $do_payment         =   $this->CheckToken_DoPayment($input['token'], $payment_amount);

    //         if($do_payment['status'] == false)
    //         {
    //             throw new Exception($make_booking['message']);
    //         }

    //         $result =   ['status'=>true, 'message'=> $make_booking['message']];
    //     }
    //     catch (\Exception $e)
    //     {
    //         $result = ['status'=>false, "message"=>$e->getMessage()];
    //     }
    //     return response()->json($result);
    // }


    // Get Salons Under Category
    public function GetSalonsUnderCategory()
    {
        try
        {
            $get_salon  =   DB::table('salons')->select('salons.id','salons.name')
                            ->join('salon_categories','salons.id','=','salon_categories.salon_id')
                            ->where('salon_categories.category_id',request()->categoryId)
                            ->groupby('salons.id')->get();

            if(count($get_salon) > 0) {
                $result = ['status'=>true, "data"=>$get_salon];
            }
            else{
                throw new \Exception("No Salon Under this Category");
            }
        } catch (\Exception $e) {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }

    // Get Services of Selected Salon
    public function SelectSalonServices()
    {
        try
        {
            $get_services  =    DB::table('salon_services')
                                ->select('salon_services.id','salon_services.service',
                                        'salon_services.salon_id','salon_services.category_id',
                                        'salon_services.time','salon_services.amount')
                                ->where('salon_services.approved','1')
                                ->where('salon_services.salon_id',request()->salonId)
                                ->get();

            if(count($get_services) > 0)
            {
                $dt 			= new DateTime();
                $date		    = $dt->format('d-m-Y');
                $time_slots     = $this->TimeFrame($date,request()->salonId);
                $result = ['status'=>true, "data"=>$get_services,'timeslots'=>$time_slots['timeslots']];
            }
            else {
                throw new \Exception("No Services Under this Salon");
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }

    // Get Working Hours of salon from Date
    public function SelctedServiceDate()
    {
        try
        {
            $time_slots = $this->TimeFrame(request()->selected_date,request()->selected_salon);
            $result = ['status'=>true, "timeslots"=>$time_slots['timeslots']];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }

    // Get Customer Address
    public function GetCustomerAddress()
    {
        try
        {
            $customer_id    =   request()->customer_id;

            $get_address    =   DB::table('user_address')
                                ->select('id','address','default_addr')
                                ->where('user_id',$customer_id)->get();
            if(count($get_address) > 0){
                $result = ['status'=>true, "data"=>$get_address];
            }else{
                throw new Exception("No Address found for User");
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }






//=========================================================================================//
//======================= Functionalities in API ====================================//
//=========================================================================================//

    public function is_future_date($date)
    {
        try
        {

            $result =   ['status'=>true, 'message'=>"Yes, It is Future Date"];
        }catch(Exception $e){
            $result =   ['status'=>false, 'message'=>"Please Select Future Date"];
        }
        return $result;
    }

    public function GetTimeFrame()
    {
        try
        {
            $input              =   request()->all();

            if(request()->has('selected_date') && request()->selected_date != '') {
                $booking_date   =   $input["selected_date"];
                if( strtotime($booking_date) > strtotime('now') ){
                    $is_today       =   false;
                } else {
                    $is_today       =   true;
                }
            } else {
                $booking_date   =   date('d-m-Y');
                $is_today       =   true;
            }

            $salon_id           =   $input["selected_salon"];
            $services           =   $input['selected_services'];
            $service_ids        =   [];
            $ServiceNeedsMultipleStaffs =   [];

            // Services Has Staffs as Array
            $service_staff_arr      =   [];

            foreach ($services as $key => $service)
            {
                $service_ids[]  =   $service["service_id"];
                if($service["service_type"]  == 2 )
                {
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }
            }

            $service_id_check   =   $this->ServiceIdBelongsToSalon($service_ids,$salon_id);
            if($service_id_check['status']    ==  false) {
                throw new \Exception($service_id_check['message']);
            }

            // Checks All Service Has Available Staffs
            $check_service_has_staffs   =   $this->CheckAllServicesHasStaffs($services, $salon_id);

            // dd($check_service_has_staffs);
            if($check_service_has_staffs['status']  ==  true)
            {
                $staff_ids                  =   $check_service_has_staffs['staff_ids'];
                $filtered_staffs            =   $staff_ids;

                // Check staffs by Leave or Not on Booking Date
                $get_staff_on_leave             =   $this->CheckStaffLeaveOrNot($booking_date,$salon_id,$staff_ids);

                if(!empty($get_staff_on_leave))
                {
                    foreach($get_staff_on_leave as $key=>$staff_n_leave)
                    {
                        if (in_array($staff_n_leave, $filtered_staffs))
                        {
                            unset($filtered_staffs[$key]);
                        }
                    }
                }

                if(count($filtered_staffs) == 0){
                    throw new Exception('No Staffs Available on that Day');
                }

                // Services Contains Staffs
                foreach($service_ids as $service_id){
                    $staff_service  =   DB::table('staff_services')
                                        ->where('service_id',$services)
                                        ->pluck('staff_id')->toArray();

                    $service_staff_arr[$service_id]['staffs_can_do'] =   $staff_service;

                    if (in_array($service_id, $ServiceNeedsMultipleStaffs)){
                        $service_staff_arr[$service_id]['staffs_needed']    =   2;
                    } else {
                        $service_staff_arr[$service_id]['staffs_needed']    =   1;
                    }
                }


                // get booking time of staffs on booking day
                $bookings_on_that_day   =   DB::table('booking_services')
                                            ->leftJoin('booking','booking.id',
                                            'booking_services.booking_id')
                                            ->select('staffs','start_time','end_time')
                                            ->where('booking.salon_id',$salon_id)
                                            ->groupBy('booking_services.booking_id')
                                            ->where('date',$booking_date)->get();


                $time_slots_to_remove   =   [];
                foreach($bookings_on_that_day as $staff_booking)
                {
                    $staffs_str     =   $staff_booking->staffs;
                    $staffs_arr     =   explode(',',$staffs_str);

                    $check_filtered_staffs_booking    =   array_intersect($staffs_arr,$filtered_staffs);

                    if(!empty($check_filtered_staffs_booking))
                    {

                        foreach($service_staff_arr as $service_id=>$service_det)
                        {
                            $temp_staff_can_do_service          =   $service_det['staffs_can_do'];
                            $no_of_staffs_needed                =   $service_det['staffs_needed'];
                            $staff_currently_working            =   array_intersect($temp_staff_can_do_service,$staffs_arr);

                            foreach($staff_currently_working as $working_staffs)
                            {
                                if(($key = array_search($working_staffs, $temp_staff_can_do_service)) != false)
                                {
                                    unset($temp_staff_can_do_service[$key]);
                                }
                            }
                            $duration                   =   30;
                            // Romove those times From Time Frame
                            // dd($temp_staff_can_do_service);
                            if($no_of_staffs_needed     ==  2   &&  count($temp_staff_can_do_service)>1){
                                $booking_timings =   $this->SplitTime($staff_booking->start_time, $staff_booking->end_time, $duration);
                                // dd($booking_timings);
                                // $removable_timings[] = array_merge($time_slots_to_remove, $booking_timings);
                                array_push($time_slots_to_remove, $booking_timings);

                                // $time_slots_to_remove[] =   array_intersect($time_differeces,$booking_timings);
                            }

                            if($no_of_staffs_needed     ==  1 && count($temp_staff_can_do_service)>0){
                                $booking_timings  =   $this->SplitTime($staff_booking->start_time, $staff_booking->end_time, $duration);
                                // dd($booking_timings);
                                // $removable_timings[] = array_merge($time_slots_to_remove, $booking_timings);
                                array_push($time_slots_to_remove, $booking_timings);
                                // $time_slots_to_remove[] =   array_intersect($time_differeces,$booking_timings);
                            }
                            // $time_slots_to_remove[] =   $booking_timings;
                        }
                    }
                }

                // salon working time
                $time_slots         =   $this->TimeFrame($booking_date,$salon_id);
                // dd($time_slots);

                // Salon Timings in 30 Minutes Interval as Array
                $time_differeces        =   $time_slots['timedurations'];

                // Removes First Element of Array
                array_shift($time_differeces);

                // Removes Last Element of Array
                array_pop($time_differeces);

                $salon_starting_time    =   $time_differeces[0];
                $salon_ending_time      =   end($time_differeces);
                // dd($time_slots_to_remove);
                if($is_today == true){
                    $current_time = date("h:i:sa");
                    if(strtotime($salon_ending_time) >= strtotime($current_time)+300 ){
                        $duration   =   30;
                        $salon_ending_time      	= strtotime ($salon_ending_time);
                        $times_to_remove_today    = $this->SplitTime($salon_starting_time, $current_time, $duration);
                        if(count($times_to_remove_today) > 0){
                            $time_differeces = array_diff($time_differeces,$times_to_remove_today);
                        }
                    } else {
                        throw new Exception("Salon Closed. Select Future Date to Continue Booking");
                    }

                    // if($salon_ending_time)
                    // $arrayA = array('one','two');

                    // $arrayB = array('one','two','three','four','five');



                    // $newArray = array_diff($arrayB,$arrayA);
                    // // dd($times_to_remove_today);
                }
                // dd($time_slots_to_remove);
                $times_to_send  =   [];

                // dd($time_slots_to_remove);
                if(count($time_slots_to_remove) > 0)
                {
                    $removable_times    =   [];
                    foreach($time_slots_to_remove as $timings)
                    {
                        foreach($timings as $t){
                            $removable_times[]  = $t;
                        }
                    }

                    array_unique($removable_times);

                    // dd($removable_times);
                    foreach($removable_times as $rem_timings)
                    {
                        // dd($rem_timings);
                        if(($key = array_search($rem_timings, $time_differeces)) != false)
                        {
                            // dd($key);
                            unset($time_differeces[$key]);
                        }
                    }

                    foreach ($time_differeces as $key => $value) {
                        $times_to_send[]   =   ['time'=>$value, 'booked'=>false];
                    }
                }
                else
                {
                    foreach ($time_differeces as $key => $value){
                        $times_to_send[]   =   ['time'=>$value, 'booked'=>false];
                    }
                }




                // array_shift($time_duration);
                // array_pop($time_duration);

                // foreach ($time_duration as $key => $value) {
                //     $time_intervals2[]   =   ['time'=>$value, 'booked'=>false];
                // }

                $result     =   [
                                    'status'            =>  true,
                                    // 'service_id'        =>  $service_ids,
                                    "time_duration"    =>  $times_to_send,
                                    // 'removable_times'   =>  $removable_times,
                                    "message"           =>  "Time Frame Listed Successfully"
                                ];
            }
            else
            {
                if ($check_service_has_staffs["service_with_zero_staff"])
                {
                    $result     =   [
                                        'status'    =>  false,
                                        'service_id'=>  $check_service_has_staffs['service_with_zero_staff'],
                                        'message'   =>  $check_service_has_staffs['message']
                                    ];
                }
                else
                {
                    throw new \Exception($check_service_has_staffs['message']);
                }
            }


            // $result =   ['status'=>true, 'message'=>"Time Frame Works Fine"];
        } catch (\Exception $e) {
            $result =   ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    // Check Availability of Staff & Service by Date & Time
    public function CheckAvailability()
    {
        try
        {
            $input          =   request()->all();
            $booking_date   =   $input["selected_date"];
            $salon_id       =   $input["selected_salon"];
            $start_time     =   $input["booking_start_time"];
            $end_time       =   $input["booking_end_time"];
            $services       =   $input['selected_services'];
            $service_ids    =   [];

            // dd($input);

            // Gets All Service ID as Array
            foreach ($services as $key => $service)
            {
                // Passing Service Id as New Array
                $service_ids[]  =   $service["service_id"];
                if($service["service_type"]  == 2 )
                {
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }
            }

            $service_id_check       =   $this->ServiceIdBelongsToSalon($service_ids,$salon_id);
            if($service_id_check['status']    ==  false){
                throw new \Exception($service_id_check['message']);
            }

            // Checks All Service Has Available Staffs
            $check_service_has_staffs               =   $this->CheckAllServicesHasStaffs($services, $salon_id);

            // dd($check_service_has_staffs);
            if($check_service_has_staffs['status']  ==  true)
            {
                $staff_ids                          =   $check_service_has_staffs['staff_ids'];

                // Checks & Selects Staff to Send
                $selected_staff_for_booking         =   $this->SelectStafftoSend($salon_id, $services, $staff_ids, $booking_date, $start_time, $end_time);
                // print("<pre>");print_r($selected_staff_for_booking);die;
                $result     =   [
                                    'status'            =>  true,
                                    'service_id'        =>  $service_ids,
                                    "selected_staffs"   =>  $selected_staff_for_booking,
                                    "message"           =>  "You Can Continue Your Booking."
                                ];
            }
            else
            {
                if ($check_service_has_staffs["service_with_zero_staff"])
                {
                    $result     =   [
                                        'status'    =>  false,
                                        'service_id'=>  $check_service_has_staffs['service_with_zero_staff'],
                                        'message'   =>  $check_service_has_staffs['message']
                                    ];
                }
                else
                {
                    throw new \Exception($check_service_has_staffs['message']);
                }
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }

        // dd($result);
        return response()->json($result);
    }

    // Does Booking Here
    public function MakeBookingApi()
    {
        try
        {
            $input          =   request()->all();
            $booking_date   =   $input["selected_date"];
            $salon_id       =   $input["selected_salon"];
            $start_time     =   $input["booking_start_time"];
            $end_time       =   $input["booking_end_time"];
            $services       =   $input['selected_services'];

            $promo_code     =   10;
            $service_ids    =   [];
            $ServiceNeedsMultipleStaffs = [];

            if(!request()->filled('card_token')){
                throw new Exception("Please Check Payment Details");
            }


            // Gets All Service ID as Array
            foreach ($services as $key => $service)
            {
                // Passing Service Id as New Array
                $service_ids[]  =   $service["service_id"];
                if($service["service_type"]  == 2 )
                {
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }
            }

            // Check All Services Ids Provided
            $service_id_check       =   $this->ServiceIdBelongsToSalon($service_ids,$salon_id);

            // dd($service_id_check);
            if($service_id_check['status']    ==  false){
                throw new \Exception($service_id_check['message']);
            }

            // Checks All Service Has Available Staffs
            $check_service_has_staffs               =   $this->CheckAllServicesHasStaffs($services,$salon_id);
            // dd($check_service_has_staffs);
            if($check_service_has_staffs['status']  ==  true)
            {

                $staff_ids                          =   $check_service_has_staffs['staff_ids'];

                // dd($staff_ids);
                // Checks & Selects Staff to Send

                $selected_staff_for_booking         =   $this->SelectStafftoSend($salon_id, $services, $staff_ids, $booking_date, $start_time, $end_time);
                // print("<pre>");print_r($selected_staff_for_booking);die;

                $selected_staff_string      =   implode(',',$selected_staff_for_booking);
                $input['selected_staffs']   =   $selected_staff_for_booking;
                $input['staffs_string']     =   $selected_staff_string;
                // dd($input['selected_staffs']);
                if(request()->header('User-Token'))
                {
                    $api_token  =   request()->header('User-Token');
                    $user       =   UserToken::where("api_token",$api_token)->first();
                    if(isset($user)&& isset($user->user_id))
                    {
                        $customer_id    =   $user->user_id;
                    }
                    else
                    {
                        throw new Exception("Api Token Expired");
                    }

                    $VerifyCustAddrId = DB::table('user_address')
                                        ->where('id',request()->address_id)
                                        ->where('user_id',$customer_id)->first();

                    if(!$VerifyCustAddrId) {
                        throw new Exception("Please Select Verify Address Id");
                    }
                    $customer       =   Customers::where("id",$customer_id)
                                        ->select("id","first_name","last_name","phone","address","email")
                                        ->first();
                    $customer_email =   isset($customer->email)?$customer->email:'';
                }
                else
                {
                    $customer_id    =   0;
                    if(request()->email=='' || request()->first_name=='' || request()->phone=='')
                    {
                       throw new Exception("Please provide your email, first name and phone number");
                    }
                    $customer_email =  request()->email;
                }

                $input['customer_id']   =   $customer_id;

                // Checks Customer has Another Booking on Same Date and Time

                // $check_customer =   $this->CheckCustomerAvailability($customer_email,$booking_date,$start_time,$end_time);
                // if($check_customer['status'] == false){
                //     throw new \Exception($check_customer['message']);
                // }


                // dd($check_customer);
                if(request()->has('spl_request') && request()->spl_request != ''){
                    $spl_request    =   request()->spl_request;
                } else {
                    $spl_request    =   null;
                }
                $input['spl_request']   =   $spl_request;

                $total_amount   =  $input['totalamount'];
                // dd("am i coming upto here");
                // // Update Booking Services

                // Check Booking Amount & Promocode & Mood Comission
                // $check_booking_cost         =   $this->CheckCostofBooking($salon_id,$service_ids,$ServiceNeedsMultipleStaffs,$total_amount,$promo_code);
                // if($check_booking_cost['status'] == false){
                //     throw new \Exception($check_booking_cost['message']);
                // }
                // dd($input);

                // Insert In Booking and Booking Related Tables
                $make_booking       =   $this->InsertNewBookingRecord($input);

                if($make_booking['status'] == false)
                {
                    throw new Exception($make_booking['message']);
                }

                $do_payment         =   $this->CheckToken_DoPayment($input['card_token'], $total_amount);
                // dd($do_payment);
                if($do_payment['status'] == false)
                {
                    throw new Exception($do_payment['message']);
                }
                $salon_det = DB::table('salons')->where('id',$salon_id)->first();
                $notification_data =    [
                    'type'=>'2' ,
                    "redirect_id"=>$make_booking['booking_id'],
                    "data"=> [
                        "Booking_id"=>$make_booking['booking_id'],
                        "ShopTitle"=>$salon_det->name,
                        "ShopLocation"=>$salon_det->location,
                        "BookingDate"=> $booking_date,
                        "BookingTime"=> $start_time. " - " .$end_time
                    ],
                    'delivery_area' =>request()->delivery_area_coords
                ];
                PushNotificationController::sendWebNotification($notification_data);

                $result =   ['status'=>true, 'message'=> $make_booking['message']];
            }
            else
            {
                if ($check_service_has_staffs["service_with_zero_staff"])
                {
                    $result     =   [
                                        'status'    =>  false,
                                        'service_id'=>  $check_service_has_staffs['service_with_zero_staff'],
                                        'message'   =>  $check_service_has_staffs['message']
                                    ];
                }
                else
                {
                    throw new \Exception($check_service_has_staffs['message']);
                }
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function EditBooking()
    {
        try
        {
            //code...
            $input              =   request()->all();

            // Check Booking ID
            $booking_id         =   $input["booking_id"];
            $check_booking_id   =   DB::table('booking')->where('id',$booking_id)->first();
            if(!$check_booking_id){
                throw new Exception("Please check the Booking ID you have provided");
            }

            if(request()->selected_date != '' && request()->booking_start_time != '' && request()->booking_end_time != '')
            {
                $booking_date   =   $input["selected_date"];
                $salon_id       =   $check_booking_id->salon_id;
                $start_time     =   $input["booking_start_time"];
                $end_time       =   $input["booking_end_time"];

                $booking_service_data   =   DB::table('booking_services')
                                            ->where('booking_id',$booking_id)
                                            ->first();

                $old_booking_date       =   $booking_service_data->date;
                $old_starting_time      =   $booking_service_data->start_time;

                // dd($old_booking_date);
                // add 30 mins to starting time
                if($booking_date == $old_booking_date){
                    dd("Same date");
                } else {
                    dd("Different date");
                    // Check Time
                }

            } else {
                throw new Exception("Booking Date and Time Required");
            }

            if(request()->has('spl_request') && request()->spl_request != ''){
                $spl_request    =   request()->spl_request;
            }

            $booking_services   =   DB::table('booking_services')
                                    ->where('booking_id',$booking_id)->get();
            $service_details    =   [];
            $service_arr        =   [];

            foreach($booking_services as $key=>$bk_det)
            {
                if (in_array($bk_det->service_id, $service_arr) == false)
                {
                    $service_arr[]  =  $bk_det->service_id;
                    $service_details[$key]['service_id']    =   $bk_det->service_id;
                    $service_details[$key]['guest_count']   =   $bk_det->guest_count;
                    $service_details[$key]['service_type']  =   $bk_det->service_type;
                }
            }


            $services       =   $service_details;

            $service_ids    =   [];
            $ServiceNeedsMultipleStaffs = [];

            $staff_ids                          =   $check_service_has_staffs['staff_ids'];

            // Checks & Selects Staff to Send
            $selected_staff_for_booking         =   $this->SelectStafftoSend($salon_id, $services, $staff_ids, $booking_date, $start_time, $end_time);

            $selected_staff_string      =   implode(',',$selected_staff_for_booking);
            $input['selected_staffs']   =   $selected_staff_for_booking;
            $input['staffs_string']     =   $selected_staff_string;

            $result =   ['status'=>true,    'message'=>"Booking Upadted Successfully"];
        }
        catch (\Exception $e) {
            $result =   ['status'=>false,   'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function CancelBooking()
    {
        try {
            if(request()->filled('booking_id')){
                $booking_id         =   request()->booking_id;
                $check_booking_data =   DB::table('booking')->where('booking.id',$booking_id)
                                        ->Join('booking_services','booking_services.booking_id','=','booking.id')
                                        ->select('booking.id','booking_services.date','booking_services.start_time',
                                        'booking.amount','booking.active')
                                        ->first();

                if(!$check_booking_data) {
                    throw new Exception("Please Check the Booking Id");
                }

                if($check_booking_data->active == '2') {
                    throw new Exception("Booking Already Cancelled");
                }

                $current_date       =   new DateTime();
                $cancellation_date  =   $current_date->format('d-m-Y');
                $booking_date       =   $check_booking_data->date;

                if($cancellation_date == $booking_date) {
                    $cancellation_charges   =   0;
                } else {
                    $cancellation_charges   =   0;
                }

                $amount         =   $check_booking_data->amount;
                $repay_amount   =   $amount - $cancellation_charges;






                $update_data    =   DB::table('booking')->where('id',$booking_id)
                                    ->update(['active'=>2,'updated_at'=> date('Y-m-d H:i:s')]);

                if($update_data) {
                    $result         =   ['status'=>true, 'message'=> "Booking Cancelled Successfully"];
                } else {
                    throw new Exception("Something Went Wrong");
                }
            } else {
                throw new Exception("Booking Id Required");
            }
        } catch (\Exception $e) {
            $result     = ['status'=>false, 'message'=> $e->getMessage()];
        }
        return response()->json($result);
    }

    public function MyBookings()
    {
        try {
            if(!request()->header('User-Token'))
            {
                throw new Exception("Api Token Expired");
            }
            $api_token  =   request()->header('User-Token');
            $user_id    =   UserToken::where("api_token",$api_token)->first()->user_id;
            $Booking        =   DB::table('booking')->select('booking.id as bookid','booking.user_id',
                                'booking.salon_id','booking.amount as totalamount',
                                'booking.active','salons.name as salon_name','booking.created_at')
                                ->join('salons','booking.salon_id','=','salons.id')
                                ->where('user_id',$user_id)->latest();
            if(request()->has('booking_Id')){
                $booking_id = request()->booking_Id;
                if(!Booking::find($booking_id)){
                    throw new Exception("Please Check Booking Id");
                }
                $get_booking    =   $Booking->where('booking.id',$booking_id)->get();
            }else{
                $get_booking    =   $Booking->get();
            }
            $MyBookings  =   [];
            foreach ($get_booking as $key => $bookingdata) {
                $MyBookings[$key]['booking_Id']     =   $bookingdata->bookid;
                $MyBookings[$key]['user_id']        =   $bookingdata->user_id;
                $MyBookings[$key]['salon_id']       =   $bookingdata->salon_id;
                $MyBookings[$key]['salon_details']  =   DB::table('salons')->where('id',$bookingdata->salon_id)->first();
                $MyBookings[$key]['salon_details']->salonimage  =   env("IMAGE_URL")."salons/".$MyBookings[$key]['salon_details']->image;
                $MyBookings[$key]['salon_reviews']  =   DB::table('salon_reviews')->where('salon_id',$bookingdata->salon_id)
                ->leftJoin('user','user.id','=','salon_reviews.user_id')->get();
                $reviews                =DB::table("salon_reviews")
                                        ->join("user", "user.id","=","salon_reviews.user_id")
                                        ->where("salon_id",$bookingdata->salon_id)
                                        ->whereNull("salon_reviews.deleted_at")
                                        ->whereNotNull("reviews")
                                        ->select("salon_reviews.*","user.first_name","user.last_name")->get();
                $rating_count       =   SalonReviews::where("salon_id",$bookingdata->salon_id)->get()->count();
                $review_count       =   SalonReviews::where("salon_id",$bookingdata->salon_id)->whereNotNull("reviews")->get()->count();

                $MyBookings[$key]['reviews']=$reviews;
                $MyBookings[$key]['rating_count']=$rating_count;
                $MyBookings[$key]['review_count']=$review_count;
                if(isset($reviews)&&count($reviews)>0)
                {
                    $rating=0;
                    foreach($reviews as $review)
                    {
                        $rating=$rating+$review->rating;
                    }
                    $overall=$rating/$review_count;
                    if($overall>=4.5)
                    {
                        $overall=5;
                    }
                    elseif($overall>=4 && $overall<4.5)
                    {
                        $overall=4.5;
                    }
                    elseif($overall>=3.5 && $overall<4)
                    {
                        $overall=4;
                    }
                    elseif($overall>=3 && $overall<3.5)
                    {
                        $overall=3.5;
                    }
                    elseif($overall>=2.5 && $overall<3)
                    {
                        $overall=3;
                    }
                    elseif($overall>=2 && $overall<2.5)
                    {
                        $overall=2.5;
                    }
                    elseif($overall>=1.5 && $overall<2)
                    {
                        $overall=2;
                    }
                    elseif($overall>=1 && $overall<1.5)
                    {
                        $overall=1.5;
                    }
                    elseif($overall>=0.5 && $overall<1)
                    {
                        $overall=1;
                    }
                    elseif($overall>=0 && $overall<0.5)
                    {
                        $overall=0.5;
                    }
                    else
                    {
                        $overall=0;
                    }
                    // $salon->overall_rating=$overall;
                // $salon->overall_rating=round($overall, 2);

                }
                else
                {
                    $MyBookings[$key]['rating']="No ratings yet";
                    $overall=0;
                }

                $MyBookings[$key]['overall_rating']   = strval($overall);

                $MyBookings[$key]['salon_name']     =   $bookingdata->salon_name;
                $MyBookings[$key]['totalamount']    =   $bookingdata->totalamount;
                $MyBookings[$key]['status_id']      =   $bookingdata->active;
            if($bookingdata->active == 1)
            {
                $MyBookings[$key]['status']         =   "Success";
            }
            elseif($bookingdata->active == 2)
            {
                $MyBookings[$key]['status']         =   "Cancelled";
            }
            elseif($bookingdata->active == 3)
            {
                $MyBookings[$key]['status']         =   "Completed";
            }
            elseif($bookingdata->active == 4)
            {
                $MyBookings[$key]['status']         =   "ReScheduled";
            }
            else {
                $MyBookings[$key]['status']         =   "Pending";
            }
                $booking_service_details        =   DB::table('booking_services')->select('booking_services.date',
                                                    'booking_services.start_time','booking_services.end_time',
                                                    'booking_services.guest_count','booking_services.service_type',
                                                    'booking_services.staff_id','booking_services.service_id',
                                                    'salon_services.service as service_name','salon_services.time as service_time',
                                                    'salon_services.amount')
                                                    ->leftJoin('salon_services','booking_services.service_id','=','salon_services.id')
                                                    ->where('booking_id',$bookingdata->bookid)->get();
                foreach ($booking_service_details as $e => $det) {
                    $MyBookings[$key]['date']           =   $det->date;
                    $booking_time                       =   $this->RemoveTimeBeforeAndAfterDuration($det->start_time,$det->end_time);

                    // $test_arr = ['start_time'=>$det->start_time,
                    //             'end_time'=>$det->end_time,
                    //                 'removed_str_time'=>$booking_time['start_time'], 'removed_end_time'=>$booking_time['end_time']];
                    // dd($test_arr);
                    $MyBookings[$key]['staff_start_time']     =   $det->start_time;
                    $MyBookings[$key]['staff_end_time']       =   $det->end_time;
                    $MyBookings[$key]['start_time']     =   $booking_time['start_time'];
                    $MyBookings[$key]['end_time']       =   $booking_time['end_time'];
                    // $BookDtTime     = $det->date.' '.$det->end_time;
                    $dt_status      =  $this->CheckDateTimeGreaterThanNow($det->date, $det->end_time);
                    if($dt_status != true){
                        $MyBookings[$key]['booking_dt_status']     =   "Upcoming";
                    } else {
                        $MyBookings[$key]['booking_dt_status']     =   "Past";
                    }
                }
                // foreach
                $MyBookings[$key]['booking_service_details']    =   $booking_service_details;
                $MyBookings[$key]['booking_address']            =   DB::table('booking_address')
                                                                    ->select('first_name','email','phone','address')
                                                                    ->where('booking_id',$bookingdata->bookid)->first();
            }
            $result = ['status'=>true, 'data'=>$MyBookings];

        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

}
