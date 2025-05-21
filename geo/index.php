<?php
include(__DIR__ . "/geoip.inc");
$gi = geoip_open("GeoIP.dat",GEOIP_STANDARD);

$record = geoip_country_code_by_addr($gi, $public_ip);
?> 
