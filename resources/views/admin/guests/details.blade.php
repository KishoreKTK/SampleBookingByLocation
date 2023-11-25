@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('content')

<div class="pcoded-content">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>{{$guest->first_name}} {{$guest->last_name}}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/guests">Guests</a></li>
                        <li class="breadcrumb-item"><a>Details</a></li>
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



                        <div class="col-xl-3 col-lg-12 col-md-12 col-12">
                            <!-- Product detail page start -->
                            <div class="card ">
                                <div class="card-block">
                                    <div class="row justify-content-center">
                                        <div class="col-xl-12 col-md-4">

                                            <div class="prof-img">
                                                <img class="img img-fluid d-block" src="{{$guest->image}}" alt="">
                                            </div>

                                        </div>
                                        <div class="col-xl-12 col-md-8 product-detail" id="product-detail">


                                            <!-- <span class="f-right">Availablity : <a href="#!"> In Stock </a> </span> -->
                                            <!-- <h6>{{$guest->first_name}} {{$guest->last_name}}</h6>
                                                                <span class="d-block">Id: <a href="#!"> {{$guest->id}} </a> </span>
                                                                <span class="d-block">Email: {{$guest->email}}</span>
                                                                <span class="d-block">Email: {{$guest->email}}</span>
                                                                <span class="d-block">Email: {{$guest->email}}</span>
                                                                <span class="d-block">Email: {{$guest->email}}</span>
                                                                <span class="d-block">Email: {{$guest->email}}</span> -->
                                            <div class="table-responsive profile-table">

                                                <table class="table table-sm table-hover">
                                                    <tbody>
                                                        <tr data-toggle="tooltip" data-placement="right"
                                                            title="User Name">
                                                            <td><i class="fas fa-user-alt"></i></td>

                                                            <td>
                                                                {{$guest->first_name}}
                                                                {{$guest->last_name}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right"
                                                            title="User ID">
                                                            <td><i class="far fa-id-card"></i></td>
                                                            <td>
                                                                {{$guest->id}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right" title="Email">
                                                            <td> <i class="far fa-envelope"></i></td>
                                                            <td>

                                                                <span class="wrap">{{$guest->email}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right" title="Phone">
                                                            <td><i class="fas fa-phone-alt"></i></td>
                                                            <td>
                                                                {{$guest->phone}}</td>
                                                        </tr>

                                                        <tr data-toggle="tooltip" data-placement="right" title="Gender">
                                                            <td><i class="fas fa-map-marker-alt"></i></td>
                                                            <td>
                                                                {{$guest->address}}</td>
                                                        </tr>

                                                    </tbody>
                                                </table>

                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Product detail page end -->
                        </div>





                        <div class="col-xl-9 col-lg-12 col-md-12">
                            <div class="card main-tab-head">
                                <ul class="nav nav-tabs md-tabs tab-timeline" role="tablist">

                                    <li class="nav-item m-b-0">
                                        <a class="nav-link active f-18 p-b-0" data-toggle="tab" href="#booking"
                                            role="tab">Booking</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item m-b-0">
                                        <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#transactions"
                                            role="tab">Transactions</a>
                                        <div class="slide"></div>
                                    </li>

                                </ul>
                            </div>
                            <!-- Nav tabs start-->


                            <!-- Nav tabs card start-->
                            <div class="card">
                                <div class="card-block">
                                    <!-- Tab panes -->
                                    <div class="tab-content bg-white">


                                        <div class="tab-pane active" id="booking" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    @if(isset($booking)&& count($booking)>0)
                                                    <thead>
                                                        <tr>
                                                            <th width="30">#</th>
                                                            <th width="80">Salon</th>
                                                            <th>Name</th>
                                                            <th>Booked On</th>
                                                            <th width="100">Status</th>
                                                            <th class="text-right">Amount Paid</th>


                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($booking as $index=>$each)
                                                        <tr>
                                                            <td>{{$index+1}}</td>

                                                            <td>
                                                                <img class="img-fluid" src="{{$each->image}}">
                                                            </td>
                                                            <td>{{$each->name}}</td>
                                                            <td>{{$each->created_at}}</td>
                                                            <td>
                                                                @if($each->active==1)
                                                                <a class="btn btn-success btn-block">Success</a>
                                                                @elseif($each->active==2)
                                                                <a class="btn btn-danger btn-block">Cancelled</a>
                                                                 @elseif($each->active==3)
                                                                 <a class="btn btn-success btn-block">Processed</a>  
                                                                  @elseif($each->active==4)
                                                                 <a class="btn btn-danger btn-block">Rejected</a>  
                                                                @else
                                                                <a class="btn btn-warning btn-block">Pending</a>
                                                                @endif
                                                            </td>
                                                            <td class="text-right">{{$each->amount}} AED</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @else
                                                    <p>No bookings yet</p>
                                                    @endif

                                                </table>
                                            </div>
                                        </div>


                                        <div class="tab-pane" id="transactions" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    @if(isset($booking)&& count($booking)>0)
                                                    <thead>
                                                        <tr>
                                                            <th width="30">#</th>

                                                            <th>Salon</th>
                                                            <th>Booked On</th>
                                                            <th class="text-right">Paid</th>
                                                            <!-- <th class="text-right">Pending</th> -->
                                                            <th class="text-right">Commission</th>
                                                            <th class="text-right">VAT</th>
                                                            <th class="text-right">Actual Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($booking as $index=>$each)
                                                        <tr>
                                                            <td>{{$index+1}}</td>
                                                            <td>{{$each->name}}</td>
                                                            <td>{{$each->created_at}}</td>
                                                            <td class="text-right">{{$each->amount}} AED</td>
                                                            <td class="text-right">{{$each->mood_commission}} AED
                                                            </td>
                                                            <td class="text-right">0 AED</td>
                                                            <td class="text-right">{{$each->actual_amount}} AED</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @else
                                                    <p>No transactions yet</p>
                                                    @endif

                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Nav tabs start-->

                    <!-- Nav tabs card end-->
                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>
</div>

@stop