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
                    <h5>Coupens</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                     <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/coupens"> coupens </a>
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
            <h5>Add coupens</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')

        {!! Form::open(["url"=>env('ADMIN_URL')."/coupens/add","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-10">
                {!! Form::text("name",'',["placeholder"=>"Title","class"=>"form-control","required"=>"required"]) !!}
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Coupen Code</label>
            <div class="col-sm-10">
                {!! Form::text("code",'',["placeholder"=>"Coupen Code","class"=>"form-control","required"=>"required"]) !!}
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                {!! Form::textarea("description",'', ["placeholder"=>"Describe the Purpose","class"=>"form-control", "required"=>"required", "rows"=>"2"]) !!}
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Max Use</label>
            <div class="col-sm-10">
                {!! Form::number("max_uses", "", ["placeholder"=>"Maximum Use Limit","class"=>"form-control", "required"=>"required"]) !!}
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Max Use per Person</label>
            <div class="col-sm-10">
                {!! Form::number("user_max_uses", "", ["placeholder"=>"Maximum Use Limit for Single User","class"=>"form-control", "required"=>"required"]) !!}
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Discount Type</label>
            <div class="col-sm-10">
                <select id="coupen_type" name="type" class="form-control">
                    <option value="1">Percentage</option>
                    <option value="2">Amount</option>
                </select>
            </div>
        </div>

        <div class="form-group row" id="coupen_discount_field">
            <label class="col-sm-2 col-form-label">Discount</label>
            <div class="col-sm-10">
                {!! Form::text("discount",'',["placeholder"=>"Discount","class"=>"form-control", "required"=>"required"]) !!}
                <span class="messages"></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Choose Start date</label>
            <div class="col-sm-10">
                {!!Form::text("starts_at",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off", "required"=>"required"]) !!}
                <span class="messages"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Choose End date</label>
            <div class="col-sm-10">
                {!!Form::text("expires_at",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off", "required"=>"required"]) !!}
                <span class="messages"></span>
            </div>
        </div>



        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->

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
