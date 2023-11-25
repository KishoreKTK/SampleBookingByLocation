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
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/services">Services</a></li>
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
            <h5>Edit Services</h5>
        </div>
        <div class="card-block">
        @include('includes.msg')
            @if(isset($approved)&& $approved==0)
             <p style="color:red"> Your service is waiting for approval.</p>
             @endif
        {!! Form::open(["url"=>env('ADMIN_URL')."/salon/services/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Service</label>
            <div class="col-sm-10">
                {!! Form::text("service",$service->service,["placeholder"=>"Service","class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
         <div class="form-group row">
            <label class="col-sm-2 col-form-label">Time(in Minutes)</label>
            <div class="col-sm-10">
                    <select name="time" class="form-control">
                            @foreach($time as $key)
                                <option value="{{ $key }}"

                            @if ($key == $service->time) selected="selected" @endif>{{ $key }}</option>


                    @endforeach
                </select>
                <span class="messages"></span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Amount(in AED)</label>
            <div class="col-sm-10">
                {!! Form::text("amount",$service->amount,["placeholder"=>"Amount","class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
       
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Category</label>
            <div class="col-sm-10">
                {!! Form::select("category",$categories,$service->category_id,["placeholder"=>"Category","class"=>"form-control",'required' => 'required']) !!}

                <span class="messages"></span>
            </div>
        </div>
      
        <div class="form-group row">
            <label class="col-sm-2"></label>
            <div class="col-sm-10">
                <!-- <button type="submit" class="btn btn-primary m-b-0">Submit</button> -->
                {!! Form::hidden('id',$id) !!}
                @if(isset($approved)&& $approved>0)
                 {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
                 @else
                 {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0","disabled"=>"disabled"]) !!}
                 @endif
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