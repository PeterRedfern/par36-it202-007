<?php

function fetch_games($symbol)
{
    $data = ["symbol" => $_GET["symbol"], "datatype" => "json"];
    $endpoint = "https://rawg-video-games-database.p.rapidapi.com/games";
    $isRapidAPI = true;
    $rapidAPIHost = "rawg-video-games-database.p.rapidapi.com";
    $result = get($endpoint, "API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
    /*if (isset($result["Global Quote"])) { 
        $quote = $result["Global Quote"];
        $quote = array_reduce(
            array_keys($quote),
            function ($temp, $key) use ($quote) {
                $k = explode(" ", $key)[1];
                if ($k === "change") {
                    $k = "per_change";
                }
                $temp[$k] = str_replace('%', '', $quote[$key]);
                return $temp;
            }
        );
        $result = $quote;
    }
    */
    return $result;
}