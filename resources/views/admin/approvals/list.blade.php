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
                        <h5>Approvals</h5>
                    </div>
                </div>
                <div class="col-md-4">
                   <ul class="breadcrumb">
                       <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Approvals</a>
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
                    @include('includes.msg')
                    
                    <!-- Horizontal-border table start -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Approvals</h5>
                        </div>
                        <div class="card-block table-border-style">
                            <div class="table-responsive">
                                @if(isset($approvals)&& count($approvals)>0)
                                <table class="table table-sm table-hover table-framed">
                                    <thead>
                                        <tr>
                                        <th width="35">#</th>
                                            <th>Salon</th>
                                            <th>Name</th>
                                            <th>Title</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            
                                            <th width="100"></th>
                                            <th width="100"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvals as $index=>$each)
                                        <tr>
                                            <th scope="row"> {{$index+$approvals->firstItem() }}</th>
                                            <td>
                                                <img src="{{$each->image}}" style="width:100px;" class="img-fluid"
                                                    >
                                            </td>
                                            <td>{{$each->name}}</td>
                                            <td>{{$each->title}}</td>
                                            <td>{{$each->email}}</td>
                                            <td>{{$each->phone}}</td>
                                           <td><a class="btn btn-primary btn-block" href="{{ env('ADMIN_URL') }}/approvals/details?id={{$each->id}}">View</a></td>
                                            <td><a class="btn btn-primary btn-block" href="{{env('ADMIN_URL')}}/approvals/approve?id={{$each->id}}">Approve</a></td>
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
                                {!! $approvals->appends(Illuminate\Support\Facades\Request::except('page'))->links()  !!}
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
