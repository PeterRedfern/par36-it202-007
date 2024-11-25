<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
?>

<?php
//TODO handle game fetch
if (isset($_POST["game_title"])) { 
    $action = $_POST["action"];
    $game = [];
    $search = $_POST["game_title"]; 
    if ($action === "fetch") {
        $result = fetch_games($search); 

        error_log("Data from API" . var_export($result, true));
        if ($result) {
            $game = $result;
            foreach ($game as &$g) {
                $g['is_api'] = 1;
            }
            unset($g);
        }
    } else if ($action === "create") {
        foreach ($_POST as $k => $v) {
            if (!in_array($k, ["game_title", "platforms", "genre", "release_date"])) {
                unset($_POST[$k]);
            }
            $game = [$_POST];
            error_log("Cleaned up POST: " . var_export($game, true));
        }
    }
    //insert data
    try {
        insert("IT202-F2024-Games", $game, ["debug" => false, "update_duplicate" => true, "columns_to_update" => ["game_title", "platforms", "genre", "release_date", "is_api"]]);
        flash("Inserted record ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
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
            <?php render_input(["type" => "text", "name" => "game_title", "placeholder" => "Game Title", "label" => "Game Title", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">

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