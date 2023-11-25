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
                        <h5>Admin Users</h5>
                    </div>
                </div>
                <div class="col-md-4">
                   <ul class="breadcrumb">
                       <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>List Admins</a>
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
                    <div class="card">
                        <div class="card-header">
                            <h5>List Admins</h5>
                        </div>
                        <div class="card-block table-border-style">
                            @include('includes.msg')
                            <div class="table-responsive">
                                @if(isset($users)&& count($users)>0)
                                <table class="table table-sm table-hover table-framed">
                                    <thead>
                                        <tr>
                                            <td width="35">#</td>
                                            <td>Name</td>
                                            <td>Email</td>
                                            <td>Date</td>
                                            <td width="35"></td>
                                            <td width="35"></td>
                                            <td width="35"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $index=>$each)
                                        <tr>
                                            <td>{{$index+$users->firstItem() }}</td>
                                            <td>{{$each->name}}</td>
                                            <td>{{$each->email}}</td>
                                            <td>{{$each->created_at}}</td>
                                            <!-- <td>{{$each->amount}}</td> -->
                                             @if($each->master_admin==0)
                                            @if($each->suspend==1)
                                            <td class="text-center"  data-toggle="tooltip" data-placement="left" title="Suspend"><a href="{{env('ADMIN_URL')}}/admin_users/notsuspend?id={{$each->id}}" class="text-muted d-block"><i class="fa fa-ban"  style="color: red;"></i></a></td>
                                            @else
                                            <td class="text-center"  data-toggle="tooltip" data-placement="left" title="Suspend"><a href="{{env('ADMIN_URL')}}/admin_users/suspend?id={{$each->id}}" class="text-muted d-block"><i class="fa fa-ban"  style="color: #b3b3b3;"></i></a></td>

                                            @endif
                                            @else
                                            <td></td>
                                            @endif
                                            @if($each->master_admin==0)
                                            <td class="text-center"  data-toggle="tooltip" data-placement="left" title="Edit"><a href="{{env('ADMIN_URL')}}/admin_users/edit?id={{$each->id}}" class="text-muted d-block"><i class="icofont icofont-ui-edit"></i></a></td>
                                            <td class="text-center"  data-toggle="tooltip" data-placement="left" title="Delete"><a href="{{env('ADMIN_URL')}}/admin_users/delete?id={{$each->id}}" class="text-muted d-block"><i class="icofont icofont-delete-alt"></i></a></td>
                                            @else
                                            <td></td>
                                            <td></td>

                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <table>
                                    <thead>No records found</thead>
                                </table>
                                @endif
                                <center>
                                {!! $users->appends(Illuminate\Support\Facades\Request::except('page'))->links()  !!}
                                </center>
                            </div>
                        </div>
                    </div>
                    <!-- Horizontal-border table end -->
                    
                </div>
                <!-- Page-body end -->
            </div>
        </div>
        <!-- Main-body end -->

        </div>
    </div>
</div>


@stop
