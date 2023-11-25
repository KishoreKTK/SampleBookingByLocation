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
                        <h5>Salon Schedules</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/schedules">Schedules</a>
                        </li>
                        <li class="breadcrumb-item"><a>Details</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page-header end -->
    <div class="pcoded-inner-content">
        <!-- Main-body start -->
        <div class="main-body">
            <div class="page-wrapper">
                <!-- Page-body start -->
                <div class="page-body">
                    <!-- Horizontal-border table start -->

                    <div class="row">
                        <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Details</h5>
                        </div>
                        <div class="card-block">
                            <div class="table-responsive">
                                @if(isset($booking))
                                <table class="table table-sm table-hover">

                                    <tbody>
                                        <tr>
                                            <th width="200">Salon</th>
                                            <td>{{$booking->name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Booked By</th>
                                            <td>{{$booking->first_name}} {{$booking->last_name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Booking On</th>
                                            <td>{{$booking->created_at}}</td>
                                        </tr>
                                        <tr>
                                            <th>Amount Paid</th>
                                            <td>{{$booking->amount}}</td>
                                        </tr>
                                        
                                        <tr>
                                            <th>Mood <br>Commission</th>
                                            <td>{{$booking->mood_commission}}</td>
                                        </tr>
                                        <tr>
                                            <th>VAT Amount</th>
                                            <td>0 AED</td>
                                        </tr>
                                        <tr>
                                            <th>Actual Amount</th>
                                            <td>{{$booking->actual_amount}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                    


                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="col-xl-8 col-lg-12 col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <h5>Services</h5>
                        </div>
                        <div class="card-block">
                            <div class="table-responsive">

                                <table class="table table-sm table-hover">
                                    <thead>
                                    @if(isset($services) && count($services)>0)
                                        <tr>
                                            <th width="30">#</th>
                                            <th>Service</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Staff</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                           
                                        @foreach($services as $index=>$service)
                                        <tr>
                                            <td> {{$index+1 }}</td>
                                            <td> {{$service->service}}</td>
                                            <td> {{$service->date}}</td>
                                            <td>{{$service->start_time}} - {{$service->end_time}}
                                            </td>
                                            <td>{{$service->staff}}</td>

                                        </tr>
                                        @endforeach
                                        @endif
                                        @else
                                        <thead>No records found</thead>

                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>




                    </div>

                    


                </div>
                <!-- Page-body end -->
            </div>
        </div>
        <!-- Main-body end -->

    </div>
</div>
</div>


@stop
