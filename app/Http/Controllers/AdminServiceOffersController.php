<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\Salons;
use Carbon\Carbon;
use App\SalonServices;
use App\ServiceOffers;
use Illuminate\Http\Request;

class AdminServiceOffersController extends Controller
{
    public function index(Request $request)
    {
    	$rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $salon_id=$request->salon_id;
            $service_id=$request->service_id;
	    	$activePage="Salons";
            $offers= DB::table("service_offers")
            ->join("salon_services", "salon_services.id","=","service_offers.service_id")
            ->where('salon_services.salon_id',$salon_id)->where("service_id",$service_id)
            ->whereNull('service_offers.deleted_at')
            ->whereNull('salon_services.deleted_at')
            ->groupBy("service_offers.id")->select("service_offers.*","salon_services.service","salon_services.amount")->get();
	        // $offers=ServiceOffers::where("salon_id",$salon_id)->where("service_id",$service_id)->get();
	    	return view("admin.service_offers.list",compact('activePage','offers','salon_id','service_id'));
    	}
    }

    public function add(Request $request)
    {
    	$rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $salon_id=$request->salon_id;
            $service_id=$request->service_id;
            $service=SalonServices::where("id",$service_id)->first();
	    	$activePage="Salons";
	    	// return view("admin.service_offers.add",compact('activePage','type','service','salons','salon_id','service_id'));
            return view("admin.service_offers.add",compact('activePage','service','salon_id','service_id'));
	    }
    }

    public function add_offer(Request $request)
    {
    	$activePage="Salons";
    	$rules=[
    	  	"salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "discount_price"=>"required",
            "start_date"=>"required",
            "end_date"=>"required",
            ];
        $msg=[
            "service.required"=>"Service is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $salon_id=$request->salon_id;
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

            $new_offer=ServiceOffers::insertGetId(['discount_price'=>$discount_price,'salon_id'=>$salon_id,'service_id'=>$service_id,'start_date'=>$start_date,'end_date'=>$end_date,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                return redirect()->back()->with("error", false)->with("msg", "New offer added successfully");
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
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $salon_id=$request->salon_id;
            $service_id=$request->service_id;
        	$activePage="Salons";
            $offer=ServiceOffers::where("id",$id)->first();
            $service=SalonServices::where("id",$service_id)->first();
           
            return view("admin.service_offers.edit",compact('activePage','offer','service','id','salon_id','service_id'));
        }
    }
    
    public function update(Request $request)
    {
        $activePage="Salons";
          $rules=[
            "id"=>"required|exists:service_offers,id,deleted_at,NULL",
          	"salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "service_id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "discount_price"=>"required",
            "start_date"=>"required",
            "end_date"=>"required",
            ];
        $msg=[
            "salons.required"=>"Service is required",
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
            $salon_id=$request->salon_id;
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
             $new_offer=ServiceOffers::where("id",$id)->update(['discount_price'=>$discount_price,'salon_id'=>$salon_id,'service_id'=>$service_id,'start_date'=>$start_date,'end_date'=>$end_date,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
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
