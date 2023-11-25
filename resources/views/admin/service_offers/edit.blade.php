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
                    <h5>Offers</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salons">Salons</a></li>
                    <li class="breadcrumb-item"><a  href="{{env('ADMIN_URL')}}/salons/details?id={{$salon_id}}">Details</a></li>
                     <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salons/services/offers?salon_id={{$salon_id}}&service_id={{$service_id}}"> Offers </a>
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

     <div class="card">
        <div class="card-header">
            <h5>Edit Offers</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/salons/services/offers/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
      
      
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">New price</label>
            <div class="col-sm-10">
                {!! Form::text("discount_price",$offer->discount_price,["placeholder"=>"New price","class"=>"form-control"]) !!}

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
        <label class="col-sm-2 col-form-label">Choose Start date</label>

        <div class="col-sm-10">

            {!!Form::text("start_date",$offer->start_date,["class"=>"form-control datepicker","id"=>"", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}
            <span class="messages"></span>

        </div>
        </div>
        <div class="form-group row">
        <label class="col-sm-2 col-form-label">Choose End date</label>

        <div class="col-sm-10">
            {!!Form::text("end_date",$offer->end_date,["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
            <span class="messages"></span>
        </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                {!! Form::hidden('id',$id) !!}
                {!! Form::hidden('salon_id',$salon_id) !!}
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