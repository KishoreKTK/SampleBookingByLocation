<?php

namespace App\Http\Controllers\app;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Stadium;
use App\Booking;
use Carbon\Carbon;
use App\UserToken;
use App\SalonReviews;
use App\ReviewOption;
use App\UserReviewOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;

class AppUserReviewsController extends Controller
{
    public function reviews(Request $request)
    {
    	$api_token=request()->header('User-Token');
        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
        $reviews=DB::table("salon_reviews")
        	->join("salons", "salons.id","=","salon_reviews.salon_id")
            ->join("user","user.id","=","salon_reviews.user_id")
            ->whereNull('salon_reviews.deleted_at')
        	->select("salon_reviews.id","user.first_name","user.last_name","salon_reviews.salon_id","salon_reviews.reply","salon_reviews.rating","salon_reviews.reviews","salons.name","salons.image","salon_reviews.created_at")->get();   //->where("salon_reviews.user_id",$user_id)->get();

	    if(isset($reviews)&& count($reviews)>0)
        {
        	foreach($reviews as $each)
    		{
    			 if(isset($each->image)&&$each->image!='')
	            {
	                $each->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$each->image;
	                $each->image= env("IMAGE_URL")."salons/".$each->image;
	            }
	            else
	            {
	                $each->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
	                $each->image= env("IMAGE_URL")."logo/no-picture.jpg";

	            }
                $date=Carbon::parse($each->created_at);
                $each->created_at=$date->format('d-M');
                $review_options=UserReviewOption::where('review_id',$each->id)->get();
                $each->review_option=$review_options;
    		}
        	$return['error']=false;
        	$return['msg']="Your reviews listed successfully";
	    	$return['reviews']=$reviews;
        }
        else
        {
        	$return['error']=true;
            $return['msg']="No reviews yet";
        }

	    return $return;
    }

