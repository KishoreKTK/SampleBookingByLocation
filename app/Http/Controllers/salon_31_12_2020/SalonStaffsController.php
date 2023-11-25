<?php

namespace App\Http\Controllers\salon;
use DB;
use Auth;
use Session;
use Validator;
use App\Salons;
use App\Currency;
use Carbon\Carbon;
use App\Categories;
use App\SalonsToken;
use App\SalonStaffs;
use App\StaffServices;
use App\SalonServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonStaffsController extends Controller
{
    public function index(Request $request)
    {
        $activePage="Staffs";
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

        if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
            $staffs=SalonStaffs::where(function ($q) use ($keyword) {
            $q->where("staff","like",'%'.$keyword.'%')
            ->orWhere("description","LIKE",'%'.$keyword.'%');
            })->where('salon_id',$salon_id)
            ->get();

        }
        else
        {
            $keyword='';
            $staffs=SalonStaffs::where('salon_id',$salon_id)->get();

        }
        return view("salon.staffs.list", compact("salon_id","staffs",'activePage','keyword'));

    }
     public function add(Request $request)
    {
        $activePage="Staffs";
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
        $services=SalonServices::where("salon_id",$salon_id)->get();
        if(count($services)==0)
        {
            $service=0;
        }
        else
        {
            $service=1;
        }

    	return view("salon.staffs.add", compact("salon_id",'services','service','activePage'));
    }
     public function add_staff(Request $request)
    {
      $rules=[
            "staff"=>"required",
            "services"=>"required",
            ];
        $msg=[
            "staff.required"=>"staff is required",
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
            $staff=$request->staff;
            $description=$request->description;
            $time=Carbon::now();
           
            $new_staff=SalonStaffs::insertGetId(['staff'=>$staff,'salon_id'=>$salon_id,'description'=>$description, 'created_at'=> $time,"updated_at"=>$time]);

            if($new_staff)
            {
                 if(isset($request->services))
                {
                    foreach($request->services as $service)
                    {
                        $insert=StaffServices::insert([
                            "staff_id"=>$new_staff,
                            "service_id"=>$service,
                            'created_at'=> $time,
                            "updated_at"=>$time
                            ]);
                    }
                }
                return redirect()->back()->with("error", false)->with("msg", "New staff added successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
            }
        }
    }
    public function edit(Request $request)
    {
        $activePage="Staffs";
    	$rules=[
            "id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
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
            $c_services=[];
            $services=SalonServices::where("salon_id",$salon_id)->get();
            foreach (StaffServices::where('staff_id', '=',$id)->get() as $each)
            {
                $c_services[]=$each->service_id;
            }
            $staff=SalonStaffs::where("id",$id)->first();
        	return view("salon.staffs.edit", compact("salon_id",'id','staff','services','c_services','activePage'));
        }
    }
    public function edit_staff(Request $request)
    {
      $rules=[
            "id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "staff"=>"required",
            ];
        $msg=[
            "staff.required"=>"staff is required",
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
            $staff=$request->staff;
            $description=$request->description;
            $time=Carbon::now();
           
            $new_staff=SalonStaffs::where("id",$id)->where("salon_id",$salon_id)->update(['staff'=>$staff,'description'=>$description,"updated_at"=>$time]);

            if($new_staff)
            {
                 foreach (StaffServices::where('staff_id', '=',$id)->get() as $service)
                {
                    $delete_categories=StaffServices::where("id",$service->id)->delete();
                }
                  if(isset($request->services))
                {
                    foreach($request->services as $service)
                    {
                        $insert=StaffServices::insert([
                            "staff_id"=>$id,
                            "service_id"=>$service,
                            'created_at'=> $time,
                            "updated_at"=>$time
                            ]);
                    }
                }
                return redirect(env("ADMIN_URL").'/salon/staffs')->with("error", false)->with("msg", "Your staff updated successfully")->withInput();
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
            "id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
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
          $delete=SalonStaffs::where("id",$id)->where("salon_id",$salon_id)->delete();
          if($delete)
          {
            return redirect()->back()->with("error", false)->with("msg", "Staff deleted successfully");
          }
          else
          {
            return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
          }
        
        }

    }
}
