<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["symbol"])) {
    //function=GLOBAL_QUOTE&symbol=MSFT&datatype=json
    $data = ["search" => $_GET["symbol"], "datatype" => "json"];
    $endpoint = "https://rawg-video-games-database.p.rapidapi.com/games";
    $isRapidAPI = true;
    $rapidAPIHost = "rawg-video-games-database.p.rapidapi.com";
    $result = get($endpoint, "API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    //example of cached data to save the quotas, don't forget to comment out the get() if using the cached data for testing
    /* $result = ["status" => 200, "response" => '{
    "Global Quote": {
        "01. symbol": "MSFT",
        "02. open": "420.1100",
        "03. high": "422.3800",
        "04. low": "417.8400",
        "05. price": "421.4400",
        "06. volume": "17861855",
        "07. latest trading day": "2024-04-02",
        "08. previous close": "424.5700",
        "09. change": "-3.1300",
        "10. change percent": "-0.7372%"
    }
}'];*/
    error_log("Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
 
        $games = [];
        foreach($result as $g){
          $game = [
            "name"=>$g["game_title"],
            "release"=>$g["release_date"],
            "genre"=>$g["genre"]
            ];
          $platforms = [];
          foreach($g["platforms"] as $plat){
            array_push($platforms, $plat["platform"]["name"]);
          }
          $game["platforms"] = "," . join($platforms); // par36 - 11/21/24: joins together all the different platforms
          // continued mapping
        }
        $result = $games; 
    }
    } else {
        $result = [];
    }

    if (isset($result)) { // par36 - 11/20/24: test data mapping
        try {
            insert("IT202-F2024-Games", $result, $opts = ["debug" => false, "update_duplicate" => false, "columns_to_update" => ["game_title", "platforms", "genre", "release_date"]]);
            flash("Inserted record", "success");
        } catch (PDOException $e) {
            error_log("Something broke with the query" . var_export($e, true));
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
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $game) : ?>
                <pre>
                    <?php var_export($game);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");