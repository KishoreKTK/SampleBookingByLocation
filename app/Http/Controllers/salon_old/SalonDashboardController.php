<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Booking;
use App\SalonStaffs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonDashboardController extends Controller
{
  
    public function index()
    {
    	$activePage="Dashboard";
        $who=Session::get('user');
        if(isset($who) && $who == 'salon')
        {
            $salon_id= Auth::guard('salon-web')->user()->id;
        }
        else
        {
            $salon_id= Auth::guard('salons-web')->user()->salon_id;
        }
    	$booking=Booking::where("active",1)->where("salon_id",$salon_id)->get()->count();
    	$reviews=DB::table("salon_reviews")
            ->join("user", "user.id","=","salon_reviews.user_id")
            ->where("salon_id",$salon_id)
            ->whereNull("salon_reviews.deleted_at")
            ->whereNotNull("reviews")
            ->select("salon_reviews.*","user.first_name","user.last_name")->get()->count();
        $staffs=SalonStaffs::where('salon_id',$salon_id)->get()->count();
         $services=DB::table("salon_services")
        ->join("categories", "categories.id","=","salon_services.category_id")
        ->where('salon_services.salon_id',$salon_id)
        ->whereNull('salon_services.deleted_at')
        ->groupBy("salon_services.id")
        ->select("salon_services.*","categories.category")->get()->count();
    	return view('salon.dashboard',compact('activePage','booking','reviews','staffs','services'));
    }
     public function user(Request $request)
    {
        $rules=[
            "id"=>"required|exists:user,id,deleted_at,NULL",
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
            $activePage="Users";
            
            $user=DB::table("user")
            ->leftJoin("countries", "countries.id","=","user.country_id")
            ->whereNull('user.deleted_at')->where("user.id",$id)
            ->select("user.*","countries.name as country")->first();

            if($user->gender_id==1)
            {
                $user->gender="Male";
            }
            elseif($user->gender_id==2)
            {
                $user->gender="Female";
            }
            else
            {
                $user->gender="Not added";
            }
              $reviews=DB::table("salon_reviews")
                ->join("user", "user.id","=","salon_reviews.user_id")
                ->join("salons", "salons.id","=","salon_reviews.salon_id")
                ->where("salon_reviews.user_id",$id)
                ->whereNull("salon_reviews.deleted_at")
                ->whereNotNull("reviews")
                ->select("salon_reviews.*","user.first_name","user.last_name","salons.name","salons.image")->get();
                if(isset($reviews)&& count($reviews)>0)
                {
                    foreach($reviews as $review)
                    {
                         if(isset($review)&& $review->image!='')
                        {
                             $review->thumbnail=env('IMAGE_URL').'salons/thumbnails/'.$review->image;
                             $review->image=env('IMAGE_URL').'salons/'.$review->image;
                        }
                        else
                        {
                            $review->thumbnails=env('IMAGE_URL').'logo/no-picture.jpg';
                            $review->image=env('IMAGE_URL').'logo/no-picture.jpg';
                        }
                    }
                }
            $favorites=DB::table("user_favorites")
            ->join("salons", "salons.id","=","user_favorites.salon_id")
            ->whereNull('user_favorites.deleted_at')
            ->select("user_favorites.*","salons.name","salons.image")->where("user_favorites.user_id",$id)->get();
            if(isset($favorites)&& count($favorites)>0)
            {
                foreach($favorites as $favorite)
                {
                     if(isset($favorite)&& $favorite->image!='')
                    {
                         $favorite->thumbnail=env('IMAGE_URL').'salons/thumbnails/'.$favorite->image;
                         $favorite->image=env('IMAGE_URL').'salons/'.$favorite->image;
                    }
                    else
                    {
                        $favorite->thumbnails=env('IMAGE_URL').'logo/no-picture.jpg';
                        $favorite->image=env('IMAGE_URL').'logo/no-picture.jpg';
                    }
                }
            }
            $booking=DB::table("booking")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->join("user", "user.id","=","booking.user_id")
            ->where("booking.user_id",$id)
            ->whereNull('booking.deleted_at')
            // ->where("booking.active",1)
            ->select("booking.*","user.first_name","user.last_name","user.email","salons.name","salons.image")->get();

              if(isset($booking)&& count($booking)>0)
            {
                foreach($booking as $book)
                {
                    # code...
                    $book->amount=$book->amount+$book->balance_amount;
                     if(isset($book->pricing)&& $book->pricing!=null)
                    {
                        $book->mood_commission=$book->amount * ($book->pricing/100);
                    }
                    else
                    {
                        $book->mood_commission="0.00";
                    }
                     if(isset($book)&& $book->image!='')
                    {
                         $book->thumbnail=env('IMAGE_URL').'salons/thumbnails/'.$book->image;
                         $book->image=env('IMAGE_URL').'salons/'.$book->image;
                    }
                    else
                    {
                        $book->thumbnails=env('IMAGE_URL').'logo/no-picture.jpg';
                        $book->image=env('IMAGE_URL').'logo/no-picture.jpg';
                    }
                }
            }
            if(isset($user)&& $user->image!='')
            {
                 $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
                 $user->image=env('IMAGE_URL').'users/'.$user->image;
            }
            else
            {
                $user->thumbnail=env('IMAGE_URL').'logo/no-profile.jpg';
                $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
            }
        }

        return view('salon.user',compact('activePage','user','id','favorites','reviews','booking'));
    }
}
