<?php
// Note: we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

$id = se($_GET, "id", -1, false); // par36 - 11/24/24: gets id variable
function delete_req($id) {
    if ($id > -1) { // par36 - 11/23/24: checks for all ids that are 1 or greater
        $db = getDB(); // gets db
        $query = "DELETE FROM `IT202_F2024_Games` WHERE id = :id"; // checks db for id to delete
        try {
            $deletereq = $db->prepare($query); // prepares delete query
            $deletereq->execute([":id" => $id]); // executes query
            flash("Delete Successful", "success"); // sends user friendly confirmation message
        } catch (PDOException $e) {
            error_log("Error deleting game: " . var_export($e, true)); // logs error messages
            flash("Delete Unsuccessful", "danger"); // sends error message to user
        }
    } else {
        flash("Invalid ID", "danger"); // tells user if game id is invalid
    }
    die(header("Location: " . get_url("admin/list_games.php"))); // redirects to list_games after delete
}
delete_req($id); // calls the delete function
?>