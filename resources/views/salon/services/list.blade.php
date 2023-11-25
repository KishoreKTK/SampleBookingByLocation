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
                    <h5>Services</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Services</a>
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
            <h5 class="align-self-center">Services</h5>
        <a href="{{ env('ADMIN_URL') }}/salon/services/add" class="btn btn-primary float-right align-self-center">Add</a></div>


        <div class="card-block">


        <div class="table-responsive">
    @include('includes.msg')

            <table class="table table-sm table-hover">
                @if(isset($services)&& count($services)>0)
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Category</th>
                        <th>Time(In minutes)</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th width="30">Edit</th>
                        <th width="30">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $each)
                    <tr>
                    <td>{{$each->service}}</td>
                    <td>{{$each->category}}</td>
                    <td>{{$each->time}}</td>
                    <td >{{$each->amount}} AED</td>
                    @if($each->approved==1 && $each->pending==1)
                        <td  style="color:green">Approved</td>
                    @else
                        <td  style="color:red">Not Approved</td>
                    @endif

                    {{-- <td width="35" class="text-center">
                       <a href="{{env('ADMIN_URL')}}/salon/services/offers?service_id={{$each->id}}" class="btn btn-primary" data-toggle="tooltip" data-placement="left" title="">Offers</a></td> --}}
                    <td width="35" class="text-center">
                       <a href="{{env('ADMIN_URL')}}/salon/services/edit?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a></td>
                    <td width="35" class="text-center">
                       <a href="{{env('ADMIN_URL')}}/salon/services/delete?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                    </td>

                    </tr>
                    @endforeach
                </tbody>
                @else
                <tr><td>No services found</td></tr>
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
