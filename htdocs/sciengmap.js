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
  			url: 'json.php',
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

	function gotData(alldata) {
		var data = alldata.data;
		var routes = alldata.routes;
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
			// iterate over start & end times
			for( var t=0; t<10; ++t ) {
				if( activity["start"+t] && activity["end"+t] ) {
					var start = activity["start"+t];
					var end = activity["end"+t];

					var event = { start: start, end: end, activity: id };
					if( !times[ start ] ) { times[ start ] = []; }
					times[ start ].push( event );
					if( !activity.times[ start ] ) { activity.times[ start ] = []; }
					activity.times[start].push( event );
					if( activity.location && locations[activity.location] ) {
						var location = locations[activity.location];
						if( !location.times[ start ] ) { location.times[ start ] = []; }
						location.times[start].push( event );
					}
				}
			}
		}
		var formap = {
			all:"Great for everyone",
			primary:"Great for primary",
			secondary:"Great for secondary" };

		// render locations	
		$('#locations').html('');
		for( var l_id in locations ) {
			var loc = locations[l_id];
			var tab = $('<div class="tab text-tab" id="location-'+l_id+'"></div>');
			tab.append( $('<h2>'+loc.title+'</h2>' ));
			tab.append( $('<p>'+loc.description+'</p>' ));
tab.append( $('<div></div>').text( JSON.stringify( loc  ) ));
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
					if( activity.room ) {
						tr.append( $("<td></td>").text("Sublocation: "+activity.room+". "));
					}
					tr.append( $("<td></td>").text(formap[activity.for]+"."));
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
		var act_ids = Object.keys( activities );
		act_ids.sort( function(a,b) { return activities[a].title>activities[b].title; } );
		for( var i=0; i<act_ids.length; ++i ) {
			var a_id = act_ids[i];
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
			} else {
				tab.append( "<p>MISSING LOCATION for "+a_id+"</p>" )
			}
			tab.append( $('<p>'+act.description+'</p>' ));
			var table = $('<table></table>');
			for( var start in act.times) {
				for( var time_i in act.times[start] ) {
					if( loc ) {
						var event = loc.times[start][time_i];
						var tr = $("<tr></tr>");
						tr.append( $("<td></td>").text(start));
						tr.append( $("<td></td>").text("-"));
						tr.append( $("<td></td>").text(event.end));
						table.append( tr );
					}
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
		for( var route_id in routes ) {
			var route = routes[route_id];
			var polyline = L.polyline(route.route, {dashArray: route.dash, color: route.color}).addTo(map);
			bounds.extend( polyline.getBounds() );
		}
		map.fitBounds(bounds);
		
	}


	function failedData( jqXHR, textStatus, errorThrown ) {
		updating = false;
		alert( "URK: "+textStatus );
	}
});
