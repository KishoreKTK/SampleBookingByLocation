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
                        <h5>Blocked slots</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/booking"> Booking </a>
                        </li>
                        <li class="breadcrumb-item"><a>Blocked slots</a>
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
                                    <h5>List Blocked Slots</h5>
                                </div>

                                <div class="card-block">
                                @include('includes.msg')

                                    <div class="table-responsive">

                                        <table class="table table-sm table-hover">
                                            @if(isset($slots)&& count($slots)>0)
                                            <thead>
                                                <tr>
                                                    <th>Staff</th>
                                                    <th>Date</th>
                                                    <!-- <th>ID</th> -->
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                    
                                                    <th></th>
                                                    <th width="30"></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($slots as $slot)
                                                <tr>

                                                    <td class="pro-name">
                                                        <h6>{{$slot->staff}}</h6>
                                                    </td>
                                                  
                                                    <td class="pro-name">
                                                        <h6>{{$slot->date}}</h6>
                                                    </td>
                                                     <!--  <td class="pro-name">
                                                        <h6>{{$slot->booking_id}}</h6>
                                                    </td> -->
                                                    <td class="pro-name">
                                                        <h6>{{$slot->start_time}}</h6>
                                                    </td>
                                                    <td class="pro-name">
                                                        <h6>{{$slot->end_time}}</h6>
                                                    </td>
                                                   <!--  <td width="35" class="text-center">
                                                   <a href="{{env('ADMIN_URL')}}/booking/list_block/edit?id={{$slot->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a></td> -->
                                                <td  width="35" class="text-center">
                                                    @if($slot->delete==1)

                                                   <a href="{{env('ADMIN_URL')}}/booking/block_slot/delete?id={{$slot->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                                                   @endif
                                                </td>
                                                    <td></td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                            @else
                                            <tr>
                                                <td>No records found</td>
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