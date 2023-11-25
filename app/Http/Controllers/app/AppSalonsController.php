<?php

namespace App\Http\Controllers\app;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Offers;
use App\Salons;
use App\Content;
use App\UserToken;
use Carbon\Carbon;
use App\Categories;
use App\SalonStaffs;
use App\SalonImages;
use App\SalonReviews;
use App\WorkingHours;
use App\StaffServices;
use App\SalonServices;
use App\ServiceOffers;
use App\UserFavorites;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppSalonsController extends Controller
{


    // Kishore Works - Salon Listing
    public function SaloonListing(Request $request)
    {
        try
        {

            // dd($salons_to_list);
            // $sort        =   ['alphabet_asc','latest','highest_rated','most_popular'];
            // $category_id    =   "2";
            // $keyword        =   "hosur";
            $active_salon_ids   =   DB::table('salons')->whereNull("salons.deleted_at")
                                    ->where("salons.active",1) ->pluck("id")->toArray();

                                    // New Salon  ID 25
            // dd($active_salon_ids);
            $salons_ids_toshow  =   [];
            foreach($active_salon_ids as $id){

                // dd($id);
                if($this->CheckSalontoList($id) == true){
                    $salons_ids_toshow[]    =   $id;
                }
            }
            // CheckSalontoList($salon_id);
            // dd($salons_ids_toshow);
            $sort           =   ($request->has('sortdata')) ? $request->input('sortdata') : "";
            $category_id    =   ($request->has('category_id')) ? $request->input('category_id') : "";
            $keyword        =   ($request->has('keyword')) ? $request->input('keyword') : "";
            $filter_salons  =   ($request->has('filter_salons')) ? $request->input('filter_salons') : "";


            $longitude_x        =   ($request->has('latitude')) ? $request->input('latitude') : 37.62813;
            $latitude_y         =   ($request->has('longitude')) ? $request->input('longitude') : -77.45833;

            $salons_to_list     =   [];

            // foreach($salon_ids as $id){
            $delivery_area  =   DB::table('salons')->where("salons.active",1)
                                ->whereNotNull('salons.delivery_area_coords')
                                ->whereIn('id',$salons_ids_toshow)->get();
                                // dd($delivery_area);
            if(count($delivery_area) > 0)
            {
                foreach($delivery_area as $key=>$salons)
                {
                    $delivery_area_coords   =   json_decode($salons->delivery_area_coords);
                    $vertices_x =   [];
                    $vertices_y =   [];
                    foreach($delivery_area_coords as $coords){
                        $vertices_x[] = floatval($coords->lat);
                        $vertices_y[] = floatval($coords->lng);
                    }

                    $points_polygon         =   count($vertices_x) - 1;
                    if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)){
                        $salons_to_list[]   =   $salons->id;
                    }
                }

                if(count($salons_to_list) == 0){
                    throw new \Exception('No Salons Found in Your Location');
                }
            }
            else
            {
                throw new \Exception('No Salons Found');
            }



            $salon          =   DB::table("salons")
                                ->select('salons.id','salons.name','salons.sub_title','salons.email','salons.image',
                                        'salons.location','salons.latitude','salons.longitude','salons.logo',
                                        DB::raw("IFNULL( ROUND (AVG(salon_reviews.rating), 2), '0') as avgrating"),
                                        DB::raw("IFNULL(Bookingtbl.BookingCount, '0') as BookingCount"),
                                        'salons.created_at')
                                ->leftjoin("salon_reviews", "salon_reviews.salon_id","=","salons.id")
                                ->leftjoin(DB::raw('(SELECT
                                              B.salon_id,
                                              COUNT(B.salon_id) AS BookingCount
                                            FROM
                                              booking AS B
                                            WHERE B.deleted_at IS NOT NULL
                                            GROUP BY B.salon_id
                                        ) AS Bookingtbl'),
                                        function($join)
                                        {
                                            $join->on('salons.id', '=', 'Bookingtbl.salon_id');
                                        })
                                ->whereNull("salons.deleted_at")
                                ->whereIn('salons.id',$salons_to_list)
                                ->whereNull("salon_reviews.deleted_at")
                                ->where("salons.active",1);

            if(isset($filter_salons) && $filter_salons != ''){
                $salon_id_array    = explode(',', $filter_salons);
                $salon->whereIn("salons.id",$salon_id_array);
            }

            if(isset($category_id) && $category_id != '')
            {
                $salon  ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
                        ->join("categories", "categories.id","=","salon_categories.category_id")
                        ->where("salon_categories.category_id",$category_id)
                        ->whereNull("salon_categories.deleted_at")
                        ->addSelect('categories.id as CategoryID');
            }

            if(isset($keyword) && $keyword != '')
            {
                $service_list = DB::table('salon_services')
                                    ->select('service',
                                            DB::raw("GROUP_CONCAT(DISTINCT(category_id)) as category_ids,
                                            GROUP_CONCAT(salon_id) AS salon_ids,
                                            COUNT(salon_id) AS SalonsCount"))
                                    ->where('salon_services.service','like','%'.$keyword.'%')
                                    ->whereNull('salon_services.deleted_at')
                                    ->whereIn('salon_services.salon_id',$salons_to_list)
                                    ->where('salon_services.approved',1)
                                    ->where('salon_services.pending',1)
                                    ->groupBy("salon_services.service")
                                    ->get();
                $salon->where("salons.name","like",'%'.$keyword.'%');
            }

            if($sort == "most_popular"){
                $salon  ->orderBy('BookingCount', 'DESC');
            }

            if($sort ==  'highest_rated'){
                $salon  ->orderby('Rating','desc');
            }

            if($sort == 'alphabet_asc'){
                $salon->orderBy("salons.name",'asc');
            }

            if($sort == 'latest'){
                $salon->latest("salons.created_at");
            }

            $salon->groupBy("salons.id");
            $salon_data     =   $salon->get();
            $salon_count    =   count($salon_data);
            $salon_ids   =    [];

            if(isset($keyword) && $keyword != '')
            {
                $service_list_count     = count($service_list);
                if($salon_count == 0 && $service_list_count == 0) {
                    $result         =   array(
                        'status'        =>  false,
                        'salon_count'   =>  $salon_count,
                        'msg'           =>  "No Salon / Service Found",
                    );
                }
                else if($service_list_count > 0 && $salon_count == 0){
                    $result         =   array(
                        'status'        =>  True,
                        'msg'           =>  "Services listed successfully",
                        'service'       =>  $service_list,
                        'service_count' =>  count($service_list)
                    );
                } else {
                    if(request()->header('User-Token'))
                    {
                        $api_token=request()->header('User-Token');
                        $user=UserToken::where("api_token",$api_token)->first();
                        if(isset($user)&& isset($user->user_id))
                        {
                            $user_id    =   $user->user_id;
                            $favorites  =   DB::table("user_favorites")
                                            ->join("user", "user.id","=","user_favorites.user_id")
                                            ->join("salons", "salons.id","=","user_favorites.salon_id")
                                            ->where("user_favorites.user_id",$user_id)
                                            ->whereNull('user_favorites.deleted_at')
                                            ->groupBy("user_favorites.salon_id")
                                            ->pluck("user_favorites.salon_id")->toArray();
                            $filter_data['user_id'] = $user_id;
                            $filter_data['favorite']    = $favorites;
                        }
                        else
                        {
                            $filter_data['user_id'] = null;
                            $filter_data['favorite']    = null;
                        }
                    }

                    foreach($salon_data as $salon)
                    {
                        $salon_ids[]   =  $salon->id;
                        if(isset($salon->BookingCount))
                        if(isset($favorites)&& count($favorites)>0)
                        {
                            if(in_array($salon->id, $favorites))
                            {
                                $salon->is_favorite=true;
                            }
                            else
                            {
                                $salon->is_favorite=false;
                            }
                        }
                        else
                        {
                            $salon->is_favorite=false;
                        }

                        if($salon->avgrating > 0)
                        {
                            $overall    =   $salon->avgrating;
                            if($overall >=  4.5)
                            {
                                $overall    =  5;
                            }
                            elseif($overall>=4 && $overall<4.5)
                            {
                                $overall    =  4.5;
                            }
                            elseif($overall>=3.5 && $overall<4)
                            {
                                $overall    =   4;
                            }
                            elseif($overall>=3 && $overall<3.5)
                            {
                                $overall    =   3.5;
                            }
                            elseif($overall>=2.5 && $overall<3)
                            {
                                $overall    =   3;
                            }
                            elseif($overall>=2 && $overall<2.5)
                            {
                                $overall=2.5;
                            }
                            elseif($overall>=1.5 && $overall<2)
                            {
                                $overall=2;
                            }
                            elseif($overall>=1 && $overall<1.5)
                            {
                                $overall=1.5;
                            }
                            elseif($overall>=0.5 && $overall<1)
                            {
                                $overall=1;
                            }
                            elseif($overall>=0 && $overall<0.5)
                            {
                                $overall=0.5;
                            }
                            else
                            {
                                $overall=0;
                            }
                            // $salon->overall_rating=$overall;
                            // $salon->overall_rating=round($overall, 2);
                        }
                        else{
                            $overall=0;
                        }

                        // $salon->overall_rating=number_format( (float) $overall, 2, '.', '');
                        $salon->overall_rating = strval($overall);

                        if(isset($salon->image)&&$salon->image!='')
                        {
                            $salon->thumbnail   = env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                            $salon->image       = env("IMAGE_URL")."salons/".$salon->image;
                        }
                        else
                        {
                            $salon->thumbnail   = env("IMAGE_URL")."logo/no-picture.jpg";
                            $salon->image       = env("IMAGE_URL")."logo/no-picture.jpg";
                        }

                        if(isset($salon->logo)&&$salon->logo!='')
                        {
                            $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
                        }
                        else
                        {
                            $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                        }
                    }

                    $result         =   array(
                                        'status'        =>  True,
                                        'msg'           =>  "Salons listed successfully",
                                        'salon_count'   =>  $salon_count,
                                        'salons'        =>  $salon_data,
                                        'service'       =>  $service_list,
                                        'service_count' =>  count($service_list)
                                    );
                }
            } else {
                if($salon_count > 0)
                {
                    if(request()->header('User-Token'))
                    {
                        $api_token=request()->header('User-Token');
                        $user=UserToken::where("api_token",$api_token)->first();
                        if(isset($user)&& isset($user->user_id))
                        {
                            $user_id    =   $user->user_id;
                            $favorites  =   DB::table("user_favorites")
                                            ->join("user", "user.id","=","user_favorites.user_id")
                                            ->join("salons", "salons.id","=","user_favorites.salon_id")
                                            ->where("user_favorites.user_id",$user_id)
                                            ->whereNull('user_favorites.deleted_at')
                                            ->groupBy("user_favorites.salon_id")
                                            ->pluck("user_favorites.salon_id")->toArray();
                            $filter_data['user_id'] = $user_id;
                            $filter_data['favorite']    = $favorites;
                        }
                        else
                        {
                            $filter_data['user_id'] = null;
                            $filter_data['favorite']    = null;
                        }
                    }

                    foreach($salon_data as $salon)
                    {
                        $salon_ids[]   =  $salon->id;
                        if(isset($salon->BookingCount))
                        if(isset($favorites)&& count($favorites)>0)
                        {
                            if(in_array($salon->id, $favorites))
                            {
                                $salon->is_favorite=true;
                            }
                            else
                            {
                                $salon->is_favorite=false;
                            }
                        }
                        else
                        {
                            $salon->is_favorite=false;
                        }

                        if($salon->avgrating > 0)
                        {
                            $overall    =   $salon->avgrating;
                            if($overall >=  4.5)
                            {
                                $overall    =  5;
                            }
                            elseif($overall>=4 && $overall<4.5)
                            {
                                $overall    =  4.5;
                            }
                            elseif($overall>=3.5 && $overall<4)
                            {
                                $overall    =   4;
                            }
                            elseif($overall>=3 && $overall<3.5)
                            {
                                $overall    =   3.5;
                            }
                            elseif($overall>=2.5 && $overall<3)
                            {
                                $overall    =   3;
                            }
                            elseif($overall>=2 && $overall<2.5)
                            {
                                $overall=2.5;
                            }
                            elseif($overall>=1.5 && $overall<2)
                            {
                                $overall=2;
                            }
                            elseif($overall>=1 && $overall<1.5)
                            {
                                $overall=1.5;
                            }
                            elseif($overall>=0.5 && $overall<1)
                            {
                                $overall=1;
                            }
                            elseif($overall>=0 && $overall<0.5)
                            {
                                $overall=0.5;
                            }
                            else
                            {
                                $overall=0;
                            }
                            // $salon->overall_rating=$overall;
                            // $salon->overall_rating=round($overall, 2);
                        }
                        else{
                            $overall=0;
                        }

                        // $salon->overall_rating=number_format( (float) $overall, 2, '.', '');
                        $salon->overall_rating = strval($overall);

                        if(isset($salon->image)&&$salon->image!='')
                        {
                            $salon->thumbnail   = env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                            $salon->image       = env("IMAGE_URL")."salons/".$salon->image;
                        }
                        else
                        {
                            $salon->thumbnail   = env("IMAGE_URL")."logo/no-picture.jpg";
                            $salon->image       = env("IMAGE_URL")."logo/no-picture.jpg";
                        }

                        if(isset($salon->logo)&&$salon->logo!='')
                        {
                            $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
                        }
                        else
                        {
                            $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                        }
                    }

                    $result         =   array(
                                            'status'        =>  True,
                                            'msg'           =>  "Salons listed successfully",
                                            'salon_count'   =>  $salon_count,
                                            'salons'        =>  $salon_data
                                        );

                }
                else
                {
                    $result         =   array(
                                        'status'        =>  false,
                                        'salon_count'   =>  $salon_count,
                                        'msg'           =>  "No Salon Found",
                                    );
                }

            }

            // if(isset($keyword) && $keyword != '')
            // {

            //     if($result['salon_count'] == 0){
            //         $result['msg']      = "No Salons Found Under the Name You Searched";
            //     } else {
            //         $result['service']          = $service_list;
            //         $result['service_count']    = count($service_list);
            //     }

            // }
        }
        catch(\Exception $e)
        {
            $result     =   array(
                                'status'=> False,
                                'msg'   => $e->getMessage(),
                                'error' => $e->getMessage()
                            );
        }
        return response()->json($result);
    }

    // ,"Error In Getting Record"


    function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
    {
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
            ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
            $c = !$c;
        }
        return $c;
    }

    function CheckSalontoList($salon_id){

        $salon_to_show      = true;

        // Check Delivery Area Updated
        $delivery_area_updated =    DB::table('salons')->where('id',$salon_id)->whereNull("salons.deleted_at")
                                    ->whereNotNull('delivery_area_coords')->count();
        // dd($delivery_area_updated);
        if($delivery_area_updated == 0){
            $salon_to_show      = false;
        }

        // Check Services Available
        $salon_service_count    =   DB::table('salon_services')->where('salon_id',$salon_id)
                                    ->whereNull("salon_services.deleted_at")->where('approved',1)
                                    ->count();
        if($salon_service_count == 0){
            $salon_to_show      = false;
        }

        // Check Staffs Available
        $salon_staff_count      =   DB::table('salon_staffs')->where('salon_id',$salon_id)
                                    ->whereNull("salon_staffs.deleted_at")->count();
        if($salon_staff_count == 0){
            $salon_to_show      = false;
        }

        // Check Working Time Updated

        $working_hours_time      =   DB::table('working_hours_time')->where('salon_id',$salon_id)
                                    ->whereNull("working_hours_time.deleted_at")->count();
        if($working_hours_time == 0){
            $salon_to_show      = false;
        }

        return $salon_to_show;
    }



    public function test(Request $request)
    {
        $rules=[
            "booking_id"=>"required|exists:booking,id,deleted_at,NULL",
            ];
        $msg=[
            "booking_id.required"=>"ID is required",
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
            $time=Carbon::now();
            $booking_id=$request->booking_id;
            if(request()->header('User-Token'))
            {
                $api_token=request()->header('User-Token');
                // $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
                 $user=UserToken::where("api_token",$api_token)->first();
                if(isset($user)&& isset($user->user_id))
                {
                    $user_id=$user->user_id;
                }
                else
                {
                    $return['error']=true;
                    $return['msg']="API Token expired";
                    return $return;
                }
            }
            else
            {
                $user_id=0;
            }

        $details    =   DB::table("booking")
                        ->join("booking_services", "booking_services.booking_id","=","booking.id")
                        ->join("booking_address", "booking_address.booking_id","=","booking.id")
                        ->join("salons", "salons.id","=","booking.salon_id")
                        ->whereNull('booking.deleted_at')
                        ->whereNull("booking_services.deleted_at")
                        ->whereNull('salons.deleted_at')
                        ->where('booking.user_id',$user_id)
                        ->where('booking.id',$booking_id)
          // ->select("booking.*","salons.name as salon_name","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid")
           ->select("booking.id","salons.name as salon_name","salons.pricing","booking_address.first_name","booking_address.last_name","booking_address.email","booking_address.address","booking_address.phone","salons.id as salon_id","booking.actual_amount as amount_total","booking.amount as amount_paid","salons.email as salon_email")
        ->first();
                $today=Carbon::now()->format("d-m-Y");

         $book_services= DB::table("booking_services")
        ->join("salon_services", "salon_services.id","=","booking_services.service_id")
        ->join("salon_staffs", "salon_staffs.id","=","booking_services.staff_id")
        ->where("booking_services.booking_id",$booking_id)
        ->whereNull('booking_services.deleted_at')
        ->whereNull('salon_services.deleted_at')
        ->whereNull('salon_staffs.deleted_at')
        ->select("booking_services.staff_id","booking_services.date","salon_services.service","salon_services.time","salon_services.amount","booking_services.service_id","salon_staffs.staff","booking_services.start_time","booking_services.end_time")->get();
         if(isset($book_services) && count($book_services)>0)
        {
            foreach($book_services as $ser)
            {
                $ser->start_time=substr($ser->start_time, 0, -3);
                $ser->end_time=substr($ser->end_time, 0, -3);
            }
        }
         $terms_c=Content::where("id",3)->select("id","title","description","created_at")->first();
        $terms=isset($terms_c->description)?$terms_c->description:'';
        $data = ['details' => $details,'today'=>$today,"book_services"=>$book_services,"terms"=>$terms];


        $datas=[
                "name"=>$details->first_name. " " .$details->last_name,
                "email"=>$details->email,
                "address"=>$details->address,
                "country"=>'United Arab Emirates',
                "phone"=>$details->phone,
                "salon_name"=>$details->salon_name,
                'billing_id'=>$booking_id,
                'amount'=>$details->amount_paid,
                'actual_amount'=>$details->amount_total,

                 ];
                 $email=$details->email;
                 $salon_email=$details->salon_email;
             $mail=Mail::send('emails.booking_cancel', ["data"=>$datas], function ($message) use ($data,$email)

              {
                $message->to($email)->subject("Your booking cancelled | Mood");
                });
              $mail2=Mail::send('emails.booking_cancel_salon', ["data"=>$datas], function ($message) use ($data,$salon_email)

              {
                $message->to($salon_email)->subject("Your booking cancelled | Mood");
                });

             $return['error']=false;
            $return['msg']="Your booking cancelled successfully";
            $return['booking_details']=$details;
            return $return;

        }


        $return['error']=false;
        $return['msg']="Your booking cancelled successfully";
        $return['booking_details']=$details;

        return $return;
    }

    public function categories(Request $request)
	{
	    $categories=Categories::where('active_status',1)->whereNull('deleted_at')->get();
        foreach($categories as $category)
        {
            if(isset($category->image)&&$category->image!='')
            {
                $category->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$category->image;
                $category->image= env("IMAGE_URL")."categories/".$category->image;
            }
            else
            {
                $category->image= env("IMAGE_URL")."logo/no-picture.jpg";
                $category->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
            }
        }
		$return['error']=false;
	    $return['msg']="Salon categories listed successfully";
	    $return['categories']=$categories;
	    return $return;
	}

    public function salons(Request $request)
	{
        $sort='';
		if(isset($request->record_start)&&$request->record_start!="")
        {
            $record_start=(int)$request->record_start;
        }
        else
        {
            $record_start=0;
        }

        if(isset($request->record_count)&&$request->record_count!="")
        {
            $record_count=(int)$request->record_count;
        }
        else
        {
            $record_count=10;
        }

        if(isset($request->sort)&& $request->sort!='')
        {
            $sort=$request->sort;
        }

        if(request()->header('User-Token'))
        {
            $api_token=request()->header('User-Token');
            $user=UserToken::where("api_token",$api_token)->first();

            if(isset($user)&& isset($user->user_id))
            {
                $user_id=$user->user_id;
            }
            else
            {
                $return['error']=true;
                $return['msg']="API Token expired";
                return $return;
            }
            $favorites	=DB::table("user_favorites")
                        ->join("user", "user.id","=","user_favorites.user_id")
                        ->join("salons", "salons.id","=","user_favorites.salon_id")
						->where("user_favorites.user_id",$user_id)
                        ->whereNull('user_favorites.deleted_at')
                        ->groupBy("user_favorites.salon_id")
                        ->pluck("user_favorites.salon_id")->toArray();
        }

        if($sort=="offers")
        {
			$salons=DB::table("salons")
                ->join("salon_services", "salon_services.salon_id","=","salons.id")
                ->join("service_offers", "service_offers.service_id","=","salon_services.id")
                ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
                ->join("categories", "categories.id","=","salon_categories.category_id")
                ->whereNull("salons.deleted_at")
                ->whereNull("salon_categories.deleted_at")
                ->whereNull("service_offers.deleted_at")
                ->where("salons.active",1)
                ->where("service_offers.approved",1)
                ->groupBy("salons.id");
        }

        else if($sort=="price")
        {
			$salons=DB::table("salons")
                ->join("salon_services", "salon_services.salon_id","=","salons.id")
                ->join("categories", "categories.id","=","salon_services.category_id")
                ->join("salon_categories", "salon_categories.category_id","=","categories.id")
                ->where('salon_services.approved',1)
                ->whereNull("salons.deleted_at")->where("salons.active",1)
                ->whereNull("salon_categories.deleted_at")
                ->whereNull("salon_services.deleted_at")
                ->groupBy("salons.id");
        }

        else if($sort=="rating")
        {
            $rating=$reviews=$review=$salon_list=[];
            $salon_list=Salons::pluck("id")->toArray();
            if(isset($salon_list)&& count($salon_list)>0)
            {
                foreach($salon_list as $each)
                {
                        $review=SalonReviews::where("salon_id",$each)
                    ->select("salon_reviews.*", DB::raw("avg(rating) as average"))->groupBy("salon_reviews.salon_id")->orderBy("average","desc")->first();
                    if(!empty($review))
                    {
                        $reviews[]=$review;
                    }
                }
            }

            // $rating=$collection->sortByDesc('average');
            if(isset($reviews)&& count($reviews)>0)
            {
                $rating		=	collect($reviews)->sortBy('average')->reverse()->pluck("salon_id")->toArray();
                $ids_ordered= 	implode(',', $rating);
				$salons		=	DB::table("salons")
								->join("salon_reviews", "salon_reviews.salon_id","=","salons.id")
								->join("salon_services", "salon_services.salon_id","=","salons.id")
								->join("categories", "categories.id","=","salon_services.category_id")
								->join("salon_categories", "salon_categories.category_id","=","categories.id")
								->where('salon_services.approved',1)->where("salons.active",1)
								->whereIn("salons.id",$rating)
								->whereNull("salons.deleted_at")
								->whereNull("salon_reviews.deleted_at")
								->whereNull("salon_categories.deleted_at")
								->whereNull("salon_services.deleted_at")
								->orderByRaw("FIELD(salons.id, $ids_ordered)");
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry no salons found";
                $return['salons']=[];
                return $return;
            }
            // return $salons->get();
        }
	    else
        {
            $salons=DB::table("salons")
                    ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
                    ->join("categories", "categories.id","=","salon_categories.category_id")
                    ->whereNull("salons.deleted_at")->where("salons.active",1)
                    ->whereNull("salon_categories.deleted_at")
                    ->groupBy("salons.id");
        }

        if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
             $salons=$salons
                    ->where(function ($q) use ($keyword) {
                    $q->where("salons.name","like",'%'.$keyword.'%')
                    // ->orWhere("salons.email","like",'%'.$keyword.'%')
                    // ->orWhere("salons.description","like",'%'.$keyword.'%')
                    ->orWhere("salons.location","like",'%'.$keyword.'%')
                    ->orWhere("salons.sub_title","like",'%'.$keyword.'%')
                    ->orWhere("salons.longitude","like",'%'.$keyword.'%')
                    ->orWhere("salons.latitude","like",'%'.$keyword.'%');
                    });
        }

        if($request->category_id && $request->category_id != 'null')
        {
            // $category_id=json_decode($request->categories);
            $category_id=$request->category_id;

            // if(count($category_id)>0)
            // {
                $salons->where("salon_categories.category_id",$category_id);
            // }
        }

        $latitude       =   isset($request->latitude)?$request->latitude:0;
        $longitude      =   isset($request->longitude)?$request->longitude:0;
        $min_distance   =   isset($request->min_distance)?$request->min_distance:0;
        $max_distance   =   $request->max_distance;

        if($request->latitude   && $request->longitude)
        {
            if($sort=="distance")
            {
                if(isset($max_distance)&& $max_distance>0)
                {
                      $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                        DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(salons.latitude))
                    * cos(radians(salons.longitude) - radians(" . $longitude . "))
                    + sin(radians(" .$latitude. "))
                    * sin(radians(salons.latitude))) AS distance"))
                    ->having("distance",">=",$min_distance)
                    ->having("distance","<=",$max_distance)
                    ->orderBy("distance")->get();
                }
                else
                {
                     $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                        DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(salons.latitude))
                    * cos(radians(salons.longitude) - radians(" . $longitude . "))
                    + sin(radians(" .$latitude. "))
                    * sin(radians(salons.latitude))) AS distance"))
                    ->orderBy("distance")->get();
                }
            }
            else if($sort=="price")
            {
                $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                        * cos(radians(salons.latitude))
                        * cos(radians(salons.longitude) - radians(" . $longitude . "))
                        + sin(radians(" .$latitude. "))
                        * sin(radians(salons.latitude))) AS distance"))
                        ->orderByRaw('SUM(salon_services.amount) ASC')
                        // ->orderBy("distance")
                    ->get();
            }
            else if($sort=="offers")
            {
                 $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude", DB::raw("(salon_services.amount-service_offers.discount_price)*100/salon_services.amount as offer"),
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(salons.latitude))
                    * cos(radians(salons.longitude) - radians(" . $longitude . "))
                    + sin(radians(" .$latitude. "))
                    * sin(radians(salons.latitude))) AS distance"))
                    ->orderBy("offer","desc")
                    ->groupBy("salons.id")->get();
            }
            else if($sort=="rating")
            {
                 $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(salons.latitude))
                    * cos(radians(salons.longitude) - radians(" . $longitude . "))
                    + sin(radians(" .$latitude. "))
                    * sin(radians(salons.latitude))) AS distance"))
                    ->groupBy("salons.id")->get();
            }
            else
            {
                $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                    * cos(radians(salons.latitude))
                    * cos(radians(salons.longitude) - radians(" . $longitude . "))
                    + sin(radians(" .$latitude. "))
                    * sin(radians(salons.latitude))) AS distance"))
                    ->orderBy("salons.created_at","desc")->get();
            }
        }
        else
        {
            if($sort=="price")
            {
                $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude")
                ->orderByRaw('SUM(salon_services.amount) ASC')
                ->get();
            }
            else if($sort=="offers")
            {
                 $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude", DB::raw("(salon_services.amount-service_offers.discount_price)*100/salon_services.amount as offer"))
                    ->orderBy("offer","desc")
                    ->groupBy("salons.id")->get();
            }
            else if($sort=="rating")
            {

                 $salons	=	$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude")
								->groupBy("salons.id")->get();
            }
            else
            {
                $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude")
                ->orderBy("salons.created_at","desc")->get();
            }

            foreach($salons as $salon)
            {
                $salon->distance=null;
            }
        }
        foreach($salons as $salon)
        {
            if(isset($salon->distance)&& $salon->distance>0)
            {
                $salon->distance=round($salon->distance, 1);
            }

            if(isset($favorites)&& count($favorites)>0)
            {
                if(in_array($salon->id, $favorites))
                {
                    $salon->is_favorite=true;
                }
                else
                {
                    $salon->is_favorite=false;
                }
            }
            else
            {
                $salon->is_favorite=false;
            }

            //get rating
            $reviews        =   DB::table("salon_reviews")
                                ->join("user", "user.id","=","salon_reviews.user_id")
                                ->where("salon_id",$salon->id)
                                ->whereNull("salon_reviews.deleted_at")
                                // ->whereNotNull("reviews")
                                ->select("salon_reviews.*","user.first_name","user.last_name")
                                ->get();

            $rating_count   =   SalonReviews::where("salon_id",$salon->id)
                                ->get()->count();

            $review_count   =   SalonReviews::where("salon_id",$salon->id)
                                ->whereNotNull("reviews")
                                ->get()->count();

            $salon->reviews=$reviews;
            $salon->rating_count=$rating_count;
            $salon->review_count=$review_count;
            if(isset($reviews)&&count($reviews)>0)
            {
                if($review_count > 0)
                {
                    $rating=0;
                    foreach($reviews as $review)
                    {
                        $rating=$rating+$review->rating;
                    }
                    $overall=$rating/$review_count;
                    if($overall>=4.5)
                    {
                        $overall=5;
                    }
                    elseif($overall>=4 && $overall<4.5)
                    {
                        $overall=4.5;
                    }
                    elseif($overall>=3.5 && $overall<4)
                    {
                        $overall=4;
                    }
                    elseif($overall>=3 && $overall<3.5)
                    {
                        $overall=3.5;
                    }
                    elseif($overall>=2.5 && $overall<3)
                    {
                        $overall=3;
                    }
                    elseif($overall>=2 && $overall<2.5)
                    {
                        $overall=2.5;
                    }
                    elseif($overall>=1.5 && $overall<2)
                    {
                        $overall=2;
                    }
                    elseif($overall>=1 && $overall<1.5)
                    {
                        $overall=1.5;
                    }
                    elseif($overall>=0.5 && $overall<1)
                    {
                        $overall=1;
                    }
                    elseif($overall>=0 && $overall<0.5)
                    {
                        $overall=0.5;
                    }
                    else
                    {
                        $overall=0;
                    }
                }
                else
                {
                  $overall = 0;
                }

            }
            else
            {
                // $salon->rating="No ratings yet";
                $overall=0;
            }
            $salon->overall_rating = strval($overall);
            // $salon->overall_rating=number_format( (float) $overall, 2, '.', '');

            //get rating
            $categories=DB::table("salon_categories")
                            ->join("categories", "categories.id","=","salon_categories.category_id")
                            ->whereNull("salon_categories.deleted_at")
                            ->groupBy("categories.id")
                            ->where("salon_categories.salon_id",$salon->id)
                            ->select("categories.id","categories.image","categories.category")
                            ->get();

            if(isset($categories)&& count($categories)>0)
            {
                foreach($categories as $value)
                {
                    if(isset($value->image)&&$value->image!='')
                    {
                      $value->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$value->image;
                        $value->image= env("IMAGE_URL")."categories/".$value->image;
                    }
                    else
                    {
                        $value->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $value->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }
                }
            }

            $salon->categories=$categories;

            if(isset($salon->image)&&$salon->image!='')
            {
                $salon->thumbnail   = env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                $salon->image       = env("IMAGE_URL")."salons/".$salon->image;
            }
            else
            {
                $salon->thumbnail   = env("IMAGE_URL")."logo/no-picture.jpg";
                $salon->image       = env("IMAGE_URL")."logo/no-picture.jpg";
            }

            if(isset($salon->logo)&&$salon->logo!='')
            {
                $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
            }
            else
            {
                $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
            }
        }

        if(isset($salons)&& count($salons)>0)
        {
            $return['error']=false;
            $return['msg']="Salons listed successfully";
            $return['salons']=$salons;
        }
        else
        {
            $return['error']    =   true;
            $return['msg']      =   "Sorry no salons found";
            $return['salons']   =   [];
        }

	    return $return;
	}



    public function salon_detail(Request $request)
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
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
            $id=$request->id;
            $todate=Carbon::now()->format("Y-m-d");

            if(request()->header('User-Token'))
            {
                $api_token=request()->header('User-Token');
                // $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
                 $user=UserToken::where("api_token",$api_token)->first();
				if(isset($user)&& isset($user->user_id))
				{
					$user_id=$user->user_id;
				}
				else
				{
					$return['error']=true;
					$return['msg']="API Token expired";
					return $return;
				}
                $favorites=DB::table("user_favorites")
							->join("user", "user.id","=","user_favorites.user_id")
							->join("salons", "salons.id","=","user_favorites.salon_id")
							->whereNull('user_favorites.deleted_at')
							->where("user_favorites.user_id",$user_id)
							 ->groupBy("user_favorites.salon_id")
							->pluck("user_favorites.salon_id")->toArray();
            }
            $salon=DB::table("salons")
            ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->whereNull("salons.deleted_at")
            ->whereNull("salon_categories.deleted_at")
            ->where("salons.id",$id)
            ->select("salons.id","salons.name","salons.email",'salons.minimum_order_amt',"salons.description","salons.logo","salons.sub_title","salons.location","salons.image","salons.latitude","salons.longitude","salons.min_price","salons.cancellation_policy","salons.reschedule_policy","salons.phone","salons.manager_phone")->first();
            if(isset($salon))
            {
                if(isset($salon->image)&&$salon->image!='')
                {
                  $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                    $salon->image= env("IMAGE_URL")."salons/".$salon->image;
                }
                else
                {
                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }

                if(isset($favorites)&& count($favorites)>0)
                {
                    if(in_array($salon->id, $favorites))
                    {
                        $salon->is_favorite=true;
                    }
                    else
                    {
                        $salon->is_favorite=false;
                    }
                }
                else
                {
                    $salon->is_favorite=false;
                }
                 $images=SalonImages::where("salon_id",$id)->where("approved",1)->select("id","salon_id","image")->get();
                if(isset($images)&&count($images)>0)
                {
                    foreach($images as $each)
                    {
                        $each->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$each->image;
                        $each->image= env("IMAGE_URL")."salons/".$each->image;
                    }
                }
                $salon->images=$images;
                $working_hours=WorkingHours::where("salon_id",$id)->first();
                 $working=[];
                if(isset($working_hours))
                {
                    $sunday["day"]='sunday';
                    $sunday["start_time"]=isset($working_hours->sunday_start)?$working_hours->sunday_start:'';
                    $sunday["end_time"]=isset($working_hours->sunday_end)?$working_hours->sunday_end:'';
                    $working[]=$sunday;

                    $monday["day"]='monday';
                    $monday["start_time"]=isset($working_hours->monday_start)?$working_hours->monday_start:'';
                    $monday["end_time"]=isset($working_hours->monday_end)?$working_hours->monday_end:'';
                    $working[]=$monday;

                    $tuesday["day"]='tuesday';
                    $tuesday["start_time"]=isset($working_hours->tuesday_start)?$working_hours->tuesday_start:'';
                    $tuesday["end_time"]=isset($working_hours->tuesday_end)?$working_hours->tuesday_end:'';
                    $working[]=$tuesday;

                    $wednesday["day"]='wednesday';
                    $wednesday["start_time"]=isset($working_hours->wednesday_start)?$working_hours->wednesday_start:'';
                    $wednesday["end_time"]=isset($working_hours->wednesday_end)?$working_hours->wednesday_end:'';
                    $working[]=$wednesday;

                    $thursday["day"]='thursday';
                    $thursday["start_time"]=isset($working_hours->thursday_start)?$working_hours->thursday_start:'';
                    $thursday["end_time"]=isset($working_hours->thursday_end)?$working_hours->thursday_end:'';
                    $working[]=$thursday;

                    $friday["day"]='friday';
                    $friday["start_time"]=isset($working_hours->friday_start)?$working_hours->friday_start:'';
                    $friday["end_time"]=isset($working_hours->friday_end)?$working_hours->friday_end:'';
                    $working[]=$friday;

                    $saturday["day"]='saturday';
                    $saturday["start_time"]=isset($working_hours->saturday_start)?$working_hours->saturday_start:'';
                    $saturday["end_time"]=isset($working_hours->saturday_end)?$working_hours->saturday_end:'';
                    $working[]=$saturday;
                }
                $salon->working_hours=$working;

                $salon->staffs=SalonStaffs::where('salon_id',$id)->get();
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
                    if($overall>=4.5)
                    {
                        $overall=5;
                    }
                    elseif($overall>=4 && $overall<4.5)
                    {
                        $overall=4.5;
                    }
                    elseif($overall>=3.5 && $overall<4)
                    {
                        $overall=4;
                    }
                    elseif($overall>=3 && $overall<3.5)
                    {
                        $overall=3.5;
                    }
                    elseif($overall>=2.5 && $overall<3)
                    {
                        $overall=3;
                    }
                    elseif($overall>=2 && $overall<2.5)
                    {
                        $overall=2.5;
                    }
                    elseif($overall>=1.5 && $overall<2)
                    {
                        $overall=2;
                    }
                    elseif($overall>=1 && $overall<1.5)
                    {
                        $overall=1.5;
                    }
                    elseif($overall>=0.5 && $overall<1)
                    {
                        $overall=1;
                    }
                    elseif($overall>=0 && $overall<0.5)
                    {
                        $overall=0.5;
                    }
                    else
                    {
                        $overall=0;
                    }
                    // $salon->overall_rating=$overall;
                // $salon->overall_rating=round($overall, 2);

                }
                else
                {
                    $salon->rating="No ratings yet";
                    $overall=0;
                }

                $salon->overall_rating   = strval($overall);
                $categories=DB::table("salon_categories")
                ->join("categories", "categories.id","=","salon_categories.category_id")
                ->whereNull("salon_categories.deleted_at")
                ->groupBy("categories.id")
                ->where("salon_categories.salon_id",$salon->id)
                ->select("categories.id","categories.image","categories.category")
                ->get();
                if(isset($categories)&& count($categories)>0)
                {
                    foreach($categories as $value)
                    {
                        if(isset($value->image)&&$value->image!='')
                        {
                          $value->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$value->image;
                            $value->image= env("IMAGE_URL")."categories/".$value->image;
                        }
                        else
                        {
                          $value->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                            $value->image= env("IMAGE_URL")."logo/no-picture.jpg";
                        }
                    }
                }

                //check offer
                $new_offer=DB::table("offers")
                            ->join("salons", "salons.id","=","offers.salon_id")
                            ->whereNull('offers.deleted_at')
                            ->where('offers.salon_id',$id)
                            ->whereNull('salons.deleted_at')->select("offers.*","salons.id as salon_id","salons.name")
                            ->orderBy('offers.amount', 'asc')
                            ->whereDate("offers.start_date","<=",$todate)
                            ->whereDate("offers.end_date",">=",$todate)
                            ->first();

                $services=DB::table("salon_services")
                            ->join("categories", "categories.id","=","salon_services.category_id")
                            ->join("salon_categories", "salon_categories.category_id","=","categories.id")
                            ->where('salon_services.salon_id',$id)
                            ->where('salon_services.approved',1)
                            ->where('salon_categories.salon_id',$id)
                            ->whereNull('salon_services.deleted_at')
                            ->whereNull('salon_categories.deleted_at')
                            ->groupBy("salon_services.id")
                            ->select("salon_services.*","categories.category")->get();
                if(isset($services)&& count($services)>0)
                {

                    foreach($services as $ser)
                    {
                        $disco=$ser->amount;

                        //checking offer
                        if(isset($new_offer)&& isset($new_offer->amount))
                        {
                            $disco=$ser->amount*$new_offer->amount/100;
                            $disco=$ser->amount-$disco;

                            $new_offer->discount_price=$disco;
                        }
                        else
                        {
                            $disco=$ser->amount;
                        }
                        // return $disco;
                        $discount=ServiceOffers::where("salon_id",$id)->where("service_id",$ser->id)->where("approved",1)
                        ->get();
                        // dd(DB::getQueryog());
                        if(isset($discount)&& count($discount)>0)
                        {
                            $ids=[];
                            foreach($discount as $disc)
                            {
                                $start=new Carbon($disc->start_date);
                                $start_date=$start->format('Y-m-d');
                                $end=new Carbon($disc->end_date);
                                $end_date=$end->format('Y-m-d');

                                if($start_date<=$todate && $end_date>=$todate)
                                {
                                    $ids[]=$disc->id;
                                }
                            }
                            if(isset($ids)&& count($ids)>0)
                            {
                                $offer=ServiceOffers::where("salon_id",$id)->where("service_id",$ser->id)->where("approved",1)->whereIn("id",$ids)->orderBy('discount_price', 'asc')->first();
                                if($offer->discount_price>$disco)
                                {
                                    $ser->offers=$new_offer;

                                }
                                else
                                {
                                $ser->offers=$offer;

                                }

                            }
                            else
                            {
                               if(isset($new_offer)&& isset($new_offer->amount))
                                {
                                    $ser->offers=$new_offer;
                                }
                                else
                                {
                                    $ser->offers=null;

                                }

                            }

                        }
                        else
                        {
                            if(isset($new_offer)&& isset($new_offer->amount))
                            {
                                $ser->offers=$new_offer;
                            }
                            else
                            {
                                $ser->offers=null;

                            }
                        }

                    }

                }

                $salon->services=$services;

                $salon->categories=$categories;

                //salon offers

                // $offers=null;
                // ///get all salon offers

                // $all_offers=Offers::where("active",1)->where("offer_type",1)->pluck("offers.id")->toArray();

                // // get selected salon offers
                // $offer_ids=[];
                // $salon_offers=DB::table("offers")
                //     ->join("offer_salons", "offers.id","=","offer_salons.offer_id")
                //     ->join("salons", "salons.id","=","offer_salons.salon_id")
                //     ->whereNull("offer_salons.deleted_at")
                //     ->whereNull("offers.deleted_at")
                //     ->where("offers.active",1)
                //     ->where("offers.offer_type",2)
                //     ->groupBy("offers.id")
                //     ->pluck("offers.id")->toArray();
                // //get selected salon service offers

                // $service_offers=DB::table("offers")
                //     ->join("offer_services", "offers.id","=","offer_services.offer_id")
                //     ->join("salon_services", "salon_services.id","=","offer_services.service_id")
                //     ->join("categories", "categories.id","=","salon_services.category_id")
                //     ->join("salons", "salons.id","=","offer_services.salon_id")
                //     ->whereNull("offers.deleted_at")
                //     ->whereNull("offer_services.deleted_at")
                //     ->where("offers.active",1)
                //     ->where("offers.offer_type",3)
                //     ->groupBy("offers.id")
                //      ->pluck("offers.id")->toArray();
                //      if(isset($all_offers)&& count($all_offers)>0)
                //     {
                //         foreach($all_offers as $id)
                //         {
                //             $offer_ids[]=$id;
                //         }
                //     }
                //     if(isset($salon_offers)&& count($salon_offers)>0)
                //     {
                //         foreach($salon_offers as $id)
                //         {
                //             $offer_ids[]=$id;
                //         }
                //     }

                //      if(isset($service_offers)&& count($service_offers)>0)
                //     {
                //         foreach($service_offers as $id)
                //         {
                //             $offer_ids[]=$id;
                //         }
                //     }
                // $offers=Offers::where("active",1)->whereIn("id",$offer_ids)->get();

                // if(isset($offers)&& count($offers)>0)
                // {
                //     foreach($offers as $offer)
                //     {
                //         if($offer->amount_type==1)
                //         {
                //             $offer->amount_in="Percentage";
                //         }
                //         else
                //         {
                //             $offer->amount_in="Cash";
                //         }
                //         if($offer->offer_type==1)
                //         {
                //             $offer->offer_valid="For All Salons";
                //         }
                //         elseif($offer->offer_type==2)
                //         {
                //             $offer->offer_valid="For All Services";
                //         }
                //         else
                //         {
                //             $offer->offer_valid="For Selected Services";
                //             $services=DB::table("offer_services")
                //             ->join("salon_services", "salon_services.id","=","offer_services.service_id")
                //             ->join("categories", "categories.id","=","salon_services.category_id")
                //             ->join("salons", "salons.id","=","salon_services.salon_id")
                //             ->whereNull("offer_services.deleted_at")
                //             ->where("offer_services.offer_id",$offer->id)
                //             ->select("offer_services.id","salons.id as salon_id","salons.name","salons.image","salon_services.service","salon_services.time","salon_services.amount","categories.category")
                //             ->get();
                //             if(isset($services)&&count($services)>0)
                //             {
                //                 foreach($services as $service)
                //                 {
                //                     if(isset($service->image)&&$service->image!='')
                //                     {
                //                         $service->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$service->image;
                //                         $service->image= env("IMAGE_URL")."salons/thumbnails/".$service->image;
                //                     }
                //                     else
                //                     {
                //                       $service->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                //                         $service->image= env("IMAGE_URL")."logo/no-picture.jpg";
                //                     }
                //                 }
                //             }
                //             $offer->services=$services;
                //         }
                //         if(isset($offer->image)&&$offer->image!='')
                //         {
                //             $offer->thumbnail= env("IMAGE_URL")."offers/thumbnails/".$offer->image;
                //             $offer->image= env("IMAGE_URL")."offers/thumbnails/".$offer->image;
                //         }
                //         else
                //         {
                //           $offer->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                //             $offer->image= env("IMAGE_URL")."logo/no-picture.jpg";
                //         }
                //     }
                // }
                //     $salon->offers=$offers;

                $return['error']=false;
                $return['msg']="Salon details listed successfully";
                $return['salon']=$salon;
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry no records found";
            }

        }
        return $return;
    }

    public function featured(Request $request)
    {
         if(request()->header('User-Token'))
        {
            $api_token=request()->header('User-Token');
            // $user_id=UserToken::where("api_token",$api_token)->first()->user_id;

             $user=UserToken::where("api_token",$api_token)->first();
            if(isset($user)&& isset($user->id))
            {
                $user_id=$user->id;
            }
            else
            {
                $return['error']=true;
                $return['msg']="API Token expired";
                return $return;
            }
            $favorites=DB::table("user_favorites")
            ->join("user", "user.id","=","user_favorites.user_id")
            ->join("salons", "salons.id","=","user_favorites.salon_id")
                ->where("user_favorites.user_id",$user_id)
            ->whereNull('user_favorites.deleted_at')
              ->groupBy("user_favorites.salon_id")
                ->pluck("user_favorites.salon_id")->toArray();
        }
        $salons=DB::table("salons")
        ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
        ->join("categories", "categories.id","=","salon_categories.category_id")
        ->whereNull("salons.deleted_at")
        ->where("salons.featured",1)
        ->whereNull("salon_categories.deleted_at")
        ->groupBy("salons.id");

        if($request->categories)
        {
            $category_id=json_decode($request->categories);
            $salons->whereIn("salon_categories.category_id",$category_id);
        }
        $latitude=isset($request->latitude)?$request->latitude:'0';
        $longitude=isset($request->longitude)?$request->longitude:'0';
        $min_distance=$request->min_distance;
        $max_distance=$request->max_distance;
        if(isset($min_distance) && isset($max_distance))
        {
              $salons=$salons->select("salons.id","salons.name","salons.email","salons.sub_title","salons.description","salons.image","salons.featured","salons.latitude","salons.location","salons.logo","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->having("distance",">=",$min_distance)
            ->having("distance","<=",$max_distance)
            ->orderBy("distance")->get();

        }
        else
        {
            $salons=$salons->select("salons.id","salons.name","salons.email","salons.sub_title","salons.description","salons.image","salons.featured","salons.latitude","salons.location","salons.logo","salons.longitude",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(salons.latitude))
                * cos(radians(salons.longitude) - radians(" . $longitude . "))
                + sin(radians(" .$latitude. "))
                * sin(radians(salons.latitude))) AS distance"))
                ->orderBy("distance")->get();
        }
        foreach($salons as $salon)
        {
            if(isset($favorites)&& count($favorites)>0)
            {
                if(in_array($salon->id, $favorites))
                {
                    $salon->is_favorite=true;
                }
                else
                {
                    $salon->is_favorite=false;
                }
            }
            else
            {
                $salon->is_favorite=false;
            }
            $categories=DB::table("salon_categories")
            ->join("categories", "categories.id","=","salon_categories.category_id")
            ->whereNull("salon_categories.deleted_at")
            ->groupBy("categories.id")
            ->where("salon_categories.salon_id",$salon->id)
            ->select("categories.id","categories.image","categories.category")
            ->get();
            if(isset($categories)&& count($categories)>0)
            {
                foreach($categories as $value)
                {
                    if(isset($value->image)&&$value->image!='')
                    {
                      $value->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$value->image;
                        $value->image= env("IMAGE_URL")."categories/".$value->image;
                    }
                    else
                    {
                      $value->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                        $value->image= env("IMAGE_URL")."logo/no-picture.jpg";
                    }
                }
            }
            $salon->categories=$categories;

            if(isset($salon->image)&&$salon->image!='')
            {
              $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                $salon->image= env("IMAGE_URL")."salons/".$salon->image;
            }
            else
            {
              $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
            }
              if(isset($salon->logo)&&$salon->logo!='')
            {
                $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
            }
            else
            {
                $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
            }

        }
        $return['error']=false;
        $return['msg']="Featured salons listed successfully";
        $return['salons']=$salons;
        return $return;
    }

    public function mark_favorite(Request $request)
    {
         $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "favorite"=>"required",
            ];
        $msg=[
            "salon_id.required"=>"Please choose a salon",
            "favorite.required"=>"Select a salon as favorite",

             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
        }
        else
        {
            $favorite=$request->favorite;
            $salon_id=$request->salon_id;
            $time=Carbon::now();

            $api_token=request()->header('User-Token');
            $user_id=UserToken::where("api_token",$api_token)->first()->user_id;
            $favorites=UserFavorites::where("user_id",$user_id)->where("salon_id",$salon_id)
                ->first();

            if(isset($favorites))
            {
                if($favorite==1)
                {
                    $return['error']=false;
                    $return['msg']="You already added this salon as your favorite";
                }
                else
                {
                    $remove=UserFavorites::where("user_id",$user_id)->where("salon_id",$salon_id)->delete();
                    $return['error']=false;
                    $return['msg']="Successfully removed from your favorites.";
                }
            }
            else
            {
                if($favorite==1)
                {
                    $add=UserFavorites::insertGetId(["user_id"=>$user_id,"salon_id"=>$salon_id,"created_at"=>$time,"updated_at"=>$time]);

                    $return['error']=false;
                    $return['msg']="Successfully marked as your favorite";
                }
                else
                {
                    $return['error']=false;
                    $return['msg']="You already removed this salon from your favorites";
                }
            }
        }
        return $return;
    }

    public function favorites(Request $request)
    {
        $latitude   =   isset($request->latitude)?$request->latitude:'0';
        $longitude  =   isset($request->longitude)?$request->longitude:'0';
        $api_token  =   request()->header('User-Token');
        $user_id    =   UserToken::where("api_token",$api_token)->first()->user_id;
        $favorites  =   DB::table("user_favorites")
                        ->join("user", "user.id","=","user_favorites.user_id")
                        ->join("salons", "salons.id","=","user_favorites.salon_id")
                        ->where("user_favorites.user_id",$user_id)
                        ->whereNull('user_favorites.deleted_at')
                        ->select("user_favorites.id","user_favorites.user_id","user_favorites.salon_id","user_favorites.created_at","salons.id as salon_id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                            DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                        * cos(radians(salons.latitude))
                        * cos(radians(salons.longitude) - radians(" . $longitude . "))
                        + sin(radians(" .$latitude. "))
                        * sin(radians(salons.latitude))) AS distance"))
                        ->get();


        if(isset($favorites)&&count($favorites)>0)
        {
            foreach($favorites as $each)
            {
                 if(isset($each->image)&&$each->image!='')
                {
                    $each->thumbnail    =   env("IMAGE_URL")."salons/thumbnails/".$each->image;
                    $each->image        =   env("IMAGE_URL")."salons/".$each->image;
                }
                else
                {
                    $each->thumbnail    =   env("IMAGE_URL")."logo/no-picture.jpg";
                    $each->image        =   env("IMAGE_URL")."logo/no-picture.jpg";
                }

                if(isset($each->logo)&&$each->logo!='')
                {
                    $each->logo= env("IMAGE_URL")."salons/".$each->logo;
                }
                else
                {
                    $each->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                }
                $salon_id   = $each->salon_id;
                $categories=DB::table("salon_categories")
                            ->join("categories", "categories.id","=","salon_categories.category_id")
                            ->whereNull("salon_categories.deleted_at")
                            ->groupBy("categories.id")
                            ->where("salon_categories.salon_id",$salon_id)
                            ->select("categories.id","categories.image","categories.category")
                            ->get();

                if(isset($categories)&& count($categories)>0)
                {
                    foreach($categories as $value)
                    {
                        if(isset($value->image)&&$value->image!='')
                        {
                          $value->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$value->image;
                            $value->image= env("IMAGE_URL")."categories/".$value->image;
                        }
                        else
                        {
                          $value->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                            $value->image= env("IMAGE_URL")."logo/no-picture.jpg";
                        }
                    }
                }
                $each->categories=$categories;


                $reviews=DB::table("salon_reviews")
                ->join("user", "user.id","=","salon_reviews.user_id")
                ->where("salon_id",$salon_id)
                ->whereNull("salon_reviews.deleted_at")
                ->whereNotNull("reviews")
                ->select("salon_reviews.*","user.first_name","user.last_name")->get();
                $rating_count=SalonReviews::where("salon_id",$salon_id)->get()->count();
                $review_count=SalonReviews::where("salon_id",$salon_id)->whereNotNull("reviews")->get()->count();
                $each->reviews=$reviews;
                $each->rating_count=$rating_count;
                $each->review_count=$review_count;
                if(isset($reviews)&&count($reviews)>0)
                {
                    $rating=0;
                    foreach($reviews as $review)
                    {
                        $rating=$rating+$review->rating;
                    }
                    $overall=$rating/$review_count;
                    if($overall>=4.5)
                    {
                        $overall=5;
                    }
                    elseif($overall>=4 && $overall<4.5)
                    {
                        $overall=4.5;
                    }
                    elseif($overall>=3.5 && $overall<4)
                    {
                        $overall=4;
                    }
                    elseif($overall>=3 && $overall<3.5)
                    {
                        $overall=3.5;
                    }
                    elseif($overall>=2.5 && $overall<3)
                    {
                        $overall=3;
                    }
                    elseif($overall>=2 && $overall<2.5)
                    {
                        $overall=2.5;
                    }
                    elseif($overall>=1.5 && $overall<2)
                    {
                        $overall=2;
                    }
                    elseif($overall>=1 && $overall<1.5)
                    {
                        $overall=1.5;
                    }
                    elseif($overall>=0.5 && $overall<1)
                    {
                        $overall=1;
                    }
                    elseif($overall>=0 && $overall<0.5)
                    {
                        $overall=0.5;
                    }
                    else
                    {
                        $overall=0;
                    }
                    // $salon->overall_rating=$overall;
                // $salon->overall_rating=round($overall, 2);

                }
                else
                {
                    $each->rating="No ratings yet";
                    $overall=0;
                }

                $each->overall_rating   = strval($overall);
            }
            $return['error']=false;
            $return['msg']="Your favorite salons listed successfully";
            $return['favorites']=$favorites;
        }
        else
        {
            $return['error']=true;
            $return['msg']="No favorites yet";
            $return['favorites']=[];

        }
        return $return;
    }

    public function salon_services(Request $request)
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
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
            $id=$request->id;
            $todate=Carbon::now()->format("Y-m-d");

            $new_offer=DB::table("offers")
                        ->join("salons", "salons.id","=","offers.salon_id")
                        ->whereNull('offers.deleted_at')
                        ->whereNull('salons.deleted_at')->select("offers.*","salons.id as salon_id","salons.name")
                        ->orderBy('offers.amount', 'asc')
                        ->where('offers.salon_id',$id)
                        ->whereDate("offers.start_date","<=",$todate)
                        ->whereDate("offers.end_date",">=",$todate)
                        ->first();
            if(isset($request->keyword)&&$request->keyword!="")
            {
                $keyword=$request->keyword;
                $services=DB::table("salon_categories")
                            ->join("categories", "categories.id","=","salon_categories.category_id")
                            ->join("salon_services", "salon_services.category_id","=","categories.id")
                            ->join("salon_staffs", "salon_staffs.salon_id","=","salon_services.salon_id")
                            ->join("staff_services", "staff_services.service_id","=","salon_services.id")
                            ->where(function ($q) use ($keyword) {
                                    $q->where("categories.category","like",'%'.$keyword.'%')
                                    ->orWhere("salon_services.service","like",'%'.$keyword.'%')
                                    ->orWhere("salon_services.amount","like",'%'.$keyword.'%');
                            })
                            ->whereNull('salon_categories.deleted_at')
                            ->where("salon_categories.salon_id",$id)
                            ->where('salon_services.approved',1)
                            ->where("salon_services.salon_id",$id)
                            ->groupBy("salon_categories.id")
                            ->select("categories.id as id","categories.category","categories.image")->get();

            }
            else
            {
                $services=DB::table("salon_categories")
                ->join("categories", "categories.id","=","salon_categories.category_id")
                ->join("salon_services", "salon_services.category_id","=","categories.id")
                ->join("salon_staffs", "salon_staffs.salon_id","=","salon_services.salon_id")
                ->join("staff_services", "staff_services.service_id","=","salon_services.id")
                ->whereNull('salon_categories.deleted_at')
                ->where('salon_services.approved',1)
                ->whereNull('salon_services.deleted_at')
                ->groupBy("salon_categories.id")
                ->where("salon_categories.salon_id",$id)
                ->where("salon_services.salon_id",$id)
                ->select("categories.id as id","categories.category","categories.image")->get();
            }

            if(isset($services)&&count($services)>0)
            {
                foreach($services as $each)
                {
                    $each->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$each->image;
                    $each->image= env("IMAGE_URL")."categories/thumbnails/".$each->image;

                    // if(isset($request->keyword)&&$request->keyword!="")
                    // {
                    //     $keyword=$request->keyword;
                    //     $each->services=DB::table("salon_services")
                    //     ->join("categories", "categories.id","=","salon_services.category_id")
                    //     ->where(function ($q) use ($keyword) {
                    //     $q->where("categories.category","like",'%'.$keyword.'%')
                    //     ->orWhere("salon_services.service","like",'%'.$keyword.'%')
                    //     ->orWhere("salon_services.amount","like",'%'.$keyword.'%');
                    //     })
                    //     ->whereNull('salon_services.deleted_at')
                    //     ->where("salon_services.category_id",$each->id)
                    //     ->where('salon_services.salon_id',$id)
                    //     ->groupBy("salon_services.id")
                    //     ->select("salon_services.*","categories.category","categories.image")->get();
                    // }
                    // else
                    // {
                        $cat_services=DB::table("salon_services")
                        ->join("categories", "categories.id","=","salon_services.category_id")
                        ->join("salon_staffs", "salon_staffs.salon_id","=","salon_services.salon_id")
                        ->join("staff_services", "staff_services.service_id","=","salon_services.id")
                        ->where('salon_services.salon_id',$id)
                        ->whereNull('salon_services.deleted_at')
                        ->where("salon_services.category_id",$each->id)
                        ->where('salon_services.approved',1)
                        ->groupBy("salon_services.id")
                        ->select("salon_services.*","categories.category","categories.image")->get();

                        if(isset($cat_services)&& count($cat_services)>0)
                        {

                            foreach($cat_services as $ser)
                            {
                                $disco=$ser->amount;
                                //checking offer
                                if(isset($new_offer)&& isset($new_offer->amount))
                                {
                                    $disco=$ser->amount*$new_offer->amount/100;
                                    $disco=$ser->amount-$disco;

                                    $new_offer->discount_price=$disco;
                                }
                                else
                                {
                                    $disco=$ser->amount;
                                }

                                $discount=ServiceOffers::where("salon_id",$id)->where("approved",1)->where("service_id",$ser->id)
                                ->get();
                                // dd(DB::getQueryog());
                                if(isset($discount)&& count($discount)>0)
                                {
                                    $ids=[];
                                    foreach($discount as $disc)
                                    {
                                        $start=new Carbon($disc->start_date);
                                        $start_date=$start->format('Y-m-d');
                                        $end=new Carbon($disc->end_date);
                                        $end_date=$end->format('Y-m-d');

                                        if($start_date<=$todate && $end_date>=$todate)
                                        {
                                            $ids[]=$disc->id;
                                        }
                                    }
                                    if(isset($ids)&& count($ids)>0)
                                    {
                                        $offer=ServiceOffers::where("salon_id",$id)->where("service_id",$ser->id)->where("approved",1)->whereIn("id",$ids)->orderBy('discount_price', 'asc')->first();
                                       if($offer->discount_price>$disco)
                                        {
                                            $ser->offers=$new_offer;

                                        }
                                        else
                                        {
                                        $ser->offers=$offer;

                                        }

                                    }
                                    else
                                    {
                                         if(isset($new_offer)&& isset($new_offer->amount))
                                        {
                                            $ser->offers=$new_offer;
                                        }
                                        else
                                        {
                                            $ser->offers=null;

                                        }

                                    }

                                }
                                else
                                {
                                     if(isset($new_offer)&& isset($new_offer->amount))
                                        {
                                            $ser->offers=$new_offer;
                                        }
                                        else
                                        {
                                            $ser->offers=null;

                                        }
                                }

                            }

                        }

                    $each->services=$cat_services;
                    // }
                }

            }

            // if(isset($request->keyword)&&$request->keyword!="")
            // {
            //     $keyword=$request->keyword;
            //     $services=DB::table("salon_services")
            //     ->join("categories", "categories.id","=","salon_services.category_id")
            //     ->where(function ($q) use ($keyword) {
            //     $q->where("categories.category","like",'%'.$keyword.'%')
            //     ->orWhere("salon_services.service","like",'%'.$keyword.'%')
            //     ->orWhere("salon_services.amount","like",'%'.$keyword.'%');
            //     })
            //     ->whereNull('salon_services.deleted_at')
            //     ->where('salon_services.salon_id',$id)
            //     ->groupBy("salon_services.id")
            //     ->select("salon_services.*","categories.category","categories.image")->get();
            // }

            // else
            // {
            //     $keyword="";
            //      $services=DB::table("salon_services")
            //     ->join("categories", "categories.id","=","salon_services.category_id")
            //     ->where('salon_services.salon_id',$id)
            //     ->whereNull('salon_services.deleted_at')
            //     ->groupBy("salon_services.id")
            //     ->select("salon_services.*","categories.category","categories.image")->get();
            // }

            // if(isset($services)&&count($services)>0)
            // {
            //     foreach($services as $each)
            //     {
            //         $each->thumbnail= env("IMAGE_URL")."categories/thumbnails/".$each->image;
            //         $each->image= env("IMAGE_URL")."categories/thumbnails/".$each->image;
            //     }
            // }
            if(isset($services))
            {
                $return['error']=false;
                $return['msg']="Salon services listed successfully";
                $return['services']=$services;
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry no records found";
            }
        }

        return $return;
    }

    public function salon_reviews(Request $request)
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
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
            $id=$request->id;
            $reviews=DB::table("salon_reviews")
                ->join("user", "user.id","=","salon_reviews.user_id")
                ->where("salon_id",$id)
                ->whereNull("salon_reviews.deleted_at")
                ->whereNotNull("reviews")
                ->select("salon_reviews.*","user.first_name","user.last_name")->get();
            $rating_count=SalonReviews::where("salon_id",$id)->get()->count();
            $review_count=SalonReviews::where("salon_id",$id)->whereNotNull("reviews")->get()->count();

            if(isset($reviews)&&count($reviews)>0)
            {
                $rating=0;
                $overall=0;
                foreach($reviews as $review)
                {
                    $rating=$rating+$review->rating;
                }
                $rating_count=$rating_count;
                $review_count=$review_count;
                $overall=$rating/$review_count;
                $reviews->overall_rating=round($overall, 2);

                // $overall_rating=$overall;
                $return['error']=false;
                $return['msg']="Salon reviews listed successfully";
                $return['rating_count']=$rating_count;
                $return['review_count']=$review_count;
                $return['overall_rating']=$overall;
                $return['reviews']=$reviews;
            }
            else
            {
                $return['error']=true;
                $return['msg']="No ratings yet";
            }

        }

        return $return;
    }

    public function salon_staffs(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salons,id,deleted_at,NULL",
            "service_id"=>"exists:salon_services,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required",
             ];
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            $return['error']=true;
            $return['msg']= implode( ", ",$validator->errors()->all());
            return $return;
        }
        else
        {
            $id=$request->id;
            if($request->service_id)
            {
                $service_id=$request->service_id;
                $staffs=DB::table("salon_staffs")
                ->join("staff_services", "staff_services.staff_id","=","salon_staffs.id")
                ->where("salon_staffs.salon_id",$id)
                ->where("staff_services.service_id",$service_id);
            }
            else
            {
                 $staffs=DB::table("salon_staffs")
                ->where("salon_staffs.salon_id",$id);
            }
            if(isset($request->keyword)&&$request->keyword!="")
            {
                $keyword=$request->keyword;
                 $staffs=$staffs
                ->where(function ($q) use ($keyword) {
                $q->where("staff","like",'%'.$keyword.'%')
                ->orWhere("description","like",'%'.$keyword.'%');
                })->groupBy("salon_staffs.id")->select("salon_staffs.*")->get();
            }

            else
            {
                 $staffs=$staffs->groupBy("salon_staffs.id")->select("salon_staffs.*")->get();
            }

            if(isset($staffs))
            {
                $return['error']=false;
                $return['msg']="Salon staffs listed successfully";
                $return['staffs']=$staffs;
            }
            else
            {
                $return['error']=true;
                $return['msg']="Sorry no records found";
            }
        }

        return $return;
    }

    public function salon_test(Request $request)
    {
        $latitude=isset($request->latitude)?$request->latitude:'0';
        $longitude=isset($request->longitude)?$request->longitude:'0';
        $min_distance=$request->min_distance;
        $max_distance=$request->max_distance;
        // discount price=actual price * discount percentage /100
        // discount percentage = ((actual price - discount price)*100)/ actual price
        $salons=DB::table("salons")
        ->join("salon_services", "salon_services.salon_id","=","salons.id")
        ->join("service_offers", "service_offers.service_id","=","salon_services.id")
        ->join("salon_categories", "salon_categories.salon_id","=","salons.id")
        ->join("categories", "categories.id","=","salon_categories.category_id")
        ->whereNull("service_offers.deleted_at")
        ->whereNull("salons.deleted_at")
        ->whereNull("salon_categories.deleted_at");

        $salons=$salons->select("salons.id","service_offers.service_id","service_offers.discount_price","salon_services.amount","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude", DB::raw("(salon_services.amount-service_offers.discount_price)*100/salon_services.amount as offer"))
            ->orderBy("offer","desc")
            ->groupBy("salons.id")->get();
            return $salons;
          if(isset($min_distance) && isset($max_distance))
        {
              $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"), DB::raw("service_offers.discount_price*100/salon_services.amount as offer"))
            ->having("distance",">=",$min_distance)
            ->having("distance","<=",$max_distance)
            ->max("offer")
            ->orderBy("distance")->get();

        }
        else if($latitude!=0 && $longitude!= 0)
        {
             $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->orderBy("distance")->get();
        }
        else
        {
            $salons=$salons->select("salons.id","salons.name","salons.email","salons.description","salons.location","salons.sub_title","salons.logo","salons.image","salons.latitude","salons.longitude",
                DB::raw("6371 * acos(cos(radians(" . $latitude . "))
            * cos(radians(salons.latitude))
            * cos(radians(salons.longitude) - radians(" . $longitude . "))
            + sin(radians(" .$latitude. "))
            * sin(radians(salons.latitude))) AS distance"))
            ->orderBy("salons.created_at","desc")->get();
        }

        if(isset($salons)&&count($salons)>0)
        {
            foreach($salons as $salon)
            {
                if(isset($salon->image)&&$salon->image!='')
                {
                    $salon->thumbnail= env("IMAGE_URL")."salons/thumbnails/".$salon->image;
                    $salon->image= env("IMAGE_URL")."salons/".$salon->image;
                }
                else
                {
                  $salon->thumbnail= env("IMAGE_URL")."logo/no-picture.jpg";
                    $salon->image= env("IMAGE_URL")."logo/no-picture.jpg";
                }
                  if(isset($salon->logo)&&$salon->logo!='')
                {
                    $salon->logo= env("IMAGE_URL")."salons/".$salon->logo;
                }
                else
                {
                    $salon->logo= env("IMAGE_URL")."logo/no-picture.jpg";
                }

                $return['error']=false;
                $return['msg']="Offer salons listed successfully";
                $return['salons']=$salons;
            }
        }
        else
        {
            $return['error']=true;
            $return['msg']="Sorry no records found";
        }

        return $return;
    }

}
