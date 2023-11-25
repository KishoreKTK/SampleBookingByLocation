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
                    <li class="breadcrumb-item"><a>Offers</a>
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
        <div class="card-header justify-content-between">
            <h5 class="align-self-center">Offers</h5><a href="{{ env('ADMIN_URL') }}/salons/services/offers/add?salon_id={{$salon_id}}&service_id={{$service_id}}" class="btn btn-primary align-self-center">Add</a>
        </div>

        <div class="card-block">

            <div class="table-responsive">
    @include('includes.msg')
        
            <table class="table table-sm table-hover dt-responsive nowrap">
                @if(isset($offers)&& count($offers)>0)
                <thead>
                    <tr> 
                        <th>#</th>
                        <th>Original Price</th>
                        <th>New Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Created at</th>
                        <th width="30"></th>
                        <th width="30"></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($offers as $index=>$each)
                    <tr>
                    <td>{{$index+1}}</td>
                    <td>{{$each->amount}} AED</td>
                    <td>{{$each->discount_price}} AED</td>
                    <td>{{$each->start_date}}</td>
                    <td> {{$each->end_date}}</td>
                    <td> {{$each->created_at}}</td>
                    <td>
                        <a href="{{env('ADMIN_URL')}}/salons/services/offers/edit?id={{$each->id}}&salon_id={{$salon_id}}&service_id={{$service_id}}" class="text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                    </td>
                    <td>
                        <a href="{{env('ADMIN_URL')}}/salons/services/offers/delete?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
                @else
                <tr><td>No offers found</td></tr>
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