<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["symbol"])) {
    $data = ["search" => $_GET["symbol"], "datatype" => "json"];
    $endpoint = "https://rawg-video-games-database.p.rapidapi.com/games";
    $isRapidAPI = true;
    $rapidAPIHost = "rawg-video-games-database.p.rapidapi.com";
    $result = get($endpoint, "API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
        $games = []; // par36 - 11/22/24: Creates games array
        foreach ($result["results"] as $g) { // gets each result
            $game = [
                "game_title" => $g["name"], 
                "release_date" => $g["released"], 
                "genre" => isset($g["genres"]) ? implode(", ", array_map(fn($genre) => $genre["name"], $g["genres"])) : "", 
            ];  // ^ puts all of the genres together to be inserted
            $platforms = [];
            if (isset($g["platforms"])) {
                foreach ($g["platforms"] as $plat) { // goes through all of the platforms in the platforms category
                    $platforms[] = $plat["platforms"]["name"]; // maps platforms to database
                }
            }
            $game["platforms"] = implode(", ", $platforms); // puts all of the platforms together to be inserted
            
            $games[] = $game; // populates game array with results
        }
        $result = $games; // gives game results to result
    } else {
        $result = [];
    }
}
if (isset($result)) {
    try {
        insert("IT202-F2024-Games", $result, $opts = ["debug" => false, "update_duplicate" => false, "columns_to_update" => ["game_title", "platforms", "genre", "release_date"]]);
        flash("Inserted record", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query: " . var_export($e, true));
        flash("An error occurred", "danger");
    }
}
?>
<div class="container-fluid">
    <h1>Game Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Game" />
        </div>
    </form>
    <div class="row">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $game) : ?>
                <pre>
                    <?php var_export($game); ?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php require(__DIR__ . "/../../partials/flash.php");