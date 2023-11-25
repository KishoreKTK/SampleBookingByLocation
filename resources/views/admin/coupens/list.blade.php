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
                    <h5>Promo Code</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Promo Codes</a>
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
            <h5>Promo Codes</h5>
        </div>

        <div class="card-block">
    @include('includes.msg')
            <div class="table-responsive">
                        <table class="table table-sm table-hover dt-responsive nowrap">
                            @if(isset($promocodes)&& count($promocodes)>0)
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Voucher Code</th>
                                        <th>Discount</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Max Use per User</th>
                                        <th>Max Limit</th>
                                        <th width="30"></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($promocodes as $each)
                                    <tr>
                                        <td>{{$each->name}}</td>
                                        <td>{{$each->code}}</td>
                                        <td>{{ $each->discount }} @if($each->type == 1) % @else AED @endif</td>
                                        <td>{{$each->starts_at}}</td>
                                        <td> {{$each->expires_at}}</td>
                                        <td>{{ $each->user_max_uses }}</td>
                                        <td>{{ $each->max_uses }}</td>
                                        <td>
                                            <a href="{{env('ADMIN_URL')}}/coupens/delete?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tr>
                                    <td>No coupens found</td>
                                </tr>
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
