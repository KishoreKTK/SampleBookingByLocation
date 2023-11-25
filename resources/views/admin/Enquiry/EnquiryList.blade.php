@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('content')
<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script> -->
<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h5>Join Mood Enquiry</h5>
                        {{-- <a href="{{url('mdadmin/transactions/export')}}"
                            class="btn btn-sm btn-primary float-right">
                            Download Report
                        </a> --}}
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a>Join Mood Enquiry</a>
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
                        <div class="card-block table-border-style">
                            <div class="row">
                                <div class="table-responsive">
                                @include('includes.msg')
                                    <table class="table table-sm table-hover table-framed">
                                        <thead>
                                            <tr>
                                                <th width="35">#</th>
                                                <th>Name & Role</th>
                                                <th>Business</th>
                                                <th >Categories</th>
                                                <th >Contact</th>
                                                <th>Location</th>
                                                <th width="100">Price Sheet</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($enquiry_list)&& count($enquiry_list) > 0)
                                            @foreach ($enquiry_list as $key=>$enquiries)
                                            <tr>
                                                <td>{{ $key + $enquiry_list->firstItem() }}</td>
                                                <td>
                                                    <p>{{ $enquiries->user_name }}
                                                    <span class="badge badge-pill badge-primary">{{ $enquiries->user_role }}</span> </p>
                                                </td>
                                                <td>
                                                    <p>{{ $enquiries->business_name }}</p>
                                                </td>
                                                <td>
                                                    @php
                                                        $category_ids               =   $enquiries->category;
                                                        $catid_arr                  =   explode(',',$category_ids);
                                                        $cat_names_arr              =   DB::table('categories')
                                                                                        ->whereIn('id',$catid_arr)
                                                                                        ->pluck('category')->toArray();
                                                        $category_names             =   implode(',',$cat_names_arr);
                                                    @endphp
                                                    <p>{{ $category_names }}</p>
                                                </td>
                                                <td>
                                                    <p>{{ $enquiries->email }}</p>
                                                    <p>{{ $enquiries->phone_number }}</p>
                                                </td>
                                                <td>{{ $enquiries->emirates }}</td>
                                                <td>
                                                    @if($enquiries->price_sheet != null)
                                                        <a class="btn btn-primary" target="_blank" href="{{ asset($enquiries->price_sheet) }}">View</a>
                                                    @else
                                                        <p> - </p>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                                <tr>
                                                    <th colspan="7">
                                                        <center>No Enquiries Yet</center>
                                                    </th>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>
                                    @if(isset($enquiry_list)&& count($enquiry_list) > 0)
                                    <center>
                                        {!! $enquiry_list->appends(Illuminate\Support\Facades\Request::except('page'))->links()
                                        !!}
                                    </center>
                                    @endif
                                </div>
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
