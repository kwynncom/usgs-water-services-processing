<?php

if (PHP_SAPI !== 'cli') die('cli only');

require_once('/opt/kwynn/kwutils.php');
require_once('dao.php');

new water();

class water {
    
    const urlBase = 'https://waterservices.usgs.gov/nwis/iv/?format=json&sites=';

    public function __construct()  {
	$this->dao = new dao_water();
	$a = $this->getList();
	$this->getWater($a);
    }
    
private function getWater($ain) {

    $siteID = $ain['sid'];
    $url = self::urlBase . $siteID;
    $dbr = $this->regGet($url);
    $o = json_decode($dbr['res'], 1);
    
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
    }
}

private function getList() {
    $a = [	'sid' => '02334430', 
	    'vars' => [
			[	'key' => ['sourceInfo', 'siteName'],
			    'off' => 'CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA', 
			    'my' => 'ChattDam'
			],
			[	'key' => ['variable', 'variableDescription'],
			    'multi' => [
				['off' => 'Temperature, water, degrees Celsius', 'my' => 'C'],
				['off' => 'Discharge, cubic feet per second', 'my' => 'cfs'],
				['off' => 'Gage height, feet', 'my' => 'ft', 'active' => 0]
			      ]
			],
			[	'key' => ['values', 0, 'value', 0, 'value'],
			    'var' => 1
			],
			[	'key' => ['values', 0, 'value', 0, 'dateTime'],
			    'var' => 1,
			    'my'  => 'dates'
			    ]

		]
	];

    return $a;
}

private function regGet($url) {
    $dbr = $this->dao->getRecent();
    if ($dbr) return $dbr;

    $res = $this->getActual($url);
    $this->dao->put($res);
}

private function getActual($url) {
    $b   = microtime(1);
    $res = file_get_contents($url); unset($url);
    $e = microtime(1);
    $callTS = intval(round($b));
    $callTime = $e - $b; unset($e, $b);

    unset($http_response_header); // note!  This has an "Expires" for later use

    return get_defined_vars();
}
}