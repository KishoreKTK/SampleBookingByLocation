<html>
<head>
    <title>Google Maps Draw Polygon Get Coordinates</title>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR-KEY"></script>
    <script type="text/javascript">
        var draggablePolygon; function InitMap() {
            var location = new google.maps.LatLng(28.620585, 77.228609);

            var mapOptions = {
                zoom: 7,
                center: location,
                mapTypeId: google.maps.MapTypeId.RoadMap
            };

            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

            var shapeCoordinates = [
                new google.maps.LatLng(28.525416, 79.870605),
                new google.maps.LatLng(27.190518, 77.530518),
                new google.maps.LatLng(29.013807, 77.67334)
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
        }

        function Getpolygoncoordinates() {
            var len = draggablePolygon.getPath().getLength();
            var strArray = "";
            for (var i = 0; i < len; i++) {
                strArray += draggablePolygon.getPath().getAt(i).toUrlValue(5) + "<br>";
            }
            document.getElementById('info').innerHTML = strArray;
            console.log(strArray);
        }

    </script>
</head>
<body onload="InitMap();Getpolygoncoordinates();">
    <h2>Google Maps Draw Polygon Get Coordinates - Demo</h2>
    <div id="map-canvas" style="height: 400px; width: auto;"></div>
    <h4>Updated Coordinates (X,Y)</h4>
    <div id="info" style="position:absolute; color:red; font-family: Arial; height:200px; font-size: 12px;"></div>
</body>
</html>
