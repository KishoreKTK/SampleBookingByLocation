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
                    <h5>Pricing</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Pricing</a>
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
            <h5>Pricing</h5>
        </div>

        <div class="card-block">

            <div class="table-responsive">
            @include('includes.msg')
         
            <table class="table table-sm table-hover dt-responsive nowrap">
                @if(isset($pricing)&& count($pricing)>0)
                <thead>
                    <tr> 
                        <th width="60">Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Date</th>
                        <th>Pricing (%)</th>
                        <th>Min Price (%)</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($pricing as $each)
                    <tr>
                        <td>
                            <a >
                                    <img src="{{$each->image}}" style="width:150px;" class="img-fluid" >
                                </a>
                        </td>
                        <td class="pro-name">
                            <h6>{{$each->name}}</h6>
                            <!-- <span>Lorem ipsum dolor sit consec te imperdiet iaculis ipsum..</span> -->
                        </td>
                        <td>{{$each->email}}</td>
                        <td>{{$each->country}}</td>
                        <td>{{$each->created_at}}</td>
                        {!! Form::open(["url"=>env('ADMIN_URL')."/pricing/update","method"=>"post", "class"=>"form-horizontal row-border"]) !!}
                        @csrf
                        <td>{!! Form::text("pricing",$each->pricing,["class"=>"form-control", "placeholder"=>"Price"]) !!}</td>
                        <td>{!! Form::text("min_price",$each->min_price,["class"=>"form-control", "placeholder"=>"Min Price"]) !!}</td>
                        {!! Form::hidden('id',$each->id) !!}
                        @if(isset($each->pricing)|| isset($each->min_price))
                         <td>{!! Form::submit('Update',["class"=>"btn btn-primary btn-block"]) !!}</td>
                        @else
                         <td>{!! Form::submit('Add',["class"=>"btn btn-primary btn-block"]) !!}</td>
                         @endif
                        {!! Form::close() !!}
                       
                    </tr>
                    @endforeach
                </tbody>
                @else
                <tr><td>No pricing found</td></tr>
                @endif
            </table>
                
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