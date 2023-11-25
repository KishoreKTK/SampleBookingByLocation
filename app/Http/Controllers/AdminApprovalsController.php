<?php

namespace App\Http\Controllers;
use DB;
use Session;
use Validator;
use App\Salons;
use App\Approvals;
use Carbon\Carbon;
use App\SalonImages;
use App\SalonServices;
use App\ServiceOffers;
use Illuminate\Http\Request;

class AdminApprovalsController extends Controller
{
    public function index(Request $request)
    {
        $activePage="Salons";
        $time=Carbon::now();
        foreach (Approvals::where('read', '=',0)->get() as $data)
        {
            $read=Approvals::where("id",$data->id)->update(["read"=>1,"updated_at"=>$time]);
        }
        Session::put('approvals', 0);
        $approvals =    DB::table('approvals')
                        ->join("salons", "salons.id","=","approvals.salon_id")
                        ->leftJoin("salon_services", "salon_services.id","=","approvals.service_id")
                        ->whereNull("approvals.deleted_at")
                        // ->where(function ($q) {
                        // $q->where("salons.approved",0)
                        // ->orWhere("salon_services.approved",0);
                        // })
                        ->whereNull("salon_services.deleted_at")
                        ->orderBy("approvals.id","desc")
                        ->groupBy("approvals.id")
                        ->select("approvals.*","salons.id as salonId","salons.name","salons.email","salons.phone","salons.description as SalonDesc","salons.image","salons.featured","salons.active")
                        ->paginate(20);
      if(isset($approvals)&& count($approvals)>0)
      {
        foreach($approvals as $salon)
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
      return view("admin.approvals.list",compact("approvals","activePage"));
    }
    
    public function details(Request $request)
    {
      $activePage="Salons";

       $rules=[
            "id"=>"required|exists:approvals,id,deleted_at,NULL",
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

            $approve=DB::table('approvals')
            ->join("salons", "salons.id","=","approvals.salon_id")
            ->whereNull("approvals.deleted_at")
            ->where("approvals.id",$id)
            ->select("approvals.*")->first();
              $old=[];

            if(isset($approve))
            {
                $type=isset($approve->type_id)?$approve->type_id:0;
                if($type==1)
                {
                  $old=DB::table('approvals')
                  ->join("salons", "salons.id","=","approvals.salon_id")
                  ->whereNull("approvals.deleted_at")
                  ->where("approvals.id",$id)
                  ->select("salons.*")->first();

                   $new=DB::table('approvals')
                  ->join("salon_log", "salon_log.salon_id","=","approvals.salon_id")
                  ->whereNull("approvals.deleted_at")
                  ->whereNull("salon_log.deleted_at")
                  ->where("approvals.id",$id)
                  ->where("approvals.service_id",0)->where("approvals.type_id",1)
                  ->where("salon_log.salon_id",$approve->salon_id)
                  ->select("salon_log.*")->first();
                  if(isset($new) && !empty($new))
                  {

                    if(isset($new->image)&&$new->image!='')
                    {
                      $new->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$new->image;
                      $new->image= env("IMAGE_URL")."salons/thumbnails/".$new->image;
                    }
                    else
                    {
                        $new->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $new->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                    if(isset($new->logo)&& $new->logo!='')
                    {
                        $new->logo= env("IMAGE_URL")."salons/".$new->logo;
                    }
                    else
                    {
                        $new->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                    }
                      $subs=$sub_images=[];

                    $sub_images=SalonImages::where("salon_id",$approve->salon_id)->select("id","image")->get();
                    if(isset($sub_images)&&count($sub_images)>0)
                    {
                        foreach($sub_images as $one)
                        {
                            $sub_image=$one->image;
                            $img_id=$one->id;

                            $one->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$sub_image;
                            $one->image= env("IMAGE_URL")."salons/".$sub_image;
                            $subs[]=$one;
                        }
                    }
                    $new->sub_images=$subs;

                  }
                  if(isset($old) && !empty($old))
                  {
                      $subs=$sub_images=[];

                     $sub_images=SalonImages::where("salon_id",$approve->salon_id)->where("approved",1)->select("id","image")->get();
                    if(isset($sub_images)&&count($sub_images)>0)
                    {
                        foreach($sub_images as $one)
                        {
                            $sub_image=$one->image;
                            $img_id=$one->id;

                            $one->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$sub_image;
                            $one->image= env("IMAGE_URL")."salons/".$sub_image;
                           
                            $subs[]=$one;
                        }
                    }
                    $old->sub_images=$subs;


                    if(isset($old->image)&&$old->image!='')
                    {
                      $old->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$old->image;
                      $old->image= env("IMAGE_URL")."salons/thumbnails/".$old->image;
                    }
                    else
                    {
                        $old->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $old->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                    if(isset($old->logo)&& $old->logo!='')
                    {
                        $old->logo= env("IMAGE_URL")."salons/".$old->logo;
                    }
                    else
                    {
                        $old->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                  }
                   if(isset($new))
                    {
                        return view("admin.approvals.salon",compact("approve","id","new","old","activePage"));
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");

                    }
                  
                 
                }
                // if type 1
                elseif($type==2)
                {
                  if(isset($approve->action) && $approve->action=="Added")
                  {
                    $old=[];
                     $new=DB::table('approvals')
                  ->join("salons", "salons.id","=","approvals.salon_id")
                  ->join("salon_services", "salons.id","=","salon_services.salon_id")
                  ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                  ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                  ->whereNull("approvals.deleted_at")
                  ->whereNull("salon_services.deleted_at")
                  ->where("salon_services.approved",0)
                  ->where("salon_services.id",$approve->service_id)
                  ->where("salon_services.salon_id",$approve->salon_id)
                  ->where("approvals.id",$id)
                  ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","salon_services.id as service_id","salon_services.service","salon_services.time","salon_services.amount","categories.category")->first();
                  }
                  else
                  {
                     $new=DB::table('approvals')
                  ->join("salons", "salons.id","=","approvals.salon_id")
                  ->join("salon_services_log", "approvals.service_id","=","salon_services_log.service_id")
                  ->leftJoin("salon_categories", "salon_categories.id","=","salon_services_log.category_id")
                  ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                  ->whereNull("approvals.deleted_at")
                  ->whereNull("salon_services_log.deleted_at")
                  ->where("salon_services_log.service_id",$approve->service_id)
                  ->where("salon_services_log.salon_id",$approve->salon_id)
                  ->where("approvals.id",$id)
                  ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","salon_services_log.service_id","salon_services_log.service","salon_services_log.time","salon_services_log.amount","categories.category")->first();

                   $old=DB::table('approvals')
                  ->join("salons", "salons.id","=","approvals.salon_id")
                  ->join("salon_services", "salons.id","=","salon_services.salon_id")
                  ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                  ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                  ->whereNull("approvals.deleted_at")
                  ->whereNull("salon_services.deleted_at")
                  ->where("salon_services.salon_id",$approve->salon_id)
                  ->where("salon_services.id",$approve->service_id)
                  ->where("approvals.id",$id)
                  ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","salon_services.id as service_id","salon_services.service","salon_services.time","salon_services.amount","categories.category")->first();
                  }
                   if(isset($new)&& !empty($new))
                  {

                    if(isset($new->image)&&$new->image!='')
                    {
                      $new->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$new->image;
                      $new->image= env("IMAGE_URL")."salons/thumbnails/".$new->image;
                    }
                    else
                    {
                        $new->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $new->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                    if(isset($new->logo)&& $new->logo!='')
                    {
                        $new->logo= env("IMAGE_URL")."salons/".$new->logo;
                    }
                    else
                    {
                        $new->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                  }
                  if(isset($old) && !empty($old))
                  {
                    if(isset($old->image)&&$old->image!='')
                    {
                      $old->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$old->image;
                      $old->image= env("IMAGE_URL")."salons/thumbnails/".$old->image;
                    }
                    else
                    {
                        $old->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $old->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                    if(isset($old->logo)&& $old->logo!='')
                    {
                        $old->logo= env("IMAGE_URL")."salons/".$old->logo;
                    }
                    else
                    {
                        $old->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                  }
                  
                  if(isset($new))
                  {
                    return view("admin.approvals.service",compact("approve","new","id","old","activePage"));
                  }
                  else
                  {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");

                  }
                }
                else
                {
                  if(isset($approve->action) && $approve->action=="Added")
                  {
                    $old=[];
                     $new=DB::table('approvals')
                    ->join("salons", "salons.id","=","approvals.salon_id")
                    ->join("salon_services", "salons.id","=","salon_services.salon_id")
                    ->join("service_offers", "approvals.offer_id","=","service_offers.id")
                    ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                    ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                    ->whereNull("approvals.deleted_at")
                    ->whereNull("service_offers.deleted_at")
                    ->where("service_offers.approved",0)
                    ->whereNull("salon_services.deleted_at")
                    ->where("service_offers.id",$approve->offer_id)
                    ->where("salon_services.salon_id",$approve->salon_id)
                    ->where("salon_services.id",$approve->service_id)
                    ->where("approvals.id",$id)
                    ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","service_offers.discount_price","service_offers.start_date","service_offers.end_date","salon_services.service","salon_services.time","salon_services.amount","categories.category")->first();
                  }
                  else
                  {
                    $new=DB::table('approvals')
                    ->join("salons", "salons.id","=","approvals.salon_id")
                    ->join("salon_services", "salons.id","=","salon_services.salon_id")
                    ->join("service_offers_log", "approvals.offer_id","=","service_offers_log.offer_id")
                    ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                    ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                    ->whereNull("approvals.deleted_at")
                    ->whereNull("service_offers_log.deleted_at")
                    ->whereNull("salon_services.deleted_at")
                    ->where("service_offers_log.offer_id",$approve->offer_id)
                    ->where("salon_services.salon_id",$approve->salon_id)
                    ->where("salon_services.id",$approve->service_id)
                    ->where("approvals.id",$id)
                    ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","service_offers_log.discount_price","service_offers_log.offer_id","service_offers_log.start_date","service_offers_log.end_date","salon_services.service","salon_services.time","salon_services.amount","categories.category")->first();

                    $old=DB::table('approvals')
                    ->join("salons", "salons.id","=","approvals.salon_id")
                    ->join("salon_services", "salons.id","=","salon_services.salon_id")
                    ->join("service_offers", "approvals.offer_id","=","service_offers.id")
                    ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                    ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                    ->whereNull("approvals.deleted_at")
                    ->whereNull("service_offers.deleted_at")
                    ->whereNull("salon_services.deleted_at")
                    ->where("service_offers.id",$approve->offer_id)
                    ->where("salon_services.salon_id",$approve->salon_id)
                    ->where("salon_services.id",$approve->service_id)
                    ->where("approvals.id",$id)
                    ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","service_offers.discount_price","service_offers.start_date","service_offers.end_date","salon_services.service","salon_services.time","salon_services.amount","categories.category")->first();
                     
                  }
                   if(isset($new) && !empty($new))
                  {

                    if(isset($new->image)&&$new->image!='')
                    {
                      $new->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$new->image;
                      $new->image= env("IMAGE_URL")."salons/thumbnails/".$new->image;
                    }
                    else
                    {
                        $new->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $new->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                    if(isset($new->logo)&& $new->logo!='')
                    {
                        $new->logo= env("IMAGE_URL")."salons/".$new->logo;
                    }
                    else
                    {
                        $new->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                  }
                  if(isset($old) && !empty($old))
                  {
                    if(isset($old->image)&&$old->image!='')
                    {
                      $old->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$old->image;
                      $old->image= env("IMAGE_URL")."salons/thumbnails/".$old->image;
                    }
                    else
                    {
                        $old->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $old->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                    if(isset($old->logo)&& $old->logo!='')
                    {
                        $old->logo= env("IMAGE_URL")."salons/".$old->logo;
                    }
                    else
                    {
                        $old->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                  }
                  if(isset($new))
                  {
                    return view("admin.approvals.offer",compact("approve","new","id","old","activePage"));
                  }
                  else
                  {
                    return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");

                  }
                }
            }
          
        }
    }

    public function approve(Request $request)
    {

       $rules=[
            "id"=>"required|exists:approvals,id,deleted_at,NULL",
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

            $approve=DB::table('approvals')
            ->join("salons", "salons.id","=","approvals.salon_id")
            ->where("approvals.id",$id)->select("approvals.*")
            ->first();
            $time=Carbon::now();
                  $now=Carbon::now();

             if(isset($approve))
            {
                $type=isset($approve->type_id)?$approve->type_id:0;
                 if($type==1)
                {
                    $new=DB::table('approvals')
                  ->join("salon_log", "salon_log.salon_id","=","approvals.salon_id")
                  ->whereNull("approvals.deleted_at")
                  ->where("approvals.id",$id)
                  ->where("approvals.service_id",0)->where("approvals.type_id",1)
                  ->where("salon_log.salon_id",$approve->salon_id)
                  ->select("salon_log.*")->first();

                  $name=isset($new->name)?$new->name:'';
                  $sub_title=isset($new->sub_title)?$new->sub_title:'';
                  $location=isset($new->location)?$new->location:'';
                  $latitude=isset($new->latitude)?$new->latitude:'';
                  $longitude=isset($new->longitude)?$new->longitude:'';
                  $description=isset($new->description)?$new->description:'';
                  $cancellation_policy=isset($new->cancellation_policy)?$new->cancellation_policy:'';
                  $city=isset($new->city)?$new->city:'';
                  $phone=isset($new->phone)?$new->phone:'';
                  $manager_phone=isset($new->manager_phone)?$new->manager_phone:'';
                  $image=isset($new->image)?$new->image:'';
                  $logo=isset($new->logo)?$new->logo:'';

                   $approve_salon=Salons::where("id",$approve->salon_id)->update(['name'=>$name,'sub_title'=>$sub_title,'location'=>$location,'latitude'=>$latitude,'longitude'=>$longitude,'description'=>$description,'cancellation_policy'=>$cancellation_policy,'city'=>$city, 'phone'=>$phone, 'manager_phone'=>$manager_phone,'approved'=>1,"pending"=>1,'image'=>$image,'logo'=>$logo,"updated_at"=>$time]);
                   if($approve_salon)
                  {
                    $del_log=DB::table('salon_log')->where("audit_id",$id)->delete();
                     foreach (SalonImages::where('salon_id', '=',$approve->salon_id)->where("approved",0)->get() as $each)
                    {
                        SalonImages::where("id",$each->id)->update(["approved"=>1,"updated_at"=>$now]);
                    }
                  }

                }
                elseif($type==2)
                {
                  if(isset($approve->action) && $approve->action=="Updated")
                  {
                    
                     $new=DB::table('approvals')
                  ->join("salons", "salons.id","=","approvals.salon_id")
                  ->join("salon_services_log", "approvals.service_id","=","salon_services_log.service_id")
                  ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                  ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                  ->whereNull("approvals.deleted_at")
                  ->whereNull("salon_services_log.deleted_at")
                  ->where("salon_services_log.service_id",$approve->service_id)
                  ->where("salon_services_log.salon_id",$approve->salon_id)
                  ->where("approvals.id",$id)
                  ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","salon_services_log.service_id","salon_services_log.service","salon_services_log.time","salon_services_log.amount","categories.category","salon_services_log.category_id")->first();

                  $service=isset($new->service)?$new->service:'';
                  $amount=isset($new->amount)?$new->amount:'';
                  $time=isset($new->time)?$new->time:'';
                  $category_id=isset($new->category_id)?$new->category_id:'';

                   $approve_salon=SalonServices::where("id",$approve->service_id)->where("salon_id",$approve->salon_id)->update(["approved"=>1,"pending"=>1,'service'=>$service,'amount'=>$amount,'time'=>$time,'category_id'=>$category_id,"updated_at"=>$now]);
                    if($approve_salon)
                    {
                      $del_log=DB::table('salon_services_log')->where("audit_id",$id)->delete();
                    }
                  }
                  else
                  {
                    $approve_salon=SalonServices::where("id",$approve->service_id)->update(["approved"=>1,"pending"=>1,"updated_at"=>$now]);
                  }
                }
                else
                {
                  if(isset($approve->action) && $approve->action=="Updated")
                  {
                     $new=DB::table('approvals')
                    ->join("salons", "salons.id","=","approvals.salon_id")
                    ->join("salon_services", "salons.id","=","salon_services.salon_id")
                    ->join("service_offers_log", "approvals.offer_id","=","service_offers_log.offer_id")
                    ->leftJoin("salon_categories", "salons.id","=","salon_categories.salon_id")
                    ->leftJoin("categories", "categories.id","=","salon_categories.category_id")
                    ->whereNull("approvals.deleted_at")
                    ->whereNull("service_offers_log.deleted_at")
                    ->whereNull("salon_services.deleted_at")
                    ->where("service_offers_log.offer_id",$approve->offer_id)
                    ->where("salon_services.salon_id",$approve->salon_id)
                    ->where("salon_services.id",$approve->service_id)
                    ->where("approvals.id",$id)
                    ->select("approvals.*","salons.id as salon_id","salons.name","salons.image","salons.email","service_offers_log.discount_price","service_offers_log.start_date","service_offers_log.end_date","salon_services.service","salon_services.time","salon_services.amount","categories.category")->first();

                  $discount_price=isset($new->discount_price)?$new->discount_price:'';
                  $service_id=isset($new->service_id)?$new->service_id:'';
                  $start_date=isset($new->start_date)?$new->start_date:'';
                  $end_date=isset($new->end_date)?$new->end_date:'';

                    $approve_salon=ServiceOffers::where("id",$approve->offer_id)->where("salon_id",$approve->salon_id)->update(["approved"=>1,"pending"=>1,'discount_price'=>$discount_price,'service_id'=>$service_id,'start_date'=>$start_date,'end_date'=>$end_date,"updated_at"=>$time]);

                    if($approve_salon)
                      {
                        $del_log=DB::table('service_offers_log')->where("audit_id",$id)->delete();
                      }
                  }
                  else
                  {
                   $approve_salon=ServiceOffers::where("id",$approve->offer_id)->update(["approved"=>1,"pending"=>1,"updated_at"=>$time]);
                  }
                }
                // return $approve_salon;
                if($approve_salon)
                {
                  $del_approval=Approvals::where("id",$id)->delete();
                  return redirect(env("ADMIN_URL")."/approvals")->with("error", false)->with("msg", "Approved successfully");
                  // return $del_approval;
                }
                else
                {
                  return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
                }
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");

            }


        }
    }
}
