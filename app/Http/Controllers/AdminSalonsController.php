<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Salons;
use App\Currency;
use Carbon\Carbon;
use App\Countries;
use App\Categories;
use App\SalonsToken;
use App\SalonImages;
use App\SalonStaffs;
use App\SalonReviews;
use App\WorkingHours;
use App\SalonCategories;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use App\Exports\SalonReport;
use Excel;
use App\Http\Controllers\Notification\PushNotificationController;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;


class AdminSalonsController extends Controller
{
    public function index(Request $request)
    {

        // dd("Am i here");
            $activePage="Salons";
            $latest=[];
            if(isset($request->keyword)&&$request->keyword!="")
            {
                $keyword=$request->keyword;
                $salons=Salons::where("name","like",'%'.$keyword.'%')
                    ->where(function ($q) use ($keyword) {
                    $q->where("name","like",'%'.$keyword.'%')
                    ->orWhere("email","like",'%'.$keyword.'%')
                    ->orWhere("description","like",'%'.$keyword.'%');
                    })
                    ->orderBy("created_at","desc")
                    ->select("id","name","email","description","image","featured","active")->paginate(20);
            }
            else
            {
                $keyword="";
                $salons=Salons::orderBy("created_at","desc")->select("id","name","email","description","image","featured","active")->paginate(20);
            }
            $latest=Salons::whereDate('created_at', '=', date('Y-m-d'))->pluck("id")->toArray();

            if(isset($salons)&& count($salons)>0)
            {
                foreach($salons as $salon)
                {
                    if(in_array($salon->id,$latest))
                    {
                        $salon->new=1;
                    }
                    else
                    {
                        $salon->new=0;
                    }
                    $reviews        =   SalonReviews::where("salon_id",$salon->id)->get();
                    $rating_count   =   SalonReviews::where("salon_id",$salon->id)->get()->count();
                    $review_count   =   SalonReviews::where("salon_id",$salon->id)->whereNotNull("reviews")->get()->count();
                    $salon->rating_count=$rating_count;
                    $salon->review_count=$review_count;
                    if(isset($reviews)&&count($reviews)>0 )
                    {
                        $rating=0;
                        foreach($reviews as $review)
                        {
                            $rating=$rating+$review->rating;
                        }
                        if($review_count==0)
                        {
                            $salon->rating="No ratings yet";
                            $salon->overall_rating=0;
                        }
                        else
                        {
                            $overall=$rating/$review_count;
                            // $salon->overall_rating=$overall;
                            $salon->overall_rating=round($overall, 2);
                        }
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
            }
            return view('admin.salons.list',compact('activePage','salons','keyword'));
    }

    public function add(Request $request)
    {
        $activePage     =   "Salons";
        $countries      =   Countries::orderBy("name")->pluck("name","id");
        $categories     =   Categories::where('active_status',1)->whereNull('deleted_at')->get();
        return view('admin.salons.add',compact('activePage','countries','categories'));
    }


    public function add_salon(Request $request)
    {
        $rules  =   [
                        "name"=>"required|unique:salons,name",
                        "sub_title"=>"required",
                        "email"=>"required|email|unique:salons,email",
                        "password"=>"required|min:6",
                        'password_confirmation'=>'required|same:password',
                        "image"=>"required",
                        "description"=>"max:500",
                        "country"=>"required",
                        "location"=>"required",
                    ];
        $msg    =   [
                        "name.required"=>"Name is required",
                    ];
        $validator  =   Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $name           =   $request->name;
            $sub_title      =   $request->sub_title;
            $email          =   $request->email;
            $description    =   $request->description;
            $image          =   $request->image;
            $logo           =   $request->logo;
            $password       =   $request->password;
            $country        =   $request->country;
            $location       =   $request->location;

            $minimum_order_amt=   $request->minimum_order_amt;
            $city           =   $request->city;
            $phone          =   $request->phone;
            $manager_phone  =   $request->manager_phone;
            $time           =   Carbon::now();
            $logoName       =   "";
            $files          =   $request->file('sub_images');
            if(!empty($files))
            {
                $count=count($files);
            }
            else
            {
                $count=0;
            }

            if($count>5)
            {
                $message    = "Maximum images allowed are 5";
                // return redirect()->back()->with("error", true)->with("msg", "Maximum images allowed are 5")->withInput();
            }

            $url            = public_path('img/salons/');
            $turl           = public_path('img/salons/thumbnails/');
            // $url            =   $_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/salons/";
            // $turl           =   $_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/salons/thumbnails/";
            if (isset($image))
            {
                $imageName  =   md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();
                $desti      =   $url.$imageName;
                $t_desti    =   $turl.$imageName;
                $resize     =   Image::make($image->getRealPath())->resize(300, null,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                });

                $resize->save($t_desti);
                $saveImage  =   $image->move($url, $imageName);
            }
            if (isset($logo))
            {
                $logoName   =   md5(rand(1000,9078787878)).'.'.$logo->getClientOriginalExtension();
                $desti      =   $url.$logoName;
                $t_desti    =   $turl.$logoName;

                $resize     =   Image::make($logo->getRealPath())->resize(300, null,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                $resize->save($t_desti);
                $savelogo   =   $logo->move($url, $logoName);
            }

            $b_password     =   bcrypt($request->password);
            $api_token      =   md5(rand(10000,1000000).$email);
            $pricing        =   isset($request->pricing)?$request->pricing:'55';

            $latitude       =   isset($request->latitude)?$request->latitude:'25.2048';
            $longitude      =   isset($request->longitude)?$request->longitude:'55.2708';
            $delivery_area_coords   =   isset($request->delivery_area_coords)?$request->delivery_area_coords:null;

            $currency_id    =   0;

            $salon_insert_data = [
                                    'name'=>$name,
                                    'sub_title'=>$sub_title,
                                    'pricing'=>$pricing,
                                    'latitude'=>$latitude,
                                    'location'=>$location,
                                    'longitude'=>$longitude,
                                    'delivery_area_coords'=>$delivery_area_coords,
                                    'description'=>$description,
                                    'cancellation_policy'=>$request->cancellation_policy,
                                    'email'=>$email,
                                    'city'=>$city,
                                    'country_id'=>$country,
                                    'currency_id'=>$currency_id,
                                    "password"=>$b_password,
                                    'phone'=>$phone,
                                    'manager_phone'=>$manager_phone,
                                    "suspend"=>0,
                                    "featured"=>0,
                                    "approved"=>1,
                                    "active"=>1,
                                    'image'=>$imageName,
                                    'logo'=>$logoName,
                                    'minimum_order_amt'=>$minimum_order_amt,
                                    'created_at'=> $time,
                                    "updated_at"=>$time
                                ];

            $new_salon      =   Salons::insertGetId($salon_insert_data);

            if($new_salon)
            {

                if($request->hasFile('sub_images'))
                {
                    foreach ($files as $file)
                    {
                        $fileName   = md5(rand(1000,9078787878)).'.'.$file->getClientOriginalExtension();
                        $desti      =   $url.$fileName;
                        $t_desti    =   $turl.$fileName;
                        $resize     =   Image::make($file->getRealPath())->resize(300, null,
                                        function ($constraint) {
                                            $constraint->aspectRatio();
                                        });
                        $resize->save($t_desti);
                        $savefile   =   $file->move($url, $fileName);
                        $new_file   =   SalonImages::insertGetId([
                                        'salon_id'=>$new_salon,
                                        'approved'=>1,
                                        'image'=>$fileName,
                                        'created_at'=> $time,
                                        "updated_at"=>$time
                                    ]);
                    }
                }
                $new_token      =   SalonsToken::insertGetId([
                                        'salon_id'=>$new_salon,
                                        'api_token'=>$api_token,
                                        'created_at'=> $time,
                                        "updated_at"=>$time
                                    ]);

                if(isset($request->categories))
                {
                    foreach($request->categories as $category)
                    {
                        $insert=SalonCategories::insert([
                                    "salon_id"      =>  $new_salon,
                                    "category_id"   =>  $category,
                                    'created_at'    =>  $time,
                                    "updated_at"    =>  $time
                                ]);
                    }
                }

                $message = "New salon added successfully";
            }
            else
            {
                $message = "Sorry error occured";
            }
        }
        return redirect()->route('salonlistpage')->with("error", true)->with("msg", $message)->withInput();
    }

    public function edit(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $c_categories=$subs=[];
            $activePage="Salons";
            $salon=Salons::where("id",$id)->first();
            $countries=Countries::orderBy("name")->pluck("name","id");
            $categories=Categories::where('active_status',1)->whereNull('deleted_at')->get();
            $sub_images=SalonImages::where("salon_id",$id)->select("id","image")->get();
            $image=isset($salon->image)?$salon->image:'';
            $logo=isset($salon->logo)?$salon->logo:'';

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
        }
        $count=count($sub_images);

        return view('admin.salons.edit',compact('activePage','countries','salon','id','subs','categories','c_categories','count'));
    }

