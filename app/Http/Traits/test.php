<?php
class test
{

    function CheckAllServicesHasStaffs($services, $booking_date, $salon_id)
    {
        try
        {
            $service_ids                =   [];
            $ServiceNeedsMultipleStaffs =   [];
            $staff_n_leave              =   [];
            $staff_service_id           =   [];
            $get_staffs_ids             =   [];
            $staff_with_multiple_serv   =   [];
            // Single Staff can do all service
            $staff_can_do_all_service = [];

            // Gets All Service ID as Array
            foreach ($services as $key => $service) {
                // Passing Service Id as New Array
                $service_ids[]  =   $service["service_id"];

                if($service["service_type"]  == 2 )
                {
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }
            }


            // Checks Count of Staffs for all Services
            $check_staffs_for_service    =   DB::table('staff_services')
                                            ->whereIn('service_id',$service_ids)
                                            ->select('staff_id','service_id')->get();

            if(count($check_staffs_for_service) == 0){
                throw new Exception("Sorry for the Inconviniance. No Staffs Available for the Service");
            }

            // getting staffs who can perform that services
            foreach ($check_staffs_for_service as $key => $value) {
                $staff_service_id[]     =   $value->service_id;
                $get_staffs_ids[]       =   $value->staff_id;
            }

            // Check All Service Has Staffs
            $CheckAllServiceHasStaffs   =   array_diff($service_ids,$staff_service_id);
            // print_r($CheckAllServiceHasStaffs);die;
            if(!empty($CheckAllServiceHasStaffs))
            {
                $result = [
                            'status'                    =>  false,
                            'no_staff_for_service_id'   =>  $CheckAllServiceHasStaffs,
                            'message'                   =>  "Sorry for inconvinance the currently there is no staffs available for this service"
                        ];
                // $result['service_id']   =   $CheckAllServiceHasStaffs;
                // throw new Exception("Sorry for Inconvinence particular Staff for that Particular Service in not Available.
                //                     You can remove that Service and Continue Booking and Add the Service Later Another Day");
            }

            $available_staff            =   array_unique($get_staffs_ids);

            $get_staff_on_leave         =   $this->CheckStaffLeaveOrNot($booking_date,$salon_id);

            if(!empty($get_staff_on_leave))
            {
                if (($key = array_search($staff_n_leave,$available_staff)) !== false) {
                    unset($available_staff[$key]);
                }
            }

            // Assign array for the staff with multiple services
            foreach ($check_staffs_for_service as $key => $value)
            {
                if (in_array($value->staff_id, $available_staff)){
                    $staff_with_multiple_serv[$value->staff_id][]   = $value->service_id;
                }
            }

            // Check Available Staff Who can do all services
            foreach ($staff_with_multiple_serv as $key => $value)
            {
                $check_service_with_staff_service   =   array_diff($service_ids,$value);
                if(empty($check_service_with_staff_service))
                {
                    $staff_can_do_all_service[] = $key;
                }
                // else
                // {
                //     // Get Common Service and with common Stafs
                //     $get_common_services                =   array_intersect($duplicate_of_service_ids, $value);
                //     // Check for staff
                //     $check_staff_with_some_uniqu_servic =   array_diff($get_common_services, $value);
                //     if(empty($check_staff_with_some_uniqu_servic))
                //     {
                //         $multipl_staff_filter_for_all_service[]     =   $key;
                //     }
                //     if (($key = array_search($get_common_services,$duplicate_of_service_ids)) !== false) {
                //         unset($duplicate_of_service_ids[$key]);
                //     }
                //     // $pending_service_to_be_assigned     =   array_diff($duplicate_of_service_ids,$get_common_services);
                //     // $duplicate_of_service_ids           =   $pending_service_to_be_assigned;
                // }
            }


            // Multiple Staff Needed to do all Service
            // $multipl_staff_filter_for_all_service = [];


            // if(count($ServiceNeedsMultipleStaffs) > 0){
            //     $duplicate_of_service_ids       =   $service_ids;
            //     $duplicate_of_staff_ids         =   $staff_with_multiple_serv;

            //     // $get_service_id;
            //     foreach ($duplicate_of_staff_ids as $key => $value)
            //     {

            //         // Get Common Service and with common Stafs
            //         $get_common_services                =   array_intersect($duplicate_of_service_ids, $value);
            //         // Check for staff
            //         $check_staff_with_some_uniqu_servic =   array_diff($get_common_services, $value);
            //         if(empty($check_staff_with_some_uniqu_servic))
            //         {
            //             $multipl_staff_filter_for_all_service[]     =   $key;
            //         }
            //         if (($key = array_search($get_common_services,$duplicate_of_service_ids)) !== false) {
            //             unset($duplicate_of_service_ids[$key]);
            //         }
            //         // $pending_service_to_be_assigned     =   array_diff($duplicate_of_service_ids,$get_common_services);
            //         // $duplicate_of_service_ids           =   $pending_service_to_be_assigned;

            //     }
            // }


            if(!empty($staff_can_do_all_service)){
                $assing_staff =  $staff_can_do_all_service;
            }
            else{
                throw new Exception("Sorry for Inconviniance Please Staff is Not Available for all the service");

            }
            // else{
            //     $assing_staff =  $multipl_staff_filter_for_all_service;
            // }

            // Check Do any Service Needed Multiple Staffs?
            // if yes chose another staff
            // Add to the assigned_staff

            // Check Assinged_staff is Free on that time
            // if yes take those staff and proceed futher
            // else take chose some other staff to check on it.

            $result = [
                        'status'    => true,
                        'service_detail'    =>  $services,
                        // 'check_staff_id'    =>  $get_staffs_ids,
                        // 'available_staff'   =>  $available_staff,
                        //  'is_all_service_has_staff'=>$CheckAllServiceHasStaffs,
                        // 'staff_service_id'  =>  $staff_service_id,
                        // 'service_ids'       =>  $service_ids,
                        // 'staff_with_multiple_serv'  => $staff_with_multiple_serv,
                        // 'staff_can_do_all_service'  => $staff_can_do_all_service,
                        'staffs_free_to_do'     => $assing_staff,
                        // 'staffs_capable_off'    => $staff_capable_service
            ];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

    // Checks Staff has Another Booking on Date & Time
    function CheckStaffAvailability($assigned_staff, $booking_date,$start_time,$end_time)
    {
        try{
            $check_staff_availability   =   DB::table('booking_services')
                                            ->where('date',$booking_date)
                                            // ->where('start_time',$start_time)
                                            // ->where('end_time',$end_time)
                                            ->whereIn('staff_id',$assigned_staff)
                                            ->get();
            // print_r($check_staff_availability);die;
            if(count($check_staff_availability) == 0){
                // if(count($assigned_staff) == 1){
                    $booking_staff = $assigned_staff;
                // }
                // else{
                    // print("Am i coming Here ???? ");die;
                // }
                $result =   ['status'=>true, 'staffs_for_booking'=>$booking_staff];
            }
            else{
                // print("why this since array is empty??????");die;
                throw new Exception("Staff has Another Booking on that day");
            }

        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }

        return $result;
    }

    // Checks Booking Cost & PromoCodes & commissions
    function GetCostofBooking($salon_id,$services, $total_amount,$total_time,$promo_code)
    {
        try
        {
            $service_ids = [];
            foreach ($services as $key => $service) {
                $service_ids[]  =   $service["service_id"];
                if($service["service_type"]  == 2 )
                {
                    $ServiceNeedsMultipleStaffs[] = $service["service_id"];
                }
            }

            // Checks service details
            $check_service_details      =   DB::table('salon_services')
                                            ->whereIn('id',$service_ids)
                                            ->where("approved",1)
                                            ->select('salon_id','time','amount')
                                            ->get();
            $service_time   =   0;
            $service_amount =   0;
            if(count($check_service_details) == count($service_ids)){
                foreach ($check_service_details as $key => $service) {
                    // if($service->salon_id != $salon_id){
                    //     throw new Exception("Please Check Salon you Provided");
                    // }
                    // if($service->salon_id != $serice_time){
                    //     throw new Exception("Please Check Salon you Provided");
                    // }
                    // if($service->salon_id != $service_amount){
                    //     throw new Exception("Please Check Salon you Provided");
                    // }
                    $service_time   =   $service_time   + $service->time;
                    $service_amount =   $service_amount + $service->amount;
                }

                if($total_time != $service_time) {
                    throw new Exception("Please Check the Details of the Service you have Provided");
                }
                if($total_amount != $service_amount) {
                    throw new Exception("Please Check the Details of the Service you have Provided");
                }
                $actual_amount      =   $total_amount;
                $promo_detection    =   $total_amount-$total_amount*$promo_code/100;
                $mood_commission    =   DB::table('salons')->select('pricing')
                                        ->where('id',$salon_id)->first();
                $salon_commission   =   $mood_commission->pricing;

            }
            else{
                throw new Exception("Please Check the Details of the Service you have Provided");
            }
            $result = ['status'=>true, 'message'=>"You can continue your booking"];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }
        return $result;
    }

}
