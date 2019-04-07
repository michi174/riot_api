<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type:application/json");
    
    require_once("riotapi.php");



    $matchId = $_GET['matchId'];
    $region = $_GET['region'];

    $riot = new RiotAPI;
    
    echo $riot->getMatchDetails($matchId, $region);

?>