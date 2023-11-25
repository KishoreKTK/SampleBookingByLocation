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
                        <h5>Customers</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Customers</a>
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
                                    <h5>Customer List</h5>
                                </div>

                                <div class="card-block">

                                    <div class="row">
                                        <div class="table-responsive">
                                            @include('includes.msg')
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th width="35">#</th>
                                                            <th width="60">Image</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Rating</th>
                                                            <th>No of Bookings</th>
                                                            <th width="100">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($users)&& count($users)>0)
                                                            @foreach($users as $index=>$user)
                                                            <tr>
                                                                <td>{{ $index+ $users->firstItem() }}</td>
                                                                <td>
                                                                    <a>
                                                                        {{-- <p>{{ $user->image }}</p> --}}
                                                                        @if($user->image == null || $user->image == '')
                                                                        <img src="{{ asset('assets/files/assets/images/avatar-blank.jpg') }}" class="img-fluid">
                                                                        @else
                                                                            @if($user->login_type == 0)
                                                                            <img src="{{ asset($user->image) }}" class="img-fluid">
                                                                            @else
                                                                            <img src="{{ $user->image }}" class="img-fluid">
                                                                            @endif
                                                                        @endif
                                                                    </a>
                                                                </td>
                                                                <td class="pro-name">
                                                                    <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                                                                </td>
                                                                <td><a href="mailto:{{$user->email}}"><span class="badge btn-primary">{{$user->email}}</span></td>
                                                                <td>
                                                                    <?php
                                                                    $rating = 4;
                                                                    for($i=0; $i<5; ++$i){
                                                                        echo '<i class="fa fa-star',($user->overall_rating<=$i?'-o':''),'" aria-hidden="true" style="color:rgb(255, 204, 0)"></i>';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <span class="badge btn-primary">{{ $user->booking_count }}</span>
                                                                </td>
                                                                <td class="action-icon">
                                                                    <a href="{{env('ADMIN_URL')}}/salon/customers/details?id={{$user->id}}"
                                                                        class="m-r-15 text-muted" data-toggle="tooltip"
                                                                        data-placement="top" title="" data-original-title="View"><i
                                                                        class="fa fa-eye" aria-hidden="true"></i>
                                                                    </a>

                                                                    @if($user->suspend==1)
                                                                        <a href="{{env('ADMIN_URL')}}/salon/customers/unsuspend?id={{$user->id}}"
                                                                            data-toggle="tooltip" data-placement="top" title=""
                                                                            data-original-title="Unsuspend" style="color: red;">
                                                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                                                        </a>
                                                                    @else
                                                                        <a href="{{env('ADMIN_URL')}}/salon/customers/suspend?id={{$user->id}}"
                                                                            data-toggle="tooltip" data-placement="top" title=""
                                                                            data-original-title="Suspend" style="color:  #b3b3b3;">
                                                                            <i class="fa fa-ban" aria-hidden="true"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @else
                                                        <tr>
                                                            <td colspan="7"><center>No users found</center></td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>

                                            <center>
                                                {!!
                                                $users->appends(Illuminate\Support\Facades\Request::except('page'))->links()
                                                !!}
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
