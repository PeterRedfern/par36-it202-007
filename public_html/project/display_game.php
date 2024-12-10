<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

$id = se($_GET, "id", -1, false);
if (isset($_POST["delete"])) { // par36 - 11/23/24: makes delete query
    if ($id > -1) { // checks for all ids that are 1 or greater
        try {
            header("Location: " . get_url("admin/delete_game.php?id=$id")); // redirects to delete_game when clicked
        } catch (PDOException $e) {
            error_log("Error Deleting: " . var_export($e, true)); // logs error message
            flash("Delete Unsuccessful", "danger"); // sends error message to user
        }
    }
}

$id = se($_GET, "id", -1, false);
if (isset($_POST["edit"])) { // par36 - 11/23/24: makes edit query
    if ($id > -1) { // checks for all ids that are 1 or greater
        try {
            header("Location: " . get_url("admin/edit_game.php?id=$id")); // redirects to edit_game when clicked
        } catch (PDOException $e) {
            error_log("Error redirecting: " . var_export($e, true)); // logs error message
            flash("Redirect Unsuccessful", "danger"); // sends error message to user
        }
    }
}

$id = se($_GET, "id", -1, false);
if (isset($_POST["game"])) {
    error_log("POST data: " . var_export($_POST, true));
    foreach ($_POST as $k => $v) {
        if (!in_array($k, ["id", "game_title", "platforms", "genre","release_date"])) {
            unset($_POST[$k]);
        }
        $games = $_POST;
        error_log("Inserting data: " . var_export($game, true));
        error_log("Cleaned up POST: " . var_export($games, true));
    }
}
$game = [];
if ($id > -1) { // par36 - 11/25/24: gets id for many page purposes
    //fetch
    $db = getDB();
    $query = "SELECT id, game_title, platforms, genre, release_date  FROM `IT202_F2024_Games` WHERE id = :id";
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
    die(header("Location: $BASE_PATH" . "list_games.php")); // redirects to list_games when failed
}

if ($game) {
    $form = [ // par36 - 11/25/24: makes the forms (readonly) that holds the game information
        ["type" => "text", "name" => "game_title", "placeholder" => "Game Title", "label" => "Game Title", "rules" => ["readonly" => "readonly"]],
        ["type" => "text", "name" => "platforms", "placeholder" => "Game Platforms", "label" => "Game Platforms", "rules" => ["readonly" => "readonly"]],
        ["type" => "text", "name" => "genre", "placeholder" => "Game Genre", "label" => "Game Genre", "rules" => ["readonly" => "readonly"]],
        ["type" => "text", "name" => "release_date", "placeholder" => " Game Release Date", "label" => "Game Release Date", "rules" => ["readonly" => "readonly"]],
    ];
    $keys = array_keys($game);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $game[$v["name"]];
        }
    }
}
?>
<div class="container-fluid">
    <h3>Display Game</h3>
    <form method="POST">
        <?php foreach ($form as $k => $v) {
            render_input($v);
        } 
        ?>
    </form>
</div>

<div class="container-fluid"> <!-- par36 - 11/24/24: creates container for edit button -->
    <form method="POST"> <!-- creates form for request -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($game['id']); ?>" /> <!-- takes in id secretly with specialchars for security/anti-tampering -->
        <input type="hidden" name="edit"/> <!-- sets edit name -->
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Edit (Admin Only)"]); ?>
    </form>
</div>

<div class="container-fluid"> <!-- par36 - 11/23/24: creates container for delete button -->
    <form method="POST"> <!-- creates form for request -->
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($game['id']); ?>" /> <!-- takes in id secretly with specialchars for security/anti-tampering -->
        <input type="hidden" name="delete"/> <!-- sets delete name -->
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Delete (Admin Only)"]); ?>
    </form>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>