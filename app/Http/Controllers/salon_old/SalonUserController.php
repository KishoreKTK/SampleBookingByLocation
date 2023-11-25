<?php

namespace App\Http\Controllers\salon;
use DB;
use Mail;
use Auth;
use Hash;
use Session;
use App\Salons;
use Validator;
use App\SRoles;
use Carbon\Carbon;
use App\SalonUsers;
use App\SalonRoles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalonUserController extends Controller
{
    public function index(Request $request)
    {
       $activePage="SalonUsers";
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
            $users=SalonUsers::where("name","like",'%'.$keyword.'%')
            ->orWhere("email","LIKE",'%'.$keyword.'%')->where("salon_id",$salon_id)
            ->paginate(50);
        }
        else
        {
           $users=SalonUsers::where("salon_id",$salon_id)->paginate(50);
           $keyword="";
            
        }
         return view('salon.salonusers.list',compact('users','activePage','keyword')); 
    }
    public function add(Request $request)
    {
        $activePage="SalonUsers";

        $roles=SRoles::get();
        return view('salon.salonusers.add',compact('roles', 'activePage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add_user(Request $request)
    {
        $rules=[
            "name"=>"required",
            "email"=>"required|email|unique:salons,email",
            "password"=>"required|min:4",
            "roles"=>"required",
            ];
        $msg=[
            "name.alpha_spaces"=>"Name should be alphabet.",
             ];

             
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {

             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();

        }
        else 
        {
            $time=Carbon::now();
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

            $email=$request->email;
            $token=md5(rand(10000,1000000).$email);

            $add=SalonUsers::insertGetId([
            
             "name"=>$request->name,
             "email"=>$request->email,
             "password"=> Hash::make($request->password),
             "active"=>1,
             "salon_id"=>$salon_id,
             "suspend"=>0,
             "created_at"=>$time
             ]);
            if($add)
            {

                if($request->roles)
                {
                    foreach($request->roles as $value)
                    {
                        $insert=SalonRoles::insert([
                            "salon_id"=>$add,
                            "role_id"=>$value,
                            ]);
                    }

                    if($insert)
                     {
                         return redirect()->back()->with("error", false)->with("msg", "Successfully added new user");

                     }
                     else
                     {
                        return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();

                     }


                }
                else
                {
                    return redirect()->back()->with("error", false)->with("msg", "Successfully added new user");

                }
            }
            else
            {
               return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();

            }

            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Salons  $salons
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {
        
         $activePage="SalonUsers";


        $rules=[
            "id"=>"required|integer|exists:salon_users,id,deleted_at,NULL",
            ];
        $msg=[
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

            $result=SalonUsers::where('id',$request->id)->where("salon_id",$salon_id)->first();
            return view('salon.salonusers.details',compact('result', 'activePage'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Salons  $salons
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
       $rules=[
            "id"=>"required|exists:salon_users,id",
            ];
        $msg=[
            "id.exists"=>"Invalid Salon ID"
             ];

             
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {

             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();

        }
        else 
        {
        $activePage="SalonUsers";
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
        $user=SalonUsers::where("id",$request->id)->where("salon_id",$salon_id)->first();
        $password='';
        $result=SRoles::get();
        $per=SalonRoles::where("salon_id",$request->id)->get();
        $roles=[];
        foreach ($per as $value)
        {
          $roles[]=$value->role_id;
        }
        return view('salon.salonusers.edit',compact('result', 'activePage','user','password','roles','id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salons  $salons
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salon_users,id,deleted_at,NULL",
            "name"=>"required",
            "email"=>"required|email",
            ];
        $msg=[
            "id.exists"=>"Invalid Salons ID",
            "name.alpha_spaces"=>"Name should be alphabet.",

             ];

             
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {

             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();

        }
        else 
        {
            $time=Carbon::now();
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

            $email=$request->email;
            $token=md5(rand(10000,1000000).$email);
                // echo $value;
            if($request->password)
            {
                $salons=SalonUsers::where("id",$request->id)->where("salon_id",$salon_id)->update(["name"=>$request->name, "email"=>$request->email,"password"=> Hash::make($request->password),"active"=>1,"updated_at"=>$time]);
            }
            else
            {
                 $salons=SalonUsers::where("id",$request->id)->where("salon_id",$salon_id)->update(["name"=>$request->name, "email"=>$request->email,"active"=>1,"updated_at"=>$time]);
            }
           
            if($salons)
            {
                 $delete=SalonRoles::where('salon_id',$request->id)->delete();
                if($request->roles)
                {
                    foreach($request->roles as $value)
                    {
                        $insert=SalonRoles::insert([
                            "salon_id"=>$request->id,
                            "role_id"=>$value,
                            ]);
                    }

                    if($insert)
                     {
                         return redirect(env("ADMIN_URL").'/salon/salon_users')->with("error", false)->with("msg", "Successfully updated");

                     }
                     else
                     {
                        return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();

                     }


                }
                else
                {
                    return redirect()->back()->with("error", false)->with("msg", "Successfully updated");

                }
            }
            else
            {
               return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();

            }


            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salons  $salons
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salon_users,id,deleted_at,NULL",
            ];
        $msg=[
            "id.exists"=>"Invalid Salons ID"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            // $data=["status" => "failed", "message"=>$validator->errors()->all()];
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

            $salons=SalonUsers::where("id",$request->id)->where("salon_id",$salon_id)->delete();
            // return $product;
            if($salons)
            {
                $per=SalonRoles::where('salon_id',$request->id)->get();
                if(count($per)>0)
                {
                    $delete=SalonRoles::where('salon_id',$request->id)->delete();
                    if ($delete) 
                    {
                     return redirect()->back()->with("error", false)->with("msg", "Successfully deleted");
                    }
                    else
                    {
                        return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
                    }
                }
                else
                {
                     return redirect()->back()->with("error", false)->with("msg", "Successfully deleted");

                }
            }
          
        }
    }
        public function notsuspend(Request $request)
    {
        
        $rules=[
            "id"=>"required|exists:salon_users,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required",
             ];

             
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            // $data=["status" => "failed", "message"=>$validator->errors()->all()];
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

            $time=Carbon::now();
            $update=SalonUsers::where("id",$id)->where("salon_id",$salon_id)->update(["suspend"=>0,"updated_at"=>$time]);
           

             if($update)
             {
                 return redirect()->back()->with("error", false)->with("msg", "Account removed from suspended list.");

             }
             else
             {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();

             }


        }
    }
    public function suspend(Request $request)
    {
        $rules=[
            "id"=>"required|exists:salon_users,id,deleted_at,NULL",
            ];
        $msg=[
            "id.required"=>"ID is required",
             ];

             
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            // $data=["status" => "failed", "message"=>$validator->errors()->all()];
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
            
            $time=Carbon::now();
            $update=SalonUsers::where("id",$id)->where("salon_id",$salon_id)->update(["suspend"=>1,"updated_at"=>$time]);
           

             if($update)
             {
                 return redirect()->back()->with("error", false)->with("msg", "User account suspended");

             }
             else
             {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();

             }
        }
    }
}
