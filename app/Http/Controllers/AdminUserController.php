<?php

namespace App\Http\Controllers;
use DB;
use Mail;
use Auth;
use Hash;
use Session;
use App\Admin;
use Validator;
use App\Roles;
use Carbon\Carbon;
use App\AdminRoles;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request,Admin $admin)
    {
        $activePage="AdminUsers";
        if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
            $users=$admin   ->where("name","like",'%'.$keyword.'%')
                            ->orWhere("email","LIKE",'%'.$keyword.'%')
                            ->paginate(50);
        }
        else
        {
           $users   =   Admin::paginate(50);
           $keyword =   "";
        }
        // dd($users);
        return view('admin.adminusers.list',compact('users','activePage','keyword'));
    }


    public function add(Request $request,Admin $admin)
    {
        $activePage =   "AdminUsers";
        $roles      =   Roles::get();

        return view('admin.adminusers.add',compact('roles', 'activePage'));
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
            "name"      =>  "required",
            "email"     =>  "required|email|unique:admin,email",
            "password"  =>  "required|min:4",
            "roles"     =>  "required",
        ];

        $msg=[
            "name.alpha_spaces"=>"Name should be alphabet.",
         ];

         // $data = $request->all();
         // print_r($data);die;
        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {

             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();

        }
        else
        {
            $time   =   Carbon::now();
            $email  =   $request->email;
            $token  =   md5(rand(10000,1000000).$email);

            $add    =   Admin::insertGetId([
                                                "name"=>$request->name,
                                                "email"=>$request->email,
                                                "password"=> Hash::make($request->password),
                                                "master_admin"=>0,
                                                "active"=>1,
                                                "suspend"=>0,
                                                "api_token"=>$token,
                                                "created_at"=>$time
                                            ]);
            if($add)
            {

                if($request->roles)
                {
                    foreach($request->roles as $value)
                    {
                        $insert=AdminRoles::insert([
                            "admin_id"=>$add,
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
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request)
    {

         $activePage="AdminUsers";


        $rules=[
            "id"=>"required|integer|exists:admin,id,deleted_at,NULL",
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
            $result=Admin::where('id',$request->id)->first();
            return view('admin.adminusers.details',compact('result', 'activePage'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
       $rules=[
            "id"=>"required|exists:admin,id",
            ];
        $msg=[
            "id.exists"=>"Invalid admin ID"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {

             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();

        }
        else
        {
            $activePage="AdminUsers";
            $id=$request->id;
            $admin=Admin::where("id",$request->id)->first();
            $password='';
            $result=Roles::get();
            $per=AdminRoles::where("admin_id",$request->id)->get();
            $roles=[];
            foreach ($per as $value)
            {
                $roles[]=$value->role_id;
            }
            return view('admin.adminusers.edit',compact('result', 'activePage','admin','password','roles','id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules=[
            "id"=>"required|exists:admin,id,deleted_at,NULL",
            "name"=>"required",
            "email"=>"required|email",
            ];
        $msg=[
            "id.exists"=>"Invalid admin ID",
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
            $email=$request->email;
            $token=md5(rand(10000,1000000).$email);
                // echo $value;
            if($request->password)
            {
                $admin=Admin::where("id",$request->id)->update(["name"=>$request->name, "email"=>$request->email,"password"=> Hash::make($request->password),"master_admin"=>0,"active"=>1,"updated_at"=>$time,"api_token"=>$token]);
            }
            else
            {
                 $admin=Admin::where("id",$request->id)->update(["name"=>$request->name, "email"=>$request->email,"master_admin"=>0,"active"=>1,"updated_at"=>$time,"api_token"=>$token]);
            }

            if($admin)
            {
                 $delete=AdminRoles::where('admin_id',$request->id)->delete();
                if($request->roles)
                {
                    foreach($request->roles as $value)
                    {
                        $insert=AdminRoles::insert([
                            "admin_id"=>$request->id,
                            "role_id"=>$value,
                            ]);
                    }

                    if($insert)
                     {
                         return redirect(env("ADMIN_URL").'/admin_users')->with("error", false)->with("msg", "Successfully updated");

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
     * @param  \App\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $rules=[
            "id"=>"required|exists:admin,id,deleted_at,NULL",
            ];
        $msg=[
            "id.exists"=>"Invalid admin ID"
             ];

        $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
            // $data=["status" => "failed", "message"=>$validator->errors()->all()];
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();


        }
        else
        {
            $admin=Admin::where("id",$request->id)->delete();
            // return $product;
            if($admin)
            {
                $per=AdminRoles::where('admin_id',$request->id)->get();
                if(count($per)>0)
                {
                    $delete=AdminRoles::where('admin_id',$request->id)->delete();
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
            "id"=>"required|exists:admin,id,deleted_at,NULL",
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
            $time=Carbon::now();
            $update=Admin::where("id",$id)->update(["suspend"=>0,"updated_at"=>$time]);
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
            "id"=>"required|exists:admin,id,deleted_at,NULL",
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
            $time=Carbon::now();
            $update=Admin::where("id",$id)->update(["suspend"=>1,"updated_at"=>$time]);


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
