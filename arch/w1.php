<?php

if (PHP_SAPI !== 'cli') die('cli only');

$url = 'https://waterservices.usgs.gov/nwis/iv/?format=json&sites=02334430';
$json = file_get_contents($url);
$o = json_decode($json, 1);

$i = 0;
while(isset( $o['value']['timeSeries'][$i  ]['variable']['variableDescription'])) {
    $key  =  $o['value']['timeSeries'][$i  ]['variable']['variableDescription'];
    $name =  $o['value']['timeSeries'][$i  ]['sourceInfo']['siteName'];
    $lat  =  $o['value']['timeSeries'][$i  ]['sourceInfo']['geoLocation']['geogLocation']['latitude'];
    $lon  =  $o['value']['timeSeries'][$i  ]['sourceInfo']['geoLocation']['geogLocation']['longitude'];
    $v    =  $o['value']['timeSeries'][$i  ]['values'][0]['value'][0]['value'];
    $tstr =  $o['value']['timeSeries'][$i  ]['values'][0]['value'][0]['dateTime'];

    echo "$name $key $v $tstr\n";

    $i++;
} /* sample output: 
CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA Temperature, water, degrees Celsius 9.6 2020-02-15T23:30:00.000-05:00
CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA Discharge, cubic feet per second 10900 2020-02-15T23:30:00.000-05:00
CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA Gage height, feet 5.23 2020-02-15T23:30:00.000-05:00
 */