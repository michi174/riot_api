<?php
use RiotApi\RiotAPI;

header("Access-Control-Allow-Origin: *");
header("Content-type:application/json; charset=utf-8");

require 'riotApi.php';

$link = "http://michi-pc/steamclient/common/roit_api/?name=R�z�r&region=euw&lang=de_AT&api=summoners&method=by-name";

$queryString = $_SERVER['QUERY_STRING'];


$api = new RiotAPI($queryString);
$url = $api->execute();

//echo $url;
echo $api->getJSONFromURL($url);

?>
