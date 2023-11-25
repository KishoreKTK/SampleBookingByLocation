<?php

namespace App\Http\Controllers;
use DB;
use App\FAQ;
use Validator;
use App\Content;
use Carbon\Carbon;
use App\Countries;
use App\FaqCategory;
use Illuminate\Http\Request;
use Str;

class AdminContentController extends Controller
{
    public function index(Request $request)
	{
    	$activePage="Content";
    	if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
	    	$content=Content::where("title","like",'%'.$keyword.'%')
            	->orWhere("description","like",'%'.$keyword.'%')
	    		->paginate(20);
        }
        else
        {
        	$keyword="";
	    	$content=Content::whereNull('content.deleted_at')
	    	->paginate(20);

        }
         if(isset($content)&& count($content)>0)
        {
           foreach($content as $blog)
          {
               if(isset($blog->description)&& $blog->description!='')
               {
                  $blog->description=Str::limit($blog->description, 300);
               }
          }
        }
	    return view('admin.content.list',compact('activePage','content','keyword'));
	}
    
	public function view(Request $request)
    {
       $rules=
        [
            "id"=>"required|exists:content,id,deleted_at,NULL",
        ];

        $msg=
        [
            "id.required"=>"Id field is empty",
        ];
    	$activePage="content";

        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
          $id=$request->id;
          $content=Content::where("id",$id)->first();
            return view('admin.content.view',compact('content','activePage'));
        }
    }
    public function add(Request $request)
    {
    	return view('admin.content.add');
    }

    public function add_content(Request $request)
    {
        $rules=
            [
                "title"=>"required",
                "description"=>"required",
            ];

        $msg=
            [
                "title.required"=>"Title field is empty",
                "description.required"=>"Description field is empty",
            ];
        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $title=$request->title;
            $description=$request->description;
            $time=Carbon::now();

            $add=Content::insertGetId(["title"=>$title,"description"=>$description,"created_at"=>$time]);

            if($add)
            {
              return redirect()->back()->with("error", false)->with("msg", "New content added successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
            }

        }
    }

    public function edit(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:content,id,deleted_at,NULL",
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
          $activePage="Content";
          $content=Content::where("id",$id)->first();
          return view("admin.content.edit", compact('content','id','activePage'));
        }
    }

    public function update(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:content,id,deleted_at,NULL",
            "title"=>"required",
            "description"=>"required",
        ];

        $msg=
        [
            "title.required"=>"Title field is empty",
            "description.required"=>"Description field is empty",
        ];

        $validator=Validator::make($request->all(),$rules,$msg);
        if ($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
            $id=$request->id;
            $title=$request->title;
            $address=isset($request->address)?$request->address:'';
            $website=isset($request->website)?$request->website:'';
            $phone=isset($request->phone)?$request->phone:'';
            $email=isset($request->email)?$request->email:'';
            $description=$request->description;
            $time=Carbon::now();

            $update=Content::where("id",$id)->update(["title"=>$title,"address"=>$address,"website"=>$website,"phone"=>$phone,"email"=>$email,"description"=>$description,"updated_at"=>$time]);

            if($update)
            {
              return redirect(env("ADMIN_URL").'/content')->with("error", false)->with("msg", "Content updated successfully");
            }
            else
            {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
            }

        }
    }

    public function delete(Request $request)
    {
        $rules=
        [
            "id"=>"required|exists:content,id,deleted_at,NULL",
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
          $delete=Content::where("id",$id)->delete();
          if($delete)
          {
              return redirect()->back()->with("error", false)->with("msg", "Content deleted successfully");
          }
          else
          {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
          }
        }
    }
}
