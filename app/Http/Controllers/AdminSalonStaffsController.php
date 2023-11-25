<?php

namespace App\Http\Controllers;
use DB;
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

class AdminSalonStaffsController extends Controller
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
            $services=SalonServices::where("salon_id",$salon_id)->get();
            if(count($services)==0)
            {
                $service=0;
            }
            else
            {
                $service=1;
            }

        	return view("admin.staffs.add", compact("salon_id",'services','service','activePage'));
        }
    }
     public function add_staff(Request $request)
    {
      $rules=[
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $salon_id=$request->salon_id;
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
        $activePage="Salons";
    	$rules=[
            "id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
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
            $id=$request->id;
            $c_services=[];
            $services=SalonServices::where("salon_id",$salon_id)->get();
            foreach (StaffServices::where('staff_id', '=',$id)->get() as $each)
            {
                $c_services[]=$each->service_id;
            }
            $staff=SalonStaffs::where("id",$id)->first();
        	return view("admin.staffs.edit", compact("salon_id",'id','staff','services','c_services','activePage'));
        }
    }
    public function edit_staff(Request $request)
    {
      $rules=[
            "id"=>"required|exists:salon_staffs,id,deleted_at,NULL",
            "salon_id"=>"required|exists:salons,id,deleted_at,NULL",
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
            $salon_id=$request->salon_id;
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
                return redirect()->back()->with("error", false)->with("msg", "Your staff updated successfully")->withInput();
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
          $delete=SalonStaffs::where("id",$id)->delete();
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
