<?php

namespace App\Http\Controllers;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use App\FAQ;
use Validator;
use App\Salons;
use App\Booking;
use App\Customers;
use App\ContactUs;
use Carbon\Carbon;
use App\Categories;
use App\SalonReviews;
use App\ReviewOption;
use App\SalonCategories;
use Illuminate\Http\Request;
use File;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
    	$activePage =   "Dashboard";
    	$salons     =   Salons::get()->count();
    	$users      =   Customers::get()->count();
    	$categories =   Categories::where('active_status',1)->whereNull('deleted_at')->get()->count();
    	$faq        =   FAQ::get()->count();
    	$contact_us =   ContactUs::get()->count();
        $booking    =   Booking::where("active",1)->get()->count();
        $recent_booking = DB::table("booking")
                        ->join("salons", "salons.id","=","booking.salon_id")
                        ->join("booking_address", "booking_address.booking_id","=","booking.id")
                        ->whereNull('booking.deleted_at')->orderBy("booking.id","desc")
                        ->where("booking.block","!=",1)
                        ->where("booking_address.first_name","!=",'')
                        ->where("booking_address.email","!=",'')
                        ->paginate(5);
        $recent_salon=  Salons::
                                orderBy("created_at","desc")
                                ->select("id","name","email","description","image","featured","active")
                                ->paginate(5);

        // print("<pre>");
        // print_r($recent_booking);die;
    	return view('admin.dashboard',compact('activePage','salons','users','categories','faq','contact_us','booking','recent_booking','recent_salon'));
    }

    public function add_review_option(Request $request)
    {
        $review_options=ReviewOption::get();
        if(isset($review_options) && count($review_options)>0)
        {
            foreach($review_options as $review_option)
            {
                if($review_option->image!="" || $review_option->image!=null)
                {
                    $review_option->thumbnail= env("IMAGE_URL")."review_option/thumbnails/".$review_option->image;
                    $review_option->image= env("IMAGE_URL")."review_option/".$review_option->image;
                }
                else
                {
                    $review_option->thumbnail= env("IMAGE_URL")."logo/default.jpg";
                    $review_option->image== env("IMAGE_URL")."logo/default.jpg";
                }
            }
        }
        return view('admin.reviews.add_option',compact('review_options'));
    }
    public function save_review_option(Request $request)
    {
        $rules=
        [
            "title"=>"required",
            "image"=>"required"
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
            $title=$request->title;
            $image=$request->image;

            $url=$_SERVER['DOCUMENT_ROOT']."/img/review_option";
            $turl=$_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/review_option/thumbnails/";
            if (isset($image))
            {
                $imageName = md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();
                $desti=$url.$imageName;
                $t_desti=$turl.$imageName;

                $thmb_imgurl    = 'img/review_option/thumbnails/'.$imageName;
                $imgurl         = 'img/review_option/';
                $resize=Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                $resize->save($thmb_imgurl);
                // $saveImage=$image->move($thmb_imgurl, $imageName);
                $request->image->move($imgurl, $imageName);
            }
            $time=Carbon::now();
            $new_review_option=ReviewOption::insertGetId([  'title'=>$title,
                                                            'image'=>$imageName,
                                                            'created_at'=> $time,
                                                            "updated_at"=>$time]);
            if($new_review_option)
            {
                return redirect()->back()->with("error", false)->with("msg", "Review option added successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }

    }

    public function reviews(Request $request)
    {
        $activePage="Reviews";
        $reviews=DB::table("salon_reviews")
            ->join("user", "user.id","=","salon_reviews.user_id")
            ->join("salons","salons.id","=","salon_reviews.salon_id")
            ->whereNull("salon_reviews.deleted_at")
            ->whereNotNull("reviews")
            ->select("salon_reviews.*","salons.name","user.first_name","user.last_name")->get();


        if(isset($reviews)&& count($reviews)>0)
        {
            foreach($reviews as $review)
            {
                  if(isset($review->image)&&$review->image!='')
                {
                    $review->thumbnail= env("IMAGE_URL")."users/thumbnails/".$review->image;
                    $review->image= env("IMAGE_URL")."users/".$review->image;

                }
                else
                {
                    $review->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $review->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }

            }
        }
        return view('admin.reviews',compact('activePage','reviews'));

    }

    public function delete(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:salon_reviews,id,deleted_at,NULL",
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

            $delete=SalonReviews::where("id",$id)->delete();

            if($delete)
            {
              return redirect()->back()->with("error", false)->with("msg", "Review deleted successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process");

            }
        }
    }
}
