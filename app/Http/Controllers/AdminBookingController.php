<?php

namespace App\Http\Controllers;
// use DB;
use Illuminate\Support\Facades\DB;
use Excel;
use App\Exports\BookingExport;
// use Session;
use Illuminate\Support\Facades\Session;
use Validator;
use App\Salons;
use App\Content;
use App\Booking;
use Carbon\Carbon;
use App\BookingServices;
use App\Http\Traits\BookingTrait;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    use BookingTrait;

	public function booking(Request $request)
	{
	    $activePage="Booking";
        $status=[1=>"Success",2=>"Cancelled"];
        $status_id='';
        $salon=Salons::pluck("name","id");
        $salon_id=0;
        $time=Carbon::now();
        $start_date=$end_date=$new_date='';

        foreach (Booking::where('read_a', '=',0)->get() as $data)
        {
            $read=Booking::where("id",$data->id)->update(["read_a"=>1,"updated_at"=>$time]);
        }

        Session::put('booking', 0);

		if(isset($request->keyword)&&$request->keyword!="")
	    {
	        $keyword   =$request->keyword;
	    	$booking   =DB::table("booking")
            	    	->join("salons", "salons.id","=","booking.salon_id")
                        ->join("booking_address", "booking_address.booking_id","=","booking.id")
            	        ->whereNull('booking.deleted_at')->orderBy("booking.id","desc")
                        ->where("booking.block","!=",1)
                        ->where("booking_address.first_name","!=",'')
                        ->where("booking_address.email","!=",'')
            	        ->where(function ($q) use ($keyword) {
            	    	$q->where("salons.name","like",'%'.$keyword.'%')
            	    	->orWhere("booking_address.first_name","like",'%'.$keyword.'%')
            	    	->orWhere("booking_address.last_name","like",'%'.$keyword.'%')
            	        ->orWhere("booking_address.email","LIKE",'%'.$keyword.'%')
            	        ->orWhere("booking_address.phone","LIKE",'%'.$keyword.'%');
            	        });

	    }
	    else
	    {
	    	$keyword='';
	    	$booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
            ->where("booking.block","!=",1)
             ->where("booking_address.first_name","!=",'')
            ->where("booking_address.email","!=",'')
	        ->whereNull('booking.deleted_at')->orderBy("booking.id","desc");

	    }

        if($request->salon_id && $request->salon_id!=0)
        {
            $salon_id=$request->salon_id;
            $booking->where("booking.salon_id",$request->salon_id);
        }

        if(isset($request->start_date) && $request->start_date!='' && isset($request->end_date) && $request->end_date!='')
        {
            $start_date=new Carbon($request->start_date);
            $from=$start_date->format('Y-m-d');
            $end_date=new Carbon($request->end_date);
            $to=$end_date->format('Y-m-d');
            $booking->whereBetween('booking.created_at', [$from, $to])->get();
            $start_date=$start_date->format('d-m-Y');
            $end_date=$end_date->format('d-m-Y');
        }

        if(isset($request->status_id) && $request->status_id!='')
        {
            $status_id=$request->status_id;
            $booking->where("booking.active",$status_id);
        }

        $booking    =   $booking->select("booking.*","booking_address.first_name",
                        "booking_address.last_name","booking_address.email",
                        "salons.name",'booking.bookdate','booking.bookendtime')->paginate(20);

        if(isset($booking)&& count($booking)>0)
        {
            foreach($booking as $value)
            {
                $book_services=[];
                $book_services= DB::table("booking_services")
                                ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                                ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                                ->where("booking_services.booking_id",$value->id)
                                ->whereNull('booking_services.deleted_at')
                                ->whereNull('salon_services.deleted_at')
                                ->whereNull('salon_staffs.deleted_at')
                                ->select("booking_services.staff_id","salon_services.service",
                                        "salon_services.time","booking_services.amount",
                                        "booking_services.discount_price","booking_services.service_id",
                                        "salon_staffs.staff","booking_services.start_time",
                                        "booking_services.date","booking_services.end_time")->get();

                $past=0;
                $up=0;
                if(isset($book_services) && count($book_services)>0)
                {
                    foreach($book_services as $ser)
                    {
                        $up=$up+1;
                        $to_date=new Carbon($ser->date);
                        $today=$to_date->format("Y-m-d");
                        $s_date=$today. " " .$ser->start_time;

                        if($s_date>$time)
                        {
                            $past=$past+1;
                        }
                        $ser->start_time=substr($ser->start_time, 0, -3);
                        $ser->end_time=substr($ser->end_time, 0, -3);
                    }
                }
                if($past==0)
                {
                    $value->cancel=false;
                }
                else
                {
                    if($value->active!=2 && $up==$past  && $value->active!=4)
                    {
                        $value->cancel=true;
                    }
                    else
                    {
                        $value->cancel=false;
                    }
                    // $value->booking_option="Reschedule";
                }

                $booked_date    = $value->bookdate;
                $today          =   date('d-m-Y');
                if(strtotime($booked_date) < strtotime($today))
                {
                    $value->booking_dt_status     =   "Past";
                } else
                {
                    if(strtotime($booked_date) == strtotime($today))
                    {
                        $booking_end_time = strtotime($booked_date);
                        $now        =  strtotime('now');
                        if($now > $booking_end_time){
                            $value->booking_dt_status     =   "Past";
                        } else {
                            $value->booking_dt_status     =   "Upcoming";
                        }
                    } else{
                        $value->booking_dt_status     =   "Upcoming";
                    }
                }
            }
        }

		return view('admin.booking.list',compact('activePage','booking','keyword','start_date','end_date','salon','salon_id','status',"status_id"));
	}

    public function ExportBooking(){

    }

    public function slots(Request $request)
    {
        $activePage="Block Slot";
        $todate=Carbon::now()->format('Y-m-d');
        $slots=DB::table("booking_services")
            ->join("booking", "booking.id","=","booking_services.booking_id")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
            ->whereNull('booking.deleted_at')
            ->where("booking.block",1)
            ->whereNull('booking_services.deleted_at')
            ->groupBy("booking_services.id")
            ->orderBy("booking_services.id","desc")->select("booking_services.*","salon_staffs.staff")->get();
            if(isset($slots) && count($slots)>0)
            {
                foreach($slots as $slot)
                {
                    $date=new Carbon($slot->date);
                    $date=$date->format('Y-m-d');
                    if($date<=$todate)
                    {
                        $slot->delete=0;
                    }
                    else
                    {
                        $slot->delete=1;
                    }
                }
            }
            // return $slots;
        return view("admin.booking.slots",compact("slots","activePage"));
    }

    public function delete(Request $request)
    {
        $rules=[
            "id"=>"required|exists:booking_services,id,deleted_at,NULL",
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
            $id=$request->id;
            $service=BookingServices::where("id",$id)->first();
            $booking_id=isset($service->booking_id)?$service->booking_id:'';

            $count=BookingServices::where("booking_id",$booking_id)->count();

            $delete=BookingServices::where("id",$id)->delete();
             if($delete)
                {
                    if($count==1)
                    {
                        $del_book=Booking::where("id",$booking_id)->delete();
                    }
                    return redirect()->back()->with("error", false)->with("msg", "Staff closed date deleted successfully");
                }
                else
                {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }

            }
    }

	public function transactions(Request $request)
	{
	    $activePage="Transactions";
        $salon=Salons::pluck("name","id");
        $salon_id=0;
        $time=Carbon::now();

         $start_date=$end_date=$new_date='';
         foreach (Booking::where('read_ta', '=',0)->get() as $data)
        {
            $read=Booking::where("id",$data->id)->update(["read_ta"=>1,"updated_at"=>$time]);
        }
            Session::put('transactions', 0);

		if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
            $booking=DB::table("booking")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
            ->whereNull('booking.deleted_at')->orderBy("booking.id","desc")
            ->where(function ($q) use ($keyword) {
            $q->where("salons.name","like",'%'.$keyword.'%')
            ->orWhere("booking_address.first_name","like",'%'.$keyword.'%')
            ->orWhere("booking_address.last_name","like",'%'.$keyword.'%')
            ->orWhere("booking_address.email","LIKE",'%'.$keyword.'%')
            ->orWhere("booking_address.phone","LIKE",'%'.$keyword.'%');
            });

        }
        else
        {
            $keyword='';
            $booking=DB::table("booking")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
            ->whereNull('booking.deleted_at')->orderBy("booking.id","desc");

        }
         if($request->salon_id && $request->salon_id!=0)
        {
            $salon_id=$request->salon_id;
            $booking->where("booking.salon_id",$request->salon_id);
        }
        if(isset($request->start_date) && $request->start_date!='' && isset($request->end_date) && $request->end_date!='')
        {
            $start_date=new Carbon($request->start_date);
            $from=$start_date->format('Y-m-d');
            $end_date=new Carbon($request->end_date);
            $to=$end_date->format('Y-m-d');
             $booking->whereBetween('booking.created_at', [$from, $to])->get();
            $start_date=$start_date->format('d-m-Y');
            $end_date=$end_date->format('d-m-Y');

        }
          if(isset($request->status_id) && $request->status_id!='')
        {
            $status_id=$request->status_id;
           $booking->where("booking.active",$status_id);
        }
         $booking=$booking
        ->where('booking.active',1)
         ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.pricing")
            ->paginate(20);
	      if(isset($booking)&& count($booking)>0)
        {
            foreach ($booking as $key => $value)
            {
                # code...amount
                $value->amount=$value->amount+$value->balance_amount;
                 if(isset($value->pricing)&& $value->pricing!=null)
                {
                    $value->mood_commission=$value->amount * ($value->pricing/100);
                }
                else
                {
                    $value->mood_commission="0.00";
                }
            }
        }
		return view('admin.booking.transaction',compact('activePage','booking','keyword','start_date','end_date','salon','salon_id'));
	}

    // public function export(Request $request)
    // {
    //     $activePage="Transactions";
    //     $rules=[
    //         "salon_id"=>"exists:salons,id,deleted_at,NULL",
    //         ];
    //     $msg=[
    //         "salon_id.required"=>"Salon ID is required"
    //          ];
    //          $validator=Validator::make($request->all(), $rules, $msg);

    //     if($validator->fails())
    //     {
    //          return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
    //     }
    //     else
    //     {
    //         if($request->salon_id)
    //         {
    //             return Excel::store('Export data', function($excel) {

    //             $excel->sheet('Sheet', function($sheet) {
    //             $salon_id=request()->salon_id;

    //             $booking=DB::table("booking")
    //             ->join("salons", "salons.id","=","booking.salon_id")
    //             ->join("booking_address", "booking_address.booking_id","=","booking.id")
    //             ->where('booking.salon_id',$salon_id)->orderBy("booking.id","desc")
    //             ->whereNull('booking.deleted_at')
    //             // ->where('booking.active',1)
    //             ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name")
    //             ->get();

    //             foreach ($booking as $key => $value)
    //             {
    //                 # code...
    //                 $value->amount=$value->amount+$value->balance_amount;
    //                  if(isset($value->pricing)&& $value->pricing!=null)
    //                 {
    //                     $value->mood_commission=$value->actual_amount * ($value->pricing/100);

    //                 }
    //                 else
    //                 {
    //                     $value->mood_commission="0.00";
    //                 }
    //                 $arr[] = ['Salon' => $value->name,'Booked By' => $value->first_name. "" .$value->last_name, 'Amount Paid' => $value->amount, 'Mood Commission' => $value->mood_commission,'VAT Amount' => "0.00 AED", 'Actual amount' => $value->actual_amount];
    //             }


    //            $sheet->fromArray($arr);
    //           });
    //             })->download('xls');

    //         }
    //         else
    //         {
    //              return Excel::store('Export data', function($excel) {

    //             $excel->sheet('Sheet', function($sheet) {
    //             $booking=DB::table("booking")
    //             ->join("salons", "salons.id","=","booking.salon_id")
    //             ->join("booking_address", "booking_address.booking_id","=","booking.id")
    //             ->whereNull('booking.deleted_at')
    //             // ->where('booking.active',1)
    //             ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name")
    //             ->get();

    //             foreach ($booking as $key => $value)
    //             {
    //                 # code...
    //                 $value->amount=$value->amount+$value->balance_amount;
    //                  if(isset($value->pricing)&& $value->pricing!=null)
    //                 {
    //                     $value->mood_commission=$value->amount * ($value->pricing/100);

    //                 }
    //                 else
    //                 {
    //                     $value->mood_commission="0.00";
    //                 }
    //                 $arr[] = ['Salon' => $value->name,'Booked By' => $value->first_name. "" .$value->last_name, 'Amount Paid' => $value->amount, 'Mood Commission' => $value->mood_commission,'VAT Amount' => "0.00 AED", 'Actual amount' => $value->actual_amount];
    //             }


    //            $sheet->fromArray($arr);
    //           });
    //             })->download('xls');

    //         }

    //     }
    // }

    public function export(Request $request)
    {
        $activePage="Transactions";
        $rules=[
            "salon_id"=>"exists:salons,id,deleted_at,NULL",
            ];
        $msg=[
            "salon_id.required"=>"Salon ID is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            if($request->salon_id)
            {
                // return Excel::store('Export data', function($excel) {

               // $excel->sheet('Sheet', function($sheet) {
                $salon_id=request()->salon_id;

                $booking=DB::table("booking")
                        ->join("salons", "salons.id","=","booking.salon_id")
                        ->join("booking_address", "booking_address.booking_id","=","booking.id")
                        ->where('booking.salon_id',$salon_id)->orderBy("booking.id","desc")
                        ->whereNull('booking.deleted_at')
                        // ->where('booking.active',1)
                        ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name")
                        ->get();

                foreach ($booking as $key => $value)
                {
                    # code...
                    $value->amount=$value->amount+$value->balance_amount;
                     if(isset($value->pricing)&& $value->pricing!=null)
                    {
                        $value->mood_commission=$value->actual_amount * ($value->pricing/100);

                    }
                    else
                    {
                        $value->mood_commission="0.00";
                    }
                    $arr[] = ['Salon' => $value->name,'Booked By' => $value->first_name. "" .$value->last_name, 'Amount Paid' => $value->amount, 'Mood Commission' => $value->mood_commission,'VAT Amount' => "0.00 AED", 'Actual amount' => $value->actual_amount];
                }


                $booking_arr = $arr;
               // $sheet->fromArray($arr);
              // });
                // })->download('xls');

            }
            else
            {
                //  return Excel::store('Export data', function($excel) {

                // $excel->sheet('Sheet', function($sheet) {
                $booking=DB::table("booking")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->join("booking_address", "booking_address.booking_id","=","booking.id")
                ->whereNull('booking.deleted_at')
                // ->where('booking.active',1)
                ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name")
                ->get();

                foreach ($booking as $key => $value)
                {
                    # code...
                    $value->amount=$value->amount+$value->balance_amount;
                     if(isset($value->pricing)&& $value->pricing!=null)
                    {
                        $value->mood_commission=$value->amount * ($value->pricing/100);

                    }
                    else
                    {
                        $value->mood_commission="0.00";
                    }
                    $arr[] = ['Salon' => $value->name,'Booked By' => $value->first_name. "" .$value->last_name, 'Amount Paid' => $value->amount, 'Mood Commission' => $value->mood_commission,'VAT Amount' => "0.00 AED", 'Actual amount' => $value->actual_amount];
                }
                $booking_arr = $arr;


              //  $sheet->fromArray($arr);
              // });
              //   })->download('xls');

            }

            // $booking_report
            // return Excel::download(new BookingExport($booking_arr), 'Booking Report.xlsx');

            // return Excel::download(BookingExport, 'BookingReport.xlsx');

            return $booking_arr;

        }
    }


    public function invoice(Request $request)
    {
          $rules=[
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
            ];
        $msg=[
            "email.required"=>"Email is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $activePage="Booking";
            $today=Carbon::now()->format("d-m-Y");
            $terms_c=Content::where("id",3)->select("id","title","description","created_at")->first();
            $terms=isset($terms_c->description)?$terms_c->description:'';
            $booking_id=$request->booking_id;
            $booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
	    	->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')
	    	// ->where('booking.active',1)
	    	->where('booking.id',$booking_id)
	        ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.pricing","booking_address.address")->first();
            if(isset($booking))
            {
                $o_id=10000+$booking_id;
                $orderId="MD".$o_id;
                $booking->orderId=$orderId;
                 $services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$booking_id)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                $booking->amount=$booking->amount+$booking->balance_amount;
                if(isset($booking->pricing)&& $booking->pricing!=null)
                {
                    $booking->mood_commission=$booking->amount * ($booking->pricing/100);
                }
                else
                {
                    $booking->mood_commission="0.00";
                }
            }
            $data = ['booking' => $booking,'today'=>$today];
        	return view('admin.booking.invoice',compact('booking','activePage','today','terms','services'));
        }
    }

    public function details(Request $request)
    {
          $rules=[
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
            ];
        $msg=[
            "email.required"=>"Email is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $activePage="Booking";

            $booking_id =   $request->booking_id;
            $booking    =   DB::table("booking")
                            ->join("salons", "salons.id","=","booking.salon_id")
                            ->join("booking_address", "booking_address.booking_id","=","booking.id")
                            ->whereNull('booking.deleted_at')
                            // ->where('booking.active',1)
                            ->where('booking.id',$booking_id)
                            ->select("booking.*",'booking.staffs as allstaffs',"booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.pricing","booking_address.address")->first();
            if(isset($booking))
            {
                 $services= DB::table("booking_services")
                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                    ->where("booking_services.booking_id",$booking_id)
                    ->whereNull('booking_services.deleted_at')
                    ->whereNull('salon_services.deleted_at')
                    ->whereNull('salon_staffs.deleted_at')
                    ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
                $booking->amount=$booking->amount+$booking->balance_amount;
                if(isset($booking->pricing)&& $booking->pricing!=null)
                {
                    $booking->mood_commission=$booking->amount * ($booking->pricing/100);
                }
                else
                {
                    $booking->mood_commission="0.00";
                }
            }
            $booking_address = DB::table('booking_address')->where('booking_id',$booking_id)->first();
        	return view('admin.booking.details',compact('booking','activePage','services','booking_address'));
        }
    }

    public function cancel(Request $request)
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
            return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all());
        }
        else
        {
            $time=Carbon::now();
            $booking_id=$request->booking_id;
           //check booking_status
            $check_book=Booking::where("id",$booking_id)->where("active",2)->first();
            if(isset($check_book))
            {
            return redirect()->back()->with("error", true)->with("msg", "You already cancelled this booking");
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
                ->where('booking.id',$booking_id)
                   ->select("booking.id","salons.name as salon_name","salons.pricing","booking_address.first_name","booking_address.last_name","booking_address.email","booking_address.address","booking_address.phone","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid","salons.email as salon_email","booking.transaction_id")
                ->first();

                $reason=isset($request->reason)?$request->reason:'Request for Cancellation';
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

                    $res = curl_exec($curl);
                    $err = curl_error($curl);
                    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                     if($err)
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                    }

                    else
                    {
                        $res=json_decode($res);
                         $result_res='';
                        $result_res=$res;
                        if(isset($res->refund_request_id))
                        {
                            $result_res=isset($res->result)?$res->result:'No response from gateway';

                            $up_booking=Booking::where("id",$booking_id)->update(["refund_response"=>$result_res,"refund_id"=>$res->refund_request_id,"updated_at"=>$time]);
                        return redirect()->back()->with("error", false)->with("msg", "Your booking cancelled and refund initiated");
                        }
                        else
                        {
                             $up_booking=Booking::where("id",$booking_id)->update(["refund_response"=>$result_res,"updated_at"=>$time]);

                            return redirect()->back()->with("error", false)->with("msg", "Your booking cancelled successfully");
                        }

                    }
                }

                return redirect()->back()->with("error", false)->with("msg", "Your booking cancelled successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }

        }
    }


    public function DownloadBookingReport(Request $request)
    {
        $status_id='';
        $time=Carbon::now();
        $start_date=$end_date=$new_date='';
		if(isset($request->keyword)&&$request->keyword!="")
	    {
	        $keyword   =$request->keyword;
	    	$booking   =DB::table("booking")
            	    	->join("salons", "salons.id","=","booking.salon_id")
                        ->join("booking_address", "booking_address.booking_id","=","booking.id")
            	        ->whereNull('booking.deleted_at')->orderBy("booking.id","desc")
                        ->where("booking.block","!=",1)
                        ->where("booking_address.first_name","!=",'')
                        ->where("booking_address.email","!=",'')
            	        ->where(function ($q) use ($keyword) {
            	    	$q->where("salons.name","like",'%'.$keyword.'%')
            	    	->orWhere("booking_address.first_name","like",'%'.$keyword.'%')
            	    	->orWhere("booking_address.last_name","like",'%'.$keyword.'%')
            	        ->orWhere("booking_address.email","LIKE",'%'.$keyword.'%')
            	        ->orWhere("booking_address.phone","LIKE",'%'.$keyword.'%');
            	        });
	    }
	    else
	    {
	    	$keyword='';
	    	$booking=DB::table("booking")
                        ->join("salons", "salons.id","=","booking.salon_id")
                        ->join("booking_address", "booking_address.booking_id","=","booking.id")
                        ->where("booking.block","!=",1)
                        ->where("booking_address.first_name","!=",'')
                        ->where("booking_address.email","!=",'')
                        ->whereNull('booking.deleted_at')->orderBy("booking.id","Asc");

	    }

        if($request->salon_id && $request->salon_id!=0)
        {
            $salon_id=$request->salon_id;
            $booking->where("booking.salon_id",$request->salon_id);
        }

        if(isset($request->start_date) && $request->start_date!='' && isset($request->end_date) && $request->end_date!='')
        {
            $start_date=new Carbon($request->start_date);
            $from=$start_date->format('Y-m-d');
            $end_date=new Carbon($request->end_date);
            $to=$end_date->format('Y-m-d');
            $booking->whereBetween('booking.created_at', [$from, $to])->get();
            $start_date=$start_date->format('d-m-Y');
            $end_date=$end_date->format('d-m-Y');
        }

        if(isset($request->status_id) && $request->status_id!='')
        {
            $status_id=$request->status_id;
            $booking->where("booking.active",$status_id);
        }
        DB::statement(DB::raw('set @rownum=0'));

        $booking    =   $booking->select(DB::raw("(@rownum:=@rownum + 1) AS sno"),
                        "booking.id as bookingid",
                        "salons.name as salonname","booking.bookdate",
                        'booking.bookstrttime','booking.bookendtime',
                        'booking.staffs',
                        DB::raw("IFNULL(booking.promocode, '0') as Promocode"),
                        'booking.amount','booking.special_requests',
                        DB::raw("
                        (
                            CASE
                                WHEN booking.active=1 THEN 'Success'
                                ELSE 'Cancelled'
                            END
                        ) AS activestatus"),"booking_address.first_name as username",
                        "booking_address.email as useremail",'booking.created_at')->get();
        // print("<pre>");print_r($booking);die;
        if(isset($booking)&& count($booking)>0)
        {

            foreach($booking as $key=>$value)
            {
                $book_services  =   [];

                $assigned_staffs = $value->staffs;
                $staffs = explode( ',', $assigned_staffs);
                $staff_names =  [];
                foreach($staffs as $staff){
                    array_push($staff_names,DB::table('salon_staffs')->where('id',$staff)->first()->staff);
                }
                $value->staffs  =   implode(', ', $staff_names);
                $booking_time   =   $this->RemoveTimeBeforeAndAfterDuration($value->bookstrttime,$value->bookendtime);
                $value->bookstrttime    =   $booking_time['start_time'];
                $value->bookendtime     =   $booking_time['end_time'];

                $book_services  =   DB::table("booking_services")
                                    ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
                                    ->join("salon_services", "salon_services.id","=","booking_services.service_id")
                                    ->where("booking_services.booking_id",$value->bookingid)
                                    ->whereNull('booking_services.deleted_at')
                                    ->whereNull('salon_services.deleted_at')
                                    ->whereNull('salon_staffs.deleted_at')
                                    ->select("salon_services.service",
                                    "booking_services.guest_count",
                                    DB::raw("
                                    (
                                        CASE
                                            WHEN booking_services.service_type=1 THEN 'Back to Back'
                                            ELSE 'At the Same Time'
                                        END
                                    ) AS service_type"))->get();
                $service_arr    =   [];
                foreach($book_services as $k=>$service){
                    $service_arr[$k]['service']    =   $service->service;
                    $service_arr[$k]['guest_count']    =   $service->guest_count;
                    $service_arr[$k]['service_type']    =   $service->service_type;
                }
                // dd($service_arr);
                $new_arr    =   [];
                foreach($service_arr as $s=>$serv_str){
                    $new_arr[$s] =  implode(",",$serv_str);
                }
                $value->booking_service = implode(" | ",$new_arr);
                $value->booked_on=   Carbon::parse($value->created_at)->toFormattedDateString();
                unset($value->created_at);
            }
            return Excel::download(new BookingExport($booking), 'BookingReport.xlsx');
        } else {
            return redirect()->back()->with("error", true)->with("msg", "No Records to Download Report")->withInput();
        }
    }
}
