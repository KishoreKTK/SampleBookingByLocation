<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use Carbon\Carbon;
use App\SalonReviews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonReviewsController extends Controller
{
    public function index(Request $request)
    {
    	$activePage="Reviews";
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
    	$reviews=DB::table("salon_reviews")
            ->join("user", "user.id","=","salon_reviews.user_id")
            ->where("salon_id",$salon_id)
            ->whereNull("salon_reviews.deleted_at")
            ->whereNotNull("reviews")
            ->select("salon_reviews.*","user.first_name","user.last_name")->get();
        $time=Carbon::now();
         foreach (SalonReviews::where('read', '=',0)->where("salon_id",$salon_id)->get() as $data)
        {
            $read=SalonReviews::where("id",$data->id)->update(["read"=>1,"updated_at"=>$time]);
        }
            Session::put('sreviews', 0);

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
        return view('salon.reviews.list',compact('activePage','reviews'));

    }
     public function details(Request $request)
    {
    	$rules=[
            "id"=>"required|exists:salon_reviews,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
	    	$activePage="Reviews";
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
	    	$review=DB::table("salon_reviews")
	            ->join("user", "user.id","=","salon_reviews.user_id")
	            ->where("salon_id",$salon_id)
	            ->where("salon_reviews.id",$id)
	            ->whereNull("salon_reviews.deleted_at")
	            ->whereNotNull("reviews")
	            ->select("salon_reviews.*","user.first_name","user.last_name","user.email")->first();
        	return view('salon.reviews.details',compact('activePage','review','id'));
        }

    }
     public function edit(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salon_reviews,id,deleted_at,NULL",
            "reply"=>"required",
            ];
        $msg=[
            "id.required"=>"ID is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $activePage="Reviews";
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
            $review=SalonReviews::where("salon_id",$salon_id)->where("salon_reviews.id",$id)->update(["reply"=>$request->reply,"updated_at"=>$time]);
            if($review)
            {
                 return redirect(env("ADMIN_URL").'/salon/reviews')->with("error", false)->with("msg", "Your reply added successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }

    }
}
