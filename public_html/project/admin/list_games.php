<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
is_logged_in(true); // par36 - 11/25/24: makes sure user is logged in
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <div> <!-- par36 - 11/23/24: HTML form that takes in how big the user wants the list to be -->
        <label for="listsize">List Size:</label>
        <input type="text" name="listsize" required />
        <input type="submit" value="Go" />
    </div>    
</form>
<?php
$listsize = 10; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $listsize = $_POST['listsize'];
}
$query = "SELECT id, game_title, platforms, genre, release_date, is_api FROM `IT202-F2024-Games` ORDER BY id ASC LIMIT $listsize"; 
$db = getDB();                                                                                // ^ makes it so games appear by ordered id number
$stmt = $db->prepare($query);                                                                 // par36 - 11/23/24
$results = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching games " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$table = ["data" => $results, "edit_url" => get_url("admin/edit_game.php"), "delete_url" => get_url("admin/delete_game.php"), "view_url" => get_url("admin/display_game.php")];
?>
<div class="container-fluid">
    <h3>List Games</h3>
    <?php render_table($table); ?>
</div>
<?php
require_once(__DIR__ . "/../../../partials/flash.php"); // par36 - 11/26/24: to show flash messages properly
?>