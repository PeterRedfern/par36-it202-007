<?php

function get_genres()
{
    $genre = [];
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT DISTINCT id,genre FROM IT202_F2024_Games");
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            $genre =  array_map(fn($t) => [$t["id"] => $t["genre"]], $r);
            array_unshift($genre, ["-1" => "Select"]);
        }
    } catch (PDOException $e) {
        error_log("Error fetching genres: " . var_export($e, true));
    }
    return $genre;
}

function get_platforms()
{
    $platforms = [];
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT DISTINCT id,platforms FROM IT202_F2024_Games");
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            $platforms = array_map(fn($t) => [$t["id"] => $t["name"]], $r);
            array_unshift($platforms, ["-1" => "Select"]);
        }
    } catch (PDOException $e) {
        error_log("Error fetching providers: " . var_export($e, true));
    }
    return $platforms;
}