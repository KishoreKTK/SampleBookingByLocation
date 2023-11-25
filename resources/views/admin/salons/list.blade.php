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
                        <h5>Salons</h5>
                        <!-- <p class="m-b-0">Lorem Ipsum is simply dummy text of the printing</p> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Salons</a>
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
                <!-- Product list start -->
            <div class="col-sm-12">

            <!-- Product list card start -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex d-flex justify-content-between align-items-center">
                        <div><h5>Salons List</h5></div>
                        <div >
                            <a href="{{url('mdadmin/salons/downloadReport')}}"
                                class="btn btn-primary float-right">
                                Download Report
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-block">
                    <div class="row">

                    <div class="col-lg-12 col-xl-12">
                        <div class="mail-box-head row ">
                            <div class="col-md-6">
                                {!! Form::open(["url"=>env('ADMIN_URL')."/salons","method"=>"get",
                                "class"=>"form-material"]) !!}

                                <div class="material-group searchgroup">
                                    <div class="form-group form-default">
                                        <!-- <input type="text" name="footer-email" class="form-control" required=""> -->
                                        {!!
                                        Form::text("keyword",$keyword,["class"=>"form-control",'required' =>
                                        'required']) !!}

                                        <span class="form-bar"></span>
                                        <label class="float-label">Search</label>
                                    </div>
                                    {{ Form::button('<i class="fas fa-search"></i>', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
                                    <a href="{{ env('ADMIN_URL') }}/salons" class='btn btn-primary'><i
                                            class="fas fa-times"></i></a>
                                    <!-- <div class="material-addone">
                                      <i class="icofont icofont-search"></i>
                                    </div> -->
                                </div>
                                {!! Form::close() !!}
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="table-responsive">
                    @include('includes.msg')

                        @if(isset($salons)&& count($salons)>0)
                         <table class="table table-sm table-hover">

                            <thead>
                                <tr>
                                    <th width="35">#</th>
                                    <th width="60">Image</th>
                                    <th>Salon</th>
                                    <th>Email</th>
                                    <th>Rating</th>
                                    <th>Area</th>
                                    <td></td>
                                    <th>Featured</th>
                                    <th>Details</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($salons as $index=>$salon)
                            <tr>
                            <td>{{ $index+ $salons->firstItem() }}</td>
                            <td>
                                <a>
                                    <!-- <img class="user-img img-radius" src="{{env('ADMIN_URL')}}/public/assets/files/assets/images/user-profile/user-img.jpg" alt="user-img"> -->
                                    <img src="{{$salon->image}}" class="img-fluid">
                                </a>
                                <!-- <img src="{{env('ADMIN_URL')}}/public/assets/files/assets/images/product-list/pro-l2.png" class="img-fluid" alt="tbl"> -->

                            </td>
                            <td class="pro-name">
                                <h6><a href="{{env('ADMIN_URL')}}/salons/details?id={{$salon->id}}"> {{$salon->name}}</a></h6>
                                <!-- <span>Lorem ipsum dolor sit consec te imperdiet iaculis ipsum..</span> -->
                            </td>
                            <td>{{$salon->email}}</td>

                            <td> <div class="">
                                <label class="label label-success">{{$salon->overall_rating}} <i class="fa fa-star" style="color:rgb(255, 204, 0)"></i></label><a class="text-muted f-w-600">
                                {{$salon->review_count}} Reviews</a>
                                </div>
                            </td>

                            <td>
                                <a class="btn btn-primary m-b-0" href="{{env('ADMIN_URL')}}/salons/deliveryarea?id={{$salon->id}}">Area</a>
                            </td>
                            <td>
                                @if($salon->new==1)
                                <div class="p-new"><a href=""> New </a></div>
                                @endif
                            </td>
                            <td>
                                @if($salon->featured==1)
                                <span class="prod-price"><small><a
                                        href="{{env('ADMIN_URL')}}/salons/remove_featured?id={{$salon->id}}"
                                        style="color: #f00000 !important;" class="m-r-15 text-muted"
                                        data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="Remove featured"><i class="fas fa-certificate"></i></a></small>
                                </span>
                                @else
                                <span class="prod-price"><small><a
                                        href="{{env('ADMIN_URL')}}/salons/featured?id={{$salon->id}}"
                                        style="color: #b3b3b3;" class="m-r-15 text-muted" data-toggle="tooltip"
                                        data-placement="top" title="" data-original-title="Mark featured"><i class="fas fa-certificate"></i></a></small>
                                </span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-primary m-b-0" href="{{env('ADMIN_URL')}}/salons/details?id={{$salon->id}}"> View  </a>
                            </td>
                            <td  class="action-icon">
                                <span class="prod-price">
                                    <a href="{{env('ADMIN_URL')}}/salons/edit?id={{$salon->id}}" class="m-r-15 text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                </span>
                                @if($salon->active==1)
                                    <span class="prod-price"> <a
                                        href="{{env('ADMIN_URL')}}/salons/inactive?id={{$salon->id}}" class="text-muted"
                                        data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="Hide"><i class="fas fa-eye"></i></a>
                                    </span>
                                @else
                                    <span class="prod-price"> <a href="{{env('ADMIN_URL')}}/salons/active?id={{$salon->id}}"
                                        class="text-muted" data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="Show"><i class="fas fa-eye-slash"></i></a>
                                    </span>
                                @endif
                                <span class="prod-price">
                                    <a href="{{env('ADMIN_URL')}}/salons/delete?id={{$salon->id}}" class="m-l-15 text-muted" data-toggle="tooltip" data-placement="top" title=""
                                    data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>
                                </span>
                            </td>

                            </tr>
                            @endforeach

                            </tbody>
                        </table>

                         @else
                        <table>
                            <td>No salons found</td>
                        </table>
                        @endif
                        <center>
                        {!! $salons->appends(Illuminate\Support\Facades\Request::except('page'))->links() !!}
                        </center>
                    </div>
                </div>
                </div>
            </div>
            </div>
            </div>


            </div>
            <!-- Page body end -->
            </div>
        </div>
    </div>
@stop
