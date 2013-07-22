var Nadeshiko = Nadeshiko || {};

Nadeshiko.extend = function(src, dst) {
    
}

Nadeshiko.gMap = Nadeshiko.gMap || {};
Nadeshiko.gMap.currentPosition = null; // 現在地
    
var gMap = Nadeshiko.gMap;

gMap.setMapCanvasName = function(mapCanvasName) {
    gMap.mapCanvasName = mapCanvasName;
}
gMap.getMapCanvasName = function() {
    return gMap.mapCanvasName;
}

gMap.direction = {
    getDirection: function() { return this.latlng;},
    setDirection:function(latlng) { this.latlng = latlng;}
}

gMap.getCurrentLatlng = function(position) {
    gMap.currentPosition = position;
    
    var coords = position.coords;
    var latlng = new google.maps.LatLng(
                                    coords.latitude,
                                    coords.longitude
                                    );
    return latlng;
}

gMap.viewCurrentPos = function(position) {
    var gMap = Nadeshiko.gMap;
    var latlng = gMap.getCurrentLatlng(position);

    var options = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var gmap = gMap.createMap(gMap.getMapCanvasName(), options);
    var marker = gMap.createMarker(latlng);    
}

gMap.createMap = function(mapCanvasName, options) {
    var map = new google.maps.Map(document.getElementById(mapCanvasName), options);
    Nadeshiko.gMap.map = map;
    return map;
}

gMap.createMarker = function(latlng) {
    var marker = new google.maps.Marker({
        position:latlng,
        map: Nadeshiko.gMap.map
    });
    return marker;
}

gMap.createInfoWindow = function(contentString) {
    var infoWindow = new google.maps.InfoWindow({ content: contentString});
    return infoWindow;
}

gMap.addListener = function(marker, eventKind, callback) {
    google.maps.event.addListener(marker, eventKind, callback);
}
