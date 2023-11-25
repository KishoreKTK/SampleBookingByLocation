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
                        <h5>{{$new->name}}</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/approvals">Approvals</a></li>
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

                                    @if(isset($new) && !empty($new))
                                    <h6>New values</h6>

                                    <div class="row">

                                        <div class="col-lg-4 col-xs-12">
                                            <div class="port_details_all_img row">
                                                <div class="col-lg-12">
                                                    <div id="big_banner">
                                                        <div class="port_big_img">
                                                            <img class="img img-fluid" src="{{$new->image}}"
                                                                alt="Big_ Details">
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                              
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xs-12 product-detail" id="product-detail">
                                            <div class="row">
                                            <div class="col-lg-10">
                                            <span class="txt-muted d-inline-block">ID: <a href="#!">
                                                        {{$new->id}} </a> </span>
                                                <h5 class="pro-desc">{{$new->name}}</h5>
                                            </div>
                                            <!-- <div class="col-lg-2">

                                            <img class="img img-fluid" src="{{$new->thumbnail}}"  alt="small-details"> -->
                                           

                                            <div class="col-lg-2">

                                             
                                                <span class="f-right"> <a
                                                        href="{{env('ADMIN_URL')}}/approvals/approve?id={{$id}}">
                                                        <button class="btn btn-primary d-block">Approve
                                                        </button></a> </span>
                                            </div>
                                        </div>
                                         <div class="row">
                                                @if(isset($new->sub_images) && count($new->sub_images)>0)
                                                @foreach($new->sub_images as $sub)
                                                <div class="col-lg-2">
                                                <img class="img img-fluid" src="{{$sub->thumbnail}}"  alt="small-details">
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>

                                            <div class="col-lg-12">

                                                <hr>
                                                <p>{{$new->description}}
                                                </p>
                                                <p>Cancellation Policy: {{$new->cancellation_policy}}
                                                </p>
                                                 <!-- <p>Reschedule Policy: {{$new->reschedule_policy}} -->
                                                <!-- </p> -->
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td>Sub Title</td>
                                                                <td>{{$new->sub_title}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email</td>
                                                                <td>{{$new->email}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phone</td>
                                                                <td>{{$new->phone}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Manager Phone</td>
                                                                <td>{{$new->manager_phone}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Location</td>
                                                                <td>{{$new->location}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>City</td>
                                                                <td>{{$new->city}}</td>
                                                            </tr> 
                                                            <tr>
                                                                <td>Latitude</td>
                                                                <td>{{$new->latitude}}</td>
                                                            </tr>
                                                             <tr>
                                                                <td>Longitude</td>
                                                                <td>{{$new->longitude}}</td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                               
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                     @if(isset($old) && !empty($old))
                                    <h6>Previous values</h6>

                                    <div class="row">

                                        <div class="col-lg-4 col-xs-12">
                                            <div class="port_details_all_img row">
                                                <div class="col-lg-12">
                                                    <div id="big_banner">
                                                        <div class="port_big_img">
                                                            <img class="img img-fluid" src="{{$old->image}}"
                                                                alt="Big_ Details">
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                              
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xs-12 product-detail" id="product-detail">
                                            <div class="row">
                                            <div class="col-lg-10">
                                            <span class="txt-muted d-inline-block">ID: <a href="#!">
                                                        {{$old->id}} </a> </span>
                                                <h5 class="pro-desc">{{$old->name}}</h5>
                                            </div>
                                           <!--  <div class="col-lg-2">

                                            <img class="img img-fluid" src="{{$old->logo}}"  alt="small-details">
                                            </div> -->
                                           </div>
                                            <div class="row">
                                                @if(isset($old->sub_images) && count($old->sub_images)>0)
                                                @foreach($old->sub_images as $sub)
                                                <div class="col-lg-2">
                                                <img class="img img-fluid" src="{{$sub->thumbnail}}"  alt="small-details">
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                            <div class="col-lg-12">

                                                <hr>
                                                <p>{{$old->description}}
                                                </p>
                                                <p>Cancellation Policy: {{$old->cancellation_policy}}
                                                </p>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td>Sub Title</td>
                                                                <td>{{$old->sub_title}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email</td>
                                                                <td>{{$old->email}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Phone</td>
                                                                <td>{{$old->phone}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Manager Phone</td>
                                                                <td>{{$old->manager_phone}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Location</td>
                                                                <td>{{$old->location}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>City</td>
                                                                <td>{{$old->city}}</td>
                                                            </tr> 
                                                            <tr>
                                                                <td>Latitude</td>
                                                                <td>{{$old->latitude}}</td>
                                                            </tr>
                                                             <tr>
                                                                <td>Longitude</td>
                                                                <td>{{$old->longitude}}</td>
                                                            </tr>
                                                           
                                                              

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                               
                                            </div>
                                        </div>
                                    </div>

                                    @endif




                                </div>
                            </div>
                            <!-- Product detail page end -->
                        </div>
                    </div>


                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>
</div>
@stop