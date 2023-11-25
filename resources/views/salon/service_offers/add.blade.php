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
                    <h5>Offers</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                   <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                      <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/services">Services </a>
                    </li>
                     <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/services/offers?service_id={{$service_id}}"> Offers </a>
                    </li>
                    <li class="breadcrumb-item"><a>Add</a>
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

     <div class="card">
        <div class="card-header">
            <h5>Add Offers</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/salon/services/offers/add","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Service</label>
            <div class="col-sm-10">
                {!! Form::text("service",$service->service,["placeholder"=>"New Price","class"=>"form-control","readonly"=>true]) !!}

                <span class="messages"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Original Price</label>
            <div class="col-sm-10">
                {!! Form::text("org_price",$service->amount,["placeholder"=>"New Price","class"=>"form-control","readonly"=>true]) !!}

                <span class="messages"></span>
            </div>
        </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">New Price</label>
            <div class="col-sm-10">
                {!! Form::text("discount_price",'',["placeholder"=>"New Price","class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
        
        <div class="form-group row">
        <label class="col-sm-2 col-form-label">Choose Start date</label>

        <div class="col-sm-10">
            {!!Form::text("start_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off","required"=>"required"]) !!}
            <span class="messages"></span>

        </div>
        </div>
        <div class="form-group row">
        <label class="col-sm-2 col-form-label">Choose End date</label>

        <div class="col-sm-10">
 
              {!!Form::text("end_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off","required"=>"required"]) !!}
            <span class="messages"></span>
        </div>
        </div>
      
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                {!! Form::hidden('service_id',$service_id) !!}
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