    public function reviews_add(Request $request)
    {
    	 $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
            "reviews"=>"required",
            "review_rating"=>"required",
            ];
        $msg=[
            "rating.required"=>"Rating is required"
             ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
        	$return['error']=true;
		    $return['msg']=$validator->errors()->all();
        }
        else
        {
        //   return  count($request->review_optons);
	    	$api_token=request()->header('User-Token');
	        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;

            // return $user_id;
	        $salon_id   =   $request->salon_id;
            $booking_id =   $request->booking_id;
            $check_already_reviewd  =   SalonReviews::where('salon_id',$salon_id)->where('user_id',$user_id)
                                        ->where('booking_id',$booking_id)->first();
            if($check_already_reviewd)
            {
                $return['error']=true;
                $return['msg']="Already reviewd";
                return $return;
            }
	        $reviews=$request->reviews;
	        $salon_id=$request->salon_id;

 			$time=Carbon::now();
 			 $times=$request->get('review_rating');
            $times= json_decode($times);
            $add_review=SalonReviews::insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'booking_id'=>$booking_id,'rating'=>0,'reviews'=>$reviews, 'created_at'=> $time,"updated_at"=>$time]);
            if(isset($times) && count($times)>0)
            {
                $total_rating=0;
                $count=0;
                foreach($times as $value)
                {
                    $review_option=$value->review_option_id;
                    $review_rate=$value->rating;
                    $total_rating=$total_rating+$review_rate;
                    $count=$count+1;
                    $add_sub_review=DB::table('salon_sub_reviews')->insertGetId(['review_id'=>$add_review,'user_id'=>$user_id,'review_option_id'=>$review_option,'rating'=>$review_rate, 'created_at'=> $time,"updated_at"=>$time]);
                }
                $overall_rating=$total_rating/$count;
                if($add_sub_review)
                {
                    $update_sub_review=SalonReviews::where("id",$add_review)->update(['rating'=>$overall_rating,"updated_at"=>$time]);
                }
            }
            //  $add_sub_reviews=
                //  $add_review=SalonReviews::insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'rating'=>$rating,'reviews'=>$reviews, 'created_at'=> $time,"updated_at"=>$time]);
                // $review_opts=$request->review_optons;
 				// if(isset($review_opts) && count($review_opts)>0)
                //  {
                //     //  $dlt_review_options=UserReviewOption::where('id',$add_review)->delete();
                //     foreach($review_opts as $review_opt)
                //     {
                //         $add_review=DB::table('user_review_options')->insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'review_id'=>$add_review,'review_option_id'=>$review_opt, 'created_at'=> $time,"updated_at"=>$time]);
                //     }
                //  }



		    if($add_review)
	        {
                $update_booking_review    =   DB::table('booking')
                                            ->where('id',$booking_id)
                                            ->update(['booking_review'=>1]);
	        	$return['error']=false;
	        	$return['msg']="Thank you for your review.";
	        }
	        else
	        {
	        	$return['error']=true;
	            $return['msg']="Sorry error occured";
	        }
	    }

	    return $return;
    }

    public function reviews_addss(Request $request)
    {
    	 $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "rating"=>"required",
            "review"=>"required",
            ];
        $msg=[
            "rating.required"=>"Rating is required"
             ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
        	$return['error']=true;
		    $return['msg']=$validator->errors()->all();
        }
        else
        {
        //   return  count($request->review_optons);
	    	$api_token=request()->header('User-Token');
	        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            // return $user_id;
	        $salon_id=$request->salon_id;
	        $rating=$request->rating;
	        $reviews=$request->reviews;
	        $salon_id=$request->salon_id;

 			$time=Carbon::now();

                 $add_review=SalonReviews::insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'rating'=>$rating,'reviews'=>$reviews, 'created_at'=> $time,"updated_at"=>$time]);
                $review_opts=$request->review_optons;
 				if(isset($review_opts) && count($review_opts)>0)
                 {
                    //  $dlt_review_options=UserReviewOption::where('id',$add_review)->delete();
                    foreach($review_opts as $review_opt)
                    {
                        $add_review=DB::table('user_review_options')->insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'review_id'=>$add_review,'review_option_id'=>$review_opt, 'created_at'=> $time,"updated_at"=>$time]);
                    }
                 }



		    if($add_review)
	        {
	        	$return['error']=false;
	        	$return['msg']="Thank you for your review.";
	        }
	        else
	        {
	        	$return['error']=true;
	            $return['msg']="Sorry error occured";
	        }
	    }

	    return $return;
    }


     public function reviews_update(Request $request)
    {
    	 $rules=[
    	 	"id"=>"exists:salons,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "rating"=>"required",
            "review"=>"required",
            ];
        $msg=[
            "rating.required"=>"Rating is required"
             ];


        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
        	$return['error']=true;
		    $return['msg']=$validator->errors()->all();
        }
        else
        {
	    	$api_token=request()->header('User-Token');
	        $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            // return $user_id;
	        $salon_id=$request->salon_id;
	        $rating=$request->rating;
	        $reviews=$request->reviews;
	        $salon_id=$request->salon_id;

 			$time=Carbon::now();
 			if($request->id)
 			{
 				$id=$request->id;
 				$check=SalonReviews::where("id",$id)->where("user_id",$user_id)->where("salon_id",$salon_id)->first();
 				if(isset($check))
 				{
 					$add_review=SalonReviews::where("id",$id)->where("user_id",$user_id)->where("salon_id",$salon_id)->update(['rating'=>$rating,'reviews'=>$reviews,"updated_at"=>$time]);
 				}
 				else
 				{
 					$return['error']=true;
        			$return['msg']="You don't have the permisssion to perform this action";
        			return $return;
 				}
 			}
 			else
 			{
                $check=SalonReviews::where("user_id",$user_id)->where("salon_id",$salon_id)->first();
                if(isset($check))
                {
                    // return $check;
                    $add_review=SalonReviews::where("user_id",$user_id)->where("salon_id",$salon_id)->update(['rating'=>$rating,'reviews'=>$reviews,"updated_at"=>$time]);
                }
                else
                {
                    //check booking

                    $booking=DB::table("booking_services")
                    ->join("booking", "booking_services.booking_id","=","booking.id")
                    ->where("booking.salon_id",$salon_id)
                    ->where("booking.user_id",$user_id)
                    ->whereNull("booking.deleted_at")
                    ->whereNull("booking_services.deleted_at")
                    ->get();
                    // return $booking;
                    $val=0;
                    if(isset($booking)&& count($booking)>0)
                    {
                        foreach ($booking as $key => $value)
                        {
                            $date=new Carbon($value->date);
                            if($date<$time)
                            {
                                $val=1;
                            }
                        }
                        if($val==1)
                        {
                            $add_review=SalonReviews::insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'rating'=>$rating,'reviews'=>$reviews, 'created_at'=> $time,"updated_at"=>$time]);
                        }
                        else
                        {
                            $return['error']=true;
                            $return['msg']="You don't have the permisssion to perform this action";
                            return $return;
                        }
                    }
                    else
                    {
                        $return['error']=true;
                        $return['msg']="You don't have the permisssion to perform this action";
                        return $return;
                    }
                // $check_booking=Booking::where("user_id",$user_id)->where("salon_id",$salon_id)->first();
                // if(isset($check_booking))
                // {
                //     $add_review=SalonReviews::insertGetId(['salon_id'=>$salon_id,'user_id'=>$user_id,'rating'=>$rating,'reviews'=>$reviews, 'created_at'=> $time,"updated_at"=>$time]);
                // }
                // else
                // {
                //     $return['error']=true;
                //     $return['msg']="You don't have the permisssion to perform this action";
                //     return $return;
                // }

                }

 			}

		    if($add_review)
	        {
	        	$return['error']=false;
	        	$return['msg']="Thank you for your review.";
	        }
	        else
	        {
	        	$return['error']=true;
	            $return['msg']="Sorry error occured";
	        }
	    }

	    return $return;
    }

    public function review_options(Request $request)
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


            $return['error']=false;
            $return['msg']="Review options listed successfully";
            $return['review_options']=$review_options;
        }
        else
        {
            $return['error']=true;
            $return['msg']="No options found";
        }

        return $return;

    }
}
