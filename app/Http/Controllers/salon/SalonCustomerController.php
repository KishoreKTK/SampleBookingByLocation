<?php

namespace App\Http\Controllers\salon;

use App\Customers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalonCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activePage =   "Customers";
    	$who        =   Session::get('user');

        if(isset($who) && $who == 'salon') {
            $salon_id   =   Auth::guard('salon-web')->user()->id;
        } else {
            $salon_id   =   Auth::guard('salons-web')->user()->salon_id;
        }

        $get_user_ids   =   DB::table('booking')->where('salon_id',$salon_id)->whereNull('deleted_at')->pluck('user_id')->toArray();
        $userids        =   array_filter($get_user_ids, function($a) { return ($a !== 0); });
        $unique_user_ids=   array_unique($userids);
        $users          =   DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->whereNull('user.deleted_at')->whereIn('user.id',$unique_user_ids)
                            ->select("user.id","user.first_name","user.last_name","user.suspend","user.image","user.email","user.login_type","user.gender_id","user.phone","user.created_at","countries.name as country")
                            ->paginate(20);
        // dd($users);
        foreach($users as $key=>$user)
        {
            $user_id                    =   $user->id;
            // dd($user_id);
            $users[$key]->booking_count =   DB::table('booking')->where('salon_id',$salon_id)
                                            ->whereNull('deleted_at')->where('user_id',$user_id)->count();

            $rating        =   DB::table('salon_reviews')->where('salon_id',$salon_id)->where("user_id",$user_id)->whereNotNull("reviews")->pluck('rating')->toArray();
            // dd(['user_id'=>$user_id,'salon_id'=>$salon_id]);
            // dd($rating);
            $int_rating    =   array_map('floatval', $rating);
            // dd($int_rating);

            $review_count                   =   count($int_rating);
            if($review_count == 0){
                $users[$key]->overall_rating    =   0;
            } else {
                $total_review                   =   array_sum($int_rating);
                $overall                        =   $total_review/$review_count;
                $users[$key]->overall_rating    =   (int)round($overall);
            }
        }
        // dd($users);
    	return view("salon.customers.list",compact('activePage','users'));
    }


    public function details(Request $request)
    {
        $who        =   Session::get('user');

        if(isset($who) && $who == 'salon') {
            $salon_id   =   Auth::guard('salon-web')->user()->id;
        } else {
            $salon_id   =   Auth::guard('salons-web')->user()->salon_id;
        }

        // dd("am i getting called");
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

            $user   =   DB::table("user")
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
            $reviews    =   DB::table("salon_reviews")
                            ->join("user", "user.id","=","salon_reviews.user_id")
                            ->join("salons", "salons.id","=","salon_reviews.salon_id")
                            ->where("salon_reviews.user_id",$id)
                            ->where('salon_reviews.salon_id',$salon_id)
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

            $favorites  =   DB::table("user_favorites")
                            ->join("salons", "salons.id","=","user_favorites.salon_id")
                            ->whereNull('user_favorites.deleted_at')
                            ->where('user_favorites.salon_id',$salon_id)
                            ->select("user_favorites.*","salons.name","salons.image")
                            ->where("user_favorites.user_id",$id)->get();

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


            $booking    =   DB::table("booking")
                            ->join("salons", "salons.id","=","booking.salon_id")
                            ->join("user", "user.id","=","booking.user_id")
                            ->where("booking.user_id",$id)
                            ->where('booking.salon_id',$salon_id)
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

            $address    =   DB::table('user_address')->where('user_id',$id)->whereNull('deleted_at')
                            ->select('first_name','phone_num','address','location','city')->get();

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
        // dd($address);
        return view('salon.customers.details',compact('activePage','user','id','favorites','reviews','booking','address'));
    }

    public function unsuspend(Request $request)
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
            $time=Carbon::now();

            $update=Customers::where("id",$id)->update(["suspend"=>0,"updated_at"=>$time]);
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

    public function suspend(Request $request)
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
            $time=Carbon::now();
            $update=Customers::where("id",$id)->update(["suspend"=>1,"updated_at"=>$time]);
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

}
