<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type:application/json");
    
    require_once("riotapi.php");

    $summonerid = $_GET['summonerid'];
    $region = $_GET['region'];

    $riot = new RiotAPI;

    echo $riot->getSummonerRank($summonerid, $region);

?>