<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-type:application/json");
    
    require_once("riotapi.php");

    $accountId = $_GET['accountId'];
    $region = $_GET['region'];

    $riot = new RiotAPI;
    
    echo $riot->getMatchList($accountId, $region);

?>