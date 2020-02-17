<?php

require_once('/opt/kwynn/kwutils.php');
require_once('dao.php');

new water();

class water {
    
    const urlBase = 'https://waterservices.usgs.gov/nwis/iv/?format=json&sites=';
    const datv = 5;

public function __construct()  {
    $this->dao = new dao_water();
    $this->popList();
    $this->dao->updat(self::datv);
    $this->call();
    $this->doDat();
}

private function call() {
    $siteCode = key($this->wvars['sites']);
    $url = self::urlBase . $siteCode; // only 1 for now
    $dbr = $this->regGet($url); unset($url);
}

private function doDat() {
    $res = $this->dao->getunp(self::datv);
    foreach($res as $row) $this->doWater($row['json'], $row['_id']);
}

private function doWater($json, $dbid) {

    $otomy = $this->wvars['otomy'];

    $o = json_decode($json, 1); unset($json);
    
    $dat = [];
    
    $i = 0;
    while(isset( $o['value']['timeSeries'][$i  ]['variable']['variableDescription'])) {
	
	$a = $o['value']['timeSeries'][$i  ];
	
	$key  =  $a['variable']['variableDescription'];
	$oname =  $a['sourceInfo']['siteName'];
	$lat  =  $a['sourceInfo']['geoLocation']['geogLocation']['latitude'];
	$lon  =  $a['sourceInfo']['geoLocation']['geogLocation']['longitude'];
	$v    =  $a['values'][0]['value'][0]['value'];
	$tstr =  $a['values'][0]['value'][0]['dateTime'];
	
	$mykey = $otomy[$key];
	
	if ($mykey === 'C') {
	    $v = floatval($v);
	    $dat['F'] = intval(round($v * (9/5) + 32)); 
	}
	
	$dat[$mykey] = $v; unset($key, $mykey, $v);
	
	$dat['obsats'] = $tstr;
	$dat['obsatts'] = strtotime($tstr); unset($tstr);
	
	$myname = $otomy[$oname];
	$dat['siteName'] = $myname;
	
	$siteCode = $a['sourceInfo']['siteCode'][0]['value'];
	
	$this->setSiteInfo(get_defined_vars());
	
	unset($lat, $lon, $oname);

	$i++;
    } unset($otomy, $i, $a, $o, $siteCode);
    
    $dat['datv'] = self::datv;
    
    $this->dao->up($dbid, $dat);
    $x = 72;
}

private function setSiteInfo($din) {
    static $fs = ['myname', 'oname', 'siteCode', 'lat', 'lon'];
    foreach($fs as $f) {
	kwas(isset($din[$f]), $f . ' undefined site info water 1108');
	$r[$f] = $din[$f];
    }
    
    $this->dao->putSite($r);

}

    private function popList() {
	
	$a['sites'] = [ '02334430' => [
			'official' => 'CHATTAHOOCHEE RIVER AT BUFORD DAM, NEAR BUFORD, GA',
			'my' => 'ChattDam' ]];
		
	$a['vars'] =    [   'Temperature, water, degrees Celsius' => 'C',
			    'Discharge, cubic feet per second' => 'cfs',
			    'Gage height, feet' => 'ft'];
	
	foreach($a['sites'] as $site) $a['otomy'][$site['official']] = $site['my'];
	foreach($a['vars']  as $o => $my) $a['otomy'][$o] = $my;
	$this->wvars = $a;
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