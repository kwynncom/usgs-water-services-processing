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

    $url = self::urlBase . $ain['sid'];
    $dbr = $this->regGet($url); unset($url);
    $o = json_decode($dbr['res'], 1); unset($dbr);
    
    $dat = [];
    
    $i = 0;
    while(isset( $o['value']['timeSeries'][$i  ]['variable']['variableDescription'])) {
	
	$a = $o['value']['timeSeries'][$i  ];

	foreach($ain['vars'] as $var) {
	    $j=0;
	    $b = $a;
	    while (isset($var['key'][$j]) && isset($b[$var['key'][$j]])) {
		$b =     $b[$var['key'][$j]];
		if ($j === count($var['key']) - 1) {
		    if (isset($var['off'])) {
			if (is_string($var['off'])) kwas($b === $var['off'], 'key mismatch 1 - water - 924');
			else {
			    kwas(isset($var['off'][$b]), 'key fail 2 - water - 942');
			    $key =      $var['off'][$b];
			    
			    $lat  =  $o['value']['timeSeries'][$i  ]['sourceInfo']['geoLocation']['geogLocation']['latitude'];
			    $lon  =  $o['value']['timeSeries'][$i  ]['sourceInfo']['geoLocation']['geogLocation']['longitude'];
			    $v    =  $o['value']['timeSeries'][$i  ]['values'][0]['value'][0]['value'];
			    $tstr =  $o['value']['timeSeries'][$i  ]['values'][0]['value'][0]['dateTime'];
			    
			    $dat[$key] = $v; unset($v, $key);
			    $dat['obsats'] = $tstr;
			    $dat['obsatts'] = strtotime($tstr); unset($tstr);
			    unset($lat, $lon);
			}
			
		    }
		    
		    if (isset($var['my']) && is_array($var['my'])) {
			$a3 =      $var['my'];
			$key = key($a3);
			$val = reset($a3);
			$dat[$key] = $val; unset($key, $val, $a3);
		    }
		}
		$j++;
	    } 
	} unset($var);
	$i++;
    } unset($i, $j, $o, $b, $ain, $var, $a);
    
    $x = 5;
}
    private function getList() {
	$a = [	'sid' => '02334430', 
		'vars' => [
			    [	'key' => ['sourceInfo', 'siteName'],
				'off' => 'CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA', 
				'my' => ['siteName' => 'ChattDam']
			    ],
			    [	'key' => ['variable', 'variableDescription'],
				'off' => 
				    ['Temperature, water, degrees Celsius' => 'C',
				    'Discharge, cubic feet per second' => 'cfs',
				    'Gage height, feet' => 'ft']
			    ],
		    ]
	    ];
	
	return $a;
    }
    
    private function regGet($url) {
	$dbr = $this->dao->getRecent();
	if ($dbr) return $dbr;
	
	$res = $this->getActual($url);
	$this->dao->put($res);
	
	return $res;
    }
    
    private function getActual($url) {
	$b   = microtime(1);
	$json = file_get_contents($url); unset($url);
	$e = microtime(1);
	$callts      = intval(round($b));
	$callElapsed = $e - $b; unset($e, $b);
	
	unset($http_response_header); // note!  This has an "Expires" for later use
		
	return get_defined_vars();
    }
}