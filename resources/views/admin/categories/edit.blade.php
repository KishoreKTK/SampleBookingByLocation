@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('content')

<div class="pcoded-content">
                        <!-- Page-header start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5>Categories Edit</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/categories" >Categories</a>
                    </li>
                     <li class="breadcrumb-item"><a>Edit</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
    <!-- Page-header end -->
<div class="pcoded-inner-content">
<div class="main-body">
<div class="page-wrapper">
    <!-- Page body start -->
    <div class="page-body">
    <div class="row">
    <div class="col-sm-12">
        <!-- Product list card start -->
    <div class="card">
        <div class="card-header">
            <h5>Categories Edit</h5>
        </div>
        <div class="card-block">
                     @include('includes.msg')
            
            <!-- <form id="main" method="post" action="/" novalidate> -->
                {!! Form::open(["url"=>env('ADMIN_URL')."/categories/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category</label>
                    <div class="col-sm-10">
                        <!-- <input type="text" class="form-control" name="name" id="name" placeholder="Text Input Validation"> -->
                        {!! Form::text("category",$category->category,["placeholder"=>"Category","id"=>"name","class"=>"form-control",'required' => 'required']) !!}

                        <span class="messages"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2"><label class="">Choose an image</label></div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="" aria-describedby="">

                                <label class="custom-file-label" for="">Choose
                                    file</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Category Color Code</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="cat_title_clr_code" value="{{ $category->cat_text_clr }}" placeholder="Title Color Code">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="cat_bg_code" value="{{ $category->img_bg_clr }}" placeholder="Background Color Code">
                    </div>
                </div>
                
               
                <div class="form-group row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-10">
                        <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                        {!! Form::hidden('id',$id) !!}

                         {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
                         
                    </div>
                </div>
                 {!! Form::close() !!}
        </div>

  
    </div>
        <!-- Product list card end -->
    </div>
    </div>

        <!-- Add Contact Ends Model end-->
    </div>
    <!-- Page body end -->
</div>
</div>
</div>
</div>

@stop