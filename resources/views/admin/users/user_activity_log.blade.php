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
                        <h5>Activity Logs</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Users</a>
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
                                    <h5>Activity Log</h5>
                                </div>

                                <div class="card-block">
                                
                            <div class="row">

                                    <div class="table-responsive">
                                    @include('includes.msg')
                                        
                                            @if(isset($users)&& count($users)>0)

                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="35">#</th>
                                                    <th width="60">Image</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th></th>
                                                    <th width="100">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($users as $index=>$user)
                                                <tr>
                                                    <td>{{ $index+ $users->firstItem() }}</td>
                                                    <td>
                                                        <a>
                                                            <!-- <img class="user-img img-radius" src="{{env('ADMIN_URL')}}/public/assets/files/assets/images/user-profile/user-img.jpg" alt="user-img"> -->
                                                            <img src="{{$user->image}}" class="img-fluid">
                                                        </a>
                                                        <!-- <img src="{{env('ADMIN_URL')}}/public/assets/files/assets/images/product-list/pro-l2.png" class="img-fluid" alt="tbl"> -->

                                                    </td>
                                                    <td class="pro-name">
                                                        <h6><a href="{{env('ADMIN_URL')}}/users/details?id={{$user->id}}">{{$user->first_name}} {{$user->last_name}}</a></h6>
                                                        <!-- <span>Lorem ipsum dolor sit consec te imperdiet iaculis ipsum..</span> -->
                                                    </td>
                                                    <td>{{$user->email}}</td>
                                                    <td></td>
                                                    <td class="action-icon">
                                                        <a href="{{env('ADMIN_URL')}}/users/details?id={{$user->id}}"
                                                            class="m-r-15 text-muted" data-toggle="tooltip"
                                                            data-placement="top" title="" data-original-title="View"><i
                                                                class="fa fa-eye" aria-hidden="true"></i></a>

                                                        @if($user->suspend==1)

                                                        <a href="{{env('ADMIN_URL')}}/users/unsuspend?id={{$user->id}}"
                                                            data-toggle="tooltip" data-placement="top" title=""
                                                            data-original-title="Unsuspend" style="color: red;"><i
                                                                class="fa fa-ban" aria-hidden="true"></i></a>
                                                    @else
                                                    <a href="{{env('ADMIN_URL')}}/users/suspend?id={{$user->id}}"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Suspend" style="color:  #b3b3b3;"><i
                                                            class="fa fa-ban" aria-hidden="true"></i></a>
                                                    @endif
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                           
                                        </table>
                                         @else
                                            <table>
                                                <td>No activities found</td>
                                            </table>
                                            @endif
                                            <center>
                                               
                                            </center>

                                    </div>
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