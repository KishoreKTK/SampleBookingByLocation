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
                    <h5>Edit Profile</h5>
                </div>
            </div>
            <div class="col-md-4">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/profile">Profile</a></li>
                    <li class="breadcrumb-item"><a>Edit</a></li>
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
            <h5>Edit Profile</h5>
        </div>
        <div class="card-block">
                     @include('includes.msg')
                     @if(isset($approved)&& $approved==0)
                     <p style="color:red"> Your profile is waiting for approval.</p>
                     @endif

            <!-- <form id="main" method="post" action="/" novalidate> -->
                {!! Form::open(["url"=>env('ADMIN_URL')."/salon/profile/edit","id"=>"main","method"=>"post",'files'=> true]) !!}
            @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text("name",$salon->name,["placeholder"=>"Name","id"=>"name","class"=>"form-control"]) !!}


                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Sub Title</label>
                    <div class="col-sm-10">
                        {!! Form::text("sub_title",$salon->sub_title,["placeholder"=>"Sub Title","id"=>"name","class"=>"form-control"]) !!}


                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        {!! Form::text("email",$salon->email,["placeholder"=>"Email","id"=>"name","class"=>"form-control","readonly"=>"true"]) !!}


                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Contact Number</label>
                    <div class="col-sm-10">
                        {!! Form::text("phone",$salon->phone,["placeholder"=>"Contact Number","id"=>"name","class"=>"form-control"]) !!}


                    </div>
                </div>
                 <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Manager's Contact Number</label>
                    <div class="col-sm-10">
                        {!! Form::text("manager_phone",$salon->manager_phone,["placeholder"=>"Manager's Contact Number","id"=>"name","class"=>"form-control"]) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Minimum Booking Amount</label>
                    <div class="col-sm-10">
                        {!! Form::text("minimum_order_amt",$salon->minimum_order_amt,["placeholder"=>"Minimum Booking Amount","id"=>"min_bkng_amt","class"=>"form-control"]) !!}
                    </div>
                </div>

                 <!--  @if(isset($salon->logo)&& $salon->logo!='')
                <div class="form-group row">
                <div class="col-md-2 align-self-center"><label class="">Logo</label></div>
                <div class="col-md-1 align-self-center">   <img class="img-fluid" src="{{$salon['logo']}}"
                            alt="..."></div>

                </div>
                <div class="row">
                    <div class="col-md-2"><label class="">Change Logo</label></div>
                    <div class="col-md-10">

                        <div class="input-group">

                            <div class="custom-file">
                                <input type="file" name="logo" class="custom-file-input" id="" aria-describedby="">

                                <label class="custom-file-label" for="">Choose
                                    Logo</label>
                            </div>

                        </div>
                    </div>

                </div>
                @else
                <div class="row">
                <div class="col-md-2"><label class="">Add a logo</label></div>
                <div class="col-md-10">
                    <div class="input-group">
                        <div class="custom-file">
                             <input type="file" name="logo" class="custom-file-input" id="" aria-describedby="">

                            <label class="custom-file-label" for="">Add
                                Logo</label>
                        </div>

                    </div>
                </div>

            </div>
            @endif -->
           @if(isset($salon->image)&& $salon->image!='')
            <div class="form-group row">
            <div class="col-md-2 align-self-center"><label class="">Main image</label></div>
            <div class="col-md-1 align-self-center">   <img class="img-fluid" src="{{$salon['image']}}"
                        alt="..."></div>

            </div>
            <div class="row">
                <div class="col-md-2"><label class="">Change Main image</label></div>
                <div class="col-md-10">

                    <div class="input-group">

                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="" aria-describedby="">

                            <label class="custom-file-label" for="">Choose
                                Image</label>
                        </div>

                    </div>
                    <small class="form-text text-muted">Formats: JPG PNG, Sizes: 500x 500px, Max 400KB</small>

                </div>

            </div>
            @else

            <div class="row">
                <div class="col-md-2"><label class="">Add image</label></div>
                <div class="col-md-10">

                    <div class="input-group">

                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="" aria-describedby="">

                            <label class="custom-file-label" for="">Choose
                                Image</label>
                        </div>

                    </div>
                    <small class="form-text text-muted">Add upto 5 images: Formats: JPG PNG, Sizes: 500x 500px, Max 400KB</small>

                </div>

            </div>

            @endif
            <br>

                @if(isset($subs)&&count($subs)>0)

                @foreach($subs as $index=>$value)

                <div class="form-group row">
                        <div class="col-md-2 align-self-center"><label class="">Added image</label></div>
                        <div class="col-md-1 align-self-center">   <img class="img-fluid" src="{{$value['image']}}"
                                alt="..."></div>
                        <div class="col-md-1 align-self-center">
                                <a href="{{env('ADMIN_URL')}}/salon/image/delete?id={{$value['id']}}" class="text-muted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-delete-alt"></i></a>
                        </div>

                    </div>

                @endforeach
                @endif
                @if(isset($count)&&($count)<5)

                    <div class="row after-add-more">
                    <div class="col-md-2"><label class="">Add an image</label></div>
                    <div class="col-md-10">

                        <div class="input-group">

                            <div class="custom-file">
                                <input type="file" name="sub_images[]" class="custom-file-input" id=""
                                    aria-describedby="">
                                <label class="custom-file-label" for="">Choose
                                    Image</label>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-success add-more" type="button" id=""><i
                                        class="fas fa-plus"></i></button>
                            </div>
                        </div>
                         <small class="form-text text-muted">Add upto 5 images: Formats: JPG PNG, Sizes: 1366 x 768px or 1280 x 720px, Max 400KB</small>

                    </div>

                    </div>




                <div class="copy hide">
                        <div class="row copydiv">
                            <div class="col-md-2"><label class="">Add an image</label></div>

                            <div class="col-md-10">

                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="sub_images[]" class="custom-file-input" id=""
                                            aria-describedby="">
                                        <label class="custom-file-label" for="">Choose
                                            Image</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-danger remove-one" type="button" id=""><i
                                                class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <br>
                    @endif
               <div class="form-group row">
                <label class="col-sm-2 col-control-label">Categories</label>
                <div class="col-sm-10">
                    @foreach($categories as $index=>$value)

                  <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="{{$index}}" name="categories[]" value="{{$value->id}}" @if (in_array($value->id,$c_categories)) checked @endif>
                    <label class="custom-control-label" for="{{$index}}">{{$value->category}}</label>
                  </div>
                    @endforeach

                </div>
            </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        {!! Form::textarea("description",$salon->description,["placeholder"=>"Description","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
              <!--   <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Reschedule Policy</label>
                    <div class="col-sm-10">
                        {!! Form::textarea("reschedule_policy",$salon->reschedule_policy,["placeholder"=>"Reschedule Policy","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div> -->
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cancellation Policy</label>
                    <div class="col-sm-10">
                        {!! Form::textarea("cancellation_policy",$salon->cancellation_policy,["placeholder"=>"Cancellation Policy","id"=>"name","class"=>"form-control"]) !!}

                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">City</label>
                    <div class="col-sm-10">
                        {!! Form::text("city",$salon->city,["placeholder"=>"City","id"=>"name","class"=>"form-control"]) !!}


                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Country</label>
                    <div class="col-sm-10">
                        {!! Form::select("country",$countries,$salon->country_id,["placeholder"=>"Country","id"=>"name","class"=>"form-control"]) !!}


                    </div>
                </div>

                <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="address_address">Location</label>
                <div class="col-sm-10">
                {!! Form::text("location",$salon->location,["id"=>"address-input","class"=>"form-control map-input", "placeholder"=>"Location"]) !!}
                {!! Form::hidden('latitude', $salon->latitude,["id"=>"address-latitude"]) !!}
                {!! Form::hidden('longitude', $salon->longitude,["id"=>"address-longitude"]) !!}
                </div>
                </div>

                <div id="address-map-container" style="width:100%;height:400px; ">
                    <div style="width: 100%; height: 100%" id="address-map"></div>
                </div>
                <br>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Latitude</label>
                    <div class="col-sm-10">
                        {!! Form::text("latitude",$salon->latitude,["placeholder"=>"Latitude", 'readonly' => 'true',"id"=>"address-latitude","class"=>"form-control"]) !!}

                    </div>
                </div>
                 <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Longitude</label>
                    <div class="col-sm-10">
                        {!! Form::text("longitude",$salon->longitude,["placeholder"=>"Longitude", 'readonly' => 'true',"id"=>"address-longitude","class"=>"form-control"]) !!}

                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-10">
                        @if(isset($approved)&& $approved>0)
                         {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
                         @else
                         {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0","disabled"=>"disabled"]) !!}
                         @endif
                    </div>
                </div>
                 {!! Form::close() !!}
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

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>


    <!-- <script src="{{env('ADMIN_URL')}}/public/assets/plugin/js/mapInput.js"></script> -->
    <script type="text/javascript">

         var lat = {!! $salon->latitude !!};
         var long = {!! $salon->longitude !!};
    function initialize() {


    $('form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    const locationInputs = document.getElementsByClassName("map-input");

    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    for (let i = 0; i < locationInputs.length; i++) {

        const input = locationInputs[i];
        const fieldKey = input.id.replace("-input", "");
        const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

        const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || lat;
        const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || long;

        const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
            center: {lat: latitude, lng: longitude},
            zoom: 13
        });
        const marker = new google.maps.Marker({
            map: map,
            position: {lat: latitude, lng: longitude},
        });

        marker.setVisible(isEdit);

        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.key = fieldKey;
        autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
    }

    for (let i = 0; i < autocompletes.length; i++) {
        const input = autocompletes[i].input;
        const autocomplete = autocompletes[i].autocomplete;
        const map = autocompletes[i].map;
        const marker = autocompletes[i].marker;

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            marker.setVisible(false);
            const place = autocomplete.getPlace();

            geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    setLocationCoordinates(autocomplete.key, lat, lng);
                }
            });

            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                input.value = "";
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

        });
    }
}

function setLocationCoordinates(key, lat, lng) {
    const latitudeField = document.getElementById(key + "-" + "latitude");
    const longitudeField = document.getElementById(key + "-" + "longitude");
    latitudeField.value = lat;
    longitudeField.value = lng;
}

</script>
<script type="text/javascript">

$(document).ready(function() {
var count = {!! str_replace("'", "\'", json_encode($count))!!};

  $(".add-more").click(function(){
    if(count<4)
    {
    var html = $(".copy").html();
      $(".after-add-more").after(html);
      count= count+1;
    }


  });



    $("body").on("click", ".remove-one", function () {
            $(this).parents('.copydiv').remove();
             count= count-1;
        });



});


</script>
@stop
