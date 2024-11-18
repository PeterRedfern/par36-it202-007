<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php
$id = se($_GET, "id", -1, false);
//TODO handle game fetch
if (isset($_POST["symbol"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["game_title", "publisher", "genre","release_year"])) {
            unset($_POST[$k]);
        }
        $quote = $_POST;
        error_log("Cleaned up POST: " . var_export($quote, true));
    }
    //insert data
    $db = getDB();
    $query = "UPDATE `IT202-F2024-Games` SET ";

    $params = [];
    //per record
    foreach ($quote as $k => $v) {

        if ($params) {
            $query .= ",";
        }
        //be sure $k is trusted as this is a source of sql injection
        $query .= "$k=:$k";
        $params[":$k"] = $v;
    }

    $query .= " WHERE id = :id";
    $params[":id"] = $id;
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Updated record ", "success");
    } catch (PDOException $e) {
        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
}

$game = [];
if ($id > -1) {
    //fetch
    $db = getDB();
    $query = "SELECT game_title, open, publisher, genre, release_year  FROM `IT202-F2024-Games` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $game = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    die(header("Location:" . get_url("admin/list_games.php")));
}
if ($game) {
    $form = [
        ["type" => "text", "name" => "title", "placeholder" => "Game Title", "label" => "Game Title", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "publisher", "placeholder" => "Game Publisher", "label" => "Game Publisher", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "genre", "placeholder" => "Game Genre", "label" => "Game Genre", "rules" => ["required" => "required"]],
        ["type" => "number", "name" => "release", "placeholder" => " Game Release Year", "label" => "Game Release Year", "rules" => ["required" => "required"]],
    ];
    $keys = array_keys($game);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $game[$v["name"]];
        }
    }
}
//TODO handle manual create game
?>
<div class="container-fluid">
    <h3>Edit Game</h3>
    <form method="POST">
        <?php foreach ($form as $k => $v) {

            render_input($v);
        } ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Update"]); ?>
    </form>

</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>