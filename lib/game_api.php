<?php

function fetch_games($search)
{
    $data = ["search" => $search, "datatype" => "json"];
    $endpoint = "https://rawg-video-games-database.p.rapidapi.com/games";
    $isRapidAPI = true;
    $rapidAPIHost = "rawg-video-games-database.p.rapidapi.com";
    $result = get($endpoint, "API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
        $games = []; // par36 - 11/22/24: Creates games array
        foreach ($result["results"] as $g) { // gets each result
            if($g["released"] == null) {
                $g["released"] = ""; 
            }
            $game = [
                "game_title" => $g["name"], 
                "release_date" => $g["released"], 
                "genre" => isset($g["genres"]) ? implode(", ", array_map(fn($genre) => $genre["name"], $g["genres"])) : "", 
            ];  // ^ puts all of the genres together to be inserted
            $platforms = [];
            if (isset($g["platforms"])) {
                foreach ($g["platforms"] as $plat) { // goes through all of the platforms in the platforms category
                    $platforms[] = $plat["platform"]["name"]; // maps platforms to database
                }
            }
            $game["platforms"] = implode(", ", $platforms); // puts all of the platforms together to be inserted
            foreach($game as $key=>$value) {
                if(is_array($value) || is_object($value)) {
                    throw new Exception("$key has an invalid value $value for " . var_export($game, true));
                }
            }
            $games[] = $game; // populates game array with results
        }
        $result = $games; // gives game results to result
        return $result;
    }
}