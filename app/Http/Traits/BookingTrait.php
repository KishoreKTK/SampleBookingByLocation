<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use League\OAuth1\Client\Server\Server;
use Maatwebsite\Excel\Facades\Excel;

trait BookingTrait {

// ================================================================================================= //
// ================================= Functions Used Outside to Work ================================ //
// ================================================================================================= //



    // Get Time Frame (Start and End Time of Day of Date)
    function TimeFrame($date,$salon_id)
    {
        $timeframes=$timeslots=$books=$rtimeframes=[];
        $timeslot=[];
        $times=[];
        $ndate      =   new Carbon($date);
        $new_date   =   strtolower($ndate->format('l'));
        $timeslot   =   DB::table('working_hours_time')->where("salon_id",$salon_id);
        if($new_date=='monday')
        {
            $timeslot   =   $timeslot
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
        $start_time =   isset($timeslot->start_time)?$timeslot->start_time:'';
        $end_time   =   isset($timeslot->end_time)?$timeslot->end_time:'';
        $duration   =   30;
        $get_timing_array = $this->SplitTime($start_time, $end_time, $duration);
        $result = ['timeslots'=>$timeslot, 'timedurations'=> $get_timing_array];
        return $result;
    }


    // Check Every Service Id belongs to that Salon
    function ServiceIdBelongsToSalon($service_ids,$salon_id)
    {
        $non_existing_id    =   0;
        foreach ($service_ids as $key => $value) {
            // dd($value);
            $salon_service_id =  DB::table('salon_services')->where('salon_id',$salon_id)
                                    ->where('id',$value)->whereNull('salon_services.deleted_at')->get();
            if(count($salon_service_id) == 0){
                $non_existing_id++;
            }
        }

        if($non_existing_id == 0){
            $result = ['status'=>true, 'message'=>"All Services Belongs to Salon"];
        }
        else{
            $result =   ['status'=>false, 'message'=>'Please Check the Services You have provided'];
        }
        return $result;
    }

    // Check Weather Every Services has Staff or Not - No Reworks
    function CheckAllServicesHasStaffs($services, $salon_id)
    {
        try
        {
            $get_staffs_ids             =   [];
            $service_ids                =   [];
            $ServiceNeedsMultipleStaffs =   [];
            $services_needs_staff_count =   [];
            $service_with_zero_staffs   =   [];
            $service_with_one_staffs    =   [];

            // Gets All Service ID as Array
            foreach ($services as $key => $service)
            {
                // Passing Service Id as New Array
                $service_ids[]  =   $service["service_id"];
                if($service["service_type"]  == '2' )
                {
                    $services_needs_staff_count[$service['service_id']]     =   2;
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }else{
                    $services_needs_staff_count[$service['service_id']]     =   1;
                }
            }

            // It Returns Array with Service as Key and Staff Count as Value.
            $services_with_staff_count          =   $this->CheckStaffCountForServices($salon_id);
            // In order to get the Intersecting value as an array. i am flipping and matching key values.
            $filtered_services  =   array_intersect_key($services_with_staff_count, array_flip($service_ids));

            // Check Weather All Services has Enough Staffs to Send
            foreach ($filtered_services as $service_id => $staffcount)
            {
                if($staffcount == 0) {
                    $service_with_zero_staffs[]  = $service_id;
                }

                if(count($ServiceNeedsMultipleStaffs) > 0) {
                    if(in_array($service_id,$ServiceNeedsMultipleStaffs)) {
                        if($staffcount < 2) {
                            $service_with_one_staffs[]  = $service_id;
                        }
                    }
                }
            }

            if(count($service_with_zero_staffs) != 0)
            {
                $result = [
                    'status'                    =>  false,
                    'service_with_zero_staff'   =>  $service_with_zero_staffs,
                    'message'                   =>  "Sorry for inconvenience no staffs available for this services. Remove it and Continue"
                ];
            }
            else if(count($service_with_one_staffs) != 0)
            {
                $result = [
                    'status'                    =>  false,
                    'service_with_zero_staff'   =>  $service_with_one_staffs,
                    'message'                   =>  "Sorry for inconvenience only 1 staffs available for this services. Please Reduce Back to Back and Continue"
                ];
            }
            else
            {
                // check staffs for all the service
                $check_staffs_for_service    =  DB::table('staff_services')
                                                ->join('salon_staffs','salon_staffs.id','staff_services.staff_id')
                                                ->whereIn('staff_services.service_id',$service_ids)
                                                ->select('staff_id','service_id')
                                                ->whereNull('staff_services.deleted_at')
                                                ->whereNull('salon_staffs.deleted_at')
                                                ->groupBy('salon_staffs.id')
                                                ->get();
                // getting staffs who can perform that services here getting only particular service to check with Input Service.
                foreach ($check_staffs_for_service as $key => $value) {
                    $get_staffs_ids[]       =   $value->staff_id;
                }

                $available_staff            =   array_unique($get_staffs_ids);
                $result = [
                    'status'            =>  true,
                    'service_ids'       =>  $service_ids,
                    'service_staff_count'=> $services_with_staff_count,
                    'staff_ids'         =>  $available_staff,
                ];
            }
        }
        catch (\Exception $e) {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }


    // Check Staffs Count for Services
    function CheckStaffCountForServices($salon_id)
    {
        $get_service_staff_count =  DB::table('salon_services')
                                    ->select('salon_services.id as service_id',
                                        DB::raw('count(staff_services.staff_id) as staff_count'))
                                    ->leftjoin('staff_services','salon_services.id','=','staff_services.service_id')
                                    ->where('salon_services.salon_id',$salon_id)
                                    ->whereNotNull('salon_services.pending')
                                    ->whereNull('salon_services.deleted_at')
                                    ->whereNull('staff_services.deleted_at')
                                    ->groupBy('staff_services.service_id')->get();
        // dd($get_service_staff_count);
        $service_with_num_of_staffs = [];
        foreach ($get_service_staff_count as $key => $value) {
            $service_with_num_of_staffs[$value->service_id] = $value->staff_count;
        }
        return $service_with_num_of_staffs;
    }



    // Selects Staffs to Send
    function SelectStafftoSend($salon_id, $services,$staff_ids, $booking_date, $start_time, $end_time)
    {
        try
        {
            $service_ids                =   [];
            $ServiceNeedsMultipleStaffs =   [];
            $service_count              =   0;
            $service_atsametime_count   =   0;
            $assigned_staff             =   [];
            $staff_service_count        =   [];
            $filtered_staffs            =   $staff_ids;

        // ================================================================================
        // Check staffs by Leave or Not on Booking Date
        // ================================================================================

        $get_staff_on_leave             =   $this->CheckStaffLeaveOrNot($booking_date,$salon_id,$staff_ids);
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


        // ================================================================================
        // Check Staffs free on that Booking date & Time
        // ================================================================================

        $salon_has_booking_on_date  =   DB::table('booking')
                                        ->select('bookstrttime as active_start_time',
                                                'bookendtime as active_end_time',
                                                'booking.staffs')
                                        ->where('bookdate',$booking_date)
                                        ->where('salon_id',$salon_id)
                                        ->where('booking.active','1')
                                        ->where('booking.block','0')
                                        ->get();

        if(count($salon_has_booking_on_date)>0)
        {
            $duration               =   30;
            $entered_time_n_dt      =   $this->SplitTime($start_time, $end_time, $duration);

            foreach($salon_has_booking_on_date as $getStaffs)
            {
                foreach ($filtered_staffs as $key => $staff_id)
                {
                    $bkedstaffids    = (explode(",",$getStaffs->staffs));
                    foreach($bkedstaffids as $bookedstaffid)
                    {
                        if($bookedstaffid == $staff_id)
                        {
                            $timings_occupied 		= [];

                            $active_start_time  	=  	date("H:i", strtotime($getStaffs->active_start_time));
                            $active_close_time  	=  	date("H:i", strtotime($getStaffs->active_end_time));

                            $time_differences_n_db 	= 	$this->SplitTime($active_start_time, $active_close_time, $duration);
                            array_push( $timings_occupied, $time_differences_n_db );

                            $timings_arr	=   call_user_func_array("array_merge", $timings_occupied);
                            $timings_arr    =   array_unique($timings_arr);

                            $check_existing_slot_timing = array_intersect( $timings_arr, $entered_time_n_dt);
                            if(count($check_existing_slot_timing) != 0)
                            {
                                unset($filtered_staffs[$key]);
                            }
                        }
                    }
                }
            }

            if(count($filtered_staffs) == 0){
                throw new Exception("No Staffs Available at Selected Date & Time. Please Select Some Other Timing to Continue Booking");
            }

        }



        // ================================================================================
        // Remove Staffs from Blocked Booking
        // ================================================================================
        $check_blocked_bookings     =   DB::table('booking')
                                        ->where('booking.salon_id',$salon_id)
                                        ->where('block',1)->where('active','1')
                                        ->where('bookdate',$booking_date)
                                        ->whereNull('deleted_at')
                                        ->pluck('id')->toArray();

        // dd($check_blocked_bookings);
        if(count($check_blocked_bookings) > 0)
        {
            $StaffBlockedBookingTimes   =   [];
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
                        $blkdstart_time     = str_replace(':00:00',':00',str_replace(':30:00',':30',$blocked_slots->start_time));
                        $blkdend_time       = str_replace(':00:00',':00',str_replace(':30:00',':30',$blocked_slots->end_time));
                        array_push($staff_booking,$blkdstart_time);
                        array_push($staff_booking,$blkdend_time);
                        $booked_slots[$blocked_slots->staff_id] = $staff_booking;
                        $StaffBlockedBookingTimes[$blocked_slots->staff_id][$key]   = $staff_booking;
                    }
                }
            }
            // Common Block Timings
            if(count($StaffBlockedBookingTimes) > 0)
            {
                $staff_timing_array =   [];
                foreach($StaffBlockedBookingTimes as $staff_id=>$staff_timings)
                {
                    $data = call_user_func_array('array_merge', $staff_timings);
                    $staff_timing_array[$staff_id]  =  array_unique($data) ;
                }
                $blocked_staffs_on_day  =   array_keys($staff_timing_array);
                $CheckStaffs_intersect  =   array_intersect($filtered_staffs, $blocked_staffs_on_day);
                if(count($CheckStaffs_intersect) > 0){
                    $duration               = 30;
                    $entered_time_n_dt      = $this->SplitTime($start_time, $end_time, $duration);
                    foreach($CheckStaffs_intersect as $blkedstaff_id)
                    {
                        $staff_timings = $staff_timing_array[$blkedstaff_id];
                        $check_existing_slot_timing = array_intersect($entered_time_n_dt, $staff_timings);
                        if(count($check_existing_slot_timing) > 0)
                        {
                            foreach($filtered_staffs as $k=>$free_staffs){

                                if($free_staffs == $blkedstaff_id){
                                    unset($filtered_staffs[$k]);
                                }
                            }
                        }
                    }
                }
            }
            if(count($filtered_staffs) == 0){
                throw new Exception("No Staffs Available at Selected Date & Time. Please Select Some Other Timing to Continue Booking");
            }
        }


        // ================================================================================
        // Arrange Services and Staffs by Requirement
        // ================================================================================

        foreach ($services as $key => $service)
        {
            $service_count = $service_count + 1;

            // Passing Service Id as New Array
            $service_ids[]  =   $service["service_id"];

            if($service["service_type"]  == '2' )
            {
                $service_atsametime_count       =   $service_atsametime_count +1;
                $ServiceNeedsMultipleStaffs[]   =   $service["service_id"];
            }
        }

        $filtererd_staff_with_service   =   [];
        $filtered_staffs_to_send        =   $this->GetStaffWithServices($filtered_staffs);
        $filtered_services = [];

        foreach ($filtered_staffs_to_send as $staff => $service) {
            $service_arr                            =   explode(",",$service);
            foreach($service_arr as $arr){
                $filtered_services[]    = $arr;
            }
            $staff_service_count[$staff]            =   count($service_arr);
            $filtererd_staff_with_service[$staff]   =   $service_arr;

        }

        $requested_service_ids  =       $service_ids;
        $filtered_services      =       array_unique($filtered_services);
        $check_differences      =       array_diff($requested_service_ids, $filtered_services);

        if($check_differences   !=  null){
            throw new Exception("Some Services don't have Staffs at the Selected Timing");
        }


        if(count($ServiceNeedsMultipleStaffs) > 0  && $filtered_staffs < 2){
            throw new Exception("Some Services don't have Staffs at the Selected Timing");
        }

        asort($staff_service_count);


        // dd($staff_service_count);
        // dd($filtered_staffs_to_send);


        // dd(['requested_servuces'=>$requested_service_ids, 'filtered_servcies'=>$filtered_services]);
        // dd($check_differences);

            // ================================================================================
            // Scenarios Starts Here
            // ===========================================================================

            if($service_count   ==  1 ) // 1 service
            {
                if($service_atsametime_count == 0)// Back To Back
                {
                    $assigned_staff[]   =   array_key_first($staff_service_count);
                }
                else // At The Same Time
                {
                    for ($i=0; $i < 2; $i++)
                    {
                        $get_staff_id       = array_key_first($staff_service_count);
                        $assigned_staff[]   = $get_staff_id;
                        unset($staff_service_count[$get_staff_id]);
                    }
                }
            }
            else // Multiple Services
            {
                $staff_can_do_all_service           =   [];

                // Get Combination for the Given Services First
                $combinations_of_input_services     = $this->uniqueCombination($service_ids);

                //Sort by Max to Min
                rsort($combinations_of_input_services);

                foreach($combinations_of_input_services as $e => $service_combo)
                {
                    foreach($filtered_staffs_to_send as $key => $value)
                    {
                        $staff_services                     =   explode(',',$value);
                        $check_staffs_with_staff_service    =   array_diff($service_combo,$staff_services);
                        if(empty($check_staffs_with_staff_service))
                        {
                            $service_id_count               =   count($service_ids);
                            $service_combo_count            =   count($service_combo);

                            // Checks if single staff can do all the services
                            if($service_id_count    ==  $service_combo_count)
                            {
                                $staff_can_do_all_service[$key]     =   $filtered_staffs_to_send[$key];
                                break;
                            }
                        }
                    }
                    if(!empty($staff_can_do_all_service))
                    {
                        break;
                    }
                }
                // Single Staff for All Service
                if(!empty($staff_can_do_all_service))
                {
                    foreach ($staff_can_do_all_service as $staff => $service)
                    {
                        $service_arr                    =   explode(",",$service);
                        $staff_service_count[$staff]    =   count($service_arr);
                    }
                    asort($staff_can_do_all_service);
                    $get_staff_id       = array_key_first($staff_can_do_all_service);
                    $assigned_staff[]   = $get_staff_id;
                    unset($filtered_staffs_to_send[$get_staff_id]);
                }
                // Multiple Staffs for All Service
                else
                {
                    $filtered_match         =   [];
                    $filtered_match_count   =   [];
                    foreach ($filtererd_staff_with_service as $key => $value)
                    {
                        foreach ($combinations_of_input_services as $e => $combo)
                        {
                            $array_match    =  array_diff($value,$combo);
                            if(empty($array_match))
                            {
                                    $filtered_match[$key]   =   $value;
                            }
                        }
                    }

                    foreach ($filtered_match as $staff_id => $services)
                    {
                        $filtered_match_count[$staff_id]   =   count($services);
                    }

                    $partially_assigned             =   array_key_first($filtered_match_count);
                    $assigned_staff[]               =   $partially_assigned;
                    $partially_selected_service     =   $filtererd_staff_with_service[$partially_assigned];
                    $remaning_services_needs        =   array_diff($service_ids, $partially_selected_service);
                    $get_filtered_staffs            =   [];


                    foreach ($remaning_services_needs as $remainingservices) {
                        foreach ($filtererd_staff_with_service as $key      =>  $staff_services) {
                            if(in_array($remainingservices, $staff_services)){
                                $get_filtered_staffs[$remainingservices][$key] =   $staff_services;
                            }
                        }
                    }

                    foreach ($get_filtered_staffs as $key => $remaing_service_id) {
                        $first_staff_id     =   array_key_first($remaing_service_id);
                        array_push($assigned_staff, $first_staff_id);
                    }

                }


                // At The Same Time Services Scenario
                if($service_atsametime_count =! 0)
                {

                    $assigned_staff_with_service    =   [];
                    $remaining_staff_with_service   =   [];
                    $temp_array_for_staff_filter    =   [];

                    foreach ($filtererd_staff_with_service as $staffid => $service_arrays)
                    {
                        if(in_array($staffid, $assigned_staff)) {
                            $assigned_staff_with_service[$staffid]  =   $service_arrays;
                        } else {
                            $remaining_staff_with_service[$staffid] =   $service_arrays;
                        }
                    }

                    foreach ($ServiceNeedsMultipleStaffs as $services)
                    {
                        foreach ($remaining_staff_with_service as $staff => $staff_service_array)
                        {
                            if(in_array($services, $staff_service_array))
                            {
                                $temp_array_for_staff_filter[$services][$staff] =   $staff_service_array;
                            }
                        }
                    }

                    foreach ($temp_array_for_staff_filter as $key => $remaing_service_id) {
                        asort($remaing_service_id);
                        // dd($remaing_service_id);
                        $first_staff_id     =   array_key_first($remaing_service_id);
                        array_push($assigned_staff, $first_staff_id);
                    }
                }
            }


            $selected_staffs = array_unique($assigned_staff);
            // dd($selected_staffs);

            $result =    ['status'=>true,'data'=>$selected_staffs];
        }
        catch(Exception $e){
            $result =    ['status'=>false,'message'=>$e->getMessage()];
        }
        return $result;
    }


    // Check Customer has Booking on Same Date & time
    function CheckCustomerAvailability($customer_email,$booking_date,$start_time,$end_time)
    {
        try
        {
            $customer_has_booking   =   DB::table('booking')
                                        ->join('booking_address','booking.id','=','booking_address.booking_id')
                                        ->select('start_time as active_start_time', 'end_time as active_end_time')
                                        ->join('booking_services','booking.id','=','booking_services.booking_id')
                                        ->where('booking_address.email',$customer_email)
                                        ->where('booking_services.date',$booking_date)
                                        ->get();
            // dd($customer_has_booking);
            if(count($customer_has_booking) != 0)
            {
                $check_customer_free_time =  $this->booking_time_vaccancy_fn($customer_has_booking, $start_time,$end_time);

                // dd($check_customer_free_time);
                if($check_customer_free_time['status'] == false)
                {
                    $result = ['status'=>false, 'message'=>"You have Another Booking at the Same Date & Time"];
                }
                else
                {
                    $result = ['status'=>true, 'message'=>"You can continue your booking"];
                }
            }
            else
            {
                $result = ['status'=>true, 'message'=>"You can continue your booking"];
            }
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        // dd($result);
        return $result;
    }


    // Checks Cost of Booking - Pending Rework
    function CheckCostofBooking($salon_id,$service_ids,$ServiceNeedsMultipleStaffs,$total_amount,$promo_code)
    {
        $get_services  =    DB::table('salon_services')
                            ->select('salon_services.id','salon_services.service',
                                    'salon_services.salon_id','salon_services.category_id',
                                    'salon_services.time','salon_services.amount')
                            ->where('salon_services.approved','1')
                            ->whereIn('salon_services.id',$service_ids)
                            ->get();
                            // dd($get_services);
        return $get_services;
    }


    // Insert Booking Process is Done Here. - No Reworks
    function InsertNewBookingRecord($input)
    {
        try
        {
            $booking_date   =   $input["selected_date"];
            $salon_id       =   $input["selected_salon"];
            $total_amount   =   $input["totalamount"];
            $start_time     =   $input["booking_start_time"];
            $end_time       =   $input["booking_end_time"];
            $services       =   $input['selected_services'];
            $customer_id    =   $input['customer_id'];
            $selected_staff =   $input['selected_staffs'];
            if($customer_id == 0)
            {
                $first_name =   $input['first_name'];
                $last_name  =   $input['last_name'];
                $address    =   $input['address'];
                $phone      =   $input['phone'];
                $email      =   $input['email'];
            }
            else
            {
                $address_id =   $input['address_id'];
                $user_det   =   DB::table('user_address')->where('id',$address_id)
                                ->where('user_id',$customer_id)->first();
                                // dd($user_det);
                if(!$user_det){
                    throw new Exception("Please Add Address to Continue");
                }

                $first_name =   $user_det->first_name;
                $last_name  =   $user_det->last_name;
                $address    =   $user_det->address;
                $phone      =   $user_det->phone_num;
                $email      =   DB::table('user')->where('id',$customer_id)->first()->email;
                // $email      =   $user_email->email;
            }
            $promo_code     =   $input['promocode'];
            $fcm            =   $input['fcm'];
            $device         =   $input['device'];
            $time           =   Carbon::now();

            $booking_services_arr = [];

            // Add 30mins Before Start Time and After End Time
            $Get_new_start_n_end_time = $this->AddTimeBeforeAndAfterDuration($start_time,$end_time);

            $new_start_time             =   $Get_new_start_n_end_time['start_time'];
            $new_end_time               =   $Get_new_start_n_end_time['end_time'];
            $get_staff_with_services    =   $this->GetStaffWithServices($selected_staff);

            $booking_services           =   $this->AddSevicesWithAssignedStaffs($services,$get_staff_with_services);

            foreach ($booking_services as $key => $value)
            {
                // $booking_services_arr[$key]['booking_id']   = $booking_id;
                $booking_services_arr[$key]['date']         =   $booking_date;
                $booking_services_arr[$key]['staff_id']     =   $value['staff_id'];
                $booking_services_arr[$key]['service_id']   =   $value['service_id'];
                $service_amt                                =   DB::table('salon_services')
                                                                ->where('id',$value['service_id'])
                                                                ->first()->amount;
                $booking_services_arr[$key]['amount']       =   $service_amt;
                $booking_services_arr[$key]['start_time']   =   $new_start_time;
                $booking_services_arr[$key]['end_time']     =   $new_end_time;
                $booking_services_arr[$key]['guest_count']  =   $value['service_guest'];
                $booking_services_arr[$key]['service_type'] =   $value['service_type'];
                $booking_services_arr[$key]['created_at']   =   $time;
                $booking_services_arr[$key]['updated_at']   =   $time;
            }

            // dd($booking_services_arr);
            $booking_insert_data=   [
                "user_id"       =>  $customer_id,
                "salon_id"      =>  $salon_id,
                "bookdate"      =>  $booking_date,
                "bookstrttime"  =>  $new_start_time,
                "bookendtime"   =>  $new_end_time,
                "staffs"        =>  $input['staffs_string'],
                "special_requests"=> $input['spl_request'],
                "offer_applied" =>  0,
                "promocode"     =>  $promo_code,
                'block'         =>  0,
                "amount"        =>  $total_amount,
                "actual_amount" =>  $total_amount,
                'customer_id'   =>  $input['txn_customer_id'],
                'transaction_id'=>  $input['txn_charge_id'],
                "active"        =>  1,
                "status_code"   =>  0,
                "created_at"    =>  $time,
                "updated_at"    =>  $time
            ];

            $booking_address_data = [
                "first_name"    =>  $first_name,
                "last_name"     =>  $last_name,
                "address"       =>  $address,
                "phone"         =>  $phone,
                "email"         =>  $email,
                "fcm"           =>  $fcm,
                "device"        =>  $device,
                "created_at"    =>  $time,
                "updated_at"    =>  $time
            ];

            // $test_input     =   [
            //                         'booking_data'=>$booking_insert_data,
            //                         'booking_service_data'=>$booking_services_arr,
            //                         'booking_address_data'=>$booking_address_data
            //                     ];

            // dd($test_input);
            $booking_id         =   DB::table('booking')->insertGetId($booking_insert_data);
            if($booking_id)
            {
                $booking_address_data["booking_id"]    =  $booking_id;

                $booking_address = DB::table('booking_address')->insert($booking_address_data);

                if(!$booking_address){
                    throw new Exception("Error In Addinng Booking Address");
                }

                foreach ($booking_services as $key => $value)
                {
                    $booking_services_arr[$key]['booking_id']   = $booking_id;
                    $booking_services_arr[$key]['created_at']   = $time;
                    $booking_services_arr[$key]['updated_at']   = $time;
                }

                // dd($booking_services_arr);
                $booking_service = DB::table('booking_services')->insert($booking_services_arr);

                if(!$booking_service){
                    throw new Exception("Error In Addinng Booking Service");
                }

                $result     = ['status'=>true,"booking_id"=>$booking_id, 'message'=>"Booked Successfully"];
            }
            else
            {
                throw new Exception("Error In Adding Booking");
            }
        }

        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }


// ================================================================================================= //
// ================================= Functions Used Internally So Far ============================== //
// ================================================================================================= //


    function GetStaffWithServices($available_staff)
    {
        $staff_id_with_service      =   [];
        $get_staffs_with_services   =   DB::table('staff_services')
                                        ->select('staff_id',
                                                DB::raw('group_concat(service_id) as services'))
                                        ->whereIn('staff_id',$available_staff)
                                        ->whereNull('staff_services.deleted_at')
                                        ->groupBy('staff_id')
                                        ->get();

        foreach ($get_staffs_with_services as $value) {
            $staff_id_with_service[$value->staff_id] = $value->services;
        }

        return $staff_id_with_service;
    }

    function AddTimeBeforeAndAfterDuration($start_time,$end_time)
    {
        $start_time     =   strtotime($start_time);
        $end_time       =   strtotime($end_time);
        $newstartTime   =   date("H:i", strtotime('-30 minutes', $start_time));
        $newendTime     =   date("H:i", strtotime('+30 minutes', $end_time));
        $NewDuration    =   ['start_time'=>$newstartTime,'end_time'=>$newendTime];
        return $NewDuration;
    }

    function RemoveTimeBeforeAndAfterDuration($start_time,$end_time)
    {
        $start_time     =   strtotime($start_time);
        $end_time       =   strtotime($end_time);
        $newstartTime   =   date("H:i", strtotime('+30 minutes', $start_time));
        $newendTime     =   date("H:i", strtotime('-30 minutes', $end_time));
        $NewDuration    =   ['start_time'=>$newstartTime,'end_time'=>$newendTime];
        return $NewDuration;
    }

    function AddSevicesWithAssignedStaffs($services,$get_staff_with_services)
    {
        $ServiceNeedsMultipleStaffs =   [];
        $new_service  =   [];
        foreach ($services as $key => $value)
        {
            $services[$key]['staff_id'] = array_rand($get_staff_with_services);
            if($value["service_type"]  == 2 )
            {
                $new_service['service_id']      =   $value['service_id'];
                $new_service['service_guest']   =   $value['service_guest'];
                $new_service['staff_id']        =   array_rand($get_staff_with_services);
                $new_service['service_type']    =   $value['service_type'];
                array_push($ServiceNeedsMultipleStaffs, $new_service);
            }
        }

        foreach($ServiceNeedsMultipleStaffs as $key=>$value){
            array_push($services, $value);
        }

        return $services;
    }

    function CheckDateTimeGreaterThanNow($bookdate,$Time)
    {
        $date_now   = strtotime(date("d-m-Y"));
        $check_date = strtotime($bookdate);

        if($date_now > $check_date) {
            // Upcoming
            $result = true;
        }
        elseif($date_now == $bookdate){
            $now = strtotime(date("d-m-Y H:i:s"));
            $book_dt_time = strtotime($bookdate." ".$Time);
            if($now < $book_dt_time){
                $result = true;
            }else{
                $result = false;
            }
        }else{
            // Past
            $result = false;
        }

        return $result;

    }

    function CheckStaffLeaveOrNot($date,$salon_id,$staff_ids)
    {
        $staffs_on_leave =  DB::table('staff_holidays')
                            ->select('staff_id')
                            ->join('salon_staffs','staff_holidays.staff_id','salon_staffs.id')
                            ->where('staff_holidays.salon_id',$salon_id)
                            ->where('staff_holidays.date',$date)
                            ->whereNull('staff_holidays.deleted_at')
                            ->whereNull('salon_staffs.deleted_at')
                            ->pluck('staff_holidays.staff_id')
                            ->toArray();
                            // ->whereIn('staff_id',$staff_ids)
                            // ->get();
        // dd($staffs_on_leave);
        return $staffs_on_leave;
    }

    // Slot vaccancy Check
	function booking_time_vaccancy_fn($person_has_booking, $start_time,$end_time)
	{
        $duration               = 30;
		$start_time			    = date("H:i", strtotime($start_time));
		$end_time			    = date("H:i", strtotime($end_time));
		$entered_time_n_dt      = $this->SplitTime($start_time, $end_time, $duration);
        $timings_occupied 		= [];
        foreach($person_has_booking as $k => $slot)
        {
            $active_start_time  	=  	date("H:i", strtotime($slot->active_start_time));
            $active_close_time  	=  	date("H:i", strtotime($slot->active_end_time));

            $time_differences_n_db 	= 	$this->SplitTime($active_start_time, $active_close_time, $duration);
            array_push( $timings_occupied, $time_differences_n_db );
        }

        $timings_arr	= call_user_func_array("array_merge", $timings_occupied);
        array_unique($timings_arr);

        $check_existing_slot_timing = array_intersect( $timings_arr, $entered_time_n_dt);

        if(count($check_existing_slot_timing) == 0)
        {
            $insert_status 	=   array(
                                        'status'=>true,
                                        'message'=>"You can Continue Booking"
                                    );
        }
        else
        {
            $insert_status =    array(
                                    'status'=>false,
                                    'message'=>"Please Select Another timing."
                                );
        }
		return $insert_status;
	}

    // Splits time with Duration & Gets Time Range Between two Timings
	function SplitTime($StartTime, $EndTime, $Duration)
	{
		$ReturnArray 	= array ();// Define output
		$StartTime    	= strtotime ($StartTime); //Get Timestamp
		$EndTime      	= strtotime ($EndTime); //Get Timestamp
		$AddMins  		= $Duration * 60;

		while ($StartTime <= $EndTime) //Run loop
		{
			$ReturnArray[] = date ("G:i", $StartTime);
			$StartTime += $AddMins; //Endtime check
		}
		return $ReturnArray;
	}


    function uniqueCombination($in, $minLength = 1, $max = 2000)
    {
        $count = count($in);
        $members = pow(2, $count);
        $return = array();
        for($i = 0; $i < $members; $i ++) {
            $b = sprintf("%0" . $count . "b", $i);
            $out = array();
            for($j = 0; $j < $count; $j ++) {
                $b[$j] == '1' and $out[] = $in[$j];
            }

            count($out) >= $minLength && count($out) <= $max and $return[] = $out;
            }
        return $return;
    }

}
