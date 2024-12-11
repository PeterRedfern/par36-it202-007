<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
//search before query
$game_title = se($_GET, "game_title", "", false);
$platform = se($_GET, "platforms", "", false);
$genres = se($_GET, "genre", "", false);
$release_date = se($_GET, "release_date", "", false);

$column = se($_GET, "column", "", false);
$order = se($_GET, "order", "", false);
$columns = ["id", "game_title", "platforms", "genre", "release_date", "is_api"];
$columnMap = array_map(function ($v) {
    return [$v => $v];
}, $columns);
// sanitize
if (!in_array($column, $columns)) {
    $column = "game_title";
}
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc";
}
$params = [];

$params[":user_id"] = get_user_id();


$sql = "SELECT g.id, game_title, platforms, genre, release_date, 1 as is_watched
FROM IT202_F2024_Games as g
JOIN IT202_F2024_Usergames as ug on g.id = ug.game_id
JOIN Users as u on u.id = ug.user_id";
// the first space is important
$where = " WHERE u.id = :user_id"; //filter by user


if (!empty($game_title)) {
    $where .= " AND game_title like :game_title";
    $params[":game_title"] = "%$game_title%";
}
if (!empty($platform) && $platform != "-1") {
    $where .= " AND IT202_F2024_Games.id = :platforms";
    $params[":platforms"] = $platform;
}
if (!empty($genres) && $genres != "-1") {
    $where .= " AND type = :genre";
    $params[":genre"] = $genres;
}
if (!empty($release_date)) {
    $where .= " AND release_date like :release_date";
    $params[":release_date"] = "%$release_date%";
}
$limit = 10;
if (isset($_GET["limit"]) && !is_nan($_GET["limit"])) {
    $limit = (int)$_GET["limit"];
    if ($limit < 0 || $limit > 100) {
        $limit = 10;
    }
}
$sql .= $where;
$sql .= " ORDER BY $column $order";

$sql .= " LIMIT $limit";
$db = getDB();
$results = [];
try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (Exception $e) {
    error_log(var_export($e, true));
    error_log("A Fetch Error Occured");
    flash("Failed to fetch");
}
error_log(var_export($results, true)); // par 36 - 12/9/24: test exports
$platforms = get_platforms(); //used for filter dropdown
$genre = get_genres(); // used for filter dropdown

// get total possible values based on filters
// JOINS also filter (in addition to the WHERE clause)
$total = 0;

$sql = "SELECT COUNT(DISTINCT g.id) as c
FROM IT202_F2024_Games as g
JOIN IT202_F2024_Usergames as ug on g.id = ug.game_id
JOIN Users as u on u.id = ug.user_id
$where"; //filter by user
try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $r = $stmt->fetch();
    if ($r) {
        $total = (int)$r["c"]; // called my virtual/temp column "c" for count
    }
} catch (PDOException $e) {
    flash("Error fetching count", "danger");
    error_log("Error fetching count: " . var_export($e, true));
    error_log("Query: $sql");
    error_log("Params: " . var_export($params, true));
}

// since I'm using cards and I didn't make a flexible "manager" like render_table()
// I need to transform my data
$results = array_map(function ($item) {
    if (!isset($item["id"])) {
        error_log("Missing result item id during mapping");
    }
    $id = se($item, "id", -1, false);
    $cleaned_get = $_GET;
    if (isset($_GET["id"])) {
        unset($_GET["id"]);
    }
    //$item["delete_url"] = get_url("delete_game.php?id=$id") . http_build_query($cleaned_get);
    //$item["view_url"] = get_url("display_game.php?id=$id") . http_build_query($cleaned_get);
    return $item;
}, $results);
error_log("Games: " . var_export($results, true));

$table = ["data" => $results, "view_url" => get_url("display_game.php")]
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $limit = $_POST['limit'];
}
?>
<div class="container-fluid">
    <h5>Watchlist</h5>
    <div>
        <form>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "game_title", "label" => "Game Title", "value" => $game_title]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "platforms", "label" => "Platforms", "value" => $platform, "type" => "select", "options" => $platforms]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "genre", "label" => "Genre", "value" => $genres, "type" => "select", "options" => $genre]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "release_date", "label" => "Release Date", "value" => $release_date]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "limit", "label" => "List Size (Specify 1 - 100)", "value" => $limit]); ?>
                </div>

            </div>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "column", "label" => "Sort", "value" => $column, "type" => "select", "options" => $columnMap]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "order", "label" => "Order", "value" => $order, "type" => "select", "options" => [["asc" => "asc"], ["desc" => "desc"]]]); ?>
                </div>
                <div class="col">
                    <?php render_button(["text" => "Search", "type" => "submit"]); ?>
                </div>
                <div class="col">
                    <a href="?" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col">
            Results <?php echo count($results) . "/" . $total; ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <a class="btn btn-warning" href="api/clear_watchlist.php">Clear List</a>
        </div>
    </div>
    <div class="row">
        <?php render_table($table); ?>
        <?php if (empty($results)): ?>
            No records to show
        <?php endif; ?>
    </div>
    <div class="row">
        <?php include(__DIR__ . "/../../partials/pagination_nav.php"); ?>
    </div>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>