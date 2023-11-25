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
                    <h5>Categories List</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Categories</a>
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
            <h5>Categories List</h5>
        </div>

        <div class="card-block">

            <div class="table-responsive">

                        <table class="table table-sm table-hover">
                            @if(isset($categories)&& count($categories)>0)
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Category</th>
                                    {{-- <th>Date</th>
                                    <th width="30"></th> --}}
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td class="pro-list-img">
                                        <img src="{{$category->image}}" class="img-fluid" alt="">
                                        <!-- <img src="{{url('/')}}/public/assets/files/assets/images/product-list/pro-l2.png" class="img-fluid" alt="tbl"> -->

                                    </td>
                                    <td class="pro-name">
                                        <h6>{{$category->category}}</h6>
                                        <!-- <span>Lorem ipsum dolor sit consec te imperdiet iaculis ipsum..</span> -->
                                    </td>
                                    {{-- <td>{{$category->created_at}}</td> --}}
                                    {{-- <td></td> --}}

                                </tr>
                                @endforeach
                            </tbody>
                            @else
                            <tr><td>No records found</td></tr>
                            @endif
                        </table>
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
