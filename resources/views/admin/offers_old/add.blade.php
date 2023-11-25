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
                     <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/offers"> Offers </a>
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

        {!! Form::open(["url"=>env('ADMIN_URL')."/offers/add","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Title</label>
            <div class="col-sm-10">
                {!! Form::text("title",'',["placeholder"=>"Title","class"=>"form-control"]) !!}

            </div>
        </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Promocode</label>
            <div class="col-sm-10">
                {!! Form::text("promocode",'',["placeholder"=>"Promocode","class"=>"form-control"]) !!}

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
            <label class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-10">
                {!! Form::textarea("description",'',["placeholder"=>"Description","class"=>"form-control"]) !!}

            </div>
        </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Amount Type</label>
            <div class="col-sm-10">
                {!! Form::select("amount_type",$type,null,["class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Amount</label>
            <div class="col-sm-10">
                {!! Form::text("amount",'',["placeholder"=>"Amount","class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Min Amount</label>
            <div class="col-sm-10">
                {!! Form::text("min_amount",'',["placeholder"=>"Min Amount","class"=>"form-control"]) !!}

            </div>
        </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Max Discount</label>
            <div class="col-sm-10">
                {!! Form::text("max_discount",'',["placeholder"=>"Max Discount","class"=>"form-control"]) !!}

            </div>
        </div>
        
        <div class="form-group row">
        <label class="col-sm-2 col-form-label">Choose Start date</label>

        <div class="col-sm-10">

            {!!Form::text("start_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose From Date","autocomplete"=>"off"]) !!}
            <span class="messages"></span>

        </div>
        </div>
        <div class="form-group row">
        <label class="col-sm-2 col-form-label">Choose End date</label>

        <div class="col-sm-10">
            {!!Form::text("end_date",'',["class"=>"form-control datepicker","id"=>"date", "aria-describedby"=>"emailHelp","placeholder"=>"Choose To Date","autocomplete"=>"off"]) !!}
            <span class="messages"></span>
        </div>
        </div>

       <div class="form-group row">
        <label  class="col-sm-2 col-control-label">Salons</label>
        <div class="col-sm-10">
        <div class="custom-control custom-checkbox custom-control-inline"><input class="custom-control-input" type="checkbox" value="1" checked id="checkAll" name="checkAll"> <label class="custom-control-label" for="checkAll">Check All</label></div>
            @foreach($salons as $index=>$value)
             <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input" id="{{$index}}" name="salons[]" value="{{$value->id}}">
            <label class="custom-control-label" for="{{$index}}">{{$value->name}}</label>
          </div>
               <!--  {!! Form::checkbox("salons[]",$value->id) !!} &nbsp;<span>{{$value->name}} </span> &nbsp;&nbsp;&nbsp;&nbsp;
            @endforeach
 -->
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
<script type="text/javascript">

$('#checkAll').click(function () { 
     $('input:checkbox').prop('checked', this.checked);    
 });
</script>
@stop