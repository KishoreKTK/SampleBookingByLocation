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
use App\Http\Traits\BookingCheckTrait;
use App\Http\Traits\BookingTrait;
use App\Http\Traits\PaymentTrait;
use App\SalonReviews;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class BookingController extends Controller
{

    use BookingTrait, PaymentTrait, BookingCheckTrait;

    // Booking Status
    // Active Status - 0-Pending; 1-Active; 2-Cancel


    // Time Frame
    public function GetTimeFrame()
    {
        try
        {
            // ------------------------------------------------------------------------------------//
            /*  Step - 1
                --------
                * Validates Date Here. if date is given directly shows that particular day time frame
                  else auto assigns as today to show timeframe

                * Shows Timeframes only for today and future dates.
            */
            // ------------------------------------------------------------------------------------//
            $input                  =   request()->all();
            $strdate                =   strtotime($input["selected_date"]);
            $newformat              =   date('d-m-Y',$strdate);
            $input["selected_date"] =   $newformat;

            if(request()->has('selected_date') && request()->selected_date != '')
            {
                $booking_date       =   $input["selected_date"];
                if( strtotime($booking_date) > strtotime('now') ) {
                    $is_today       =   false;
                } else {
                    $is_today       =   true;
                }
            } else {
                $booking_date       =   date('d-m-Y');
                $is_today           =   true;
            }

            $CheckBookingDate       =   $this->GreaterThanToday($booking_date);
            if($CheckBookingDate['status']== false) {
                throw new Exception($CheckBookingDate['message']);
            }

            $salon_id               =   $input["selected_salon"];
            $services               =   $input['selected_services'];
            $service_ids            =   [];
            $ServiceNeedsMultipleStaffs =   [];



            // ------------------------------------------------------------------------------------//
            /* Step - 2
               ---------
               * Checks all services Crt or Not
               * Gets Staffs for all the services
            */
            // ------------------------------------------------------------------------------------//

            foreach ($services as $key => $service)
            {
                $service_ids[]      =   $service["service_id"];
                if($service["service_type"]  == 2 )
                {
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }
            }

            $service_id_check                   =   $this->ServiceIdBelongsToSalon($service_ids,$salon_id);
            if($service_id_check['status']      ==  false) {
                throw new \Exception($service_id_check['message']);
            }

            // Checks All Service Has Available Staffs
            $check_service_has_staffs           =   $this->CheckAllServicesHasStaffs($services, $salon_id);

            // ------------------------------------------------------------------------------------//
            /* Step - 3 (if available)
               --------
               * Checks Staff leave or not and assigns as $filtered_staffs
               * Checks User Requirements and Assigns Staffs for the Services as $service_staff_arr
            */
            // ------------------------------------------------------------------------------------//

            if($check_service_has_staffs['status']  ==  true)
            {
                $staff_ids                  =   $check_service_has_staffs['staff_ids'];

                $filtered_staffs            =   $staff_ids;
                $service_staff_count        =   $check_service_has_staffs['service_staff_count'];


                // Check staffs by Leave or Not on Booking Date
                $get_staff_on_leave             =   $this->CheckStaffLeaveOrNot($booking_date,$salon_id,$staff_ids);
                // dd($get_staff_on_leave);
                if(!empty($get_staff_on_leave))
                {
                    foreach($get_staff_on_leave as $testkey=>$id)
                    {
                        if(in_array($id,$filtered_staffs)){
                            $first_key = array_search($id,$filtered_staffs);
                            unset($filtered_staffs[$first_key]);
                        }
                    }
                    if(count($filtered_staffs) == 0){
                        throw new Exception('No Staffs Available on that Day');
                    }
                }

                // Services Contains Staffs
                $service_staff_arr      =   [];
                foreach($service_ids as $service_id)
                {
                    $staff_service  =   DB::table('staff_services')
                                        ->where('service_id',$service_id)
                                        ->pluck('staff_id')->toArray();
                    $service_staff_arr[$service_id]['service_name'] =   DB::table('salon_services')->where('id',$service_id)->first()->service;
                    $service_staff_arr[$service_id]['staffs_can_do'] =   $staff_service;

                    if (in_array($service_id, $ServiceNeedsMultipleStaffs)){
                        $service_staff_arr[$service_id]['staffs_needed']    =   2;
                    } else {
                        $service_staff_arr[$service_id]['staffs_needed']    =   1;
                    }
                    $no_of_staffs_needed    = $service_staff_arr[$service_id]['staffs_needed'];
                    if($no_of_staffs_needed == 2 && count($filtered_staffs) < 2) {
                        throw new Exception("Only One Staff Available for the service ".$service_staff_arr[$service_id]['service_name']." on this Day");
                    }
                }


                // Declared Array
                $time_slots_to_remove   =   [];

                // get booking time of staffs on booking day
                $booking_timings_of_staffs  = [];


                /* ------------------------------------------------------------------------------------
                    Step - 4
                    --------
                    * First Loop Runs For Every Booking.
                    * Inner Loop Runs for Every Services of the Customers
                    * Assigns Empty TimetoRemove array and pushes times to array to
                    remove times from the list
                ------------------------------------------------------------------------------------ */

                // Booking Staffs on that Day
                $bookings_on_that_day   =   DB::table('booking_services')
                                            ->leftJoin('booking','booking.id',
                                            'booking_services.booking_id')
                                            ->select('staffs','bookstrttime','bookendtime')
                                            ->where('booking.salon_id',$salon_id)
                                            ->where('booking.active',1)
                                            ->where('booking.block',0)
                                            ->WhereNull('booking.deleted_at')
                                            ->groupBy('booking_services.booking_id')
                                            ->where('date',$booking_date)->get();

                if(count($bookings_on_that_day) > 0)
                {
                    $duration   =   30;
                    foreach($bookings_on_that_day as $staff_booking)
                    {
                        $staffs_str         =   $staff_booking->staffs;
                        $staffs_arr         =   explode(',',$staffs_str);
                        foreach($staffs_arr as $staff_id)
                        {
                            $booking_timings =   $this->SplitTime($staff_booking->bookstrttime, $staff_booking->bookendtime, $duration);
                            $booking_timings_of_staffs[$staff_id][]   =   $booking_timings;
                        }
                    }
                }

                /* ------------------------------------------------------------------------------------
                    Step - 5

                    * Checks Blocked Slots and Removes Staffs from Blocked Slot Times.

                ------------------------------------------------------------------------------------ */

                // Check Booking Blocks On that Day
                $check_blocked_bookings     =   DB::table('booking')
                                                ->where('booking.salon_id',$salon_id)
                                                ->where('block',1)->where('active','1')
                                                ->where('bookdate',$booking_date)
                                                ->whereNull('deleted_at')
                                                ->pluck('id')->toArray();
                // dd($check_blocked_bookings);

                if(count($check_blocked_bookings) > 0)
                {
                    foreach($check_blocked_bookings as $key=>$bkng_id)
                    {
                        $blocked_staffs_n_times =   DB::table('booking_services')
                                                    ->select('staff_id','date','start_time','end_time')
                                                    ->where('date',$booking_date)
                                                    ->where('booking_id',$bkng_id)->get();
                        if(count($blocked_staffs_n_times) > 0)
                        {
                            $booked_slots       =   [];
                            $staff_booking      =   [];

                            foreach($blocked_staffs_n_times as $blocked_slots) {
                                $start_time     = str_replace(':00:00',':00',str_replace(':30:00',':30',$blocked_slots->start_time));
                                $end_time       = str_replace(':00:00',':00',str_replace(':30:00',':30',$blocked_slots->end_time));
                                array_push($staff_booking,$start_time);
                                array_push($staff_booking,$end_time);

                                $booked_slots[$blocked_slots->staff_id] = $staff_booking;
                                $booking_timings_of_staffs[$blocked_slots->staff_id][]   = $staff_booking;
                            }
                        }
                    }
                }



                $requested_services =   $service_ids;
                $filtered_staffs_to_send        =   $this->GetStaffWithServices($filtered_staffs);
                // $service_staff_count
                $filtered_services = [];
                foreach ($filtered_staffs_to_send as $staff => $service)
                {
                    $service_arr                            =   explode(",",$service);
                    foreach($service_arr as $arr){
                        $filtered_services[]    = $arr;
                    }
                    $staff_service_count[$staff]            =   count($service_arr);
                    $filtererd_staff_with_service[$staff]   =   $service_arr;
                }

                $check_missing_services_from_filter = array_diff($requested_services,$filtered_services);
                if($check_missing_services_from_filter != null){
                    $service_names = [];
                    foreach($check_missing_services_from_filter as $servic_id){
                        $service_name  =    DB::table('salon_services')
                                            ->where('id',$servic_id)->first()->service;
                        $service_names[]    =   $service_name;
                    }
                    $service_name_str = implode(',',$service_names);
                    throw new Exception("Service $service_name_str don't have staffs on this day. Remove and Continue");
                }


                // dd($booking_timings_of_staffs);
                if(count($booking_timings_of_staffs) > 0)
                {
                    $staff_timing_array =   [];
                    $booked_staffs  =    [];
                    foreach($booking_timings_of_staffs as $staff_id=>$staff_timings){
                        $data = call_user_func_array('array_merge', $staff_timings);
                        $staff_timing_array[$staff_id]  =  array_unique($data);
                        $booked_staffs[]  =    $staff_id;
                    }

                    // dd($staff_timing_array);
                    // dd($filtered_staffs);
                    $booked_staffs          = array_unique($booked_staffs);
                    $get_remaingin_stafs    = array_diff($filtered_staffs,$booked_staffs);
                    $matching_staffs        = array_intersect($filtered_staffs,$booked_staffs);

                    if(count($get_remaingin_stafs) == 0){
                        $staffs_needed = array_intersect_key($staff_timing_array, array_flip($filtered_staffs));
                        $common_times_to_remove     = call_user_func_array('array_intersect', $staffs_needed);
                        if(count($common_times_to_remove) > 0){
                            array_push($time_slots_to_remove, $common_times_to_remove);
                        }
                    }
                    if(count($get_remaingin_stafs) == 1 && count($ServiceNeedsMultipleStaffs) > 0)
                    {
                        if(count($matching_staffs)>0)
                        {
                            $staffs_needed = array_intersect_key($staff_timing_array, array_flip($matching_staffs));
                            $common_times_to_remove     = call_user_func_array('array_intersect', $staffs_needed);
                            if(count($common_times_to_remove) > 0){
                                array_push($time_slots_to_remove, $common_times_to_remove);
                            }
                        }

                        if(count($matching_staffs)>0)
                        {
                            // dd($matching_staffs);
                            $staffs_needed = array_intersect_key($staff_timing_array, array_flip($matching_staffs));
                            if(count($matching_staffs) > 1)
                            {
                                if(count($staffs_needed) > 1)
                                {
                                    $common_times_to_remove     = call_user_func_array('array_intersect', $staffs_needed);
                                    if(count($common_times_to_remove) > 0){
                                        array_push($time_slots_to_remove, $common_times_to_remove);
                                    }

                                    if(count($ServiceNeedsMultipleStaffs) > 0 && count($matching_staffs) < 2)
                                    {
                                        $common_times_to_remove     = call_user_func_array('array_intersect', $staff_timing_array);
                                        if(count($common_times_to_remove) > 0)
                                        {
                                            array_push($time_slots_to_remove, $common_times_to_remove);
                                        }
                                    }
                                }
                            } else {
                                reset($staffs_needed);
                                $first_key = key($staffs_needed);
                                array_push($time_slots_to_remove, $staffs_needed[$first_key]);
                            }
                        }
                    }
                }

                // ------------------------------------------------------------------------------------//
                /* Step - 6
                    --------
                    * Gets Working Time of Salon.
                    * If date is today remove times before current time.
                    * if remove_time_slot exists it removes those times from list.
                    * Assigns every time as $time_to_send array.
                */
                // ------------------------------------------------------------------------------------//

                // salon working time
                $time_slots         =   $this->TimeFrame($booking_date,$salon_id);
                // Salon Timings in 30 Minutes Interval as Array
                $time_differeces    =   $time_slots['timedurations'];

                $first_time_val =   strtotime($time_differeces['0']);
                $last_time_val  =   strtotime(end($time_differeces));

                // Removes First Element of Array
                array_shift($time_differeces);

                // Removes Last Element of Array
                array_pop($time_differeces);

                $salon_starting_time    =   $time_differeces[0];
                $salon_ending_time      =   end($time_differeces);

                if($is_today == true)
                {
                    $current_time = date("h:i:sa");
                    if(strtotime($salon_ending_time) >= strtotime($current_time)+300 )
                    {
                        $duration   =   30;
                        $salon_ending_time      	= strtotime ($salon_ending_time);
                        $times_to_remove_today    = $this->SplitTime($salon_starting_time, $current_time, $duration);
                        if(count($times_to_remove_today) > 0)
                        {
                            $time_differeces = array_diff($time_differeces,$times_to_remove_today);
                        }
                    } else {
                        throw new Exception("Salon Closed. Select Future Date to Continue Booking");
                    }
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
                            $removable_times[]  = strtotime($t);
                        }
                    }

                    $rm_times = array_unique($removable_times);

                    // $arr_2 =
                    $convert_times_as_int = [];
                    foreach($time_differeces as $t){
                        array_push($convert_times_as_int,strtotime($t));
                    }

                    $array_differences = array_diff($convert_times_as_int, $rm_times);

                    $remainingtimes_to_show = [];
                    foreach($array_differences as $t){
                        $remainingtimes_to_show[]   =   date("H:i", $t);
                    }
                    // if Booking Contains First Value and Last Value it removes


                    // foreach($array_differences as $key=>$times){
                    //     $check_first_val_key = array_search($times, $array_differences);
                    //     if($check_first_val_key == true)
                    //     {
                    //         unset($time_differeces[$check_first_val_key]);
                    //     }
                    // }



                    // foreach($rm_times as $rem_timings)
                    // {
                    //     // dd($rem_timings);
                    //     $array_key = array_search($rem_timings, $time_differeces);
                    //     // dd($array_key);
                    //     if($array_key == True)
                    //     {
                    //     // if($key = array_search($rem_timings, $time_differeces) != false)
                    //     // {
                    //         unset($time_differeces[$key]);
                    //     }
                    // }

                    // dd($time_differeces);
                    foreach ($remainingtimes_to_show as $key => $value) {
                        $times_to_send[]   =   ['time'=>$value, 'booked'=>false];
                    }
                }
                else
                {
                    foreach ($time_differeces as $key => $value){
                        $times_to_send[]   =   ['time'=>$value, 'booked'=>false];
                    }
                }



                /* ------------------------------------------------------------------------------------
                    Step - 7 (optional logic if this time frame should be shown in edit booking
                            page means this logic gets executed. based on booking id)
                    --------

                    * In the Above Steps All the Booked Slots will be removed but in this logic
                      only the particular booking timings only will be shown.
                ------------------------------------------------------------------------------------ */

                if(request()->has('my_booking_id') && request()->my_booking_id != '')
                {
                    $booking_id     =   request()->my_booking_id;
                    $booking_det    =   DB::table('booking')->where('id',$booking_id)
                                        ->where('block',0)->where('active',1)->first();
                    if(!$booking_det){
                        throw new Exception("Please Check Booking Id");
                    }
                    $my_booking_date        =   $booking_det->bookdate;
                    $my_booking_start_time  =   $booking_det->bookstrttime;
                    $my_booking_end_time    =   $booking_det->bookendtime;
                    if(strtotime($my_booking_date) == strtotime($booking_date))
                    {
                        $first_key  =   0;
                        $duration   =   30;
                        $split_my_booking_time    = $this->SplitTime($my_booking_start_time, $my_booking_end_time, $duration);
                        end($split_my_booking_time);
                        $last_key           = key($split_my_booking_time);
                        $last_before_key    = $last_key - 1;
                        $times_to_show_only_times   =   [];
                        foreach($times_to_send as $values) {
                            array_push($times_to_show_only_times,$values['time']);
                        }

                        foreach ($split_my_booking_time as $key => $times)
                        {
                            /* Check this time exists in Existing Times to Send Array
                                if exists Just chnage booked status to true
                                add time and booked status
                            */
                            $array_key = array_search($times, $times_to_show_only_times);
                            // dd($array_key);
                            if($array_key === FALSE)
                            {
                                if($key == $first_key){
                                    $times_to_send[]   =   ['time'=>$times, 'booked'=>false];
                                } else if($key == $last_before_key){
                                    $times_to_send[]   =   ['time'=>$times, 'booked'=>false];
                                } else if($key == $last_key){
                                    $times_to_send[]   =   ['time'=>$times, 'booked'=>false];
                                } else {
                                    $times_to_send[]   =   ['time'=>$times, 'booked'=>true];
                                }
                            }
                            else
                            {
                                if($key == $first_key){
                                    $times_to_send[$array_key]['booked']  = false;
                                } else if($key == $last_before_key){
                                    $times_to_send[$array_key]['booked']  = false;
                                } else if($key == $last_key){
                                    $times_to_send[$array_key]['booked']  = false;
                                } else {
                                    $times_to_send[$array_key]['booked']  = true;
                                }
                            }
                        }

                        $get_filtered_times_only = [];
                        foreach($times_to_send as $values) {
                            array_push($get_filtered_times_only,$values['time']);
                        }

                        $convert_times_to_int = [];
                        foreach($get_filtered_times_only as $t){
                            array_push($convert_times_to_int,strtotime($t));
                        }
                        // if Booking Contains First Value and Last Value it removes

                        $check_first_val_key = array_search($first_time_val, $convert_times_to_int);
                        if($check_first_val_key == true)
                        {
                            unset($times_to_send[$check_first_val_key]);
                        }

                        $check_last_time_val_key = array_search($last_time_val, $convert_times_to_int);
                        if($check_last_time_val_key == true)
                        {
                            unset($times_to_send[$check_last_time_val_key]);
                        }

                        // Sort the array
                        usort($times_to_send, array($this, "date_compare"));
                    }
                }

                $result     =   [
                                    'status'            =>  true,
                                    "time_duration"    =>  $times_to_send,
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
            $ServiceNeedsMultipleStaffs =   [];
            // if(request()->header('User-Token'))
            // {
                // $api_token  =   request()->header('User-Token');
                // $user       =   UserToken::where("api_token",$api_token)->first();

                // if(isset($user)&& isset($user->user_id))
                // {
                //     $customer_id    =   $user->user_id;
                // }
                // else
                // {
                //     throw new Exception("API token Expired");
                // }
                // $user_email     =   DB::table('user')->where('id',$customer_id)->first()->email;
                // $customer       =   Customers::where("email",$user_email)
                //                     ->where('active',1)->where('suspend',0)->first();
                // if(!$customer){
                //     throw new Exception("User is InActive or Suspended");
                // }
            // } else {
            //     throw new Exception("Please Login to Continue");
            // }

            if(request()->filled('selected_date'))
            {
                $CheckBookingDate = $this->CheckValidBookingDate($booking_date);
                if($CheckBookingDate['status']== false) {
                    throw new Exception($CheckBookingDate['message']);
                }
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

            $service_id_check       =   $this->ServiceIdBelongsToSalon($service_ids,$salon_id);
            if($service_id_check['status']    ==  false){
                throw new \Exception($service_id_check['message']);
            }
            // Checks All Service Has Available Staffs
            $check_service_has_staffs               =   $this->CheckAllServicesHasStaffs($services, $salon_id);

            if($check_service_has_staffs['status']  ==  true)
            {
                $staff_ids                          =   $check_service_has_staffs['staff_ids'];

                // Checks & Selects Staff to Send
                $staffs_to_send         =   $this->SelectStafftoSend($salon_id, $services, $staff_ids, $booking_date, $start_time, $end_time);
                // dd($staffs_to_send);
                if($staffs_to_send['status'] == false){
                    throw new Exception($staffs_to_send['message']);
                }
                $selected_staff_for_booking   = [];
                foreach($staffs_to_send['data'] as $staffs){
                    if($staffs != null){
                        $selected_staff_for_booking[]   =   $staffs;
                    }
                }

                if(count($ServiceNeedsMultipleStaffs) > 0 && count($selected_staff_for_booking) < 2){
                    throw new Exception("There is Only One Staff Available at the Moment");
                }
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
        return response()->json($result);
    }

    // Does Booking Here
    public function MakeBookingApi()
    {
        try
        {
            $input          =   request()->all();
            $strdate        =   strtotime($input["selected_date"]);
            $newformat      =   date('d-m-Y',$strdate);
            $input["selected_date"] =   $newformat;

            $booking_date   =   $input["selected_date"];
            $salon_id       =   $input["selected_salon"];
            $start_time     =   $input["booking_start_time"];
            $end_time       =   $input["booking_end_time"];
            $services       =   $input['selected_services'];
            $service_ids    =   [];

            $ServiceNeedsMultipleStaffs = [];

            // Card Token Check
            if(!request()->filled('card_token')){
                throw new Exception("Please Check Payment Details");
            }

            if(request()->filled('selected_date'))
            {
                $CheckBookingDate = $this->CheckValidBookingDate($booking_date);
                if($CheckBookingDate['status']== false){
                    throw new Exception($CheckBookingDate['message']);
                }
            }

            if(request()->header('User-Token'))
            {
                $api_token  =   request()->header('User-Token');
                $user       =   UserToken::where("api_token",$api_token)->first();

                if(isset($user)&& isset($user->user_id))
                {
                    $customer_id    =   $user->user_id;
                    $fcm            =   $user->fcm;
                    $device         =   $user->device;
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
                                    ->where('active',1)->where('suspend',0)->first();
                if(!$customer){
                    throw new Exception("User is InActive or Suspended");
                }
                $customer_email =   isset($customer->email)?$customer->email:'';
            }
            else
            {
                $customer_id    =   0;
                $fcm            =   null;
                $device         =   null;

                if(request()->email=='' || request()->first_name=='' || request()->phone=='')
                {
                   throw new Exception("Please provide your email, first name and phone number");
                }
                $customer_email =  request()->email;
            }

            $user_id                =   $customer_id;
            $input['customer_id']   =   $customer_id;
            $input['fcm']           =   $fcm;
            $input['device']        =   $device;

            // $booking_user_id        =   $customer_id;
            if(request()->has('spl_request') && request()->spl_request != ''){
                $spl_request    =   request()->spl_request;
            } else {
                $spl_request    =   null;
            }

            $input['spl_request']   =   $spl_request;
            $total_amount   =  $input['totalamount'];

            $min_bking_amt  =   DB::table('salons')->where('id',$salon_id)->first()->minimum_order_amt;
            if($total_amount < $min_bking_amt){
                throw new Exception("Minimum Booking Amout for this Salon is ". $min_bking_amt);
            }

            // if(request()->has('promocode') && request()->promocode != null){
            //     $promocode    =   request()->promocode;
            // } else {
            //     $promocode    =   null;
            // }

            if(request()->has('promocode') && request()->promocode != null){
                $promocode          =   request()->promocode;
                $verifypromocode    =   $this->VerifyPromocode($promocode,$user_id);
                if($verifypromocode['status'] == false){
                    throw new Exception($verifypromocode['message']);
                }
            } else {
                $promocode    =   null;
            }
            $input['promocode']      =   $promocode;

            if(!(request()->filled('payment_type'))){
                throw new Exception("Payment Method Required");
            }

            if(!(request()->filled('from_save_card'))){
                throw new Exception("Card Type Required");
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

            if($service_id_check['status']    ==  false){
                throw new \Exception($service_id_check['message']);
            }

            // Checks All Service Has Available Staffs
            $check_service_has_staffs               =   $this->CheckAllServicesHasStaffs($services,$salon_id);

            // dd($check_service_has_staffs);
            if($check_service_has_staffs['status']  ==  true)
            {
                $staff_ids                          =   $check_service_has_staffs['staff_ids'];

                $selected_staffs          =   $this->SelectStafftoSend($salon_id, $services, $staff_ids, $booking_date, $start_time, $end_time);
                if($selected_staffs['status'] == false){
                    throw new Exception($selected_staffs['message']);
                }
                $selected_staff_for_booking =   [];
                foreach($selected_staffs['data'] as $staffs){
                    if($staffs != null){
                        $selected_staff_for_booking[]   =   $staffs;
                    }
                }

                if(count($ServiceNeedsMultipleStaffs) > 0 && count($selected_staff_for_booking) < 2){
                    throw new Exception("There is Only One Staff Available at the Moment");
                }

                $selected_staff_string              =   implode(',',$selected_staff_for_booking);
                $input['selected_staffs']           =   $selected_staff_for_booking;
                $input['staffs_string']             =   $selected_staff_string;

                // dd($selected_staff_for_booking);
                // Checks Customer has Another Booking on Same Date and Time
                // $check_customer =   $this->CheckCustomerAvailability($customer_email,$booking_date,$start_time,$end_time);
                // if($check_customer['status'] == false){
                //     throw new \Exception($check_customer['message']);
                // }

                // dd("am i coming upto here");
                // // Update Booking Services

                // Check Booking Amount & Promocode & Mood Comission
                // $check_booking_cost         =   $this->CheckCostofBooking($salon_id,$service_ids,$ServiceNeedsMultipleStaffs,$total_amount,$promo_code);
                // if($check_booking_cost['status'] == false){
                //     throw new \Exception($check_booking_cost['message']);
                // }


                $payment_type       = $input['payment_type'];

                //From Save Card Status 0 - false; 1- true
                if(request()->has('from_save_card') && request()->from_save_card != 0){
                    $from_save_card     = true;
                } else {
                    $from_save_card     = false;
                }

                $cardtoken          = $input['card_token'];

                $do_payment         =   $this->CheckToken_DoPayment($customer_email,$payment_type, $from_save_card, $cardtoken, $total_amount);

                if($do_payment['status'] == false)
                {
                    throw new Exception($do_payment['message']);
                }
                $input['txn_customer_id']   =   $do_payment['customer_id'];
                $input['txn_charge_id']     =   $do_payment['charge_id'];

                // Insert In Booking and Booking Related Tables
                $make_booking       =   $this->InsertNewBookingRecord($input);
                if($make_booking['status'] == false)
                {
                    throw new Exception($make_booking['message']);
                }

                // if PromoCode Applied
                if(request()->has('promocode') && request()->promocode != null){
                    $promocode          =   request()->promocode;
                    $update_promocode   =   $this->PromocodeUsed($promocode,$user_id,$make_booking['booking_id']);
                }

                // Notification Portion
                $salon_det          =   DB::table('salons')->where('id',$salon_id)->first();
                $notification_data  =   [
                                            'type'=>'2' ,
                                            'booked_user_id'   =>  $user_id,
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

    // Edit Booking
    public function EditBooking()
    {
        try
        {
            //code...
            $input              =   request()->all();
            $update_booking_data=  [];

            $booking_id         =   $input["booking_id"];
            $check_booking_id   =   DB::table('booking')->where('id',$booking_id)->first();

            if(!$check_booking_id){
                throw new Exception("Please check the Booking ID you have provided");
            }

            if(request()->has('selected_date') && request()->selected_date != '')
            {
                $salon_id                       =   $check_booking_id->salon_id;
                $new_booking_date               =   $input["selected_date"];
                $current_booking_date           =   $check_booking_id->bookdate;

                if($current_booking_date)
                $CheckBookingDate = $this->CheckValidBookingDate($current_booking_date);
                if($CheckBookingDate['status']== false) {
                    throw new Exception("Please Select Upcoming Booking to Update");
                }

                if(request()->booking_start_time != '' && request()->booking_end_time != '')
                {
                    $bkedstart_time         =   $input["booking_start_time"];
                    $bkedend_time           =   $input["booking_end_time"];
                    $new_time               =   $this->AddTimeBeforeAndAfterDuration($bkedstart_time,$bkedend_time);
                    $new_start_time         =   $new_time['start_time'];
                    $new_end_time           =   $new_time['end_time'];
                } else {
                    throw new Exception("Booking Time Required");
                }
                if(strtotime($new_booking_date) ==  strtotime($current_booking_date))
                {
                    $existing_start_time    =   $check_booking_id->bookstrttime;
                    $existing_end_time      =   $check_booking_id->bookendtime;

                    // Check new time or not.
                    if(strtotime($new_start_time) != strtotime($existing_start_time)){

                        $duration               =   30;

                        $new_booking_times      =   $this->SplitTime(strtotime($new_start_time), strtotime($new_end_time), $duration);
                        $existing_booking_diff  =   $this->SplitTime(strtotime($existing_start_time), strtotime($existing_end_time), $duration);


                    }
                } else {
                    $CheckBookingDate = $this->CheckValidBookingDate($new_booking_date);
                    if($CheckBookingDate['status']== false) {
                        throw new Exception($CheckBookingDate['message']);
                    }

                    $get_assinged_staffs        =   $check_booking_id->staffs;
                    $staffs_assigned            =   explode(',',$get_assinged_staffs);
                    $staffs_on_leave            =   DB::table('staff_holidays')
                                                    ->select('staff_id')
                                                    ->join('salon_staffs','staff_holidays.staff_id','salon_staffs.id')
                                                    ->where('staff_holidays.salon_id',$salon_id)
                                                    ->where('staff_holidays.date',$new_booking_date)
                                                    ->whereNull('staff_holidays.deleted_at')
                                                    ->whereNull('salon_staffs.deleted_at')
                                                    ->whereIn('staff_id',$staffs_assigned)
                                                    ->get();
                    if(count($staffs_on_leave) > 0){
                        // {
                        //     "selected_date": "01-02-2022",
                        //     "selected_salon": "1",
                        //     "selected_services": [
                        //         {
                        //             "service_id": 22,
                        //             "service_guest": "2",
                        //             "service_type": 1
                        //         },
                        //         {
                        //             "service_id": 1,
                        //             "service_guest": "2",
                        //             "service_type": 1
                        //         }
                        //     ],
                        //     "booking_start_time": "16:30",
                        //     "booking_end_time": "17:00"
                        // }

                        // Search for New Staffs and assign new staffs for this booking
                    } else {

                        // Check Booking on that day for our staffs. if no Booking then assign these staffs
                        $salon_has_booking_on_date  =   DB::table('booking')
                                                        ->select('bookstrttime as active_start_time',
                                                                'bookendtime as active_end_time',
                                                                'booking.staffs')
                                                        ->where('bookdate',$new_booking_date)
                                                        ->where('salon_id',$salon_id)
                                                        ->where('booking.active','1')
                                                        ->where('booking.block','0')
                                                        ->get();
                        // if(count($salon_has_booking_on_date) > 0){
                        //     $check_staffs
                        // } else {
                            $update_booking_data['bookdate']     =   $new_booking_date;
                            $update_booking_data['bookstrttime'] =   $new_start_time;
                            $update_booking_data['bookendtime']  =   $new_end_time;
                        // }
                    }
                }

                $booking_service_update = [ 'date'=>$new_booking_date, 'start_time'=>$new_start_time,'end_time'=>$new_end_time];
                $booking_service_data   =   DB::table('booking_services')
                                            ->where('booking_id',$booking_id)
                                            ->update($booking_service_update);

                // if(!$booking_service_data){
                //     throw new Exception("Unable to Update your Booking");
                // }
            }

            if(count($update_booking_data) > 0){
                $update_booking_data['updated_at']  = date('Y-m-d H:i:s');
                $update =   DB::table('booking')->where('id',$booking_id)->update($update_booking_data);
                if(!$update){
                    throw new Exception("Unable to Your Update Booking");
                }
                $result =   ['status'=>true,    'message'=>"Booking Upadted Successfully"];
            } else {
                throw new Exception("Nothing to Update");
            }
        }
        catch (\Exception $e) {
            $result =   ['status'=>false,   'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    // Cancel Booking
    public function CancelBooking()
    {
        try {
            if(request()->filled('booking_id'))
            {
                $booking_id         =   request()->booking_id;
                $check_booking_data =   DB::table('booking')->where('booking.id',$booking_id)
                                        ->Join('booking_services','booking_services.booking_id','=','booking.id')
                                        ->select('booking.id','booking_services.date','booking_services.start_time',
                                        'booking.amount','booking.active','booking.transaction_id')->where('booking.block',0)
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
                $charge_id          =   $check_booking_data->transaction_id;
                $amount             =   $check_booking_data->amount;


                $cancel_date    =   strtotime($cancellation_date);
                $bked_date      =   strtotime($booking_date);

                if($cancel_date > $bked_date) {
                    throw new Exception("Booking Can't be cancelled");
                } else if($cancel_date == $bked_date) {
                    $booking_start_time =   $check_booking_data->start_time;
                    $strt_time          =   strtotime($booking_start_time);
                    $removed_time       =   $strt_time - (2 * 60 * 60);
                    $two_hr_of_start_tm =   date("H:i:s", $removed_time);
                    $two_hr_before      =   strtotime($two_hr_of_start_tm);
                    $current_time       =   strtotime(date('H:i:s'));
                    if($current_time > $strt_time){
                        throw new Exception("Booking Can not be Cancelled");
                    } else if($current_time>$two_hr_before && $current_time < $strt_time) {
                        $cancellation_charges   =   $amount * (50/100);
                    }
                } else {
                    $cancellation_charges   =   0;
                }

                $repay_amount   =   $amount - $cancellation_charges;

                // dd(['amount'=>$amount,'repay_amt'=>$repay_amount]);
                $return_n_refund =  $this->RefundBookingPayment($charge_id,$repay_amount);
                if($return_n_refund['status'] == false){
                    throw new Exception("Payment Already Returned");
                }
                $update_data    =   DB::table('booking')->where('id',$booking_id)
                                    ->update(['refund_id'=>$return_n_refund['refund_id'],'active'=>2,'updated_at'=> date('Y-m-d H:i:s')]);

                if($update_data) {
                    $result         =   ['status'=>true, 'message'=> "Booking Cancelled Successfully"];
                } else {
                    throw new Exception("Something Went Wrong");
                }
            } else {
                throw new Exception("Booking Id Required");
            }
        } catch (\Exception $e) {
            $result     = ['status'=>false, 'message'=>  $e->getMessage()];
        }
        return response()->json($result);
    }


    public function AddBookingReview(){
        try
        {
            $api_token  =   request()->header('User-Token');
            $user_token    =   UserToken::where("api_token",$api_token)->first();

            if(!$user_token)
            {
                throw new Exception("Please Login to Continue");
            }
            $user_id        =   $user_token->user_id;
            $today          =   date('d-m-Y');
            $bookingdata    =   DB::table('booking')->select('id','bookdate','special_requests'
                                ,'salon_id','user_id','bookstrttime','bookendtime')
                                ->where('user_id',$user_id)
                                ->where('bookdate','<=',$today)
                                ->where('booking_review',0)
                                ->where('booking.active',1)
                                ->where('booking.block',0)
                                ->latest()->first();

            if($bookingdata)
            {
                $review_must_done = false;
                $booking_reviews = [];
                $booked_date = $bookingdata->bookdate;
                $today          =   date('d-m-Y');
                if(strtotime($booked_date) < strtotime($today))
                {
                    $review_must_done = true;
                }
                if(strtotime($booked_date) == strtotime($today))
                {
                    $booking_end_time = strtotime($bookingdata->bktblendtime);
                    $now        =  strtotime('now');
                    if($now > $booking_end_time){
                        $review_must_done = true;
                    }
                }

                if($review_must_done == true)
                {
                    $booking_reviews['booking_Id']     =   $bookingdata->id;
                    $booking_reviews['user_id']        =   $bookingdata->user_id;
                    $booking_reviews['bookdate']       =   $bookingdata->bookdate;
                    $booking_time                            =   $this->RemoveTimeBeforeAndAfterDuration($bookingdata->bookstrttime,$bookingdata->bookendtime);
                    $booking_reviews['start_time']     =   $booking_time['start_time'];
                    $booking_reviews['end_time']       =   $booking_time['end_time'];
                    $booking_reviews['spl_request']    =   $bookingdata->special_requests;
                    $salon_details  =   DB::table('salons')->where('id',$bookingdata->salon_id)->first();
                    $booking_reviews['salon_id']       =   $bookingdata->salon_id;
                    $booking_reviews['salon_name']     =   $salon_details->name;

                    // Service_details
                    $booking_services           =   DB::table('booking_services')
                                                    ->where('booking_id',$bookingdata->id)
                                                    ->select('service_id','guest_count','service_type')
                                                    ->groupBy('service_id')
                                                    ->get();
                    $booking_services_arr   =   [];
                    foreach($booking_services as $services){
                        if($services->service_type=='1')
                            $service_type = 'Back to Back';
                        else
                            $service_type = 'At the Same Time';

                        $services_arr = [
                            'service_id'    =>  $services->service_id,
                            'service_name'  =>  DB::table('salon_services')->where('id',$services->service_id)->first()->service,
                            'guest_count'   =>  $services->guest_count,
                            'service_type'  =>  $service_type
                        ];

                        array_push($booking_services_arr,$services_arr);
                    }
                    $booking_reviews['service_details']=$booking_services_arr;
                    $result = ['status'=>true, 'data'=>$booking_reviews, 'message'=>"Tell About Your Booking Experiance"];
                } else {
                    $result = ['status'=>false, 'message'=>"You can continue to Home Screen"];
                }
            } else {
                $result = ['status'=>false, 'message'=>"You can continue to Home Screen"];
            }

        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    public function SkipReview(){
        try
        {
            $api_token  =   request()->header('User-Token');
            $user_token    =   UserToken::where("api_token",$api_token)->first();
            if(!$user_token)
            {
                throw new Exception("Please Login to Continue");
            }
            if(!(request()->filled('booking_id'))){
                throw new Exception("Please Provide Booking Id");
            }
            $booking_id             =   request()->booking_id;

            $verify_id  = DB::table('booking')->where('id',$booking_id)->first();
            if(!$verify_id){
                throw new Exception("Please Check Booking Id");
            }
            $skip_booking_review    =   DB::table('booking')
                                        ->where('id',$booking_id)
                                        ->update(['booking_review'=>1]);

            if($skip_booking_review){
                $result = ['status'=>true, 'message'=>"Booking Review Skipped"];
            } else {
                $result = ['status'=>false, 'message'=>"You can continue to Home Screen"];
            }
        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

    // My Booking
    public function MyBookings()
    {
        try
        {
            if(!request()->header('User-Token'))
            {
                throw new Exception("Api Token Expired");
            }
            $api_token  =   request()->header('User-Token');
            $user_id    =   UserToken::where("api_token",$api_token)->first()->user_id;
            $Booking        =   DB::table('booking')->select('booking.id as bookid','booking.user_id',
                                'booking.bookdate as bktblebkdt', 'booking.bookendtime as bktblendtime',
                                'booking.salon_id','booking.amount as totalamount','booking.special_requests',
                                'booking.active','salons.name as salon_name','booking.created_at')
                                ->join('salons','booking.salon_id','=','salons.id')
                                ->where('user_id',$user_id)->latest();

            // if(request()->has('booking_type') && request()->booking_type != ""){
            //     if($booking_type == "past"){

            //     } else {

            //     }
            // }
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
            foreach ($get_booking as $key => $bookingdata)
            {
                $MyBookings[$key]['booking_Id']     =   $bookingdata->bookid;
                $MyBookings[$key]['user_id']        =   $bookingdata->user_id;
                $MyBookings[$key]['salon_id']       =   $bookingdata->salon_id;

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
                foreach ($booking_service_details as $e => $det)
                {
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
                    $booked_date    = $bookingdata->bktblebkdt;
                    $today          =   date('d-m-Y');
                    if(strtotime($booked_date) < strtotime($today))
                    {
                        $MyBookings[$key]['booking_dt_status']     =   "Past";
                    } else
                    {
                        if(strtotime($booked_date) == strtotime($today))
                        {
                            $booking_end_time = strtotime($bookingdata->bktblendtime);
                            $now        =  strtotime('now');
                            if($now > $booking_end_time){
                                $MyBookings[$key]['booking_dt_status']     =   "Past";
                            } else {
                                $MyBookings[$key]['booking_dt_status']     =   "Upcoming";
                            }
                        } else{
                            $MyBookings[$key]['booking_dt_status']     =   "Upcoming";
                        }
                    }
                }
                // foreach
                $MyBookings[$key]['booking_service_details']    =   $booking_service_details;
                $MyBookings[$key]['booking_address']            =   DB::table('booking_address')
                                                                    ->select('first_name','email','phone','address')
                                                                    ->where('booking_id',$bookingdata->bookid)->first();
                $MyBookings[$key]['spl_request']    =   $bookingdata->special_requests;
                $MyBookings[$key]['salon_details']  =   DB::table('salons')->where('id',$bookingdata->salon_id)->first();
                $MyBookings[$key]['salon_details']->salonimage  =   env("IMAGE_URL")."salons/".$MyBookings[$key]['salon_details']->image;
                $MyBookings[$key]['salon_reviews']  =   DB::table('salon_reviews')->where('salon_id',$bookingdata->salon_id)
                                                        ->leftJoin('user','user.id','=','salon_reviews.user_id')->get();
                $reviews            =   DB::table("salon_reviews")
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

                $MyBookings[$key]['overall_rating'] =   strval($overall);
            }
            $result = ['status'=>true, 'data'=>$MyBookings];

        } catch (\Exception $e) {
            $result = ['status'=>false, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }

}
