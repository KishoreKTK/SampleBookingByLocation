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
                        <h5>Users List</h5>
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
                                <div class="card-header d-flex justify-content-between">
                                    <h5>Users List</h5>
                                    <a href="{{url('mdadmin/users/CustomerReport')}}"
                                        class="btn btn-primary float-right">
                                        Download Report
                                    </a>
                                </div>

                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-lg-12 col-xl-12">
                                            <div class="mail-box-head row ">
                                                <div class="col-md-6">
                                                    {!! Form::open(["url"=>env('ADMIN_URL')."/users","method"=>"get",
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
                                                        <a href="{{ env('ADMIN_URL') }}/users" class='btn btn-primary'><i
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
                                                    <td>No users found</td>
                                                </table>
                                            @endif
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
