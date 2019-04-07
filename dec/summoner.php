<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type:application/json");
    
    require_once("riotapi.php");

    $summoner = $_GET['summoner'];
    $region = $_GET['region'];

    $riot = new RiotAPI;

    echo $riot->getSummonerInfo($summoner, $region);

?>