    public function details(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $id             =   $request->id;
            $c_categories   =   [];
            $activePage     =   "Salons";
            $salon          =   DB::table("salons")
                                ->leftJoin("countries", "countries.id","=","salons.country_id")
                                ->whereNull('salons.deleted_at')
                                ->where("salons.id",$id)
                                ->select("salons.id","salons.name","salons.email","salons.description",
                                        "salons.location","salons.country_id","countries.name as country",
                                        "salons.city","salons.featured","salons.phone","salons.active",
                                        "salons.image","salons.reschedule_policy",
                                        "salons.cancellation_policy")
                                ->first();
            if(isset($salon))
            {
                $categories =   DB::table("salon_categories")
                                ->join("categories", "categories.id","=","salon_categories.category_id")
                                ->where('salon_categories.salon_id',$id)
                                ->whereNull('salon_categories.deleted_at')
                                ->groupBy("salon_categories.id")
                                ->select("salon_categories.*","categories.category","categories.image")
                                ->get();
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


                $staffs     =   SalonStaffs::where('salon_id',$id)->get();
                $services   =   DB::table("salon_services")
                                ->join("categories", "categories.id","=","salon_services.category_id")
                                ->where('salon_services.salon_id',$id)
                                ->whereNull('salon_services.deleted_at')
                                ->groupBy("salon_services.id")
                                ->select("salon_services.*","categories.category")
                                ->get();

                $booking    =   DB::table("booking")
                                ->join("salons", "salons.id","=","booking.salon_id")
                                ->join("user", "user.id","=","booking.user_id")
                                ->whereNull('booking.deleted_at')
                                ->where('booking.salon_id',$id)
                                ->select("booking.*","user.first_name","user.last_name","user.email","salons.name","salons.pricing")
                                ->get();

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

                $salon->images  =   $images;
                $reviews        =   DB::table("salon_reviews")
                                    ->join("user", "user.id","=","salon_reviews.user_id")
                                    ->where("salon_id",$id)
                                    ->whereNull("salon_reviews.deleted_at")
                                    ->whereNotNull("reviews")
                                    ->select("salon_reviews.*","user.first_name","user.last_name")
                                    ->get();

                $rating_count   =   SalonReviews::where("salon_id",$salon->id)->get()->count();
                $review_count   =   SalonReviews::where("salon_id",$salon->id)->whereNotNull("reviews")->get()->count();
                $salon->reviews =   $reviews;
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
                    $salon->rating  =   "No ratings yet";
                    $salon->overall_rating=0;
                }
                if(isset($salon->image)&&$salon->image!='')
                {
                  $salon->thumbnail = env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                    $salon->image   = env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                }
                else
                {
                    $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $salon->image   = env("IMAGE_URL")."logo/no-picture.jpg";
                }
            }
        }
        return view('admin.salons.details',compact('activePage','reviews','salon','id','images','categories','services','staffs','booking','working_hours'));
    }

    public function update(Request $request)
    {
       $rules=[
          "id"=>"required|exists:salons,id,deleted_at,NULL",
          "name"=>"required",
          "sub_title"=>"required",
          "email"=>"required",
          "description"=>"max:500",
          "country"=>"required",
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
            $id=$request->id;
            $name=$request->name;
            $sub_title=$request->sub_title;
            $email=$request->email;
            $pricing=$request->pricing;
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
            $sub_images=SalonImages::where("salon_id",$id)->select("id","image")->count();
              if(!empty($files))
            {
                $count=count($files);
            }
            else
            {
                $count=0;
            }

            $t_count=$sub_images+$count;

            if($t_count>5)
            {
                return redirect()->back()->with("error", true)->with("msg", "Maximum images allowed are 5")->withInput();
            }
            $currency_id=0;
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

            $new_salon=Salons::where("id",$id)->update(['name'=>$name,'sub_title'=>$sub_title,'location'=>$location,'latitude'=>$latitude,'longitude'=>$longitude,'description'=>$description,'cancellation_policy'=>$cancellation_policy,'reschedule_policy'=>$reschedule_policy,'email'=>$email,'pricing'=>$pricing,'city'=>$city,'country_id'=>$country,'currency_id'=>$currency_id, 'phone'=>$phone,'minimum_order_amt'=>$minimum_order_amt,
            'manager_phone'=>$manager_phone,"suspend"=>0, "active"=>1,'image'=>$imageName,'logo'=>$logoName,"updated_at"=>$time]);

            if($new_salon)
            {
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

                        $new_file=SalonImages::insertGetId(['salon_id'=>$id, 'approved'=>1,'image'=>$fileName, 'created_at'=> $time,"updated_at"=>$time]);
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
                    return redirect(env("ADMIN_URL").'/salons')->with("error", false)->with("msg", "Salon updated successfully");
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

    public function ViewDeliveryAreaPage()
    {
        $salon_id   = request()->id;
        $salon = DB::table('salons')->where('id',$salon_id)->first();
        if(!$salon){
            return redirect()->back()->with("error", true)->with("msg", "Please Check Salon ID");
        }

        $result  = ['id'=>$salon->id,'lat'=>$salon->latitude,'lng'=>$salon->longitude,
                'name'=>$salon->name,'area'=>$salon->delivery_area_coords];
        return view('admin.salons.delivery_area',compact('result'));
    }

    public function UpdateDeliveryArea(){
        // dd(request()->all());
        $salon_id   = request()->salon_id;
        $salon = DB::table('salons')->where('id',$salon_id)->first();
        if(!$salon){
            return redirect()->back()->with("error", true)->with("msg", "Please Check Salon ID");
        }
        $old_delivery_area = $salon->delivery_area_coords;

        $update_area = ['delivery_area_coords'=>request()->delivery_area_coords,
                        'updated_at'=>date('Y-m-d H:i:s')];
        // dd($update_area);
        $update = DB::table('salons')->where('id',$salon_id)->update($update_area);
        if($update)
        {
            if($old_delivery_area == null){
                $notification_data =    [
                                            'type'=>'1' ,
                                            "redirect_id"=>$salon_id,
                                            "data"=> [
                                                "Shop_id"=>$salon_id,
                                                "ShopTitle"=>$salon->name,
                                                "ShopLocation"=>$salon->location
                                            ],
                                            'delivery_area' =>request()->delivery_area_coords
                                        ];
                PushNotificationController::sendWebNotification($notification_data);
            }
            return redirect()->back()->with("error", false)->with("msg", "Delivery Area Updated Successfully");
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

            $delete=SalonImages::where("id",$id)->delete();
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

    public function featured(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $update=Salons::where("id",$id)->update(["featured"=>1,"updated_at"=>$time]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully updated");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
            }

        }
    }

    public function remove_featured(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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

            $update=Salons::where("id",$id)->update(["featured"=>0,"updated_at"=>$time]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully updated");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
            }

        }
    }

    public function active(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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

            $update=Salons::where("id",$id)->update(["active"=>1,"updated_at"=>$time]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully updated");
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
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $update=Salons::where("id",$id)->update(["active"=>0,"updated_at"=>$time]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully updated");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
            }

        }
    }

    public function delete(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $delete=Salons::where("id",$id)->delete();
            if($delete)
            {
                return redirect()->back()->with("error", false)->with("msg", "Successfully Deleted");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
            }
        }
    }



    public function ExcelDownloadReport()
    {
        return Excel::download(new SalonReport, 'SalonReport.xlsx');
    }



}
