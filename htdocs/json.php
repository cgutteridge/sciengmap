<?php
$data = json_decode( file_get_contents( "data.json" ));
$routes = json_decode( file_get_contents( "routes.json" ));
print json_encode( array( "data"=>$data, "routes"=>$routes ));
