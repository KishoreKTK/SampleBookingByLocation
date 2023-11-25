<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Salons;
use App\Offers;
use Carbon\Carbon;
use App\OfferSalons;
use App\OfferServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;

class SalonOffersController extends Controller
{
    public function index(Request $request)
    {
    	$activePage="Offers";
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
        // $offers=Offers::get();
    	$offers=DB::table("offers")
        ->join("offer_services", "offer_services.offer_id","=","offers.id")
        ->where("offer_services.salon_id",$salon_id)
        ->where("offers.offer_type",3)
        ->whereNull('offers.deleted_at')
        ->whereNull('offer_services.deleted_at')
        ->groupBy("offers.id")
        ->select("offers.*")
        ->get();
        if(isset($offers)&& count($offers)>0)
        {
            foreach($offers as $offer)
            {
                if(isset($offer->image)&&$offer->image!='')
                {
                    $offer->thumbnail= env("IMAGE_URL")."offers/thumbnails/".$offer->image;
                    $offer->image= env("IMAGE_URL")."offers/thumbnails/".$offer->image;
                }
                else
                {
                  $offer->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $offer->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }
            }
        }
    	return view("salon.offers.list",compact('activePage','offers'));
    }
    public function add(Request $request)
    {
    	$activePage="Offers";
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
        $type=["Select Any","Percentage","Amount"];
        $services=DB::table("salon_services")
        ->join("categories", "categories.id","=","salon_services.category_id")
        ->where('salon_services.salon_id',$salon_id)
        ->whereNull('salon_services.deleted_at')
        ->groupBy("salon_services.id")
        ->select("salon_services.*","categories.category")->select("salon_services.service","salon_services.id")->get();
    	return view("salon.offers.add",compact('activePage','type','services'));
    }
    public function add_offer(Request $request)
    {
    	$activePage="Offers";
    	  $rules=[
            "title"=>"required",
            "promocode"=>"unique:offers,promocode",
            'amount_type'=>'required',
            "amount"=>"required",
            "start_date"=>"required",
            "end_date"=>"required",
            "services"=>"required",
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
            $title=$request->title;
            $image=$request->image;
            $promocode=$request->promocode;
            $amount_type=$request->amount_type;
            $amount=$request->amount;
            $description=$request->description;
            $amount=$request->amount;
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $services=$request->services;
            $min_amount=$request->min_amount;
            $max_discount=$request->max_discount;
            $time=Carbon::now();
             $url=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/offers/";
            $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/offers/thumbnails/";
            $imageName="";
            if (isset($image))
            {
                $imageName = md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();
                $desti=$url.$imageName;
                $t_desti=$turl.$imageName;

                 $resize=Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                $resize->save($t_desti);
                $saveImage=$image->move($url, $imageName);
            }

             $new_offer=Offers::insertGetId(['promocode'=>$promocode,'title'=>$title,'image'=>$imageName,"offer_type"=>3,'amount_type'=>$amount_type,'amount'=>$amount,'description'=>$description,'start_date'=>$start_date,'end_date'=>$end_date,'min_amount'=>$min_amount,'active'=>1,'max_discount'=>$max_discount,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                $start=$end=0;
                if(isset($request->services))
                {
                    foreach($request->services as $service)
                    {
                        $start=$start++;
                        $insert=OfferServices::insert([
                            "salon_id"=>$salon_id,
                            "offer_id"=>$new_offer,
                            "service_id"=>$service,
                            'created_at'=> $time,
                            "updated_at"=>$time
                            ]);
                        if($insert)
                        {
                            $end=$end++;
                        }
                    }
                }
               
                if($start==$end)
                {
                    DB::commit();
                    return redirect()->back()->with("error", false)->with("msg", "New offer added successfully")->withInput();
                }
                else
                {
                    DB::rollback();
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }
            }
            else
            {
                DB::rollback();
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
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
            $c_services=[];
        	$activePage="Offers";
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
            $offer=Offers::where("id",$id)->first();
            $type=["Select Any","Percentage","Amount"];
            $services=DB::table("salon_services")
            ->join("categories", "categories.id","=","salon_services.category_id")
            ->where('salon_services.salon_id',$salon_id)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*","categories.category")->select("salon_services.service","salon_services.id")->get();

            foreach (OfferServices::where('salon_id', '=',$salon_id)->where('offer_id', '=',$id)->get() as $each)
            {
                $c_services[]=$each->service_id;
            }
        }

    	return view("salon.offers.edit",compact('activePage','offer','id','type','services','c_services'));
    }
     public function update(Request $request)
    {
        $activePage="Offers";
          $rules=[
            "title"=>"required",
            // "promocode"=>"unique:offers,promocode",
            'amount_type'=>'required',
            "amount"=>"required",
            "start_date"=>"required",
            "end_date"=>"required",
            "services"=>"required",
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

            $title=$request->title;
            $id=$request->id;
            $promocode=$request->promocode;
            $amount_type=$request->amount_type;
            $amount=$request->amount;
            $description=$request->description;
            $amount=$request->amount;
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $services=$request->services;
            $min_amount=$request->min_amount;
            $max_discount=$request->max_discount;
            $time=Carbon::now();
            $pick=Offers::where("id",$id)->first();
            $url=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/offers/";
            $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/offers/thumbnails/";

             if (isset($request->image))
            {
                $imageName = md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();
                $desti=$url.$imageName;
                $t_desti=$turl.$imageName;

                 $resize=Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                $resize->save($t_desti);
                $saveImage=$image->move($url, $imageName);
            }
            else
            {
              $imageName=isset($pick->image)?$pick->image:'';
            }
             $new_offer=Offers::where("id",$id)->update(['promocode'=>$promocode,'title'=>$title,'amount_type'=>$amount_type,'amount'=>$amount,'description'=>$description,'start_date'=>$start_date,'end_date'=>$end_date,'min_amount'=>$min_amount,'active'=>1,'max_discount'=>$max_discount,'image'=>$imageName,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                $start=$end=0;
                if(isset($request->services))
                {
                     foreach (OfferServices::where('offer_id', '=',$id)->where("salon_id",$salon_id)->get() as $service)
                    {
                        $delete_services=OfferServices::where("id",$service->id)->delete();
                    }
                    foreach($request->services as $service)
                    {
                        $start=$start++;
                        $insert=OfferServices::insert([
                            "salon_id"=>$salon_id,
                            "offer_id"=>$id,
                            "service_id"=>$service,
                            'created_at'=> $time,
                            "updated_at"=>$time
                            ]);
                        if($insert)
                        {
                            $end=$end++;
                        }
                    }
                }
               
                if($start==$end)
                {
                    DB::commit();
                    return redirect()->back()->with("error", false)->with("msg", "Your offer updated successfully")->withInput();
                }
                else
                {
                    DB::rollback();
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }
            }
            else
            {
                DB::rollback();
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
            $salon_id=Auth::guard('salon-web')->user()->id;

            $delete=Offers::where("id",$id)->delete();
            if($delete)
            {
                  foreach (OfferServices::where('offer_id', '=',$id)->where("salon_id",$salon_id)->get() as $service)
                {
                    $delete_services=OfferServices::where("id",$service->id)->delete();
                }
              return redirect()->back()->with("error", false)->with("msg", "Offer deleted successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process");

            }
        }

    }
}
