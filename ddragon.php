<?php

use RiotApi\RiotAPI;

header("Access-Control-Allow-Origin: *");
header("Content-type:application/json; charset=utf-8");

require_once 'riotApi.php';


$queryString = $_SERVER['QUERY_STRING'];

$api = new RiotAPI();
$api->setOptions($queryString);
echo $api->getStaticData();


?>