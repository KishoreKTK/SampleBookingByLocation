<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Mail;
use Hash;
use Session;
use Validator;
use App\Salons;
use App\Currency;
use App\Countries;
use Carbon\Carbon;
use App\Approvals;
use App\Categories;
use App\SalonStaffs;
use App\SalonImages;
use App\SalonsToken;
use App\SalonReviews;
use App\WorkingHours;
use App\SalonCategories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;

class SalonProfileController extends Controller
{
    public function profile(Request $request)
    {
        $id=Auth::guard('salon-web')->user()->id;

        $c_categories=[];
        $salon=DB::table("salons")
        ->leftJoin("countries", "countries.id","=","salons.country_id")
        ->whereNull('salons.deleted_at')
        ->where("salons.id",$id)->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.country_id","countries.name as country","salons.city","salons.featured","salons.phone","salons.image","salons.reschedule_policy","salons.cancellation_policy")->first();
        if(isset($salon))
        {
            $categories=DB::table("salon_categories")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->where('salon_categories.salon_id',$id)
            ->whereNull('salon_categories.deleted_at')
            ->groupBy("salon_categories.id")
            ->select("salon_categories.*","categories.category","categories.image")->get();
             if(isset($categories)&&count($categories)>0)
            {
                foreach($categories as $each)
                {
                    if(isset($each->image)&& $each->image!='')
                    {
                        $each->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$each->image;
                        $each->image= env("IMAGE_URL")."categories/".$each->image;
                    }
                    else
                    {
                        $each->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $each->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }

                }
            }
            $images=SalonImages::where("salon_id",$id)->get();
            if(isset($images)&&count($images)>0)
            {
                foreach($images as $each)
                {
                    $each->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$each->image;
                    $each->image= env("IMAGE_URL")."salons/thumbnails/".$each->image;
                }
            }
            $working_hours=WorkingHours::where("salon_id",$id)->first();
            if(!isset($working_hours))
            {
                $working_hours=[];
                $working_hours['sunday_start']='';
                $working_hours['sunday_end']='';
                $working_hours['monday_start']='';
                $working_hours['monday_end']='';
                $working_hours['tuesday_start']='';
                $working_hours['tuesday_end']='';
                $working_hours['wednesday_start']='';
                $working_hours['wednesday_end']='';
                $working_hours['thursday_start']='';
                $working_hours['thursday_end']='';
                $working_hours['friday_start']='';
                $working_hours['friday_end']='';
                $working_hours['saturday_start']='';
                $working_hours['saturday_end']='';
            }


            $staffs=SalonStaffs::where('salon_id',$id)->get();
            $services=DB::table("salon_services")
            ->join("categories", "categories.id","=","salon_services.category_id")
            ->where('salon_services.salon_id',$id)
            ->whereNull('salon_services.deleted_at')
            ->groupBy("salon_services.id")
            ->select("salon_services.*","categories.category")->get();

            $booking=[];

            $booking=DB::table("booking")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("user", "user.id","=","booking.user_id")
            ->whereNull('booking.deleted_at')
            // ->where('booking.active',1)
            ->where('booking.salon_id',$id)
            ->select("booking.*","user.first_name","user.last_name","user.email","salons.name","salons.pricing")->get();
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
            $salon->images=$images;
            $reviews=DB::table("salon_reviews")
            ->join("user", "user.id","=","salon_reviews.user_id")
            ->where("salon_id",$id)
            ->whereNull("salon_reviews.deleted_at")
            ->whereNotNull("reviews")
            ->select("salon_reviews.*","user.first_name","user.last_name")->get();
            $rating_count=SalonReviews::where("salon_id",$salon->id)->get()->count();
            $review_count=SalonReviews::where("salon_id",$salon->id)->whereNotNull("reviews")->get()->count();
            $salon->reviews=$reviews;
            $salon->rating_count=$rating_count;
            $salon->review_count=$review_count;
            if(isset($reviews)&&count($reviews)>0)
            {
                $rating=0;
                foreach($reviews as $review)
                {
                    $rating=$rating+$review->rating;
                }
                $overall=$rating/$review_count;
                $salon->overall_rating=$overall;
            }
            else
            {
                $salon->rating="No ratings yet";
                $salon->overall_rating=0;
            }
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
        return view('salon.profile.view',compact('reviews','salon','id','images','categories','services','staffs','booking','working_hours'));
    }

    public function edit(Request $request)
    {
        $id=Auth::guard('salon-web')->user()->id;

        $c_categories=$subs=[];
        $activePage="Salons";
        $salon=Salons::where("id",$id)->first();
         $image=isset($salon->image)?$salon->image:'';
            $logo=isset($salon->logo)?$salon->logo:'';

            $approved=isset($salon->pending)?$salon->pending:1;

            if(isset($image)&& $image!='')
            {
                $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$image;
                $salon->image= env("IMAGE_URL")."salons/".$image;
            }
            else
            {
              $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
            }
            if(isset($logo)&& $logo!='')
            {
                $salon->logo= env("IMAGE_URL")."salons/".$logo;
            }
            else
            {
                $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
            }

        $countries=Countries::orderBy("name")->pluck("name","id");
        $categories=Categories::where('active_status',1)->whereNull('deleted_at')->get();
         $sub_images=SalonImages::where("salon_id",$id)->select("id","image")->get();
        if(isset($sub_images)&&count($sub_images)>0)
        {
            foreach($sub_images as $one)
            {
                $sub_image=$one->image;
                $img_id=$one->id;

                $one->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$sub_image;
                $one->image= env("IMAGE_URL")."salons/".$sub_image;
                $sb_image= env("IMAGE_URL")."salons/".$sub_image;

                $new["id"]=$img_id;
                $new["image"]=$sb_image;
                $subs[]=$new;
            }
        }
        foreach (SalonCategories::where('salon_id', '=',$id)->get() as $each)
        {
            $c_categories[]=$each->category_id;
        }
        $count=count($sub_images);
        return view('salon.profile.edit',compact('activePage','approved','count','countries','salon','id','subs','categories','c_categories'));
    }

    public function update(Request $request)
    {
       $rules=[
          "name"=>"required",
          "sub_title"=>"required",
          "email"=>"required",
          "country"=>"required",
          "description"=>"max:500",
          "location"=>"required",
          ];
      $msg=[
          "name.required"=>"Name is required",
           ];
            $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            // dd(request()->all());

        	$id=Auth::guard('salon-web')->user()->id;


            $name=$request->name;
            $files=[];
            $sub_title=$request->sub_title;
            $email=$request->email;
            $description=$request->description;
            $cancellation_policy=$request->cancellation_policy;
            $reschedule_policy=$request->reschedule_policy;
            $image=$request->image;
            $logo=$request->logo;
            $password=$request->password;
            $minimum_order_amt=   $request->minimum_order_amt;

            $country=$request->country;
            $location=$request->location;
            $city=$request->city;
            $phone=$request->phone;
            $manager_phone=$request->manager_phone;
            $time=Carbon::now();
            $files = $request->file('sub_images');
              //count of images
            if(!empty($files))
            {
                $count=count($files);
            }
            else
            {
                $count=0;
            }

            $sub_images=SalonImages::where("salon_id",$id)->select("id","image")->get()->count();

            $t_count=$sub_images+$count;

            if($t_count>5)
            {
                return redirect()->back()->with("error", true)->with("msg", "Maximum images allowed are 5")->withInput();
            }

            // $currency_id=Currency::where("country_id",$country)->first()->id;
            $currency_id    =   0;
            $imageName="";
            $url            = public_path('img/salons/');
            $turl           = public_path('img/salons/thumbnails/');
            // $url=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/salons/";
            // $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/salons/thumbnails/";
            $pick=Salons::where("id",$id)->first();

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
            if (isset($logo))
            {
                $logoName = md5(rand(1000,9078787878)).'.'.$logo->getClientOriginalExtension();
                $desti=$url.$logoName;
                $t_desti=$turl.$logoName;

                 $resize=Image::make($logo->getRealPath())->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                $resize->save($t_desti);
                $savelogo=$logo->move($url, $logoName);
            }
             else
            {
              $logoName=isset($pick->logo)?$pick->logo:'';
            }


            $latitude=isset($request->latitude)?$request->latitude:$pick->latitude;
            $longitude=isset($request->longitude)?$request->longitude:$pick->longitude;
           if($latitude==0)
            {
                $latitude='25.2048';
            }
            if($longitude==0)
            {
                $longitude='55.2708';
            }
            // $new_salon=Salons::where("id",$id)->update(['name'=>$name,'sub_title'=>$sub_title,'location'=>$location,'latitude'=>$latitude,'longitude'=>$longitude,'description'=>$description,'cancellation_policy'=>$cancellation_policy,'reschedule_policy'=>$reschedule_policy,'email'=>$email,'city'=>$city,'country_id'=>$country,'currency_id'=>$currency_id, 'phone'=>$phone,"suspend"=>0, "active"=>1,'approved'=>0,'image'=>$imageName,'logo'=>$logoName,"updated_at"=>$time]);
              $new_salon=Salons::where("id",$id)->update(['name'=>$name,'sub_title'=>$sub_title,'location'=>$location,'latitude'=>$latitude,'longitude'=>$longitude,'description'=>$description,'minimum_order_amt'=>$minimum_order_amt,'cancellation_policy'=>$cancellation_policy,'reschedule_policy'=>$reschedule_policy,'email'=>$email,'city'=>$city,'country_id'=>$country,'currency_id'=>$currency_id, 'phone'=>$phone,"suspend"=>0,  'manager_phone'=>$manager_phone,"active"=>1,'approved'=>1,'image'=>$imageName,'logo'=>$logoName,"updated_at"=>$time]);

            // $new_salon=Salons::where("id",$id)->update(["pending"=>0,"updated_at"=>$time]);
            if($new_salon)
            {

                //approval system
                // $admin=Salons::where("id",$id)->first()->name;

                //     $action="Updated";

                // $log="Updated the salon profile.";
                // $check_approval=Approvals::where("salon_id",$id)->where("service_id",0)->where("type_id",1)->first();

                // if(isset($check_approval)&& !empty($check_approval))
                // {
                //     $new_approve=Approvals::where("salon_id",$id)->where("service_id",0)->where("type_id",1)->update(["action"=>$action,"title"=>$log,"updated_at"=>$time]);
                //     $audit_id=$new_approve=$check_approval->id;


                // }
                // else
                // {
                //      $new_approve=DB::table("approvals")->insertGetId(["action"=>$action,"salon_id"=>$id,"type_id"=>1,'service_id'=>0,"title"=>$log, 'created_at'=> $time,"updated_at"=>$time]);
                //      $audit_id=$new_approve;
                // }
                // if($new_approve)
                // {

                //      $add_log=DB::table("salon_log")->insertGetId(["audit_id"=>$audit_id,"salon_id"=>$id,'name'=>$name,'sub_title'=>$sub_title,'location'=>$location,'latitude'=>$latitude,'longitude'=>$longitude,'description'=>$description,'cancellation_policy'=>$cancellation_policy,'reschedule_policy'=>$reschedule_policy,'email'=>$email,'city'=>$city,'country_id'=>$country,'currency_id'=>$currency_id, 'phone'=>$phone, 'manager_phone'=>$manager_phone,"suspend"=>0, "active"=>1,'image'=>$imageName,'logo'=>$logoName,"updated_at"=>$time]);
                // }

                // $new_approve=DB::table("approvals")->insertGetId(["salon_id"=>$id,"type_id"=>1,'service_id'=>0,"title"=>$log, 'created_at'=> $time,"updated_at"=>$time]);
                if($request->hasFile('sub_images'))
                {
                    foreach ($files as $file)
                    {
                        $fileName = md5(rand(1000,9078787878)).'.'.$file->getClientOriginalExtension();
                        $desti=$url.$fileName;
                        $t_desti=$turl.$fileName;

                         $resize=Image::make($file->getRealPath())->resize(300, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        $resize->save($t_desti);
                        $savefile=$file->move($url, $fileName);

                        $new_file=SalonImages::insertGetId(['salon_id'=>$id,'image'=>$fileName, 'approved'=>1, 'created_at'=> $time,"updated_at"=>$time]);
                        // $new_log=DB::table("salon_images_log")->insertGetId(['approve_id'=>$new_approve,'log_id'=>$log_id,'salon_id'=>$id,'image'=>$fileName, 'created_at'=> $time,"updated_at"=>$time]);
                    }
                }
                 foreach (SalonCategories::where('salon_id', '=',$id)->get() as $category)
                {
                    $delete_categories=SalonCategories::where("id",$category->id)->delete();
                }
                $start=$end=0;
                if(isset($request->categories))
                {
                    foreach($request->categories as $category)
                    {
                        $start=$start++;
                        $insert=SalonCategories::insert([
                            "salon_id"=>$id,
                            "category_id"=>$category,
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
                    return redirect(env("ADMIN_URL").'/salon/profile')->with("error", false)->with("msg", "Salon updated successfully");
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

     public function delete_img(Request $request)
    {
         $rules=
        [
            "id"=>"required|exists:salon_images,id,deleted_at,NULL",
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

            $delete=SalonImages::where("id",$id)->where("salon_id",$salon_id)->delete();
            if($delete)
            {
              return redirect()->back()->with("error", false)->with("msg", "Image deleted successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process");

            }
        }

    }
}
