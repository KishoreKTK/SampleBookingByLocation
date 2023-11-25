<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

trait BookingCheckTrait {

    // Comparison function
    function date_compare($element1, $element2) {
        $datetime1 = strtotime($element1['time']);
        $datetime2 = strtotime($element2['time']);
        return $datetime1 - $datetime2;
    }

    public function CheckAddressId($address_id, $user_id){
        // return true or false
    }

    public function GreaterThanToday($date){
        try
        {
            $bookingdate    =   strtotime($date);
            $today          =   strtotime(date('d M Y',strtotime("now")));
            if ($bookingdate < $today) {
                throw new Exception("Please Check Booking Date");
            }
            $result = ['status'=>true, 'message'=>"Valid Booking Date"];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }

        return $result;
    }

    function CheckValidBookingDate($booking_date)
    {
        try
        {
            $bookingdate    =   strtotime($booking_date);
            $today          =   strtotime(date('d M Y',strtotime("now")));
            $last_booking_dt=   strtotime(Date('d M Y', strtotime('+30 days')));
            if ($bookingdate < $today){
                throw new Exception("Please Check Booking Date");
            }
            if($bookingdate > $last_booking_dt){
                throw new Exception("You can Only Book within 30 days from Today");
            }
            $result = ['status'=>true, 'message'=>"Valid Booking Date"];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }

        return $result;
    }

    public function BookingTime($date, $start_time,$endtime){

    }

    public function CheckPromoCode($promocode){

    }

}
