<?php

session_start();
require(__DIR__ . "/../../../lib/functions.php");

$user_id = se($_GET, "user_id", get_user_id(), false);
if(!has_role("Admin") && $user_id != get_user_id()){
    $user_id = get_user_id();
}

try{ // par36 - 12/11/24: deletes all associated records based on user id
    $db = getDB();
    $query = "DELETE FROM IT202_F2024_Usergames WHERE user_id = :user_id";
    $params = [":user_id"=>$user_id];
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    flash("Cleared list", "success");
}
catch(PDOException $e){
    flash("Error clearing list", "danger");
    error_log("Error clearing list: " . var_export($e, true));
}
// no need to pass query params as the list is empty now so it doesn't matter
// using HTTP_REFERER as an example option (this is where the user came from)
// it may not always be set or accurate
$refer = $_SERVER["HTTP_REFERER"];
if(empty($refer)){
    $refer = get_url("watchlist.php");
}
die(header("Location: $refer"));