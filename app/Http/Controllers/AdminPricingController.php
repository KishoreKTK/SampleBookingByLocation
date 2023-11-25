<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\Salons;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminPricingController extends Controller
{
    public function pricing(Request $request)
    {
        $activePage="Pricing";
        $pricing=DB::table("salons")
            ->leftJoin("countries", "countries.id","=","salons.country_id")
            ->whereNull('salons.deleted_at')
            ->orderBy('salons.pricing')
            ->select("salons.*","countries.name as country")
            ->get();
            if(isset($pricing)&& count($pricing)>0)
            {
	            foreach($pricing as $salon)
		        {
		            if(isset($salon->image)&&$salon->image!='')
		            {
		              $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
	                	$salon->image= env("IMAGE_URL")."salons/thumbnails/".$salon->image;

		            }
		            else
		            {
		                $salon->thumbnail= env("IMAGE_URL")."logo/salons.png";
		            }
		           
		        }
		    }

        return view('admin.pricing.list',compact('activePage','pricing'));
    }
     public function update(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
            "pricing"=>"required",
            "min_price"=>"required",
            ];
        $msg=[
            "pricing.required"=>"Pricing is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $id=$request->id;
            $pricing=$request->pricing;
            $min_price=$request->min_price;
            $time=Carbon::now();
          
            $new_pricing=Salons::where("id",$id)->update(['pricing'=>$pricing,"min_price"=>$min_price,"updated_at"=>$time]);

            if($new_pricing)
            {
                return redirect()->back()->with("error", false)->with("msg", "Pricing updated successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    } 
}
