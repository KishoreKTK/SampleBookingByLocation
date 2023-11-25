<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Salons;
use App\Currency;
use App\Approvals;
use Carbon\Carbon;
use App\Categories;
use App\SalonsToken;
use App\SalonServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonServicesController extends Controller
{
    public function index(Request $request)
    {
        $activePage="Services";
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
        $services=DB::table("salon_services")
        ->join("categories", "categories.id","=","salon_services.category_id")
        ->where('salon_services.salon_id',$salon_id)
        ->whereNull('salon_services.deleted_at')
        ->groupBy("salon_services.id")
        ->select("salon_services.*","categories.category")->get();
        return view("salon.services.list", compact("salon_id","services",'activePage'));

    }
     public function add(Request $request)
    {
        $activePage="Services";
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
        $categories=Categories::pluck("category","id");
        $time=[15,30,45,60,75,90,105,120,135,150,165,180,195,210,225,240];
    	return view("salon.services.add", compact("salon_id","categories",'activePage','time'));
    }
     public function add_service(Request $request)
    {
        $activePage="Services";
      $rules=[
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
            $service=$request->service;
            $amount=$request->amount;
            $time=$request->time;
            $category_id=$request->category;
            $now=Carbon::now();
           
            $new_service=SalonServices::insertGetId(['service'=>$service,'salon_id'=>$salon_id,"approved"=>0,"pending"=>0,'amount'=>$amount,'time'=>$time,'category_id'=>$category_id, 'created_at'=> $now,"updated_at"=>$now]);

            if($new_service)
            {
                $admin=Salons::where("id",$salon_id)->first()->name;

                $log="Added the salon service.";
                $action="Added";

                $new_approve=DB::table("approvals")->insertGetId(["salon_id"=>$salon_id,"type_id"=>2,'service_id'=>$new_service,"title"=>$log,"action"=>$action, 'created_at'=> $now,"updated_at"=>$now]);

                // if($new_approve)
                // {
                //     $add_log=DB::table("salon_services_log")->insertGetId(["audit_id"=>$new_approve,'service_id'=>$new_service,'service'=>$service,'salon_id'=>$salon_id,'amount'=>$amount,'time'=>$time,'category_id'=>$category_id, 'created_at'=> $now,"updated_at"=>$now]);
                // }

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
        $activePage="Services";
    	$rules=[
            "id"=>"required|exists:salon_services,id,deleted_at,NULL",
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
          
            $id=$request->id;
            $time=[15,30,45,60,75,90,105,120,135,150,165,180,195,210,225,240];
            $service=SalonServices::where("id",$id)->first();
            $categories=Categories::pluck("category","id");
            $approved=isset($service->pending)?$service->pending:1;

        	return view("salon.services.edit", compact("salon_id","approved","categories",'id','service','activePage','time'));
        }
    }
    public function edit_service(Request $request)
    {
      $rules=[
            "id"=>"required|exists:salon_services,id,deleted_at,NULL",
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
           
            $service=$request->service;
            $amount=$request->amount;
            $time=$request->time;
            $category_id=$request->category;
            $now=Carbon::now();
           
            // $new_service=SalonServices::where("id",$id)->where("salon_id",$salon_id)->update(['service'=>$service,'amount'=>$amount,'time'=>$time,'category_id'=>$category_id,"updated_at"=>$now]);
            $new_service=SalonServices::where("id",$id)->where("salon_id",$salon_id)->update(["pending"=>0,"updated_at"=>$now]);

            if($new_service)
            {
                 $admin=Salons::where("id",$salon_id)->first()->name;

                $log="Updated the salon service.";
                $check_approval=Approvals::where("salon_id",$salon_id)->where("service_id",$id)->where("type_id",2)->first();
                    $action="Updated";

                if(isset($check_approval)&& !empty($check_approval))
                {
                    $audit_id=$check_approval->id;

                    $new_approve=Approvals::where("salon_id",$salon_id)->where("service_id",$id)->where("type_id",2)->update(["action"=>$action,"title"=>$log,"updated_at"=>$now]);

                }
                else
                {
                    // $action="Added";

                    $new_approve=Approvals::insertGetId(["salon_id"=>$salon_id,"type_id"=>2,'service_id'=>$id,"action"=>$action,"title"=>$log, 'created_at'=> $now,"updated_at"=>$now]);
                    $audit_id=$new_approve;

                }

                if($new_approve)
                {
                    $add_log=DB::table("salon_services_log")->insertGetId(["audit_id"=>$audit_id,'service_id'=>$id,'service'=>$service,'salon_id'=>$salon_id,'amount'=>$amount,'time'=>$time,'category_id'=>$category_id, 'created_at'=> $now,"updated_at"=>$now]);
                }

                return redirect(env("ADMIN_URL").'/salon/services')->with("error", false)->with("msg", "Your service updated successfully")->withInput();
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
    public function categories(Request $request)
    {
        $activePage="Categories";
        $categories=Categories::paginate(20);
         foreach($categories as $category)
        {
            if(isset($category->image)&&$category->image!='')
            {
                $category->image= env("IMAGE_URL")."categories/".$category->image;
            }
            else
            {
                $category->image= env("IMAGE_URL")."logo/no-picture.jpg";
            }
            if(isset($category->icon)&&$category->icon!='')
            {
                $category->icon= env("IMAGE_URL")."categories/".$category->icon;
            }
            else
            {
                $category->icon= env("IMAGE_URL")."logo/no-picture.jpg";
            }
           
        }
        return view('salon.categories.list',compact('activePage','categories'));
    }
}
