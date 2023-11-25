<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use App\Salons;
use App\Currency;
use Carbon\Carbon;
use App\Categories;
use App\SalonsToken;
use App\SalonServices;
use Illuminate\Http\Request;

class AdminSalonServicesController extends Controller
{
    public function add(Request $request)
    {
        $activePage="Salons";
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
            $salon_id=$request->id;
            $time=[15,30,45,60,75,90,105,120,135,150,165,180,195,210,225,240];
            // $categories1 =   Categories::pluck("category","id");
            // print_r($categories);die;
            $cat_data =   DB::table("salon_categories")
                            ->join("categories", "categories.id","=","salon_categories.category_id")
                            ->where('salon_categories.salon_id',$salon_id)
                            ->whereNull('salon_categories.deleted_at')
                            ->groupBy("salon_categories.id")
                            ->select("categories.id","categories.category")
                            ->get();
            $categories = [];
            foreach($cat_data as $category){
                $categories[$category->id]   =   $category->category;
            }
            // dd($categories);
        	return view("admin.services.add", compact("salon_id","categories",'activePage','time'));
        }
    }

    public function add_service(Request $request)
    {
        $activePage="Salons";
        $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "service"=>"required",
            "time"=>"required",
            'category'=>'required',
            "amount"=>"required|integer",
            ];
        $msg=[
            "service.required"=>"Service is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $salon_id=$request->salon_id;
            $service=$request->service;
            $amount=$request->amount;
            $time=$request->time;
            $category_id=$request->category;
            $now=Carbon::now();

            $new_service=SalonServices::insertGetId(['service'=>$service,'salon_id'=>$salon_id,'amount'=>$amount,"approved"=>1,'pending'=>1,'time'=>$time,'category_id'=>$category_id, 'created_at'=> $now,"updated_at"=>$now]);

            if($new_service)
            {
                // $log="Added the salon service.";
                // $action="Added";
                // DB::table("approvals")->insertGetId(["salon_id"=>$salon_id,"type_id"=>2,'service_id'=>$new_service,"title"=>$log,'read'=>1,"action"=>$action, 'created_at'=> $now,"updated_at"=>$now,'deleted_at'=>$now]);

                return redirect()->back()->with("error", false)->with("msg", "New service added successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    }

    public function edit(Request $request)
    {
        $activePage="Salons";
    	$rules=[
            "id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $salon_id=$request->salon_id;
            $time=[15,30,45,60,75,90,105,120,135,150,165,180,195,210,225,240];
            $id=$request->id;
            $service=SalonServices::where("id",$id)->first();
            $categories=Categories::where('active_status',1)->whereNull('deleted_at')->pluck("category","id");
        	return view("admin.services.edit", compact("salon_id","categories",'id','service','activePage','time'));
        }
    }

    public function edit_service(Request $request)
    {
      $rules=[
            "id"=>"required|exists:salon_services,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
            "service"=>"required",
            "time"=>"required",
            'category'=>'required',
            "amount"=>"required|integer",
            ];
        $msg=[
            "service.required"=>"Service is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $id=$request->id;
            $salon_id=$request->salon_id;
            $service=$request->service;
            $amount=$request->amount;
            $time=$request->time;
            $category_id=$request->category;
            $now=Carbon::now();

            $new_service=SalonServices::where("id",$id)->where("salon_id",$salon_id)->update(['service'=>$service,'amount'=>$amount,'time'=>$time,'category_id'=>$category_id,"updated_at"=>$now]);

            if($new_service)
            {
                return redirect()->back()->with("error", false)->with("msg", "Your service updated successfully")->withInput();
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    }

    public function delete(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:salon_services,id,deleted_at,NULL",
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
          $delete=SalonServices::where("id",$id)->delete();
          if($delete)
          {
            return redirect()->back()->with("error", false)->with("msg", "Service deleted successfully");
          }
          else
          {
            return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
          }

        }

    }


}
