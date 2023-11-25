<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\Booking;
use App\BookingAddress;

use Illuminate\Http\Request;

class AdminGuestsController extends Controller
{
     public function index(Request $request)
    {
    	$activePage="Users";
        if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
            $guests= DB::table("booking_address")
            ->join("booking", "booking_address.booking_id","=","booking.id")
            ->where("booking.user_id",0)
            ->groupBy("booking_address.email")
            ->where(function ($q) use ($keyword) {
            $q->where("first_name","like",'%'.$keyword.'%')
            ->orWhere("booking_address.first_name","like",'%'.$keyword.'%')
            ->orWhere("booking_address.last_name","like",'%'.$keyword.'%')
            ->orWhere("booking_address.email","LIKE",'%'.$keyword.'%')
            ->orWhere("booking_address.address","LIKE",'%'.$keyword.'%')
            ->orWhere("booking_address.phone","LIKE",'%'.$keyword.'%');
            })->select("booking_address.*")
            ->paginate(20);
        }
        else
        {
            $keyword="";
            $guests=DB::table("booking_address")
            ->join("booking", "booking_address.booking_id","=","booking.id")
            ->groupBy("booking_address.email")
            ->where("booking.user_id",0)->select("booking_address.*")->paginate(20);
        }
    	return view('admin.guests.list',compact('activePage','guests','keyword'));
    }
     public function details(Request $request)
    {
         $rules=[
            "guest_id"=>"required|exists:booking_address,id,deleted_at,NULL",
            ];
        $msg=[
            "guest_id.required"=>"Guest Id is required"
             ];
             $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else 
        {
            $activePage="Users";
            $guest_id=$request->guest_id;
            $guest=DB::table("booking_address")
            ->join("booking", "booking_address.booking_id","=","booking.id")
            ->groupBy("booking_address.email")
            ->where("booking_address.id",$guest_id)
            ->where("booking.user_id",0)
            ->select("booking_address.*")->first();
            $booking=DB::table("booking")
            ->join("booking_address", "booking_address.booking_id","=","booking.id")
            ->join("salons", "salons.id","=","booking.salon_id")
            ->groupBy("booking_address.email")
            ->where("booking_address.id",$guest_id)
            ->where("booking.user_id",0)
            ->select("booking.*","booking_address.first_name","booking_address.last_name","booking_address.email","salons.name","salons.image")
            ->get();
            $guest->image= env('IMAGE_URL').'logo/no-picture.jpg';
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
           
            return view('admin.guests.details',compact('activePage','booking','guest'));
        }
    }
}
