@extends('layouts.master')

@section('title')
Dashboard
@stop

@section('pagestyle')
<style>
    #map {
        height: 60%;
    }
</style>
@endsection


@section('content')

<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>{{$salon->name}}</h5>

                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salons">Salons</a></li>
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
                        <div class="col-md-12">
                            <!-- Product detail page start -->
                            <div class="card product-detail-page">
                                <div class="card-block">
                                    @include('includes.msg')
                                    <div class="row">
                                        <div class="col-lg-3 col-xs-12">
                                            <div class="port_details_all_img row">
                                                <div class="col-lg-12">
                                                    <div id="big_banner">

                                                        <div>
                                                            <div class="squareimagebox">
                                                                <img class="img img-fluid" src="{{$salon->image}}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        @if(isset($images)&& count($images)>0)
                                                        @foreach($images as $each)
                                                        <div>
                                                            <div class="squareimagebox">
                                                                <img class="img img-fluid" src="{{$each->image}}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 product-right">
                                                    <div id="small_banner">
                                                        @if(isset($images)&& count($images)>0)

                                                        <div>
                                                            <div class="squareimagebox">
                                                                <img class="img img-fluid" src="{{$salon->image}}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        @foreach($images as $each)
                                                        <div>
                                                            <div class="squareimagebox">
                                                                <img class="img img-fluid" src="{{$each->image}}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xs-12 product-detail" id="product-detail">
                                            <div class="row">
                                            <div class="col-lg-10">
                                                    <span class="txt-muted d-inline-block">ID: <a href="#!">
                                                                {{$salon->id}} </a> </span>
                                                        <h5 class="pro-desc">{{$salon->name}}</h5>
                                                    </div>
                                                    <div class="col-lg-2">


                                                        <!-- <span class="f-right"> <a
                                                                href="{{env('ADMIN_URL')}}/salons/services/add?id={{$id}}">
                                                                <button class="btn btn-primary d-block">Services
                                                                </button></a> </span> -->
                                                    </div>

                                                    <div class="col-lg-12">

                                                    <div class="stars stars-example-css">

                                                        <?php

                                                        for($i=0; $i<5; ++$i){
                                                            echo '<i class="fa fa-star',($salon->overall_rating<=$i?'-o':''),'" aria-hidden="true"  style="color:rgb(255, 204, 0)"></i>';
                                                        }
                                                        ?>
                                                    </div>
                                                    </div>
                                                    <div class="col-lg-12">

                                                        <hr>
                                                        <p>{{$salon->description}}
                                                        </p>

                                                    </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Product detail page end -->
                        </div>
                    </div>
                    <!-- Nav tabs start-->
                    <div class="card main-tab-head">
                        <ul class="nav nav-tabs md-tabs tab-timeline" role="tablist">
                            <li class="nav-item m-b-0">
                                <a class="nav-link active f-18 p-b-0" data-toggle="tab" href="#about"
                                    role="tab">About</a>
                                <div class="slide"></div>
                            </li>
                            {{-- <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#delivery_area"
                                    role="tab">Delivery Area</a>
                                <div class="slide"></div>
                            </li> --}}
                            <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#review"
                                    role="tab">Reviews</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#categories"
                                    role="tab">Categories</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#staffs" role="tab">Staff</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#services"
                                    role="tab">Services</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#hours" role="tab">Time</a>
                                <div class="slide"></div>
                            </li>
                            <li class="nav-item m-b-0">
                                <a class="nav-link f-18 p-b-0" data-toggle="tab" href="#booking" role="tab">Booking</a>
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
                                <div class="tab-pane active" id="about" role="tabpanel">
                                    @if(isset($reviews)&& count($reviews)>0)
                                    <div class="table-responsive">
                                                            <table class="table table-sm table-hover">
                                                                <tbody>
                                                                    <tr>
                                                                        <td>Email</td>
                                                                        <td>{{$salon->email}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Phone</td>
                                                                        <td>{{$salon->phone}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Location</td>
                                                                        <td>{{$salon->location}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>City</td>
                                                                        <td>{{$salon->city}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Country</td>
                                                                        <td>{{$salon->country}}</td>
                                                                    </tr>

                                                                </tbody>
                                                            </table>
                                                        </div>
                                    @else
                                    <p>No reviews yet</p>
                                    @endif

                                </div>

                                {{-- <div class="tab-pane" id="delivery_area" role="tabpanel">
                                    <div id="map"></div>
                                </div> --}}




                                <div class="tab-pane" id="review" role="tabpanel">
                                    @if(isset($reviews)&& count($reviews)>0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="80">Rating</th>
                                                    <th width="400">Reviews</th>
                                                    <th>Reviewed By</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach($reviews as $each)
                                                <tr>
                                                    <td>
                                                        <?php for($i=0; $i<5; ++$i){ echo '<i class="fa fa-star',($each->rating<=$i?'-o':''),'" aria-hidden="true"  style="color:rgb(255, 204, 0)"></i>'; } ?>
                                                    </td>
                                                    <td>{{$each->reviews}}</td>
                                                    <td>{{$each->first_name}} {{$each->last_name}}</td>
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

                                <div class="tab-pane" id="categories" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <th colspan="2">Categories</th>
                                            </thead>
                                            <tbody>
                                                @if(isset($categories)&& count($categories)>0)

                                                @foreach($categories as $each)
                                                <tr>
                                                    <td class="pro-list-img">
                                                        <img src="{{$each->image}}" class="img-fluid" alt="tbl">
                                                    </td>
                                                    <td>{{$each->category}}</td>
                                                </tr>
                                                @endforeach
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="staffs" role="tabpanel">
                                    <span class=""> <a href="{{env('ADMIN_URL')}}/salons/staffs/add?id={{$id}}"> <button
                                                class="btn btn-primary d-block">Add Staff </button></a> </span>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            @if(isset($staffs)&& count($staffs)>0)
                                            <thead>
                                                <tr>
                                                    <th>Staff</th>
                                                    <th>Description</th>
                                                    <th width="30"></th>
                                                    <th width="30"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($staffs as $each)
                                                <tr>
                                                    <td>{{$each->staff}}</td>
                                                    <td>{{$each->description}}</td>
                                                    <td>
                                                        <a href="{{env('ADMIN_URL')}}/salons/staffs/edit?id={{$each->id}}&salon_id={{$id}}"
                                                            class="text-muted" data-toggle="tooltip"
                                                            data-placement="top" title="" data-original-title="Edit"><i
                                                                class="icofont icofont-ui-edit"></i></a></td>
                                                    <td>

                                                        <a href="{{env('ADMIN_URL')}}/salons/staffs/delete?id={{$each->id}}&salon_id={{$id}}"
                                                            class="text-muted" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="Delete"><i
                                                                class="icofont icofont-delete-alt"></i></a>
                                                    </td>

                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td class="col-lg-10">No staff yet</td>
                                                </tr>

                                            </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="services" role="tabpanel">
                                    <span class=""> <a href="{{env('ADMIN_URL')}}/salons/services/add?id={{$id}}">
                                            <button class="btn btn-primary d-block">Add Service </button></a> </span>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            @if(isset($services)&& count($services)>0)
                                            <thead>
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Category</th>
                                                    <th>Time(In minutes)</th>
                                                    <th >Amount</th>
                                                    <th width="30"></th>
                                                    <th width="30"></th>
                                                    <th width="30"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($services as $each)
                                                <tr>
                                                    <td>{{$each->service}}</td>
                                                    <td>{{$each->category}}</td>
                                                    <td>{{$each->time}}</td>
                                                    <td>{{$each->amount}} AED</td>
                                                    {{-- <td width="35" class="text-center"> --}}
                                                    {{-- <a href="{{env('ADMIN_URL')}}/salons/services/offers?service_id={{$each->id}}&salon_id={{$id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="">Offers</a></td> --}}
                                                    <td>
                                                        <a href="{{env('ADMIN_URL')}}/salons/services/edit?id={{$each->id}}&salon_id={{$id}}"
                                                            class="text-muted" data-toggle="tooltip"
                                                            data-placement="top" title="" data-original-title="Edit"><i
                                                                class="icofont icofont-ui-edit"></i></a></td>
                                                    <td>

                                                        <a href="{{env('ADMIN_URL')}}/salons/services/delete?id={{$each->id}}&salon_id={{$id}}"
                                                            class="text-muted" data-toggle="tooltip"
                                                            data-placement="top" title=""
                                                            data-original-title="Delete"><i
                                                                class="icofont icofont-delete-alt"></i></a>
                                                    </td>

                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td class="col-lg-10">No services yet</td>
                                                </tr>

                                            </tbody>
                                            @endif

                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="hours" role="tabpanel">
                                    <span class=""> <a href="{{env('ADMIN_URL')}}/salons/working_hours/edit?salon_id={{$id}}">
                                            <button class="btn btn-primary d-block">Working Time </button></a> </span>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <th>Date</th>
                                                <th>Time</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="col-lg-2">Sunday</td>
                                                    <td class="col-lg-10">{{$working_hours['sunday_start']}} -
                                                        {{$working_hours['sunday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-lg-2">Monday</td>
                                                    <td class="col-lg-10">{{$working_hours['monday_start']}} -
                                                        {{$working_hours['monday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-lg-2">Tuesday</td>
                                                    <td class="col-lg-10">{{$working_hours['tuesday_start']}} -
                                                        {{$working_hours['tuesday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-lg-2">Wednesday</td>
                                                    <td class="col-lg-10">{{$working_hours['wednesday_start']}} -
                                                        {{$working_hours['wednesday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-lg-2">Thursday</td>
                                                    <td class="col-lg-10">{{$working_hours['thursday_start']}} -
                                                        {{$working_hours['thursday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-lg-2">Friday</td>
                                                    <td class="col-lg-10">{{$working_hours['friday_start']}} -
                                                        {{$working_hours['friday_end']}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-lg-2">Saturday</td>
                                                    <td class="col-lg-10">{{$working_hours['saturday_start']}} -
                                                        {{$working_hours['saturday_end']}}</td>
                                                </tr>

                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="booking" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            @if(isset($booking)&& count($booking)>0)
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Username</th>
                                                    <th>Booked On</th>
                                                    <th width="100">Status</th>
                                                    <!-- <th>Time</th> -->
                                                    <th class="text-right">Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($booking as $index=>$each)
                                                <tr>
                                                    <th scope="row"> {{$index+1 }}</th>
                                                    <td>{{$each->first_name}} {{$each->last_name}}</td>
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
                                                @else
                                                <tr>
                                                    <td class="col-lg-10">No bookings yet</td>
                                                </tr>

                                            </tbody>
                                            @endif

                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="transactions" role="tabpanel">
                                    <div class="row">
                                        <div class="card-header justify-content-between">
                                        @if(isset($booking)&& count($booking)>0)
                                            <a href="{{ env('ADMIN_URL') }}/transactions/export?salon_id={{$salon->id}}" class="btn btn-primary float-right align-self-center">Export</a>
                                        @endif
                                    </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            @if(isset($booking)&& count($booking)>0)
                                            <thead>
                                                <tr>
                                                    <th width="35">#</th>
                                                    <th>Username</th>
                                                    <th>Booked On</th>
                                                    <th class="text-right">Paid</th>
                                                    <!-- <th class="text-right">Pending</th> -->
                                                    <th class="text-right">Mood Commission</th>
                                                    {{-- <th class="text-right">VAT</th> --}}
                                                    <th class="text-right">Actual Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($booking as $index=>$each)
                                                <tr>
                                                    <th scope="row"> {{$index+1 }}</th>
                                                    <td>{{$each->first_name}} {{$each->last_name}}</td>
                                                    <td>{{$each->created_at}}</td>
                                                    <td class="text-right">{{$each->amount}} AED</td>
                                                    <td class="text-right">{{$each->mood_commission}} AED</td>
                                                    {{-- <td class="text-right">0 AED</td> --}}
                                                    <td class="text-right">{{$each->actual_amount}} AED</td>

                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td class="col-lg-10">No transactions yet</td>
                                                </tr>

                                            </tbody>
                                            @endif

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Nav tabs card end-->
                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>
</div>
@stop


@section('pagescript')
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<script type="text/javascript" src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}
            &callback=initMap&libraries=drawing&v=weekly&channel=2" async></script>


<script>
   function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 23.4241,
                lng: 53.8478
            },
            zoom: 9,
            // panControl: true,
            // zoomControl: false,
            // scaleControl: true,
            // mapTypeControl:false,
            // streetViewControl:true,
            // overviewMapControl:true,
            // rotateControl:true
        });

        // Set Delivery Area
        var shapeCoordinates = [
            new google.maps.LatLng(23.4241, 53.8478),
            new google.maps.LatLng(23.190518, 53.530518),
            new google.maps.LatLng(23.013807, 53.67334)
        ];


        // Construct the polygon
        draggablePolygon = new google.maps.Polygon({
            paths: shapeCoordinates,
            draggable: true,
            editable: true,
            strokeColor: '',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#ADFF2F',
            fillOpacity: 0.5
        });

        draggablePolygon.setMap(map);


        google.maps.event.addListener(draggablePolygon, "dragend", Getpolygoncoordinates);
        google.maps.event.addListener(draggablePolygon.getPath(), "insert_at", Getpolygoncoordinates);
        google.maps.event.addListener(draggablePolygon.getPath(), "remove_at", Getpolygoncoordinates);
        google.maps.event.addListener(draggablePolygon.getPath(), "set_at", Getpolygoncoordinates);

    }

    function Getpolygoncoordinates() {
        var len = draggablePolygon.getPath().getLength();
        var strArray = "";
        let latlngArr = [];

        // console.log(len);
        for (var i = 0; i < len; i++) {
            strArray += draggablePolygon.getPath().getAt(i).toUrlValue(5) + " | ";
        }

        // console.log(strArray);
        var myArray = strArray.split(" | ")
        myArray.forEach(function(key, value) {
            if (key) {
                // console.log(key);
                var latlngstr = key.split(',')
                latlngArr.push({
                    lat: latlngstr[0],
                    lng: latlngstr[1]
                })
            }
        });

        document.getElementById('delivery_area_field').value = JSON.stringify(latlngArr);

        // return latlngArr
    }
</script>
@endsection
