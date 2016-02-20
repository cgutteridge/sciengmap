<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Science and Engineering Day</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" href="leaflet.css" />
  <link rel="stylesheet" href="leaflet.label.css" />
  <script src="polyfill.js"></script>
  <script src="leaflet.js"></script>
  <script src="leaflet.label.js"></script>
  <script src="jquery-1.12.0.min.js"></script>

  <style>
html, body {
        margin:0;
}
#map, #mainpane {
	border-top: solid black 2px;
        position: absolute;
        width: 100%;
        height: 90%;
	top: 10%;
	overflow-y: auto;
}
.tab {
}
#acts {
	background-color: red;
}
.tab-button {
        position: absolute;
	width: 33.333%;
	height: 10%;
	top: 0%;
	font-size: 120%;
	text-align: center;
	display: table-cell;
	vertical-align: bottom;
	background-color: #eee;
	border: solid 2px black;
}
.tab-button:hover {
	background-color: #aaa;
}
.tab-button-inner {
	padding-top: 2%;
}
.tab-button.selected {
	background-color: #005c84;
	color: white;
}
#map-show {
	left: 0;
}
#actslist-show {
	left: 33.333%;
}
#info-show {
	left: 66.666%;
}
.link {
	background-color: #ccc;
	border: solid 1px #999;
	border-radius: 1em;
	padding: 0.5em;
	margin-bottom: 0.2em;
	font-weight: bold;
	cursor: pointer;
}
.link:hover {
	background-color: #aaa;
}
.text-tab {
	padding: 2%;
}
  </style>
</head>
<body>
<div class="tab-button" id="map-show"><div class='tab-button-inner'>MAP</div></div>
<div class="tab-button" id="actslist-show"><div class='tab-button-inner'>ACTIVITIES</div></div>
<div class="tab-button" id="info-show"><div class='tab-button-inner'>INFO</div></div>
<div id="mainpane">
  <div class="tab text-tab" id="actslist">
    <h2>Activities</h2> 
  </div>
  <div class="tab text-tab" id="info">
    <h2>Science and Engineering Day</h2>
    <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p> <p>Information...</p>
  </div>
  <div id="locations"></div>
  <div id="activities"></div>
</div>
<div class="tab" id="map"></div>

