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
                    <h5>Content Edit</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/content" >Content</a>
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
            <h5>Content Edit</h5>
        </div>
        <div class="card-block">
                     @include('includes.msg')
            
            <!-- <form id="main" method="post" action="/" novalidate> -->
                {!! Form::open(["url"=>env('ADMIN_URL')."/content/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        {!! Form::text("title",$content->title,["placeholder"=>"Title","id"=>"name","class"=>"form-control",'required' => 'required']) !!}

                        <span class="messages"></span>
                    </div>
                </div>
                    @if($id==1)
                <div class="form-group row">
                <label  class="col-sm-2 col-control-label">Phone</label>
                <div class="col-sm-10">
                {!! Form::text("phone",$content->phone,["class"=>"form-control", "placeholder"=>"Phone"]) !!}
                </div>
                </div>
                <div class="form-group row">
                <label  class="col-sm-2 col-control-label">Email</label>
                <div class="col-sm-10">
                {!! Form::text("email",$content->email,["class"=>"form-control", "placeholder"=>"Email"]) !!}
                </div>
                </div>
                <div class="form-group row">
                <label  class="col-sm-2 col-control-label">Website</label>
                <div class="col-sm-10">
                {!! Form::text("website",$content->website,["class"=>"form-control", "placeholder"=>"Website"]) !!}
                </div>
                </div>
                <div class="form-group row">
                <label  class="col-sm-2 col-control-label">Address</label>
                <div class="col-sm-10">
                {!! Form::textarea("address",$content->address,["class"=>"form-control", "placeholder"=>"Address"]) !!}
                </div>
                </div>
                @endif
               
               <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        {!! Form::textarea("description",$content->description,["placeholder"=>"Description","id"=>"summernote","class"=>"form-control",'required' => 'required']) !!}

                        <span class="messages"></span>
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
    <script type="text/javascript">
    $(document).ready(function() {
      $('#summernote').summernote({
         tabsize: 2,
         
            height: 200
      });
    });

</script>
@stop