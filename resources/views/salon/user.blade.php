@extends('layouts.master_salon')

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
                        <h5>{{$user->first_name}} {{$user->last_name}}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/users">Users</a></li>
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
                                                <img class="img img-fluid d-block" src="{{$user->image}}" alt="">
                                            </div>

                                        </div>
                                        <div class="col-xl-12 col-md-8 product-detail" id="product-detail">


                                            <!-- <span class="f-right">Availablity : <a href="#!"> In Stock </a> </span> -->
                                            <!-- <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                                                                <span class="d-block">Id: <a href="#!"> {{$user->id}} </a> </span>
                                                                <span class="d-block">Email: {{$user->email}}</span>
                                                                <span class="d-block">Email: {{$user->email}}</span>
                                                                <span class="d-block">Email: {{$user->email}}</span>
                                                                <span class="d-block">Email: {{$user->email}}</span>
                                                                <span class="d-block">Email: {{$user->email}}</span> -->
                                            <div class="table-responsive profile-table">

                                                <table class="table table-sm table-hover">
                                                    <tbody>
                                                        <tr data-toggle="tooltip" data-placement="right"
                                                            title="User Name">
                                                            <td><i class="fas fa-user-alt"></i></td>

                                                            <td>
                                                                {{$user->first_name}}
                                                                {{$user->last_name}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right"
                                                            title="User ID">
                                                            <td><i class="far fa-id-card"></i></td>
                                                            <td>
                                                                {{$user->id}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right" title="Email">
                                                            <td> <i class="far fa-envelope"></i></td>
                                                            <td>

                                                                <span class="wrap">{{$user->email}}</span>
                                                            </td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right" title="Phone">
                                                            <td><i class="fas fa-phone-alt"></i></td>
                                                            <td>
                                                                {{$user->phone}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right"
                                                            title="Date of Birth">
                                                            <td><i class="fas fa-birthday-cake"></i></td>
                                                            <td>
                                                                {{$user->dob}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right" title="Gender">
                                                            <td><i class="fas fa-venus-mars"></i></td>
                                                            <td>
                                                                {{$user->gender}}</td>
                                                        </tr>
                                                        <tr data-toggle="tooltip" data-placement="right"
                                                            title="Country">
                                                            <td><i class="fas fa-globe-asia"></i></td>
                                                            <td>{{$user->country}}
                                                            </td>
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
                            <!-- Nav tabs start-->
                            <div class="card main-tab-head">
                                <ul class="nav nav-tabs md-tabs tab-timeline" role="tablist">
                                    <!-- <li class="nav-item m-b-0">
                                        <a class="nav-link active f-18 p-b-0" data-toggle="tab" href="#about"
                                            role="tab">About</a>
                                        <div class="slide"></div>
                                    </li> -->
                                    <li class="nav-item m-b-0">
                                        <a class="nav-link active  f-18 p-b-0" data-toggle="tab" href="#review"
                                            role="tab">Reviews</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item m-b-0">
                                        <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#favorites"
                                            role="tab">Favorites</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item m-b-0">
                                        <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#booking"
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
                                        <!-- <div class="tab-pane active" id="about" role="tabpanel">
                                            <table class="table table-sm table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td class="col-lg-2">Email</td>
                                                        <td class="col-lg-10">{{$user->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-lg-2">Phone</td>
                                                        <td class="col-lg-10">{{$user->phone}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-lg-2">DOB</td>
                                                        <td class="col-lg-10">{{$user->dob}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-lg-2">Gender</td>
                                                        <td class="col-lg-10">{{$user->gender}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="col-lg-2">Country</td>
                                                        <td class="col-lg-10">{{$user->country}}</td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div> -->
                                        <div class="tab-pane active" id="review" role="tabpanel">
                                            @if(isset($reviews)&& count($reviews)>0)
                                            <div class="table-responsive">
                                                <table class="table table-sm profile-detail-table">
                                                    <thead>
                                                        <tr>
                                                            <th  width="60">Salon</th>
                                                            <th width="150">Name</th>

                                                            <th class="text-center" width="100">Rating</th>
                                                            <th>Reviews</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($reviews as $each)
                                                        <tr>
                                                            <td>
                                                                <img src="{{$each->image}}" class="img-fluid"
                                                                    >
                                                            </td>
                                                            <td>{{$each->name}}</td>
                                                            <td class="text-center">
                                                                <div class="rating">
                                                                    <?php 
                                                        for($i=0; $i<5; ++$i){ echo '<i class="fa fa-star',($each->rating<=$i?'-o':''),'" aria-hidden="true" style="color:rgb(255, 204, 0)"></i>';}
                                                        ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="wrap">{{$each->reviews}} </span>


                                                            </td>
                                                            <td>{{$each->created_at}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @else
                                            <p>No reviews yet</p>
                                            @endif

                                        </div>
                                        <div class="tab-pane" id="favorites" role="tabpanel">
                                            @if(isset($favorites)&& count($favorites)>0)

                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>

                                                            <th colspan="2">Salon</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($favorites as $each)
                                                        <tr>
                                                            <td width="60">
                                                                <img src="{{$each->image}}" class="img-fluid"
                                                                   >
                                                            </td>
                                                            <td>{{$each->name}}</td>


                                                        </tr>
                                                        @endforeach
                                                       

                                                    </tbody>
                                                </table>
                                            </div>
                                             @else
                                                        <p>No favorites yet</p>
                                                        @endif
                                        </div>
                                        <div class="tab-pane" id="booking" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    @if(isset($booking)&& count($booking)>0)
                                                    <thead>


                                                        <tr>
                                                            <th width="30">#</th>

                                                        <th width="60">Salon</th>

                                                            <th width="150">Name</th>
                                                            <th>Booked On</th>
                                                            <th width="100">Status</th>
                                                            <th width="100" class="text-right">Amount Paid</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($booking as $index=>$each)
                                                        <tr>
                                                            <td>{{$index+1 }}</td>
                                                            <td>
                                                                <img src="{{$each->image}}" class="img-fluid"
                                                                    >
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
                                                            <th class="text-right">Commission</th>
                                                            <th class="text-right">VAT</th>
                                                            <th class="text-right">Actual Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($booking as $index=>$each)
                                                        <tr>
                                                            <td>{{$index+1 }}</td>
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
                            <!-- Nav tabs card end-->
                        </div>

                    </div>

                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>
</div>

@stop