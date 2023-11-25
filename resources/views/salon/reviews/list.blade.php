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
                    <h5>Reviews</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Reviews</a>
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
            <h5>Reviews</h5>
        </div>

        <div class="card-block">

            <div class="table-responsive">
    @include('includes.msg')

                <table class="table table-sm table-hover">
                    @if(isset($reviews)&& count($reviews)>0)
                    <thead>
                        <tr>
                            <th width="80">User</th>
                            <th>Rating</th>
                            <th>Reviews</th>
                            <th>Reviewed By</th>
                            <th>Date</th>
                            {{-- <th></th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($reviews as $each)
                        <tr>
                        <td class="">
                                <img src="{{$each->image}}" class="img-fluid" alt="tbl">

                        </td>
                        <td>
                        <?php
                        for($i=0; $i<5; ++$i){
                            echo '<i class="fa fa-star',($each->rating<=$i?'-o':''),'" aria-hidden="true" style="color:rgb(255, 204, 0)"></i>';
                        }
                        ?>
                        </td>
                        <td>{{$each->reviews}}</td>
                        <td>{{$each->first_name}} {{$each->last_name}}</td>
                        <td>{{$each->created_at}}</td>
                        {{-- <td><a class="btn btn-primary d-block" href="{{ env('ADMIN_URL') }}/salon/reviews/details?id={{$each->id}}">Details</a></td> --}}
                    </tr>
                        @endforeach
                    </tbody>
                    @else
                    <tr><td>No reviews yet</td></tr>
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
