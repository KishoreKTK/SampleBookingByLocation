<?php

namespace App\Http\Controllers\salon;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BookingScheduleController extends Controller
{
    //
    public function BookingSchedules()
    {
        $activePage="Schedules";
        // $salon_id=Auth::guard('salon-web')->user()->id;
        $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id   = Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id   = Auth::guard('salons-web')->user()->salon_id;
        }
        $time   =   Carbon::now()->format("Y-m-d");
        if(request()->has('date'))
        {
            $r_date         =   new Carbon(request()->date);
            $date           =   $r_date->format("d-m-Y");
        }
        else
        {
            $r_date         =   Carbon::now();
            $date           =   $r_date->format("d-m-Y");
        }

        $staffs_all         =   DB::table("salon_staffs")
                                ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                                ->join("salons", "salons.id","=","salon_staffs.salon_id")
                                ->where("salons.id",$salon_id)
                                ->whereNull("salon_staffs.deleted_at")
                                ->whereNull("staff_services.deleted_at")
                                ->whereNull("salons.deleted_at")
                                ->groupBy("salon_staffs.id");
        $staffs             =   $staffs_all->pluck("salon_staffs.staff","salon_staffs.id");
        $bookings_on_day    =   DB::table("booking")
                                ->join("salons", "salons.id","=","booking.salon_id")
                                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                                ->whereNull('booking.deleted_at')
                                ->where('booking.active',1)
                                ->where('bookdate',$date)
                                ->where('booking.salon_id',$salon_id)
                                ->select("booking.id","booking.bookdate","booking.bookstrttime",
                                "booking.bookendtime",'booking.staffs as allstaffs',
                                "booking_address.first_name","booking_address.last_name",
                                "booking_address.phone","booking_address.email",
                                "booking_address.address")->get();
        if(count($bookings_on_day) > 0){
            foreach($bookings_on_day as $bookings){
                $booked_date    =   $bookings->bookdate;
                $today          =   date('d-m-Y');
                if(strtotime($booked_date) < strtotime($today))
                {
                    $bookings->booking_dt_status     =   "Past";
                } else
                {
                    if(strtotime($booked_date) == strtotime($today))
                    {
                        $booking_end_time = strtotime($bookings->bookendtime);
                        $now        =  strtotime('now');
                        if($now > $booking_end_time){
                            $bookings->booking_dt_status     =   "Past";
                        } else {
                            $bookings->booking_dt_status     =   "Upcoming";
                        }
                    } else{
                        $bookings->booking_dt_status     =   "Upcoming";
                    }
                }

                $bookings->booked_services =  DB::table('booking_services')
                                                ->where('booking_id',$bookings->id)
                                                ->leftJoin('salon_services','salon_services.id','=','booking_services.service_id')
                                                ->select('booking_services.service_id','salon_services.service','guest_count','service_type')->get();
            }
        }
        return view('salon.schedules.schedulepage',compact("activePage","staffs","salon_id","date","bookings_on_day"));
    }
}
