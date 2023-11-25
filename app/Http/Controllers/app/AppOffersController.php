<?php

namespace App\Http\Controllers\app;
use DB;
use Auth;
use Validator;
use App\Salons;
use App\Offers;
use Carbon\Carbon;
use App\UserToken;
use App\OfferSalons;
use App\WorkingHours;
use App\OfferServices;
use App\SalonServices;
use App\ServiceOffers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppOffersController extends Controller
{
    public function index(Request $request)
    {
        // $api_token=request()->header('User-Token');
        // $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
        $offers=Offers::where("active",1)->get();
        if(isset($offers)&& count($offers)>0)
        {
        	foreach($offers as $offer)
        	{
        		if($offer->amount_type==1)
        		{
        			$offer->amount_in="Percentage";
        		}
        		else
        		{
        			$offer->amount_in="Cash";
        		}
        		if($offer->offer_type==1)
        		{
        			$offer->offer_valid="For All Salons";
        		}
        		elseif($offer->offer_type==2)
        		{
        			$offer->offer_valid="For Selected Salons";
        			$salons=DB::table("offer_salons")
        			->join("salons", "salons.id","=","offer_salons.salon_id")
                    ->whereNull("offer_salons.deleted_at")
        			->where("offer_salons.offer_id",$offer->id)
        			->select("offer_salons.id","salons.id as salon_id","salons.name","salons.image")
        			->get();
        			if(isset($salons)&&count($salons)>0)
        			{
        				foreach($salons as $salon)
        				{
			        		if(isset($salon->image)&&$salon->image!='')
			                {
			                    $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
			                    $salon->image= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
			                }
			                else
			                {
			                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
			                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
			                }
        				}
        			}
        			$offer->salons=$salons;
        		}
        		else
        		{
        			$offer->offer_valid="For Selected Services";
        			$services=DB::table("offer_services")
        			->join("salon_services", "salon_services.id","=","offer_services.service_id")
                	->join("categories", "categories.id","=","salon_services.category_id")
        			->join("salons", "salons.id","=","salon_services.salon_id")
                    ->whereNull("offer_services.deleted_at")
        			->where("offer_services.offer_id",$offer->id)
        			->select("offer_services.id","salons.id as salon_id","salons.name","salons.image","salon_services.service","salon_services.time","salon_services.amount","categories.category")
        			->get();
        			if(isset($services)&&count($services)>0)
        			{
        				foreach($services as $salon)
        				{
			        		if(isset($salon->image)&&$salon->image!='')
			                {
			                    $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
			                    $salon->image= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
			                }
			                else
			                {
			                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
			                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
			                }
        				}
        			}
        			$offer->services=$services;
        		}

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

        	$return['error']=false;
		    $return['msg']="Offers listed successfully";
		    $return['offers']=$offers;
        }
        else
        {
        	$return['error']=true;
		    $return['msg']="No offers found";
		    $return['offers']=[];
        }

	    return $return;
    }

    public function offer_salons_old(Request $request)
    {
        $latitude=isset($request->latitude)?$request->latitude:'0';
        $longitude=isset($request->longitude)?$request->longitude:'0';
        $salon_ids=$offers=[];
        //take all salon offers
        $offer_all=Offers::where("active",1)->where("offer_type",1)->get();
        $offer_salons=DB::table("offer_salons")
            ->join("offers", "offers.id","=","offer_salons.offer_id")
            ->join("salons", "salons.id","=","offer_salons.salon_id")
            ->whereNull("offers.deleted_at")
            ->whereNull("offer_salons.deleted_at")
            ->where("offers.active",1)
            ->groupBy("salons.id")
            ->where("offers.offer_type",2)
            ->pluck("salons.id")
            ->toArray();

        $offer_services=DB::table("offer_services")
            ->join("salon_services", "salon_services.id","=","offer_services.service_id")
            ->join("categories", "categories.id","=","salon_services.category_id")
            ->join("salons", "salons.id","=","offer_services.salon_id")
            ->join("offers", "offers.id","=","offer_services.offer_id")
            ->whereNull("offers.deleted_at")
            ->whereNull("offer_services.deleted_at")
            ->where("offers.active",1)
            ->groupBy("salons.id")
            ->where("offers.offer_type",3)
            ->pluck("salons.id")
            ->toArray();


        if(isset($offer_all)&& count($offer_all)>0)
        {
           $salon_ids[]=Salons::pluck("id")->toArray();
        }
        if(isset($offer_salons)&& count($offer_salons)>0)
        {
            foreach($offer_salons as $id)
            {
                if(!in_array($id, $salon_ids))
                {
                    $salon_ids[]=$id;
                }
            }

        }

         if(isset($offer_services)&& count($offer_services)>0)
        {
            foreach($offer_services as $id)
            {
                if(!in_array($id, $salon_ids))
                {
                    $salon_ids[]=$id;
                }
            }

        }
        $salons=DB::table("salons")
            ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->whereNull("salons.deleted_at")
            ->whereNull("salon_categories.deleted_at")->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->whereIn("salons.id",$salon_ids)
            ->groupBy("salons.id")
            ->orderBy("salons.created_at","desc")->get();

        if(isset($salons) && count($salons)>0)
        {
            foreach($salons as $salon)
            {
                $offers=[];
                if(isset($salon->image)&&$salon->image!='')
                {
                  $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                    $salon->image= env("IMAGE_URL")."salons/".$salon->image;
                }
                else
                {
                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }
                  if(isset($salon->logo)&&$salon->logo!='')
                {
                    $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
                }
                else
                {
                    $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                }

                $categories=DB::table("salon_categories")
                ->join("categories", "categories.id","=","salon_categories.category_id")
                ->whereNull("salon_categories.deleted_at")
                ->groupBy("categories.id")
                ->where("salon_categories.salon_id",$salon->id)
                ->select("categories.id","categories.image","categories.category")
                ->get();
                if(isset($categories)&& count($categories)>0)
                {
                    foreach($categories as $value)
                    {
                        if(isset($value->image)&&$value->image!='')
                        {
                          $value->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$value->image;
                            $value->image= env("IMAGE_URL")."categories/".$value->image;
                        }
                        else
                        {
                          $value->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                            $value->image= env("IMAGE_URL")."logo/no-picture.jpg";
                        }
                    }
                }
                $salon->categories=$categories;

                ///get all salon offers

                $all_offers=Offers::where("active",1)->where("offer_type",1)->pluck("offers.id")->toArray();

                // get selected salon offers
                $offer_ids=[];
                $salon_offers=DB::table("offers")
                    ->join("offer_salons", "offers.id","=","offer_salons.offer_id")
                    ->join("salons", "salons.id","=","offer_salons.salon_id")
                    ->whereNull("offer_salons.deleted_at")
                    ->whereNull("offers.deleted_at")
                    ->where("offers.active",1)
                    ->where("offers.offer_type",2)
                    ->groupBy("offers.id")
                    ->pluck("offers.id")->toArray();
                //get selected salon service offers

                $service_offers=DB::table("offers")
                    ->join("offer_services", "offers.id","=","offer_services.offer_id")
                    ->join("salon_services", "salon_services.id","=","offer_services.service_id")
                    ->join("categories", "categories.id","=","salon_services.category_id")
                    ->join("salons", "salons.id","=","offer_services.salon_id")
                    ->whereNull("offers.deleted_at")
                    ->whereNull("offer_services.deleted_at")
                    ->where("offers.active",1)
                    ->where("offers.offer_type",3)
                    ->groupBy("offers.id")
                     ->pluck("offers.id")->toArray();
                     if(isset($all_offers)&& count($all_offers)>0)
                    {
                        foreach($all_offers as $id)
                        {
                            $offer_ids[]=$id;
                        }
                    }
                    if(isset($salon_offers)&& count($salon_offers)>0)
                    {
                        foreach($salon_offers as $id)
                        {
                            $offer_ids[]=$id;
                        }
                    }

                     if(isset($service_offers)&& count($service_offers)>0)
                    {
                        foreach($service_offers as $id)
                        {
                            $offer_ids[]=$id;
                        }
                    }
                $offers=Offers::where("active",1)->whereIn("id",$offer_ids)->get();

                if(isset($offers)&& count($offers)>0)
                {
                    foreach($offers as $offer)
                    {
                        if($offer->amount_type==1)
                        {
                            $offer->amount_in="Percentage";
                        }
                        else
                        {
                            $offer->amount_in="Cash";
                        }
                        if($offer->offer_type==1)
                        {
                            $offer->offer_valid="For All Salons";
                        }
                        elseif($offer->offer_type==2)
                        {
                            $offer->offer_valid="For All Services";
                        }
                        else
                        {
                            $offer->offer_valid="For Selected Services";
                            $services=DB::table("offer_services")
                            ->join("salon_services", "salon_services.id","=","offer_services.service_id")
                            ->join("categories", "categories.id","=","salon_services.category_id")
                            ->join("salons", "salons.id","=","salon_services.salon_id")
                            ->whereNull("offer_services.deleted_at")
                            ->where("offer_services.offer_id",$offer->id)
                            ->select("offer_services.id","salons.id as salon_id","salons.name","salons.image","salon_services.service","salon_services.time","salon_services.amount","categories.category")
                            ->get();
                            if(isset($services)&&count($services)>0)
                            {
                                foreach($services as $service)
                                {
                                    if(isset($service->image)&&$service->image!='')
                                    {
                                        $service->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$service->image;
                                        $service->image= env("IMAGE_URL")."salons/thumbnails/".$service->image;
                                    }
                                    else
                                    {
                                      $service->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                                        $service->image= env("IMAGE_URL")."logo/no-picture.jpg";
                                    }
                                }
                            }
                            $offer->services=$services;
                        }
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
                    $salon->offers=$offers;
                }
            }
            $return['error']=false;
            $return['msg']="Offers listed successfully";
            $return['salons']=$salons;
        }
        else
        {
            $return['error']=true;
            $return['msg']="No offers found";
            $return['salons']=[];
        }
        return $return;
    }

    public function apply_offer(Request $request)
    {
    	$rules=[
            "promocode"=>"required|exists:offers,promocode,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "date"=>"required",
            "amount"=>"required",
            "services"=>"required",
            ];
        $msg=[
            "id.required"=>"ID is required",
            "promocode.exists"=>"Invalid or expired promocode",
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {

        	$promocode=$request->promocode;
        	$salon_id=$request->salon_id;
        	$date=$request->date;
        	$amount=$request->amount;
        	$services=json_decode($request->services);
        	$date=new Carbon($request->date);
            $f_date=$date->format('d-m-Y');
            $new_date=strtolower($date->format('l'));
            $start_date=$new_date."_start";
            $end_date=$new_date."_end";

            $timeslot=WorkingHours::where("salon_id",$salon_id);
            $time=Carbon::now();
            if($date<=$time)
            {
                $return['error']=true;
                $return['msg']="Please check the date";
                return $return;
            }

           if($new_date=='monday')
            {
                $timeslot=$timeslot
                ->select('id','monday_start as start_time','monday_end as end_time')
                ->first();
            }
            elseif($new_date=='tuesday')
            {
                $timeslot=$timeslot
                ->select("id",'tuesday_start as start_time','tuesday_end as end_time')
                ->first();
            }
            elseif($new_date=='wednesday')
            {
                $timeslot=$timeslot
                ->select("id",'wednesday_start as start_time','wednesday_end as end_time')
                ->first();
            }
            elseif($new_date=='thursday')
            {
                $timeslot=$timeslot
                ->select("id",'thursday_start as start_time','thursday_end as end_time')
                ->first();
            }
            elseif($new_date=='friday')
            {
                $timeslot=$timeslot
                ->select("id",'friday_start as start_time','friday_end as end_time')
                ->first();
            }
            elseif($new_date=='saturday')
            {
                $timeslot=$timeslot
                ->select("id",'saturday_start as start_time','saturday_end as end_time')
                ->first();
            }
            else
            {
                $timeslot=$timeslot
                ->select("id",'sunday_start as start_time','sunday_end as end_time')
                ->first();
            }

            if(isset($timeslot))
            {
                $start_time=isset($timeslot->start_time)?$timeslot->start_time:'';
                $end_time=isset($timeslot->end_time)?$timeslot->end_time:'';

                $offer=Offers::where("promocode",$promocode)->first();

                $start_date=new Carbon($offer->start_date);
                $end_date=new Carbon($offer->end_date);
                if($start_date<=$time && $end_date>=$time)
                {
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="Sorry this promocode is not applicable for this date";
                    return $return;
                }

                $offer_type=isset($offer->offer_type)?$offer->offer_type:0;
                $min_amount=isset($offer->min_amount)?$offer->min_amount:0;
                $max_discount=isset($offer->max_discount)?$offer->max_discount:0;
                $check_price=SalonServices::where('salon_id',$salon_id)->whereIn('id', $services)->sum("amount");
                $price=$original=$discount_price=0;

                if($offer_type==1)
                {
                    $price=$check_price;
                }
                else if($offer_type==2)
                {
                    $salon=DB::table("offers")
                    ->join("offer_salons", "offer_salons.offer_id","=","offers.id")
                    ->join("salons", "salons.id","=","offer_salons.salon_id")
                    ->where("offer_salons.offer_id",$offer->id)
                    ->where("offers.promocode",$promocode)
                    ->where("offers.active",1)
                    ->whereNull("offers.deleted_at")
                    ->whereNull("offer_salons.deleted_at")
                    ->where("offer_salons.salon_id",$salon_id)
                    ->first();
                    if(isset($salon))
                    {
                        $price=$check_price;
                    }
                    else
                    {
                        $return['error']=true;
                        $return['actual_amount']=$check_price;
                        $return['msg']="Sorry this promocode is not applicable for the selected salon";
                        return $return;
                    }
                }
                else
                {
                    $check_services=DB::table("offer_services")
                        ->join("offers", "offers.id","=","offer_services.offer_id")
                        ->join("salon_services", "salon_services.id","=","offer_services.service_id")
                        ->join("salons", "salons.id","=","salon_services.salon_id")
                        ->where("offers.promocode",$promocode)
                        ->where("offer_services.offer_id",$offer->id)
                        ->whereIn("offer_services.service_id",$services)
                        ->whereNull("offers.deleted_at")
                        ->whereNull("offer_services.deleted_at")
                        ->where("offers.active",1)->get();
                        if(count($check_services)==0)
                        {
                            $return['error']=true;
                            $return['msg']="Sorry this promocode is not aplicable for the selected services";
                            return $return;
                        }

                    foreach($services as $service)
                    {
                         $offer_price=DB::table("offer_services")
                        ->join("offers", "offers.id","=","offer_services.offer_id")
                        ->join("salon_services", "salon_services.id","=","offer_services.service_id")
                        ->join("categories", "categories.id","=","salon_services.category_id")
                        ->join("salons", "salons.id","=","salon_services.salon_id")
                        ->where("offers.promocode",$promocode)
                        ->where("offer_services.offer_id",$offer->id)
                        ->where("offer_services.service_id",$service)
                        ->whereNull("offers.deleted_at")
                        ->whereNull("offer_services.deleted_at")
                        ->where("offers.active",1)
                        ->select("offer_services.id","salons.id as salon_id","salons.name","salons.image","salon_services.service","salon_services.time","salon_services.amount","categories.category")
                        ->first();
                        if(isset($offer_price))
                        {
                            $price=$price+$offer_price->amount;
                        }
                        else
                        {
                            $service_price=SalonServices::where('salon_id',$salon_id)->where('id', $service)->first();
                            if(isset($service_price))
                            {
                                $original=$original+$service_price->amount;
                            }
                        }
                    }
                }
                //calculating offer price

                if($offer->amount_type==1)
                {
                    $percentage=$offer->amount;
                    $o_price=$price*$percentage/100;
                }
                else
                {
                    $amount=$offer->amount;
                    $o_price=$amount;
                }
                if($check_price<=$o_price)
                {
                    $return['error']=true;
                    $return['msg']="Sorry this promocode is not aplicable for this order";
                    return $return;
                }
                if(isset($min_amount) && $min_amount!=0)
                {
                    if($check_price>=$min_amount)
                    {
                        if(isset($max_discount) && $max_discount!=0)
                        {
                            if($o_price<=$max_discount)
                            {
                                $discount_price=$o_price;
                            }
                            else
                            {
                                $discount_price=$max_discount;
                            }
                        }
                        else
                        {
                            $discount_price=$o_price;
                        }
                    }
                    else
                    {
                        $return['error']=true;
                        $total_price=$check_price-$discount_price+$original;
                        $return['actual_amount']=$check_price;
                        $return['offer_price']=$total_price;
                        $return['discount_price']=$discount_price;
                        $return['msg']="Sorry this promocode is only aplicable on a purchase more than ".$min_amount." AED";
                        return $return;
                    }
                }
                else
                {
                    if(isset($max_discount) && $max_discount!=0)
                    {
                        if($o_price<=$max_discount)
                        {
                            $discount_price=$o_price;
                        }
                        else
                        {
                            $discount_price=$max_discount;
                        }
                    }
                    else
                    {
                        $discount_price=$o_price;
                    }
                }
                //calculating offer price

                //calculating total price
                $total_price=$price-$discount_price+$original;
                $return['error']=false;
                $return['msg']="Your coupon applied successfully";
                $return['actual_amount']=$check_price;
                $return['offer_price']=$total_price;
                $return['discount_price']=$discount_price;

            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry error occured";
            }

        }
        return $return;
    }

    public function offer_salons(Request $request)
    {
        $latitude=isset($request->latitude)?$request->latitude:'0';
        $longitude=isset($request->longitude)?$request->longitude:'0';
        $min_distance=$request->min_distance;
        $max_distance=$request->max_distance;
        $salons=DB::table("salons")
        ->join("salon_services", "salon_services.salon_id","=","salons.id")
        ->join("service_offers", "service_offers.service_id","=","salon_services.id")
        ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
        ->join("categories", "categories.id","=","salon_categories.category_id")
        ->whereNull("service_offers.deleted_at")
        ->whereNull("salons.deleted_at")->where("salons.active",1)
        ->where("service_offers.approved",1)
        ->where("salon_services.approved",1)
        ->whereNull("salon_categories.deleted_at")
        ->groupBy("salons.id");
          if(isset($min_distance) && isset($max_distance))
        {
              $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->having("distance",">=",$min_distance)
            ->having("distance","<=",$max_distance)
            ->orderBy("distance")->get();

        }
        else if($latitude!=0 && $longitude!= 0)
        {
             $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->orderBy("distance")->get();
        }
        else
        {
            $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->orderBy("salons.created_at","desc")->get();
        }

        if(isset($salons)&&count($salons)>0)
        {
            $max_price=$min_price=0;
            foreach($salons as $salon)
            {
                //checking the best to least offer
                $max_offers=DB::table("service_offers")
                ->join("salons", "service_offers.salon_id","=","salons.id")
                ->join("salon_services", "salon_services.salon_id","=","salons.id")
                ->whereNull("service_offers.deleted_at")
                ->where("service_offers.salon_id",$salon->id)
                ->whereNull("salons.deleted_at")
                ->select("salons.id","salons.name", DB::raw("(salon_services.amount-service_offers.discount_price)*100/salon_services.amount as offer"))
                ->orderBy("offer","desc")
                ->groupBy("service_offers.id")->first();

                if(isset($max_offers)&& !empty($max_offers))
                {
                    $max_price=isset($max_offers->offer)?round($max_offers->offer):'0';
                }

                if($max_price)
                {
                    $salon->offer_caption="UP TO ". $max_price. "% OFF";
                }

                if(isset($salon->image)&&$salon->image!='')
                {
                    $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                    $salon->image= env("IMAGE_URL")."salons/".$salon->image;
                }
                else
                {
                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }
                  if(isset($salon->logo)&&$salon->logo!='')
                {
                    $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
                }
                else
                {
                    $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                }

                $return['error']=false;
                $return['msg']="Offer salons listed successfully";
                $return['salons']=$salons;
            }
        }
        else
        {
            $return['error']=true;
            $return['msg']="Sorry no records found";
        }

        return $return;
    }

    public function offer_services(Request $request)
    {
        $latitude=isset($request->latitude)?$request->latitude:'0';
        $longitude=isset($request->longitude)?$request->longitude:'0';
        $min_distance=$request->min_distance;
        $max_distance=$request->max_distance;
        if(isset($min_distance) && isset($max_distance))
        {
            $services=DB::table("salon_services")
            ->join("salons", "salon_services.salon_id","=","salons.id")
            ->join("salon_categories", "salons.id","=","salon_categories.salon_id")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->whereNull('salon_categories.deleted_at')
            ->whereNull('salons.deleted_at')
            ->whereNull('salon_services.deleted_at')
            ->where('salon_services.approved',1)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*","salons.id as salon_id","salons.name","salons.email","salons.description","salons.location","categories.category","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(salons.latitude))
                * cos(radians(salons.longitude) - radians(" . $longitude . "))
                + sin(radians(" .$latitude. "))
                * sin(radians(salons.latitude))) AS distance"))
                ->having("distance",">=",$min_distance)
                ->having("distance","<=",$max_distance)
                ->orderBy("distance")->get();
        }
         else if($latitude!=0 && $longitude!= 0)
        {
            $services=DB::table("salon_services")
            ->join("salons", "salon_services.salon_id","=","salons.id")
            ->join("salon_categories", "salons.id","=","salon_categories.salon_id")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->whereNull('salon_categories.deleted_at')
            ->whereNull('salons.deleted_at')
            ->whereNull('salon_services.deleted_at')
            ->where('salon_services.approved',1)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*","salons.id as salon_id","salons.name","salons.email","salons.description","salons.location","categories.category","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(salons.latitude))
                * cos(radians(salons.longitude) - radians(" . $longitude . "))
                + sin(radians(" .$latitude. "))
                * sin(radians(salons.latitude))) AS distance"))
                ->orderBy("distance")->get();
        }
        else
        {
           $services=DB::table("salon_services")
            ->join("salons", "salon_services.salon_id","=","salons.id")
            ->join("salon_categories", "salons.id","=","salon_categories.salon_id")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->whereNull('salon_categories.deleted_at')
            ->whereNull('salons.deleted_at')
            ->whereNull('salon_services.deleted_at')
            ->where('salon_services.approved',1)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*","salons.id as salon_id","salons.name","salons.email","salons.description","salons.location","categories.category","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(salons.latitude))
                * cos(radians(salons.longitude) - radians(" . $longitude . "))
                + sin(radians(" .$latitude. "))
                * sin(radians(salons.latitude))) AS distance"))
                ->orderBy("distance")->get();
        }

        if(isset($services)&&count($services)>0)
        {
            foreach($services as $salon)
            {
                if(isset($salon->image)&&$salon->image!='')
                {
                    $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                    $salon->image= env("IMAGE_URL")."salons/".$salon->image;
                }
                else
                {
                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }
                  if(isset($salon->logo)&&$salon->logo!='')
                {
                    $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
                }
                else
                {
                    $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                }

                $return['error']=false;
                $return['msg']="Salon services listed successfully";
                $return['services']=$services;
            }
        }
        else
        {
            $return['error']=true;
            $return['msg']="Sorry no records found";
        }

        return $return;
    }

}
