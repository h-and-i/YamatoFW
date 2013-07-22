<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>マップ</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <link rel="stylesheet" type="text/css" href="/css/metrobs/css/metro-bootstrap.css">
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript" src="/js/jquery-1.10.0.min.js"></script>
    <script type="text/javascript" src="/js/Nadeshiko.js"></script>
    <script type="text/javascript">
        var positionList = {
            araumadou: new google.maps.LatLng(34.703156,135.497764),
            kankito: new google.maps.LatLng(34.70178,135.499063),
            gatetower: new google.maps.LatLng(34.698896,135.489364),
            library: new google.maps.LatLng(34.695473,135.503054),
            centralpost: new google.maps.LatLng(34.702389,135.496445),
            daibil: new google.maps.LatLng(34.693061,135.493644),
            karaoke: new google.maps.LatLng(34.699813,135.533384),
            cafe: new google.maps.LatLng(34.710847,135.499181),
            shop: new google.maps.LatLng(34.704272,135.497609),
            recicle: new google.maps.LatLng(34.719887,135.478374),
            jusolibrary: new google.maps.LatLng(34.716412,135.481485)
        };
        
        function clickRuteEventSet(id) {
            $('#' + id)
                .click(
                    function(){
                        Nadeshiko.gMap.direction.setDirection(positionList[id]);
                        navigator.geolocation.getCurrentPosition(update);
                        return false;
                    }
                );
        }
        
        $(function(){
            Nadeshiko.gMap.setMapCanvasName("map_canvas");
            
            $('#currentPos')
                .click(
                    function() {
                        navigator.geolocation.getCurrentPosition(Nadeshiko.gMap.viewCurrentPos);
                        return false;
                    }
                );
            
            $('#directList a')
                .each(function(){
                    var myId = $(this).attr("id");
                    clickRuteEventSet(myId);
                });
        });
        
        function update(position) {
            var gMap = Nadeshiko.gMap;
            var latlng = gMap.getCurrentLatlng(position);
            var coords = position.coords;
            
            var options = {
              zoom: 15,
              center: latlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            
            var gmap = gMap.createMap(gMap.getMapCanvasName(), options);
            var marker = gMap.createMarker(latlng);
            var infoWindow = gMap.createInfoWindow('<div> test text</div>');
            
            gMap.addListener(marker, 'click', function(){
                infoWindow.open(gmap, marker);
            });
            
            var directionsService = new google.maps.DirectionsService(); // 地図表示用            
            var directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(gmap);
            directionsDisplay.setPanel(document.getElementById("route"));

            var request = {
                origin: latlng, // 出発地
                destination: Nadeshiko.gMap.direction.getDirection(),  // 目的地
                travelMode: google.maps.DirectionsTravelMode.WALKING // 徒歩で
              };
              directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                  directionsDisplay.setDirections(response); // 描画
                }
              });
            
        }
    </script>

  </head>
  <body>
    <div style="margin:10px;">
        <ul class="nav nav-pills">
            <li><a href="#" id="currentPos">現在位置</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                  Spot list<b class="caret"></b>
              </a>
              <ul class="dropdown-menu" id="directList">
                    <li><a href="#" id="araumadou">あらうま堂</a></li>
                    <li><a href="#" id="kankito">梅田換気塔</a></li>
                    <li><a href="#" id="gatetower">ゲートタワー</a></li>
                    <li><a href="#" id="centralpost">大阪中央郵便局</a></li>
                    <li><a href="#" id="library">大阪府立中之島図書館</a></li>
                    <li><a href="#" id="daibil">中之島ダイビル</a></li>
                    <li><a href="#" id="cafe">cafe</a></li>
                    <li><a href="#" id="shop">雑貨</a></li>
                    <li><a href="#" id="karaoke">京橋カラオケ</a></li>
                    <li><a href="#" id="recicle">リサイクルショップ</a></li>
                    <li><a href="#" id="jusolibrary">図書館</a></li>
              </ul>
            </li>
        </ul>
    </div>

    <div id="map_canvas" style="width:90%; height:500px ; border:solid 5px #ddd; margin:10px;"></div>
    <div id="route"></div>
    
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-tooltip.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-alert.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-button.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-carousel.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-collapse.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-dropdown.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-modal.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-popover.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-scrollspy.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-tab.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-transition.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/bootstrap-typeahead.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/jquery.validate.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/jquery.validate.unobtrusive.js"></script>
    <script type="text/javascript" src="/css/metrobs/docs/metro-bootstrap/metro-docs.js"></script>    
  </body>
</html>