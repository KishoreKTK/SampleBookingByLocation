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
                        <h5>Categories List</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Categories</a>
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
                                    <h5>Categories List</h5>
                                </div>
                                <div class="card-block">
                                    @include('includes.msg')
                                    <!-- <form id="main" method="post" action="/" novalidate> -->
                                    {!! Form::open(["url"=>env('ADMIN_URL')."/categories/add","id"=>"main","method"=>"post",'files'=>
                                    true]) !!}
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Category</label>
                                        <div class="col-sm-10">
                                            <!-- <input type="text" class="form-control" name="name" id="name" placeholder="Text Input Validation"> -->
                                            {!!
                                            Form::text("category",'',["placeholder"=>"Category","id"=>"name","class"=>"form-control",'required'
                                            => 'required']) !!}
                                            <span class="messages"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Upload Image</label>
                                        <div class="col-sm-10">
                                            <div class="">
                                                <div class="custom-file">
                                                    <input type="file" name="image" class="custom-file-input"
                                                        id="inputGroupFile02">
                                                    <label class="custom-file-label" for="inputGroupFile02"
                                                        aria-describedby="inputGroupFileAddon02">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Category Color Code</label>
                                        <div class="col-sm-3">
                                            <input type="color" name="cat_title_clr_code" value="#cc9c83" placeholder="Title Color Code">
                                            {{-- <input type="text" class="form-control" name="cat_title_clr_code" placeholder="Title Color Code"> --}}
                                        </div>
                                        <label class="col-sm-3 col-form-label">Background Color Code</label>
                                        <div class="col-sm-3">
                                            <input type="color" name="cat_bg_code" value="#cc9c83" placeholder="Background Color Code">
                                            {{-- <input type="text" class="form-control" name="cat_bg_code" placeholder="Background Color Code"> --}}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-10">
                                            <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                                            {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>

                                <div class="card-block">

                                    <div class="table-responsive">
                                        <div class="table-content">
                                            <div class="project-table">
                                                <table class="table table-sm table-hover">
                                                    @if(isset($categories)&& count($categories)>0)
                                                    <thead>
                                                        <tr>
                                                            <th width="100">Image</th>
                                                            <th>Category</th>
                                                            <th>Date</th>
                                                            <th></th>
                                                            <th width="100">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($categories as $category)
                                                        <tr>
                                                            <td class="pro-list-img">
                                                                <img src="{{$category->image}}" class="img-fluid"
                                                                    alt="tbl">
                                                                <!-- <img src="{{env('ADMIN_URL')}}/public/assets/files/assets/images/product-list/pro-l2.png" class="img-fluid" alt="tbl"> -->
                                                            </td>
                                                            <td class="pro-name">
                                                                <h6>{{$category->category}}</h6>
                                                            </td>
                                                            <td>{{$category->created_at->format('d-m-Y g:i A')}}</td>
                                                            <td>

                                                            </td>
                                                            <td class="action-icon">
                                                                <a href="{{env('ADMIN_URL')}}/categories/edit?id={{$category->id}}"
                                                                    class="m-r-15 text-muted" data-toggle="tooltip"
                                                                    data-placement="top" title=""
                                                                    data-original-title="Edit"><i
                                                                        class="icofont icofont-ui-edit"></i></a>

                                                                @if($category->active_status==1)
                                                                     <a href="{{env('ADMIN_URL')}}/categories/statusupdate?id={{$category->id}}&&active_status=0"
                                                                    class="mr-2 text-muted" data-toggle="tooltip" data-placement="top" title=""
                                                                    data-original-title="Hide"><i class="fas fa-eye"></i></a>
                                                                @else
                                                                     <a href="{{env('ADMIN_URL')}}/categories/statusupdate?id={{$category->id}}&&active_status=1"
                                                                    class="mr-2 text-muted" data-toggle="tooltip" data-placement="top" title=""
                                                                    data-original-title="Show"><i class="fas fa-eye-slash"></i></a>
                                                                @endif

                                                                <a href="{{env('ADMIN_URL')}}/categories/delete?id={{$category->id}}"
                                                                    class="text-muted" data-toggle="tooltip" data-placement="top"
                                                                    title="" data-original-title="Delete">
                                                                    <i class="icofont icofont-delete-alt"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @else
                                                    <tr>
                                                        <td>No records found</td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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
