<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Excel;
use Session;
use Validator;
use App\Salons;
use App\Booking;
use App\Content;
use Carbon\Carbon;
use App\Customers;
use App\SalonStaffs;
use App\BookingHold;
use App\WorkingHours;
use App\StaffServices;
use App\SalonServices;
use App\BookingAddress;
use App\BookingServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonBookingController extends Controller
{
    public function booking(Request $request)
	{
        // $salon_id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }
        $status=["Pending","Success","Cancelled","Processed","Rejected"];
        $start_date=$end_date=$new_date='';
        $status_id='';
	    $activePage="Booking";
        $time=Carbon::now();

        foreach (Booking::where('read_s', '=',0)->where("salon_id",$salon_id)->get() as $data)
        {
            $read=Booking::where("id",$data->id)->update(["read_s"=>1,"updated_at"=>$time]);
        }
            Session::put('sbooking', 0);

		if(isset($request->keyword)&&$request->keyword!="")
	    {
	        $keyword=$request->keyword;
	    	$booking=DB::table("booking")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')->where("booking.block","!=",1)
             ->where("booking_address.first_name","!=",'')
            ->where("booking_address.email","!=",'')
	        ->where(function ($q) use ($keyword) {
	    	$q->where("salons.name","like",'%'.$keyword.'%')
	    	->orWhere("booking_address.first_name","like",'%'.$keyword.'%')
	    	->orWhere("booking_address.last_name","like",'%'.$keyword.'%')
	        ->orWhere("booking_address.email","LIKE",'%'.$keyword.'%')
	        ->orWhere("booking_address.phone","LIKE",'%'.$keyword.'%');
	        })
	        ->where('booking.salon_id',$salon_id)->orderBy("booking.id","desc");
	    }
	    else
	    {
	    	$keyword='';
	    	$booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')
            ->where("booking.block","!=",1)
             ->where("booking_address.first_name","!=",'')
            ->where("booking_address.email","!=",'')
	        ->where('booking.salon_id',$salon_id)->orderBy("booking.id","desc");
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
         
         $booking=$booking->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.phone","booking_address.email","salons.name")
            ->paginate(20);
		return view('salon.booking.list',compact('activePage','booking','keyword','start_date','end_date','status',"status_id"));
	}
	public function transactions(Request $request)
	{
        // $salon_id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }
         $start_date=$end_date=$new_date='';

	    $activePage="Transactions";
        $time=Carbon::now();

        foreach (Booking::where('read_ts', '=',0)->where("salon_id",$salon_id)->get() as $data)
        {
            $read=Booking::where("id",$data->id)->update(["read_ts"=>1,"updated_at"=>$time]);
        }
            Session::put('stransactions', 0);

		if(isset($request->keyword)&&$request->keyword!="")
	    {
	        $keyword=$request->keyword;
	    	$booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
	    	->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')
	        ->where(function ($q) use ($keyword) {
	    	$q->where("salons.name","like",'%'.$keyword.'%')
	    	->orWhere("booking_address.first_name","like",'%'.$keyword.'%')
	    	->orWhere("booking_address.last_name","like",'%'.$keyword.'%')
	        ->orWhere("booking_address.email","LIKE",'%'.$keyword.'%')
	        ->orWhere("booking_address.phone","LIKE",'%'.$keyword.'%');
	        })
	        ->where('booking.salon_id',$salon_id)
	        ->where('booking.active',1)->orderBy("booking.id","desc");

	    }
	    else
	    {
	    	$keyword='';
	    	$booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')
	        ->where('booking.salon_id',$salon_id)
	    	->where('booking.active',1)->orderBy("booking.id","desc");
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
        $booking=$booking->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.pricing")
            ->paginate(20);
	      if(isset($booking)&& count($booking)>0)
        {
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
            }
        }
		return view('salon.booking.transaction',compact('activePage','booking','keyword','start_date','end_date'));
	}
	    public function export(Request $request)
    {

        $activePage="Transactions";
        return Excel::create('Export data', function($excel) {

        $excel->sheet('Sheet', function($sheet) {
        // $salon_id=Auth::guard('salon-web')->user()->id;
        $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }
            
        $booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')
	        ->where('booking.salon_id',$salon_id)->orderBy("booking.id","desc")
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


       $sheet->fromArray($arr);
      });
        })->download('xls');     
 
    }
     public function invoice(Request $request)
    {
        // $salon_id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }

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
	        ->where('booking.salon_id',$salon_id)
	    	->where('booking.id',$booking_id)->orderBy("booking.id","desc")
	        ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.pricing","booking_address.address")->first();
            if(isset($booking))
            {
                 $o_id=10000+$booking_id;
                $orderId="PM".$o_id;
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
            
        	return view('salon.booking.invoice',compact('booking','activePage','today','services','terms'));

            $pdf = PDF::loadView('salon.booking.invoice', $data);
      
            return $pdf->download('download.pdf');
        }
    }
    public function details(Request $request)
    {
        // $salon_id=Auth::guard('salon-web')->user()->id;
         $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }

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

            $booking_id=$request->booking_id;
            $booking=DB::table("booking")
	    	->join("salons", "salons.id","=","booking.salon_id")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
	        ->whereNull('booking.deleted_at')
	    	// ->where('booking.active',1)
	        ->where('booking.salon_id',$salon_id)
	    	->where('booking.id',$booking_id)
	        ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.phone","booking_address.email","salons.name","salons.pricing","booking_address.address")->first();
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
            
        	return view('salon.booking.details',compact('booking','activePage','services'));

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
            $can_booking=Booking::where("id",$booking_id)->update(["active"=>2,"updated_at"=>$time]);
            if($can_booking)
            {
               
                $details=DB::table("booking")
                ->join("booking_services", "booking_services.booking_id","=","booking.id")
                ->join("salons", "salons.id","=","booking.salon_id")
                ->whereNull('booking.deleted_at')
                ->whereNull("booking_services.deleted_at")
                ->whereNull('salons.deleted_at')
                ->where('booking.id',$booking_id)
                  ->select("booking.*","salons.name as salon_name","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid")
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
                        'refund_amount'    => $details->amount,  
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
                        if(isset($res->refund_request_id))
                        {
                            $up_booking=Booking::where("id",$booking_id)->update(["refund_id"=>$res->refund_request_id,"updated_at"=>$time]);
                        return redirect()->back()->with("error", false)->with("msg", "Your booking cancelled and refund initiated");                                                      
                        }
                        else
                        {
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

   
}