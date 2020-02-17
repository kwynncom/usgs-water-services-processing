<?php

require_once('/opt/kwynn/kwutils.php');

  class dao_water extends dao_generic {
    const db = 'water';
	function __construct() {
	    parent::__construct(self::db);
	    $this->wcoll    = $this->client->selectCollection(self::db, 'water');
	    $this->scoll    = $this->client->selectCollection(self::db, 'sites');
      }
      
      public function getRecent() { return $this->wcoll->findOne(['callts' => ['$gt' => time() - 1800]], ['sort' => ['callts' => -1]]); }
      
      public function put($dat)   { $this->wcoll->insertOne($dat); }
      
      public function up($id, $dat) {  $this->wcoll->upsert(['_id' => $id], $dat);     }
      
      public function putSite($dat) {  $this->scoll->upsert(['siteCode' => $dat['siteCode']], $dat);      }
      
      public function getunp($vin) {  return $this->wcoll->find(['datv' => ['$ne' => $vin]] )->toArray();      }
      
      public function updat($vin) {
	  if ($vin !== 3) return;
	  $this->wcoll->updateMany([], ['$unset' => ['siteCode' => '']]);
	  
      }
  }