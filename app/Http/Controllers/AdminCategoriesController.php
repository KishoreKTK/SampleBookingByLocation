<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use Carbon\Carbon;
use App\Categories;
use Illuminate\Http\Request;
use File;
use Intervention\Image\ImageManagerStatic as Image;
class AdminCategoriesController extends Controller
{
    public function index(Request $request)
    {
    	$activePage="Categories";
        // $testimage = env("IMAGE_URL")."logo/no-picture.jpg";
        // echo "<img src=".$testimage." alt='test image'>";
        // echo $_SERVER['DOCUMENT_ROOT'];
        // echo "<br>";
        // echo public_path('/');
        // die;
	    $categories=Categories::paginate(20);
        foreach($categories as $category)
        {
            if(isset($category->image)&&$category->image!='')
            {
                $cat_img = env("IMAGE_URL")."categories/".$category->image;
                $category->image = $cat_img;
            }
            else
            {
                $category->image= env("IMAGE_URL")."logo/no-picture.jpg";
            }

            if(isset($category->icon)&&$category->icon!='')
            {
                $cat_icon= env("IMAGE_URL")."categories/".$category->icon;

                if(File::exists($cat_icon)){
                    $category->image = $cat_icon;
                }
                else{
                    $category->image = env("IMAGE_URL")."logo/no-picture.jpg";
                }
            }
            else
            {
                $category->icon= env("IMAGE_URL")."logo/no-picture.jpg";
            }
        }
	    return view('admin.categories.list',compact('activePage','categories'));
    }

    public function add(Request $request)
    {
    	$rules=[
            "category"=>"required|unique:categories,category",
            ];
        $msg=[
            "category.required"=>"category field is required",
             ];
              $validator=Validator::make($request->all(), $rules, $msg);

        if($validator->fails())
        {
             return redirect()->back()->with("error", true)->with("msg", $validator->errors()->all())->withInput();
        }
        else
        {
        	$category           =   $request->category;
            $cat_bg_code        =   ($request->has('cat_bg_code')) ? $request->input('cat_bg_code') : "";
            $cat_title_clr_code =   ($request->has('cat_title_clr_code')) ? $request->input('cat_title_clr_code') : "";
            $image              =   $request->image;
            $imageName=$iconName="";

            if ($image)
            {
                $imageName  =   md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();

                $thmb_imgurl    = public_path('img/categories/thumbnails/'.$imageName);
                $imgurl         = public_path('img/categories/');

                $resize=Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $resize->save($thmb_imgurl);
                $request->image->move($imgurl, $imageName);

            }

            $time=Carbon::now();
            $new_category=Categories::insertGetId([
                                        'category'  =>  $category,
                                        'image'     =>  $imageName,
                                        'img_bg_clr'    =>$cat_bg_code,
                                        'cat_text_clr'  =>$cat_title_clr_code,
                                        'active_status' => '1',
                                        'created_at'=>  $time,
                                        "updated_at"=>  $time
                                    ]);
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

    public function edit(Request $request)
    {
        $rules=[
            "id"=>"required|exists:categories,id,deleted_at,NULL",
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
            $id         =   $request->id;
            $activePage =   "Categories";
            $category   =   Categories::where("id",$id)->first();
        }
        return view('admin.categories.edit',compact('activePage','category','id'));
    }

    public function update(Request $request)
    {
        $rules=[
            "id"=>"required|exists:categories,id,deleted_at,NULL",
            "category"=>"required",
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
            $id         =   $request->id;
            $category   =   $request->category;
            $activePage =   "Categories";
            $categories =   Categories::where("id",$id)->first();
            $imageName  =   isset($categories->image)?$categories->image:'';
            $image      =   $request->image;
            $imageName  =   "";
            $cat_bg_code        =   ($request->has('cat_bg_code')) ? $request->input('cat_bg_code') : "";
            $cat_title_clr_code =   ($request->has('cat_title_clr_code')) ? $request->input('cat_title_clr_code') : "";
            $url        =   public_path('img/categories/thumbnails/'.$imageName);
            $turl       =   public_path('img/categories/');

            // $url        =   $_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/categories/";
            // $turl       =   $_SERVER['DOCUMENT_ROOT']."/".env('UPLOAD_URL')."/categories/thumbnails/";

            if ($image)
            {
                $imageName  =   md5(rand(1000,9078787878)).'.'.$image->getClientOriginalExtension();
                $desti      =   $url.$imageName;
                $t_desti    =   $turl.$imageName;
                $resize     =   Image::make($image->getRealPath())->resize(300, null,
                                function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                $resize->save($t_desti);
                $saveImage  =   $image->move($url, $imageName);
            }
            else
            {
                $imageName  =   isset($categories->image)?$categories->image:'';
            }

            $time           =   Carbon::now();
            $new_sport      =   Categories::where("id",$id)
                                            ->update([
                                                'category'=>$category,
                                                'image'=>$imageName,
                                                'img_bg_clr'=>$cat_bg_code,
                                                'cat_text_clr'=>$cat_title_clr_code,
                                                "updated_at"=>$time
                                            ]);
            if($new_sport)
            {
                return redirect(env("ADMIN_URL").'/categories')->with("error", false)->with("msg", "Category updated successfully");
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
            "id"=>"required|exists:categories,id,deleted_at,NULL",
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
          $delete=Categories::where("id",$id)->delete();
          if($delete)
          {
            return redirect()->back()->with("error", false)->with("msg", "Category deleted successfully");
          }
          else
          {
            return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
          }

        }
    }

    public function StatusUpdate(Request $request){
        $rules=
        [
            "id"=>"required|exists:categories,id,deleted_at,NULL",
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
            $update=Categories::where("id",$id)->update(['active_status'=>request()->active_status,'updated_at'=>date('Y-m-d H:i:s')]);
            if($update)
            {
                return redirect()->back()->with("error", false)->with("msg", "Category Updated successfully");
            }
            else
            {
                return redirect()->back()->with("error", true)->with("msg", "Error occured during process");
            }
        }
    }
}
