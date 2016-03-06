<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Science and Engineering Day</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" href="leaflet.css" />
  <link rel="stylesheet" href="leaflet.label.css" />
  <link rel="stylesheet" href="sciengmap.css" />
  <script src="polyfill.js"></script>
  <script src="leaflet.js"></script>
  <script src="leaflet.label.js"></script>
  <script src="jquery-1.12.0.min.js"></script>
  <script src="sciengmap.js"></script>
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
    <?php include( "info.php" ); ?>
  </div>
  <div id="locations"></div>
  <div id="activities"></div>
</div>
<div class="tab" id="map"></div>
</script>

</body></html>


