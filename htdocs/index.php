<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>My Map</title>
  <link rel="stylesheet" href="leaflet.css" />
  <script src="leaflet.js"></script>
  <script src="jquery-1.12.0.min.js"></script>

  <style>
html, body {
        margin:0;
}
#map {
        position: absolute;
        width: 100%;
        height: 100%;
}
  </style>
<script>
$(document).ready( function() {
	var map = L.map('map',{
  		maxZoom:19,
	});
		L.tileLayer('http://tiles.maps.southampton.ac.uk/map/{z}/{x}/{y}.png',{
    		attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, University of Southampton',
	}).addTo(map);


	updateMap();

	var updating = false;
	var locations = {};
	var location_ids = {};
	var activities = {};
	var activity_ids = {};
	function updateMap() {
		if( updating ) { return; }
		updating = true;
		$.ajax({
  			dataType: "json",
  			url: 'data.json',
  			success: gotData,
			error: failedData 
		});
	}

	var icon = L.icon( {
        	iconUrl: 'http://data.southampton.ac.uk/map-icons/Culture-and-Entertainment/museum_science.png',
        	iconSize: [32, 37],
        	iconAnchor: [16, 37]
	} );

	function gotData(data) {
		updating = false;
		locations = {};
		location_ids = [];
		for( var i=0; i<data.locations.length; i++ ) {
			var id = data.locations[i].Location;
			locations[id] = data.locations[i];
			location_ids.push( id );
//"Location":"53","Title":"Fibrjjje-pulling towers clean room tour and more activities","Comment":"","Lat":"50.94","
//Long":"-1.40","Footprints":"purple"}
		}

		var bounds = L.latLngBounds([]);
		for( var l_id in locations ) {
			loc = locations[l_id];
        		var marker = L.marker([loc.Lat,loc.Long],{ icon: icon } );
        		marker.addTo(map);
        		marker.bindPopup( "Hello" );
        		bounds.extend( [loc.Lat, loc.Long] );
		}
		map.fitBounds(bounds);
		
	}


	function failedData( jqXHR, textStatus, errorThrown ) {
		updating = false;
		alert( "URK: "+textStatus );
	}
});
</script>

</head>
<body>
<div id="map"></div>
<script language="JavaScript">


</script>

</body></html>