<script>
$(document).ready( function() {
	var map = L.map('map',{
  		maxZoom:19,
	});
	L.tileLayer('http://tiles.maps.southampton.ac.uk/map/{z}/{x}/{y}.png',{
    		attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, University of Southampton',
	}).addTo(map);

	function showTab( tab ) {
		$('.tab').hide();
		$('.tab-button').removeClass( 'selected' );
		$('#'+tab).show();
		$('#'+tab+'-show').addClass( 'selected' );
	}
	$('#actslist-show').click( function(){showTab("actslist");} );	
	$('#info-show').click( function(){showTab("info");} );
	$('#map-show').click( function(){showTab("map");} );

	updateMap();
	showTab("map");

	var updating = false;
	var locations = {};
	var activities = {};
	var times = {};
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
    		labelAnchor: [16, -18],
        	iconAnchor: [16, 37]
	} );

	function gotData(data) {
		updating = false;
		locations = {};
		activities = {};
		times = {};
		for( var i=0; i<data.locations.length; i++ ) {
			var id = data.locations[i].location;
			locations[id] = data.locations[i];
			locations[id].activities = {};
			locations[id].times = {};
		}
		for( var i=0; i<data.activities.length; i++ ) {
			var activity = data.activities[i];
			var id = activity.activity;
			activities[id] = activity;
			activities[id].times = {};

			// add activities to location
			if( activity.location && locations[activity.location] ) {
				locations[activity.location].activities[id] = activity;
			}
		}
		for( var i=0; i<data.times.length; i++ ) {
			var event = data.times[i];
			if( !times[ event.start ] ) { 
				times[ event.start ] = [];
			}
			times[ event.start ].push( event );

			if( activities[event.activity] ) {
				if( !activities[event.activity].times[ event.start ] ) { 
					activities[event.activity].times[ event.start ] = [];
				}
				activities[event.activity].times[ event.start ].push( event );

				var location = activities[event.activity].location;
				if( locations[location] ) {
					if( !locations[location].times[ event.start ] ) { 
						locations[location].times[ event.start ] = [];
					}
					locations[location].times[ event.start ].push( event );
				}

			}
		}
		var formap = {
			all:"Great for everyone",
			primary:"Great for primary",
			secondary:"Great for secondary" };
	
		$('#locations').html('');
		for( var l_id in locations ) {
			var loc = locations[l_id];
			var tab = $('<div class="tab text-tab" id="location-'+l_id+'"></div>');
			tab.append( $('<h2>'+loc.title+'</h2>' ));
			tab.append( $('<p>'+loc.description+'</p>' ));

			var table = $('<table></table>');
			for( var start in loc.times) {
				for( var i in loc.times[start] ) {
					var event = loc.times[start][i];
					var activity = activities[event.activity];
					var tr = $("<tr></tr>");
					tr.append( $("<td></td>").text(start));
					tr.append( $("<td></td>").text("-"));
					tr.append( $("<td></td>").text(event.end));
					tr.append( $("<td class='link'></td>").text(activity.title));
					tr.append( $("<td></td>").text(activity.room));
					tr.append( $("<td></td>").text(formap[activity.for]));
					table.append( tr );
					tr.click( 
						function() { showTab(this); }.bind("activity-"+activity.activity) 
					);
				}
			}
			tab.append( table );

			tab.hide();
			$('#locations').append(tab);
		}
			
		$('#activities').html('');
		
		for( var a_id in activities ) {
			var act = activities[a_id];
			var loc = locations[act.location];
			var tab = $('<div class="tab text-tab" id="activity-'+a_id+'"></div>');
			tab.append( $('<h2>'+act.title+'</h2>' ));
			tab.append( $("<p>").text(formap[act.for]));
			if( loc ) {
				var loctext = loc.title;
				if( act.room ) { loctext += " "+act.room; }
				tab.append( $('<p class="link">Location: '+loctext+'</p>' ).click( 
					function() { showTab(this); }.bind("location-"+act.location) ));
			}
			tab.append( $('<p>'+act.description+'</p>' ));

			var table = $('<table></table>');
			for( var start in act.times) {
				for( var i in act.times[start] ) {
					var event = loc.times[start][i];
					var tr = $("<tr></tr>");
					tr.append( $("<td></td>").text(start));
					tr.append( $("<td></td>").text("-"));
					tr.append( $("<td></td>").text(event.end));
					table.append( tr );
				}
			}
			tab.append( table );

			tab.hide();
			$('#activities').append(tab);

			$('#actslist').append( 
				$('<div class="link"></div>').text( act.title ).click(
					function() { showTab(this); }.bind("activity-"+act.activity) 
				)
			);
		}
			
			
			

		var bounds = L.latLngBounds([]);
		for( var l_id in locations ) {
			var loc = locations[l_id];

			var markerOpts = {};
			markerOpts.riseOnHover = true;
			markerOpts.icon = icon;
        		var marker = L.marker([loc.lat,loc.long],markerOpts);

			//var popupOpts = {};
		  	//var popup = L.popup(popupOpts);
			//popup.setContent( "<div>"+loc.description+"</div>" );
        		//marker.bindPopup( popup );

			var labelOpts = {};
  			labelOpts.direction = 'right';
  			labelOpts.noHide = true;
  			var label = "<strong>"+loc.title+"</strong>"; 
        		marker.bindLabel( label, labelOpts );

			marker.on( "click", function() { showTab(this); }.bind("location-"+l_id) );
			
        		bounds.extend( [loc.lat, loc.long] );
        		marker.addTo(map);
		}
		map.fitBounds(bounds);
		
	}


	function failedData( jqXHR, textStatus, errorThrown ) {
		updating = false;
		alert( "URK: "+textStatus );
	}
});
</script>

</body></html>

