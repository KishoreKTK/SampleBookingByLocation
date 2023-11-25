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
        $offers=Offers::get();
    	
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
    	return view("admin.offers.list",compact('activePage','offers'));
    }
    public function add(Request $request)
    {
    	$activePage="Offers";
        $type=["Select Any","Percentage","Amount"];
        $salons=Salons::orderBy("created_at","desc")->select("id","name")->get();
    	return view("admin.offers.add",compact('activePage','type','salons'));
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
            $image=$request->image;
            $promocode=$request->promocode;
            $amount_type=$request->amount_type;
            $amount=$request->amount;
            $description=$request->description;
            $amount=$request->amount;
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $salons=$request->salons;
            $min_amount=$request->min_amount;
            $max_discount=$request->max_discount;
            $time=Carbon::now();
             $url=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/offers/";
            $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/offers/thumbnails/";
            $imageName="";
            if(isset($request->checkall)&& $request->checkall=="on")
        	{
        		$offer_type=1;
        	}
        	else
        	{
        		$offer_type=2;
        	}
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

             $new_offer=Offers::insertGetId(['promocode'=>$promocode,'title'=>$title,'image'=>$imageName,"offer_type"=>$offer_type,'amount_type'=>$amount_type,'amount'=>$amount,'description'=>$description,'start_date'=>$start_date,'end_date'=>$end_date,'min_amount'=>$min_amount,'active'=>1,'max_discount'=>$max_discount,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                $start=$end=0;
                if(isset($request->salons))
                {
                	if($offer_type==2)
                	{
                		 foreach($request->salons as $salon)
	                    {
	                        $start=$start++;
	                        $insert=OfferSalons::insert([
	                            "salon_id"=>$salon,
	                            "offer_id"=>$new_offer,
	                            'created_at'=> $time,
	                            "updated_at"=>$time
	                            ]);
	                        if($insert)
	                        {
	                            $end=$end++;
	                        }
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
        	$check_all=0;
            $id=$request->id;
            $c_salons=[];
        	$activePage="Offers";
            $offer=Offers::where("id",$id)->first();
            $offer_type=isset($offer->offer_type)?$offer->offer_type:0;
            if($offer_type==3)
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry you don't have the permission to edit this offer");
            }

        	$salons=Salons::orderBy("created_at","desc")->select("id","name")->get();

            $type=["Select Any","Percentage","Amount"];
            $check_all=isset($offer->offer_type)?$offer->offer_type:0;
            if($check_all==1)
            {
            	  foreach (Salons::get() as $each)
	            {
	                $c_salons[]=$each->id;
	            }
            }
            else
            {
            	foreach (OfferSalons::where('offer_id', '=',$id)->get() as $each)
	            {
	                $c_salons[]=$each->salon_id;
	            }
            }
            return view("admin.offers.edit",compact('activePage','offer','id','type','salons','c_salons','check_all'));

            
        }

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
 			
 			if(isset($request->checkall)&& $request->checkall=="on")
        	{
        		$offer_type=1;
        	}
        	else
        	{
        		$offer_type=2;
        	}
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
             $new_offer=Offers::where("id",$id)->update(['promocode'=>$promocode,'title'=>$title,"offer_type"=>$offer_type,'amount_type'=>$amount_type,'amount'=>$amount,'description'=>$description,'start_date'=>$start_date,'end_date'=>$end_date,'min_amount'=>$min_amount,'active'=>1,'max_discount'=>$max_discount,'image'=>$imageName,'created_at'=> $time,"updated_at"=>$time]);

            if($new_offer)
            {
                $start=$end=0;
                if(isset($request->salons))
                {
                     foreach (OfferSalons::where('offer_id', '=',$id)->get() as $salon)
                    {
                        $delete_salons=OfferSalons::where("id",$salon->id)->delete();
                    }
                    if($offer_type==2)
                	{
                		foreach($request->salons as $salon)
	                    {
	                        $start=$start++;

	                        $insert=OfferSalons::insert([
	                            "salon_id"=>$salon,
	                            "offer_id"=>$id,
	                            'created_at'=> $time,
	                            "updated_at"=>$time
	                            ]);
	                        if($insert)
	                        {
	                            $end=$end++;
	                        }
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
                  foreach (OfferSalons::where('offer_id', '=',$id)->get() as $salon)
                {
                    $delete_salons=OfferSalons::where("id",$salon->id)->delete();
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
