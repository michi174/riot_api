<?php
    require_once 'riotApi.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-type:application/json");
   

    const MAX_MATCHES = 10;
    
    $api = new RiotApi\RiotAPI();  


    $region = (isset($_GET["region"])) ? $_GET["region"] : "euw";
    $accountId = (isset($_GET["accountId"])) ? $_GET["accountId"] : "";

    $api_url = "http://".$_SERVER['HTTP_HOST']."/";

    if(isset($_GET["m"]))
    {
        foreach($_GET["m"] as $id => $gameId)
        {
            $matches[$id] = ['gameId' => $gameId];
        }
        //var_dump($matches);
    }
    else
    {
        $matches = json_decode($api->getJSONFromURL($api_url."index.php?api=matches&method=by-puuid&puuid=".$accountId), true)["matches"];
    }

    
    $player_data = null;

    $detailed_matches = Array();
    $match_detailed_data = Array();
    $win = false;
    $kills = 0;
    $deaths = 0;
    $assists = 0;
    $gold = 0;
    $minions = 0;
    $level = 1;
    $duration = 0;
    $sumspell1 = 0;
    $sumspell2 = 0;
    $perkPrimary = 0;
    $perkSecondary = 0;
    $teamId = 0;
    $teamkills = 0;
    $killParticipation = 0;
    $item0 = 0;
    $item1 = 0;
    $item2 = 0;
    $item3 = 0;
    $item4 = 0;
    $item5 = 0;
    $item6 = 0;

    $urls = Array();

    

    foreach($matches as $id => $match_data)
    {
        if($id >= MAX_MATCHES)
        {
            break;
        }

        $urls[$id] = $api_url."index.php?api=matches&method=match&matchid=".$match_data["gameId"];

    }

    
    
    
    $matches_new = $api->getResult($urls);

    //$matches_new = $api->singleRequest($urls[0]);

    //var_dump($matches_new);
    

    foreach($matches as $matchid => $match_data)
    {
        //die(var_dump($matches_new));
        //die(var_dump($matches_new[$matchid]["body"]));
        $match_detailed_data = json_decode($matches_new[$matchid]["body"], true);

        /*DEBUG Info:
         * 
         * $match_detailed_json = json_encode($match_detailed_data);
         * echo $match_detailed_json;
         */

        if(is_array($match_detailed_data))
        {
            $players = $match_detailed_data["participantIdentities"];
            $players_match_data = $match_detailed_data["participants"];
            $searched_playerId = null;
            $partId = null;

            foreach($players as $playerId => $playerData)
            {
                $searched_playerId = $playerId;
                $partId = $playerData["participantId"];

                if($playerData["player"]["accountId"] == $accountId)
                {
                    break;
                }
            }


            $win = $players_match_data[$searched_playerId]["stats"]["win"];
            $kills = $players_match_data[$searched_playerId]["stats"]["kills"];
            $deaths = $players_match_data[$searched_playerId]["stats"]["deaths"];
            $assists = $players_match_data[$searched_playerId]["stats"]["assists"];
            $gold = $players_match_data[$searched_playerId]["stats"]["goldEarned"];
            $minions = ($players_match_data[$searched_playerId]["stats"]["totalMinionsKilled"]+$players_match_data[$searched_playerId]["stats"]["neutralMinionsKilled"]);
            $level  = $players_match_data[$searched_playerId]["stats"]["champLevel"];
            $sumspell1  = $players_match_data[$searched_playerId]["spell1Id"];
            $sumspell2  = $players_match_data[$searched_playerId]["spell2Id"];
            $perkPrimary = $players_match_data[$searched_playerId]["stats"]["perk0"];
            $perkSecondary = $players_match_data[$searched_playerId]["stats"]["perkSubStyle"];
            $duration = $match_detailed_data["gameDuration"];
            $teamId = $players_match_data[$searched_playerId]["teamId"];
            $item0 = $players_match_data[$searched_playerId]["stats"]["item0"];
            $item1 = $players_match_data[$searched_playerId]["stats"]["item1"];
            $item2 = $players_match_data[$searched_playerId]["stats"]["item2"];
            $item3 = $players_match_data[$searched_playerId]["stats"]["item3"];
            $item4 = $players_match_data[$searched_playerId]["stats"]["item4"];
            $item5 = $players_match_data[$searched_playerId]["stats"]["item5"];
            $item6 = $players_match_data[$searched_playerId]["stats"]["item6"];
            $champion = $players_match_data[$searched_playerId]["championId"];
            

            $teamkills = 0;

            foreach($match_detailed_data["participants"] as $participant => $participant_data)
            {
                if($participant_data["teamId"] == $teamId)
                {
                    $teamkills += $participant_data["stats"]["kills"];
                }
            }
            $killParticipation = ($teamkills > 0) ? round(($kills+$assists) * 100 / $teamkills) : 0;

            $detailed_matches[$matchid] = $match_data;
            $detailed_matches[$matchid]["win"] = $win;
            $detailed_matches[$matchid]["kills"] = $kills;
            $detailed_matches[$matchid]["assists"] = $assists;
            $detailed_matches[$matchid]["deaths"] = $deaths;
            $detailed_matches[$matchid]["gold"] = $gold;
            $detailed_matches[$matchid]["minions"] = $minions;
            $detailed_matches[$matchid]["level"] = $level;
            $detailed_matches[$matchid]["gameDuration"] = $duration;
            $detailed_matches[$matchid]["spell1Id"] = $sumspell1;
            $detailed_matches[$matchid]["spell2Id"] = $sumspell2;
            $detailed_matches[$matchid]["perkPrimaryStyle"] = $perkPrimary;
            $detailed_matches[$matchid]["perkSubStyle"] = $perkSecondary;
            $detailed_matches[$matchid]["teamkills"] = $teamkills;
            $detailed_matches[$matchid]["killParticipation"] = $killParticipation;
            $detailed_matches[$matchid]["item0"] = $item0;
            $detailed_matches[$matchid]["item1"] = $item1;
            $detailed_matches[$matchid]["item2"] = $item2;
            $detailed_matches[$matchid]["item3"] = $item3;
            $detailed_matches[$matchid]["item4"] = $item4;
            $detailed_matches[$matchid]["item5"] = $item5;
            $detailed_matches[$matchid]["item6"] = $item6;
            $detailed_matches[$matchid]["raw"] = $match_detailed_data;
            $detailed_matches[$matchid]["partId"] = $partId;
            $detailed_matches[$matchid]["champion"] = $champion;
            $detailed_matches[$matchid]["accountId"] = $accountId;
        }
        
        if($matchid === count($matches_new)-1)
        {
            break;
        }
    }

    echo json_encode($detailed_matches);
?>