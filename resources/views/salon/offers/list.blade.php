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
                    <h5>Offers</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a>Offers</a>
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
            <h5>Offers</h5>
        </div>

        <div class="card-block">
    @include('includes.msg')

      
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            @if(isset($offers)&& count($offers)>0)
                            <thead>
                                <tr> 
                                    <th>Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-right">Amount(in %)</th>
                                    <th></th>
                                    
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($offers as $each)
                                <tr>
                              
                                <td>{{$each->title}}</td>
                                <td>{{$each->start_date}}</td>
                                <td> {{$each->end_date}}</td>
                                <td class="text-right">{{$each->amount}} %</td>
                               
                               <!--  @if($each->active==1)
                                <td width="35" class="text-center">
                               <a href="{{env('ADMIN_URL')}}/salon/offers/inactive?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Mark Inactive"><i class="fas fa-eye-slash"></i></a></small>
                               </td>
                                @else
                                <td width="35" class="text-center">
                                <a href="{{env('ADMIN_URL')}}/salon/offers/active?id={{$each->id}}"  style="color: green;" data-toggle="tooltip" data-placement="left" title="" data-original-title="Mark active"><i class="fas fa-eye"></i></a></small>
                                </td>
                                @endif -->
                                <td></td>
                                <td width="35" class="text-center">
                                   <a href="{{env('ADMIN_URL')}}/salon/offers/edit?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a></td>
                                <td></td>
                                   
                               <!--  <td  width="35" class="text-center">

                                   <a href="{{env('ADMIN_URL')}}/salon/offers/delete?id={{$each->id}}" class="text-muted" data-toggle="tooltip" data-placement="left" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                                </td> -->
                                   
                                </tr>
                                @endforeach
                            </tbody>
                            @else
                            <tr><td>No offers found</td></tr>
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