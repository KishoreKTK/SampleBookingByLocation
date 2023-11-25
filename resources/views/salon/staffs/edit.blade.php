@extends('layouts.master_salon')

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
                    <h5>Salons</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/staffs">Staff</a></li>
                    <li class="breadcrumb-item"><a>Edit</a></li>
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
            <h5>Edit Staff</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/salon/staffs/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Staff</label>
            <div class="col-sm-10">
                {!! Form::text("staff",$staff->staff,["placeholder"=>"Staff","class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                {!! Form::textarea("description",$staff->description,["placeholder"=>"Description","class"=>"form-control"]) !!}

                <span class="messages"></span>
            </div>
        </div>
         <div class="form-group row">
        <label  class="col-sm-2 col-control-label">Services</label>
        <div class="col-sm-10">

            @foreach($services as $index=>$value)
             <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="{{$index}}" name="services[]" value="{{$value->id}}" @if (in_array($value->id,$c_services)) checked @endif>
            <label class="custom-control-label" for="{{$index}}">{{$value->service}}</label>
          </div>
                <!-- {!! Form::checkbox("services[]",$value->id,in_array($value->id,$c_services)) !!} &nbsp;<span>{{$value->service}} </span> &nbsp;&nbsp;&nbsp;&nbsp; -->
            @endforeach

        </div>
      </div>
      
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                {!! Form::hidden('id',$id) !!}

                 {!! Form::submit('Update',["class"=>"btn btn-primary m-b-0"]) !!}
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