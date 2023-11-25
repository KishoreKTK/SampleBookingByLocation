<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Salons;
use App\Approvals;
use Carbon\Carbon;
use App\SalonServices;
use App\ServiceOffers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonServiceOffersController extends Controller
{
     public function index(Request $request)
    {
    	$rules=[
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
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
            $service_id=$request->service_id;
	    	$activePage="Salons";
            $offers=DB::table("service_offers")
            ->join("salon_services", "salon_services.id","=","service_offers.service_id")
            ->where('salon_services.salon_id',$salon_id)->where("service_id",$service_id)
            ->whereNull('service_offers.deleted_at')
            ->whereNull('salon_services.deleted_at')
            ->groupBy("service_offers.id")->select("service_offers.*","salon_services.service","salon_services.amount")->get();
	        // $offers=ServiceOffers::where("salon_id",$salon_id)->where("service_id",$service_id)->get();
	    	return view("salon.service_offers.list",compact('activePage','offers','salon_id','service_id'));
    	}
    }
    public function add(Request $request)
    {
    	$rules=[
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
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
            $service_id=$request->service_id;
	    	$activePage="Salons";
            $service=SalonServices::where("id",$service_id)->first();

	    	return view("salon.service_offers.add",compact('activePage','type','salons','salon_id','service_id','service'));
	    }
    }
    public function add_offer(Request $request)
    {
    	$activePage="Salons";
    	  $rules=[
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "discount_price"=>"required|integer",
            "start_date"=>"required",
            "end_date"=>"required",
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
            $service_id=$request->service_id;
            $title=$request->title;
            $discount_price=$request->discount_price;
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $time=Carbon::now();
           	$ser_amount=SalonServices::where("id",$service_id)->first()->amount;
           	if($ser_amount<=$discount_price)
           	{
                return redirect()->back()->with("error", true)->with("msg", "Discount price should be lesser than the actual price");
           	}

            $new_offer=ServiceOffers::insertGetId(['discount_price'=>$discount_price,'salon_id'=>$salon_id,'service_id'=>$service_id,'approved'=>0,'pending'=>0,'start_date'=>$start_date,'end_date'=>$end_date,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                 $log="Added a new offer.";
                $action="Added";

                 $new_approve=DB::table("approvals")->insertGetId(["action"=>$action,"salon_id"=>$salon_id,"type_id"=>3,'service_id'=>$service_id,"offer_id"=>$new_offer,"title"=>$log, 'created_at'=> $time,"updated_at"=>$time]);

                 if($new_approve)
                {
                    // $add_log=DB::table("service_offers_log")->insertGetId(["audit_id"=>$new_approve,'discount_price'=>$discount_price,'salon_id'=>$salon_id,'service_id'=>$service_id,'start_date'=>$start_date,'end_date'=>$end_date,'created_at'=> $time,"updated_at"=>$time]);
                }
                return redirect(env("ADMIN_URL").'/salon/services/offers?service_id='.$service_id)->with("error", false)->with("msg", "New offer added successfully")->withInput();
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }

        }

    }
    public function edit(Request $request)
    {
        $rules=[
            "id"=>"required|exists:service_offers,id,deleted_at,NULL",
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
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

            $service_id=$request->service_id;
        	$activePage="Salons";
            $offer=ServiceOffers::where("id",$id)->first();
            $service=SalonServices::where("id",$service_id)->first();
            $approved=isset($offer->pending)?$offer->pending:1;
            
            return view("salon.service_offers.edit",compact('activePage','approved','offer','id','salon_id','service_id','service'));
        }

    }
     public function update(Request $request)
    {
        $activePage="Salons";
          $rules=[
            "id"=>"required|exists:service_offers,id,deleted_at,NULL",
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "discount_price"=>"required|integer",
            "start_date"=>"required",
            "end_date"=>"required",
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
            $title=$request->title;
            $id=$request->id;
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
            $service_id=$request->service_id;
            $discount_price=$request->discount_price;
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $time=Carbon::now();
 			$ser_amount=SalonServices::where("id",$service_id)->first()->amount;

           	if($ser_amount<=$discount_price)
           	{
                return redirect()->back()->with("error", true)->with("msg", "Discount price should be lesser than the actual price");
           	}
             // $new_offer=ServiceOffers::where("id",$id)->update(['discount_price'=>$discount_price,'salon_id'=>$salon_id,'service_id'=>$service_id,'start_date'=>$start_date,'end_date'=>$end_date,"updated_at"=>$time]);
            $new_offer=ServiceOffers::where("id",$id)->update(["pending"=>0,"updated_at"=>$time]);
            if($new_offer)
            {
                $log="Updated salon offer.";
                $check_approval=Approvals::where("salon_id",$salon_id)->where("service_id",$service_id)->where("offer_id",$id)->where("type_id",3)->first();
                    $action="Updated";
                
                if(isset($check_approval)&& !empty($check_approval))
                {

                    $new_approve=Approvals::where("salon_id",$salon_id)->where("service_id",$service_id)->where("type_id",3)->where("offer_id",$id)->update(["action"=>$action,"offer_id"=>$id,"title"=>$log,"updated_at"=>$time]);
                    $audit_id=$check_approval->id;

                }
                else
                {
                    // $action="Added";

                    $new_approve=Approvals::insertGetId(["salon_id"=>$salon_id,"action"=>$action,"offer_id"=>$id,"type_id"=>3,'service_id'=>$service_id,"title"=>$log, 'created_at'=> $time,"updated_at"=>$time]);
                    $audit_id=$new_approve;

                }
                 if($new_approve)
                {
                    $add_log=DB::table("service_offers_log")->insertGetId(["audit_id"=>$audit_id,'discount_price'=>$discount_price,'salon_id'=>$salon_id,"offer_id"=>$id,'service_id'=>$service_id,'start_date'=>$start_date,'end_date'=>$end_date,'created_at'=> $time,"updated_at"=>$time]);
                }

                return redirect()->back()->with("error", false)->with("msg", "Your offer updated successfully")->withInput();
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }

        }

    }
  
     public function delete(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:service_offers,id,deleted_at,NULL",
        ];
        
        $msg=
        [
            "id.required"=>"Id field is empty",
        ];
        
        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $id=$request->id;

            $delete=ServiceOffers::where("id",$id)->delete();

            if($delete)
            {
              return redirect()->back()->with("error", false)->with("msg", "Offer deleted successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process");

            }
        }

    }

}
