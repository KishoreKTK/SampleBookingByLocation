<?php

namespace App\Http\Controllers;
use DB;
use App\FAQ;
use Validator;
use Carbon\Carbon;
use App\FaqCategories;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    public function index(Request $request)
	{
    	$activePage="FAQ";
    	if(isset($request->keyword)&&$request->keyword!="")
        {
            $keyword=$request->keyword;
	    	$faq=DB::table("faq")->join("faq_category","faq_category.id","=","faq.category_id")
              	->whereNull('faq.deleted_at')
            	->where("title","like",'%'.$keyword.'%')
            	->orWhere("description","like",'%'.$keyword.'%')
            	->orWhere("faq_category.category","like",'%'.$keyword.'%')
            	->select("faq.*","faq_category.category as category")
	    		->paginate(20);
        }
        else
        {
        	$keyword="";
	    	$faq=DB::table("faq")->join("faq_category","faq_category.id","=","faq.category_id")
              ->whereNull('faq.deleted_at')
	            ->select("faq.*","faq_category.category as category")
		    	->paginate(20);

        }
	    return view('admin.faq.list',compact('activePage','faq','keyword'));
	}
	
    public function add(Request $request)
   {
   		$categories=DB::table("faq_category")->whereNull('faq_category.deleted_at')->pluck("category","id");
    	return view('admin.faq.add',compact('categories'));
   }
   public function add_faq(Request $request)
   {
     $rules=
        [
        	"category_id"=>"required",
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
            $category_id=$request->category_id;
            $title=$request->title;
            $description=$request->description;
            $time=Carbon::now();

            $add=FAQ::insertGetId(["category_id"=>$category_id,"title"=>$title,"description"=>$description,"created_at"=>$time]);

            if($add)
            {
              return redirect()->back()->with("error", false)->with("msg", "New faq added successfully");
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
            "id"=>"required|exists:faq,id,deleted_at,NULL",
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
          $activePage="FAQ";
   		  $categories=DB::table('faq_category')->whereNull('faq_category.deleted_at')->pluck("category","id");
          $faq=FAQ::where("id",$id)->first();
          return view("admin.faq.edit", compact('faq','id','categories','activePage'));
        }
   }
   public function update(Request $request)
   {
    $rules=
        [
            "id"=>"required|exists:faq,id,deleted_at,NULL",
            "title"=>"required",
            "category_id"=>"required",
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
            $category_id=$request->category_id;
            $description=$request->description;
            $time=Carbon::now();

            $update=FAQ::where("id",$id)->update(["title"=>$title,"category_id"=>$category_id,"description"=>$description,"updated_at"=>$time]);

            if($update)
            {
              return redirect(env("ADMIN_URL").'/faq')->with("error", false)->with("msg", "Faq updated successfully");
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
            "id"=>"required|exists:faq,id,deleted_at,NULL",
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
          $delete=FAQ::where("id",$id)->delete();
          if($delete)
          {
              return redirect()->back()->with("error", false)->with("msg", "Faq deleted successfully");
          }
          else
          {
              return redirect()->back()->with("error", true)->with("msg", "Error occured during process")->withInput();
          }
        }
   }

   public function add_category(Request $request)
   {
    $categories=FaqCategories::get();
    return view('admin.faq.faq_categories',compact('categories'));
   }

   public function add_category_post(Request $request)
   {
    $rules=[
        "category"=>"required|unique:faq_category,category",
        ];
    $msg=[
        "category.required"=>"Category field is required",
         ];
          $validator=Validator::make($request->all(), $rules, $msg);

    if($validator->fails())
    {
         return redirect()->back()->with("error", true)->with("msgs", $validator->errors()->all())->withInput();
    }
    else 
    {
      $category=$request->category;
      $time=Carbon::now();
      $new_category=FaqCategories::insertGetId(['category'=>$category,'created_at'=> $time,"updated_at"=>$time]);
      if($new_category)
      {
          return redirect()->back()->with("error", false)->with("msg", "New category added successfully");
      }
      else
      {
          return redirect()->back()->with("error", true)->with("msg", "Sorry error occured");
      }
    }
   }
}
