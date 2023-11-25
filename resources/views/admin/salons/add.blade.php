@extends('layouts.master')

@section('title')
Dashboard
@stop
@section('pagestyle')
<style>
/* #myMap {
   height: 350px;
   width: 680px;
} */
</style>
@endsection
@section('content')
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<div class="pcoded-content">
    <!-- Page-header start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="page-header-title">
                        <h4>Salons</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{env('ADMIN_URL')}}/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salons">Salons</a></li>
                        <li class="breadcrumb-item"><a>Add</a></li>
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
                                    <h5>Add Salons</h5>
                                </div>
                                <div class="card-block">
                                    @include('includes.msg')
                                    {!! Form::open(["url"=>env('ADMIN_URL')."/salons/add_salon","id"=>"main","method"=>"post",'files'=> true]) !!}
                                    @csrf
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Name</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("name",'',["placeholder"=>"Name","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Sub Title</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("sub_title",'',["placeholder"=>"Sub Title","id"=>"name","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                     <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Commission</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("pricing",'',["placeholder"=>"Commission","id"=>"name","class"=>"form-control"]) !!}

                                            <span class="messages"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("email",'',["placeholder"=>"Email","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Password</label>
                                        <div class="col-sm-10">
                                            {!! Form::password("password",["placeholder"=>"Password","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Confirm Password</label>
                                        <div class="col-sm-10">
                                            {!! Form::password("password_confirmation",["placeholder"=>"Confirm Password","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Contact Number</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("phone",'',["placeholder"=>"Contact Number","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Manager's Contact Number</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("manager_phone",'',["placeholder"=>"Manager's Contact Number","id"=>"name","class"=>"form-control"]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Minimum Booking Amount</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("minimum_order_amt",'',["placeholder"=>"Minimum Booking Amount","id"=>"min_bkng_amt","class"=>"form-control"]) !!}
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-2"><label class="">Choose an image</label></div>
                                        <div class="col-md-10">

                                            <div class="input-group">

                                                <div class="custom-file">
                                                    <input type="file" name="image" class="custom-file-input" id="" aria-describedby="">

                                                    <label class="custom-file-label" for="">Choose
                                                        file</label>
                                                </div>

                                            </div>
                                        <small class="form-text text-muted">Formats: JPG PNG, Sizes: 500x 500px, Max 400KB</small>

                                        </div>

                                    </div>


                                </div>

                                <div class="card-header">
                                    <h5>Add more images</h5>
                                </div>
                                <div class="card-block">

                                    <div class="row after-add-more">

                                        <div class="col-md-2"><label class="">Image</label></div>
                                        <div class="col-md-10">

                                            <div class="input-group">

                                                <div class="custom-file">
                                                    <input type="file" name="sub_images[]" class="custom-file-input" id=""
                                                        aria-describedby="">
                                                    <label class="custom-file-label" for="">Choose
                                                        file</label>
                                                </div>
                                                <div class="input-group-append">
                                                    <button class="btn btn-success add-more" type="button" id=""><i
                                                            class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        <small class="form-text text-muted">Add upto 5 images: Formats: JPG PNG, Sizes: 1366 x 768px or 1280 x 720px, Max 400KB</small>

                                        </div>

                                    </div>
                                    <br>

                                    <div class="copy hide">
                                        <div class="row copydiv">
                                            <div class="col-md-2"><label class="">Image</label></div>
                                            <div class="col-md-10">

                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="sub_images[]" class="custom-file-input" id=""
                                                            aria-describedby="">
                                                        <label class="custom-file-label" for="">Choose
                                                            file</label>
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

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-control-label">Categories</label>
                                        <div class="col-sm-10">
                                            @foreach($categories as $index=>$value)

                                          <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" id="{{$index}}" name="categories[]" value="{{$value->id}}">
                                            <label class="custom-control-label" for="{{$index}}">{{$value->category}}</label>
                                          </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Description</label>
                                        <div class="col-sm-10">
                                            {!! Form::textarea("description",'',["placeholder"=>"Description","class"=>"form-control"]) !!}
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Cancellation Policy</label>
                                        <div class="col-sm-10">
                                            {!! Form::textarea("cancellation_policy",'',["placeholder"=>"Cancellation Policy","id"=>"name","class"=>"form-control"]) !!}

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">City</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("city",'',["placeholder"=>"City","class"=>"form-control"]) !!}
                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Country</label>
                                        <div class="col-sm-10">
                                            {!! Form::select("country",$countries,null,["placeholder"=>"Country","class"=>"form-control"]) !!}

                                            <!-- <span class="messages"></span> -->
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="address_address">Location</label>
                                        <!-- <input type="text" id="address-input" name="address" class="form-control map-input"> -->
                                        <div class="col-sm-10">

                                        {!! Form::text("location",'',["id"=>"address-input","class"=>"form-control map-input", "placeholder"=>"Location"]) !!}
                                        {!! Form::hidden('latitude', '',["id"=>"address-latitude"]) !!}
                                        {!! Form::hidden('longitude', '',["id"=>"address-longitude"]) !!}
                                        </div>
                                        </div>
                                        <div id="address-map-container" style="width:100%;height:400px; ">
                                            <div style="width: 100%; height: 100%" id="address-map"></div>
                                        </div>

                                    {{-- <div class="form-group row">
                                        <label class="col-sm-2 col-form-label" for="address_address">Area</label>
                                        <div class="col-sm-10">
                                            {!! Form::text("location",'',["id"=>"address-input","class"=>"form-control map-input", "placeholder"=>"Area"]) !!}
                                            <!-- <span class="messages"></span> -->
                                        </div>

                                        <input type="hidden" name="latitude" id="address-latitude" value="0" />
                                        <input type="hidden" name="longitude" id="address-longitude" value="0" />
                                    </div>
                                    <div id="address-map-container" style="width:100%;height:400px; ">
                                        <div style="width: 100%; height: 100%" id="map"></div>
                                    </div> --}}
                                    {{-- <div id="address-map-container" style="width:100%;height:400px; ">
                                        <div style="width: 100%; height: 100%" id="address-map"></div>
                                    </div> --}}
                                    {{-- <td id="latitude_id"></td>
                                    <input type="hidden" name="latitude" id="latitude_id" value="">
                                    <input type="hidden" name="longitude" id="longitude_id" value="">
                                    <input type="hidden" name="address" id="address_id" value="">
                                    <input type="hidden" name="delivery_area_coords" id="delivery_area_field" value=""> --}}
                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">
                                            {!! Form::submit('Submit',["class"=>"btn btn-primary m-b-0"]) !!}
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

{{-- callback=initMap&libraries=drawing&v=weekly&channel=2 --}}
<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>


<script type="text/javascript">

    function initialize()
    {
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

            const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
                center: {lat: 23.4241, lng: 53.8478},
                zoom: 13
            });
            const marker = new google.maps.Marker({
                map: map,
                position: {lat: 23.4241, lng: 53.8478},
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

                        console.log(lat);
                        console.log(lng);
                        document.getElementById('latitude_id').value    = lat;
                        document.getElementById('longitude_id').value   = lng;

                        // setLocationCoordinates(autocomplete.key, lat, lng);
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
    // function setLocationCoordinates(key, lat, lng) {
    //     const latitudeField = document.getElementById(key + "-" + "latitude");
    //     const longitudeField = document.getElementById(key + "-" + "longitude");
    //     latitudeField.value = lat;
    //     longitudeField.value = lng;
    // }
</script>

<script type="text/javascript">
    $(document).ready(function()
    {

        var count=1;
        $(".add-more").click(function () {
            if(count<5)
            {
                var html = $(".copy").html();
                $(".after-add-more").after(html);
                count= count+1;

            }
        });


        $("body").on("click", ".remove-one", function () {
            count= count-1;
            $(this).parents('.copydiv').remove();
        });
    });
</script>
@stop
