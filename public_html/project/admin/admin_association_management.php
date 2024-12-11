<?php
require(__DIR__ . "/../../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}

/* 
par36 - 12/11/24

IMPORTANT:
Overall, this page is incomplete due to time constraints
It renders a few text boxes but is in a non-working state
Many building blocks for potential solutions are included but not fully fleshed out/working
*/

//build search form
$form = [
    ["type" => "text", "name" => "game_title", "placeholder" => "Game Title", "label" => "Game Title", "include_margin" => false],
    ["type" => "text", "name" => "username", "placeholder" => "Username", "label" => "Username", "include_margin" => false],
];
//error_log("Form data: " . var_export($form, true));


$query = "SELECT u.username, FROM Users JOIN `IT202_F2024_Games` g ON g.game_title";
$params = [];
$results = [];


//attempt to apply
if (isset($_POST["users"]) && isset($_POST["games"])) {
    $user_ids = $_POST["users"]; //se() doesn't like arrays so we'll just do this
    $game_ids = $_POST["games"]; //se() doesn't like arrays so we'll just do this
    if (empty($user_ids) || empty($role_ids)) {
        flash("Both users and games need to be selected", "warning");
    } else {
        //for sake of simplicity, this will be a tad inefficient
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO IT202_F2024_UserGames (user_id, game_id) VALUES (:uid, :gid)");
        foreach ($user_ids as $uid) {
            foreach ($game_ids as $gid) {
                try {
                    $stmt->execute([":uid" => $uid, ":gid" => $gid]);
                    flash("Updated Game Association", "success");
                } catch (PDOException $e) {
                    flash(var_export($e->errorInfo, true), "danger");
                }
            }
        }
    }
}

//search for user by username
$users = [];
$username = "";
if (isset($_POST["username"])) {
    $username = se($_POST, "username", "", false);
    if (!empty($username)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT Users.id, username, 
        (SELECT GROUP_CONCAT(name, ' (' , IF(ur.is_active = 1,'active','inactive') , ')') from 
        UserRoles ur JOIN Roles on ur.role_id = Roles.id WHERE ur.user_id = Users.id) as roles
        from Users WHERE username like :username");
        try {
            $stmt->execute([":username" => "%$username%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                $users = $results;
            }
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    } else {
        flash("Username must not be empty", "warning");
    }
}

$table = [
    "data" => $results, "title" => "Users", "ignored_columns" => ["id"],
    "view_url" => get_url("profile.php"),
];
?>
<div class="container-fluid">
    <h3>Association Management</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">

            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_table(count($results)); ?>
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $user) : ?>
            <div class="col">
                <?php render_table($user); ?>
            </div>
        <?php endforeach; ?>
        <?php if (count($results) === 0) : ?>
            <div class="col">
                No results to show
            </div>
        <?php endif; ?>
    </div>
</div>


<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>