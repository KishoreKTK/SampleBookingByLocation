<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MAP Testing</title>
</head>
<style>
    /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
#map {
  height: 80%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
table, th, td {
  border: 1px solid black;
}
</style>
<body>
    <h1>Delivery Area Map Testing</h1>
    <div id="map"></div>
    <div id="current"></div>

    <table></table>
    <p>Output</p>
    <table style="width:100%">
        <tr>
            <th style="width: 50%">Latitude</th>
            <td id="latitude_id"></td>
        </tr>
        <tr>
            <th>Longitude</th>
            <td id="longitude_id"></td>
        </tr>
        <tr>
            <th>Address</th>
            <td id="address_id"></td>
        </tr>
        <input type="hidden" id="delivery_area_field" value="">
    </table>
    {{-- <p id="output"></p> --}}
</body>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<script type="text/javascript" src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}
            &callback=initMap&libraries=drawing&v=weekly&channel=2" async></script>

<script>
// This example requires the Drawing library. Include the libraries=drawing
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&libraries=drawing">
function initMap()
{
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 23.4241, lng: 53.8478 },
        zoom: 9,
        panControl: true,
        zoomControl: false,
        scaleControl: true,
        mapTypeControl:false,
        streetViewControl:true,
        overviewMapControl:true,
        rotateControl:true
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
        // latlng      =
        var pointer_location = new google.maps.LatLng(lat, lng);
        // var pointer_location = new google.maps.LatLng(lat, lng);

        document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';
        document.getElementById('latitude_id').innerHTML = '<span>' + lat + '</span>';
        document.getElementById('longitude_id').innerHTML = '<span>' + lng + '</span>';


        delivery_area_latlng_str    =   document.getElementById("delivery_area_field").value;
        // console.log(delivery_area_latlng_str);
        var delivery_area_coords        =   JSON.parse(delivery_area_latlng_str);

        console.log(delivery_area_coords);
        // return false;
        console.log(pointer_location);

        // return false;
        // console.log("am i coming from up to this>???");
        delivery_area   =   new google.maps.Polygon({ paths: delivery_area_coords });

        // if(google.maps.geometry.poly.containsLocation(pointer_location, delivery_area) == true) {
        //     console.log("yes");
        // } else {
        //     console.log("No");
        // }
        // This is making the Geocode request
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode({ 'latLng': pointer_location },  (results, status) =>{
            var address = (results[0].formatted_address);

            // console.log(address);
            document.getElementById('address_id').innerHTML = '<span>' + address + '</span>';

            var  value=address.split(" - ");

            count=value.length;
            country=value[count-1];
            city=value[count-2];
            area=value[count-3];

            // console.log(country);
            // console.log(city);
            // console.log(area);
            // x.innerHTML = "city name is: " + city;
            // if (status !== google.maps.GeocoderStatus.OK) {
            //     alert(status);
            // }
            // // This is checking to see if the Geoeode Status is OK before proceeding
            // if (status == google.maps.GeocoderStatus.OK) {
            //     console.log(results);

            // }
        });

        // console.log(latlng);
        CheckPointer    =   google.maps.geometry.poly.containsLocation(pointer_location,delivery_area)

        if(CheckPointer){
          alert('Inside Delivery Area');
        } else {
          alert('Outside Delivery Area');
        }
    });

    google.maps.event.addListener(myMarker, 'dragstart', function (evt) {
        document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
    });


    map.setCenter(myMarker.position);

    // Set Marker in Map
    myMarker.setMap(map);

}


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

    return latlngArr
}

</script>
</html>
