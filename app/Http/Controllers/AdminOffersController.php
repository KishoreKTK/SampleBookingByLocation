<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\Salons;
use App\Offers;
use Carbon\Carbon;
use App\OfferSalons;
use App\OfferServices;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class AdminOffersController extends Controller
{
     public function index(Request $request)
    {
    	$activePage="Offers";
        $offers=DB::table("offers")
            ->join("salons", "salons.id","=","offers.salon_id")
            ->whereNull('offers.deleted_at')
            ->whereNull('salons.deleted_at')->select("offers.*","salons.id as salon_id","salons.name")->get();
            if(isset($offers)&& count($offers)>0)
            {
                foreach($offers as $offer)
                {
                    $start_date=new Carbon($offer->start_date);
                    $offer->start_date=$start_date->format('d-m-Y');
                    $end_date=new Carbon($offer->end_date);
                    $offer->end_date=$end_date->format('d-m-Y');
                }
            }
        
    	return view("admin.offers.list",compact('activePage','offers'));
    }
    public function add(Request $request)
    {
    	$activePage="Offers";
        $salons=Salons::orderBy("created_at","desc")->pluck("name","id");
    	return view("admin.offers.add",compact('activePage','type','salons'));
    }
    public function add_offer(Request $request)
    {
    	$activePage="Offers";
    	  $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "amount"=>"required",
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
            $title=$request->title;
            $amount=$request->amount;
            $start_date=new Carbon($request->start_date);
            $start_date=$start_date->format('Y-m-d');
            $end_date=new Carbon($request->end_date);
            $end_date=$end_date->format('Y-m-d');

            $salons=$request->salons;
            $time=Carbon::now();
            $salon_id=$request->salon_id;
           
             $new_offer=Offers::insertGetId(['title'=>$title,'amount'=>$amount,'start_date'=>$start_date,'end_date'=>$end_date,"salon_id"=>$salon_id,'active'=>1,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                return redirect()->back()->with("error", false)->with("msg", "New offer added successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured")->withInput();
            }

        }

    }
    public function edit(Request $request)
    {
        $rules=[
            "id"=>"required|exists:offers,id,deleted_at,NULL",
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
        	$activePage="Offers";
            

        	$salons=Salons::orderBy("created_at","desc")->pluck("name","id");

            $offer=Offers::where("id",$id)->first();
              $start_date=new Carbon($offer->start_date);
            $offer->start_date=$start_date->format('d-m-Y');
            $end_date=new Carbon($offer->end_date);
            $offer->end_date=$end_date->format('d-m-Y');

            
            return view("admin.offers.edit",compact('activePage','offer','id','salons'));
        }

    }
     public function update(Request $request)
    {
        $activePage="Offers";
          $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "amount"=>"required",
            "start_date"=>"required",
            "end_date"=>"required",
            ];
        $msg=[
            "salon_id.required"=>"Salon is required",
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
            $amount=$request->amount;
             $start_date=new Carbon($request->start_date);
            $start_date=$start_date->format('Y-m-d');
            $end_date=new Carbon($request->end_date);
            $end_date=$end_date->format('Y-m-d');

            $time=Carbon::now();
            $pick=Offers::where("id",$id)->first();
 			
             $new_offer=Offers::where("id",$id)->update(['title'=>$title,'amount'=>$amount,'salon_id'=>$salon_id,'start_date'=>$start_date,'end_date'=>$end_date,'active'=>1,'created_at'=> $time,"updated_at"=>$time]);

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
    public function active(Request $request)
    {
        
        $rules=[
            "id"=>"required|exists:offers,id,deleted_at,NULL",
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
            $time=Carbon::now();

            $update=Offers::where("id",$id)->update(["active"=>1,"updated_at"=>$time]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully markerd as active");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
            }

        }
    }
    public function inactive(Request $request)
    {
        $rules=[
            "id"=>"required|exists:offers,id,deleted_at,NULL",
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
            $time=Carbon::now();
            $update=Offers::where("id",$id)->update(["active"=>0,"updated_at"=>$time]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully marked as inactive");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
            }
            
        }
    }
     public function delete(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:offers,id,deleted_at,NULL",
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

            $delete=Offers::where("id",$id)->delete();
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
