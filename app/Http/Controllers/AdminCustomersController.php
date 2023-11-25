<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\Booking;
use App\Currency;
use App\Customers;
use App\Countries;
use Carbon\Carbon;
use App\SalonReviews;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
// use Maatwebsite\Excel\Excel;
use App\Exports\UserReportExport;
use Excel;
class AdminCustomersController extends Controller
{
    public function index(Request $request)
    {
    	$activePage    =   "Users";
        if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword    =   $request->keyword;
            $users      =   DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->whereNull('user.deleted_at')
                            ->where("first_name","like",'%'.$keyword.'%')
                            ->where("last_name","like",'%'.$keyword.'%')
                            ->orWhere("email","like",'%'.$keyword.'%')
                            ->orWhere("countries.name","like",'%'.$keyword.'%')
                            ->orWhere("phone","like",'%'.$keyword.'%')
                            ->select("user.id","user.first_name","user.last_name","user.suspend","user.image","user.email","user.login_type","user.gender_id","user.phone","user.created_at","countries.name as country")
                            ->paginate(20);
        }
        else
        {
            $keyword    =   "";
            $users      =   DB::table("user")
                            ->leftJoin("countries", "countries.id","=","user.country_id")
                            ->whereNull('user.deleted_at')
                            ->select("user.id","user.first_name","user.last_name","user.suspend","user.image","user.email","user.login_type","user.gender_id","user.phone","user.created_at","countries.name as country")
                            ->paginate(20);
        }
        if(isset($users)&& count($users)>0)
        {
        	foreach($users as $user)
	        {
                $login_type=isset($user->login_type)?$user->login_type:0;
                if($login_type==0)
                {
                    // if(isset($user->image)&& $user->image!='')
                    // {
                    //     $user->thumbnail=env('IMAGE_URL').'users/thumbnails/'.$user->image;
                    //     $user->image=env('IMAGE_URL').'users/'.$user->image;
                    // }
                    // else
                    // {
                    //     $user->image=env('IMAGE_URL').'logo/no-profile.jpg';
                    // }
                }
	        }
        }
    	return view('admin.users.list',compact('activePage','users','keyword'));
    }


    public function details(Request $request)
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


            $booking    =   DB::table("booking")
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
        return view('admin.users.details',compact('activePage','user','id','favorites','reviews','booking','address'));
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

    public function activity_log(Request $request)
    {
        return view('admin.users.user_activity_log');
    }


    Public function downloadcustomerreport(){
        return Excel::download(new UserReportExport, 'Customer Report.xlsx');
    }

}
