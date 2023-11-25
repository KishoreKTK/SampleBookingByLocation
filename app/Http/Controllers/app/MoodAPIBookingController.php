<?php

namespace App\Http\Controllers\app;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\BookingTrait;

class MoodAPIBookingController extends Controller
{
    use BookingTrait;
    //
    public function GetTimeFrames(){
        $date       =   request()->booking_date;
        $salon_id   =   request()->salon_id;
        try
        {
            $timeframe              =   $this->TimeFrame($date,$salon_id);
            $time_intervals         =   [];
            $time_intervals2        =   [];
            $time_duration          =   $timeframe['timedurations'];
            foreach ($time_duration as $key => $value) {
                $time_intervals[]   =   ['time'=>$value, 'booked'=>false];
            }
            array_shift($time_duration);
            array_pop($time_duration);
            foreach ($time_duration as $key => $value) {
                $time_intervals2[]   =   ['time'=>$value, 'booked'=>false];
            }
            $result                 =   [
                                            'status'        =>  true,
                                            // 'data'          =>  $timeframe,
                                            // 'time_interval' =>  $time_intervals,
                                            'time_duration' =>  $time_intervals2,
                                            'message'       =>  'TimeFrames Listed Successfully'
                                        ];
        }
        catch (\Exception $e)
        {
            $result = ['status'=>false, "message"=>$e->getMessage()];
        }

        return response()->json($result);
    }
}
