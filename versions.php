<?php
use RiotApi\RiotAPI;

header("Access-Control-Allow-Origin: *");
header("Content-type:application/json; charset=utf-8");

require_once 'riotApi.php';

$api= new RiotAPI();

echo $api->getJSONFromURL("https://ddragon.leagueoflegends.com/api/versions.json");

?>
