<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
is_logged_in(true); // par36 - 11/25/24: makes sure user is logged in
if (!has_role("Admin")) { // par36 - 11/25/24: makes sure only admins can access this page/create games
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
?>

<?php
//TODO handle game fetch
if (isset($_POST["game_title"])) { // par36 - 11/25/24: checks if game_title is set
    $action = $_POST["action"];
    $game = [];
    $search = $_POST["game_title"]; // sets search parameter to the game title
    if ($action === "fetch") { // if someone clicks the fetch button
        $result = fetch_games($search); // search with the game_api

        error_log("Data from API" . var_export($result, true));
        if ($result) {
            $game = $result; // puts api results in game
            foreach ($game as &$g) { // par36 - 11/25/24: sets each api marker to 1 which means it is from an api
                $g['is_api'] = 1;
            }
            unset($g);
        }
    } else if ($action === "create") { // par36 - 11/25/24: if someone clicks the create button 
        foreach ($_POST as $k => $v) { // checks for each key in an array 
            if (!in_array($k, ["game_title", "platforms", "genre", "release_date"])) { // if the array doesn't have the queries
                unset($_POST[$k]); // unset the array (for security)
            }
            $game = [$_POST]; // sets manual data into game variable
            error_log("Cleaned up POST: " . var_export($game, true));
        }
    }
    //insert data
    try {
        insert("IT202-F2024-Games", $game, ["debug" => false, "update_duplicate" => true, "columns_to_update" => ["game_title", "platforms", "genre", "release_date", "is_api"]]);
        flash("Inserted record ", "success"); // par36 - 11/25/24: user-friendly confirmation message
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger"); // par36 - 11/25/24: user-friendly error message
    }
}
//TODO handle manual create game
?>
<div class="container-fluid">
    <h3>Create or Fetch </h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-info" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-info" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <!-- par36 - 11/25/24: shows field for the game title set as text and the button to submit the search request -->
            <?php render_input(["type" => "text", "name" => "game_title", "placeholder" => "Game Title", "label" => "Game Title", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <!-- par36 - 11/25/24: shows fields for the queries all set as text as well as the button to submit the data/create it in the database -->
            <?php render_input(["type" => "text", "name" => "game_title", "placeholder" => "Game Title", "label" => "Game Title", "rules" => ["required" => "required"]]); ?> 
            <?php render_input(["type" => "text", "name" => "platforms", "placeholder" => "Game Platforms", "label" => "Game Platforms", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "genre", "placeholder" => "Game Genre", "label" => "Game Genre", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "release_date", "placeholder" => " Game Release Date", "label" => "Game Release Date", "rules" => ["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>