@extends('layouts.master_salon')

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
                            <a href="{{env('ADMIN_URL')}}/salon/dashboard"> <i class="fa fa-home"></i> </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{env('ADMIN_URL')}}/salon/">Salons</a></li>
                        <li class="breadcrumb-item"><a>Delivery Area</a></li>
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
                                    <h5>{{ $result['name'] }} - Delivery Area</h5>
                                </div>
                                <form action="{{ env('ADMIN_URL')."/salon/update_deliveryarea" }}" method="POST">
                                    @csrf
                                    @include('includes.msg')
                                    <div class="card-block">
                                        <div id="address-map-container" style="width:100%;height:400px; ">
                                            <div style="width: 100%; height: 100%" id="map"></div>
                                        </div>
                                        <input type="hidden" name="salon_id" id="salon_id" value="{{ $result['id'] }}">
                                        <input type="hidden" id="latitude_id" value="{{ $result['lat'] }}">
                                        <input type="hidden" id="longitude_id" value="{{ $result['lng'] }}">
                                        <input type="hidden" name="delivery_area_coords" id="delivery_area_field" value="{{ $result['area'] }}">
                                        <br>
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-primary float-right">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
        const salon_lat = parseFloat($("#latitude_id").val());
        const salon_lng = parseFloat($("#longitude_id").val());
        var delivery_area_latlng_str    =   document.getElementById("delivery_area_field").value;

        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: salon_lat, lng: salon_lng },
            zoom: 8,
            // panControl: true,
            // zoomControl: false,
            // scaleControl: true,
            // mapTypeControl:false,
            // streetViewControl:true,
            // overviewMapControl:true,
            // rotateControl:true
        });

        // Add Marker
        var myMarker = new google.maps.Marker({
            position: new google.maps.LatLng(salon_lat, salon_lng),
            draggable: true
        });

        if(delivery_area_latlng_str){
            const deliveryarea = JSON.parse(delivery_area_latlng_str);

            var shapeCoordinates    = [];
            $.each(deliveryarea,function(key,val){
                console.log("testgin - "+ val.lat);
                shapeCoordinates.push(new google.maps.LatLng(parseFloat(val.lat), parseFloat(val.lng)))
            });

            // var shapeCoordinates = [
            //     new google.maps.LatLng(23.4241, 53.8478),
            //     new google.maps.LatLng(23.190518, 53.530518),
            //     new google.maps.LatLng(23.013807, 53.67334)
            // ];
        }
        else {
            first_lat   = salon_lat + 0.200;
            second_lat  = salon_lat + 0.4400;
            third_lat   = salon_lat + 0.6200;
            first_lng   = salon_lng + 0.800;
            second_lng  = salon_lng + 0.2300;
            third_lng   = salon_lng + 0.29700;

            var shapeCoordinates = [
                new google.maps.LatLng(first_lat, first_lng),
                new google.maps.LatLng(second_lat, second_lng),
                new google.maps.LatLng(third_lat, third_lng)
            ];
        }

        console.log(shapeCoordinates);
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
    myMarker.setMap(map);

    // Set Map
    draggablePolygon.setMap(map);

    google.maps.event.addListener(draggablePolygon, "dragend", Getpolygoncoordinates);
    google.maps.event.addListener(draggablePolygon.getPath(), "insert_at", Getpolygoncoordinates);
    google.maps.event.addListener(draggablePolygon.getPath(), "remove_at", Getpolygoncoordinates);
    google.maps.event.addListener(draggablePolygon.getPath(), "set_at", Getpolygoncoordinates);

    // delivery_area_coords        =   JSON.parse(delivery_area_latlng_str);
    // console.log(delivery_area_coords);
    // var pointer_latlng = new google.maps.LatLng(lat, lng);

    // if(google.maps.geometry.poly.containsLocation(pointer_latlng, delivery_area) == true) {
    //     alert("You can now Continue");
    // } else {
    //     alert("Please Place Pointer Inside Delivery Area");
    // }


   // map.setCenter(myMarker.position);

    // // Set Marker in Map

    // google.maps.event.addListener(myMarker, 'dragend', function (evt)
    // {
    //     var lat     =   evt.latLng.lat().toFixed(3);
    //     var lng     =   evt.latLng.lng().toFixed(3);

    //     // document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';
    //     document.getElementById('latitude_id').value    = lat;
    //     document.getElementById('longitude_id').value   = lng;


    //     // console.log(delivery_area_latlng_str);

    //     delivery_area   =   new google.maps.Polygon({ paths: delivery_area_coords });
    //     console.log(latlng)

    //     // This is making the Geocode request
    //     var geocoder = new google.maps.Geocoder();

    //     geocoder.geocode({ 'latLng': latlng },  (results, status) =>{
    //         var address = (results[0].formatted_address);

    //         console.log(address);
    //         document.getElementById('address_id').value = address;

    //         var  value=address.split(" - ");

    //         count   =   value.length;
    //         country =   value[count-1];
    //         city    =   value[count-2];
    //         area    =   value[count-3];

    //         console.log(country);
    //         console.log(city);
    //     });
    // });


    // google.maps.event.addListener(myMarker, 'dragstart', function (evt) {
    //     document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
    // });




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
