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
  height: 60%;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
</style>
<body>
    <h1>Delivery Area Map Testing</h1>
    <div id="map"></div>
    <p>Deliviery Area Marked</p>
    <p id="output"></p>
</body>


<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
<script type="text/javascript" src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}
            &callback=initMap&libraries=drawing&v=weekly&channel=2" async></script>

{{-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=drawing&v=weekly&channel=2"
async></script> --}}

<script>
// This example requires the Drawing library. Include the libraries=drawing
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&libraries=drawing">
// function initMap() {
// // This example requires the Geometry library. Include the libraries=geometry
// // parameter when you first load the API. For example:
// // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=geometry">

//   const map = new google.maps.Map(
//     document.getElementById("map") as HTMLElement,
//     {
//       center: { lat: 24.886, lng: -70.269 },
//       zoom: 5,
//     }
//   );

//   const triangleCoords = [
//     { lat: 25.774, lng: -80.19 },
//     { lat: 18.466, lng: -66.118 },
//     { lat: 32.321, lng: -64.757 },
//   ];

//   const bermudaTriangle = new google.maps.Polygon({ paths: triangleCoords });

//   google.maps.event.addListener(map, "click", (e) => {
//     const resultColor = google.maps.geometry.poly.containsLocation(
//       e.latLng,
//       bermudaTriangle
//     )
//       ? "blue"
//       : "red";

//     const resultPath = google.maps.geometry.poly.containsLocation(
//       e.latLng,
//       bermudaTriangle
//     )
//       ? // A triangle.
//         "m 0 -1 l 1 2 -2 0 z"
//       : google.maps.SymbolPath.CIRCLE;

//     new google.maps.Marker({
//       position: e.latLng,
//       map,
//       icon: {
//         path: resultPath,
//         fillColor: resultColor,
//         fillOpacity: 0.2,
//         strokeColor: "white",
//         strokeWeight: 0.5,
//         scale: 10,
//       },
//     });


//   });


// }

// This example requires the Geometry library. Include the libraries=geometry
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&libraries=geometry">
    function initMap() {
  const map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 24.886, lng: -70.269 },
    zoom: 5,
  });
  const triangleCoords = [
    { lat: 25.774, lng: -80.19 },
    { lat: 18.466, lng: -66.118 },
    { lat: 32.321, lng: -64.757 },
  ];
  const bermudaTriangle = new google.maps.Polygon({ paths: triangleCoords });

  google.maps.event.addListener(map, "click", (e) => {

    console.log(e.latLng);
    return false;
    const resultColor = google.maps.geometry.poly.containsLocation(
      e.latLng,
      bermudaTriangle
    )
      ? "blue"
      : "red";
    const resultPath = google.maps.geometry.poly.containsLocation(
      e.latLng,
      bermudaTriangle
    )
      ? // A triangle.
        "m 0 -1 l 1 2 -2 0 z"
      : google.maps.SymbolPath.CIRCLE;

    new google.maps.Marker({
      position: e.latLng,
      map,
      icon: {
        path: resultPath,
        fillColor: resultColor,
        fillOpacity: 0.2,
        strokeColor: "white",
        strokeWeight: 0.5,
        scale: 10,
      },
    });
  });
}
</script>
</html>
