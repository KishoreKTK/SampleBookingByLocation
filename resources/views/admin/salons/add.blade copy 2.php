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
                                  <!--   <div class="form-group row">
                                        <div class="col-md-2"><label class="">Choose a logo</label></div>
                                        <div class="col-md-10">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                     <input type="file" name="logo" class="custom-file-input" id="" aria-describedby="">
                                                    <label class="custom-file-label" for="">Choose
                                                        file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
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
                                    </div>
                                    <td id="latitude_id"></td>
                                    <input type="hidden" name="latitude" id="latitude_id" value="">
                                    <input type="hidden" name="longitude" id="longitude_id" value="">
                                    <input type="hidden" name="address" id="address_id" value="">
                                    <input type="hidden" name="delivery_area_coords" id="delivery_area_field" value="">
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

<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<script type="text/javascript" src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}
            &callback=initMap&libraries=drawing&v=weekly&channel=2" async></script>


<script type="text/javascript">
 function initMap()
{
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 23.4241, lng: 53.8478 },
        zoom: 8,
        // panControl: true,
        // zoomControl: false,
        // scaleControl: true,
        // mapTypeControl:false,
        // streetViewControl:true,
        // overviewMapControl:true,
        // rotateControl:true
    });

    // Set Delivery Area
    var shapeCoordinates = [
        new google.maps.LatLng(23.4241, 53.8478),
        new google.maps.LatLng(23.190518, 53.530518),
        new google.maps.LatLng(23.013807, 53.67334)
    ];

    // Construct the polygon
    draggablePolygon = new google.maps.Polygon({
        paths: shapeCoordinates,
        draggable: true,
        editable: true,
        strokeColor: '',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#ADFF2F',
        fillOpacity: 0.5
    });

    // Set Map
    draggablePolygon.setMap(map);


    google.maps.event.addListener(draggablePolygon, "dragend", Getpolygoncoordinates);
    google.maps.event.addListener(draggablePolygon.getPath(), "insert_at", Getpolygoncoordinates);
    google.maps.event.addListener(draggablePolygon.getPath(), "remove_at", Getpolygoncoordinates);
    google.maps.event.addListener(draggablePolygon.getPath(), "set_at", Getpolygoncoordinates);


    // Add Marker
    var myMarker = new google.maps.Marker({
        position: new google.maps.LatLng(23.4241, 53.8478),
        draggable: true
    });


    google.maps.event.addListener(myMarker, 'dragend', function (evt)
    {
        var lat     =   evt.latLng.lat().toFixed(3);
        var lng     =   evt.latLng.lng().toFixed(3);

        // document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';
        document.getElementById('latitude_id').value    = lat;
        document.getElementById('longitude_id').value   = lng;

        var latlng = new google.maps.LatLng(lat, lng);

        delivery_area_latlng_str    =   document.getElementById("delivery_area_field").value;
        // console.log(delivery_area_latlng_str);
        delivery_area_coords        =   JSON.parse(delivery_area_latlng_str);
        console.log(delivery_area_coords);
        delivery_area   =   new google.maps.Polygon({ paths: delivery_area_coords });
        console.log(latlng)
        // if(google.maps.geometry.poly.containsLocation(latlng, delivery_area) == true) {
        //     alert("You can now Continue");
        // } else {
        //     alert("Please Place Pointer Inside Delivery Area");
        // }

        // This is making the Geocode request
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({ 'latLng': latlng },  (results, status) =>{
            var address = (results[0].formatted_address);

            console.log(address);
            document.getElementById('address_id').value = address;

            var  value=address.split(" - ");

            count   =   value.length;
            country =   value[count-1];
            city    =   value[count-2];
            area    =   value[count-3];

            console.log(country);
            console.log(city);
        });
    });


    // google.maps.event.addListener(myMarker, 'dragstart', function (evt) {
    //     document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
    // });


    map.setCenter(myMarker.position);

    // Set Marker in Map
    myMarker.setMap(map);

}


// function Getpolygoncoordinates()
// {
//     var len = draggablePolygon.getPath().getLength();
//     var strArray = "";
//     let latlngArr = [];

//     // console.log(len);
//     for (var i = 0; i < len; i++) {
//         strArray += draggablePolygon.getPath().getAt(i).toUrlValue(5) + " | ";
//     }

//     // console.log(strArray);
//     var myArray = strArray.split(" | ")
//     myArray.forEach(function(key, value)
//     {
//       if (key) {
//         // console.log(key);
//         var latlngstr   = key.split(',')
//         latlngArr.push({ lat: parseInt(latlngstr[0]), lng: parseInt(latlngstr[1]) })
//       }
//     });

//     document.getElementById('delivery_area_field').value = JSON.stringify(latlngArr);

//     // return latlngArr
// }


function Getpolygoncoordinates()
{
    var len = draggablePolygon.getPath().getLength();
    var strArray = "";
    let latlngArr = [];

    // console.log(len);
    for (var i = 0; i < len; i++) {
        strArray += draggablePolygon.getPath().getAt(i).toUrlValue(5) + " | ";
    }

    // console.log(strArray);
    var myArray = strArray.split(" | ")
    myArray.forEach(function(key, value)
    {
      if (key) {
        // console.log(key);
        var latlngstr   = key.split(',')
        latlngArr.push({ lat: parseFloat(latlngstr[0]), lng: parseFloat(latlngstr[1]) })
      }
    });

    document.getElementById('delivery_area_field').value = JSON.stringify(latlngArr);

    // return latlngArr
}


</script>

<script type="text/javascript">
    $(document).ready(function()
    {
        $('form').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });


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
