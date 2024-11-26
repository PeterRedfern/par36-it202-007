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
if (isset($_POST["game_title"])) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["game_title", "platforms", "genre", "release_date"])) {
            unset($_POST[$k]);
        }
        $games = $_POST;
        error_log("Cleaned up POST: " . var_export($games, true));
    }
    //insert data
    try {
        insert("IT202-F2024-Games", $games, ["debug" => false, "update_duplicate" => true, "columns_to_update" => ["game_title", "platforms", "genre", "release_date"]]);
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
    $query = "SELECT id, game_title, platforms, genre, release_date  FROM `IT202-F2024-Games` WHERE id = :id";
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

if (isset($_POST["delete"])) { // par36 - 11/23/24: makes delete query for database
    if ($id > -1) { // checks for all ids that are 1 or greater
        $db = getDB(); // gets db
        $query = "DELETE FROM `IT202-F2024-Games` WHERE id = :id"; // checks db for id to delete
        try {
            $deletereq = $db->prepare($query); // prepares delete query
            $deletereq->execute([":id" => $id]); // executes query
            flash("Delete Successful", "success"); // sends user friendly confirmation message
            die(header("Location: " . get_url("admin/list_games.php"))); // redirects to list_games after delete
        } catch (PDOException $e) {
            error_log("Error deleting game: " . var_export($e, true)); // logs error message
            flash("Delete Unsuccessful", "danger"); // sends error message to user
        }
    }
}
if ($game) {
    $form = [
        ["type" => "text", "name" => "game_title", "placeholder" => "Game Title", "label" => "Game Title", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "platforms", "placeholder" => "Game Platforms", "label" => "Game Platforms", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "genre", "placeholder" => "Game Genre", "label" => "Game Genre", "rules" => ["required" => "required"]],
        ["type" => "text", "name" => "release_date", "placeholder" => " Game Release Date", "label" => "Game Release Date", "rules" => ["required" => "required"]],
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
        <?php render_button(["type" => "submit", "text" => "Update"]); ?>
    </form>

</div>

<div class="container-fluid"> <!-- par36 - 11/23/24: creates container for delete button -->
    <form method="POST"> <!-- creates form to send to db -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($game['id']); ?>" /> <!-- takes in id secretly with specialchars for security/anti-tampering -->
        <input type="hidden" name="delete" value="1" /> <!-- sends delete by setting value to 1 -->
        <?php render_button(["text" => "Delete", "type" => "submit"]); ?> <!-- renders delete button using render_button -->
    </form>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>