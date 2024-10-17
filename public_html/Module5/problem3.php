<?php
// Array set A (user information)
$a1_users = [
    ["userId" => 1, "name" => "Alice", "age" => 28],
    ["userId" => 2, "name" => "Bob", "age" => 34]
];

$a2_users = [
    ["userId" => 3, "name" => "Charlie", "age" => 22],
    ["userId" => 4, "name" => "Diana", "age" => 29]
];

$a3_users = [
    ["userId" => 5, "name" => "Eve", "age" => 31],
    ["userId" => 6, "name" => "Frank", "age" => 26]
];

$a4_users = [
    ["userId" => 7, "name" => "Grace", "age" => 25],
    ["userId" => 8, "name" => "Hank", "age" => 30]
];

// Array set B (user activity)
$a1_activities = [
    ["userId" => 1, "activity" => "Running"],
    ["userId" => 2, "activity" => "Swimming"]
];

$a2_activities = [
    ["userId" => 3, "activity" => "Cycling"],
    ["userId" => 4, "activity" => "Hiking"]
];

$a3_activities = [
    ["userId" => 5, "activity" => "Climbing"],
    ["userId" => 6, "activity" => "Skiing"]
];

$a4_activities = [
    ["userId" => 7, "activity" => "Diving"],
    ["userId" => 8, "activity" => "Surfing"]
];

function joinArrays($users, $activities) {
    echo "<br>Processing Arrays:<br><pre>Users: " . var_export($users, true) . "<br>Activities: " . var_export($activities, true) . "</pre>";
    echo "<br>Joined output:<br>";
    
    // Note: use the $users and $activities variables to iterate over, don't directly touch $a1-$a4 arrays
    // TODO add logic here to join the arrays on userId
    $joined = []; // result array
    // Start edits
    $activityCheck = []; // par36 - 10/16/24: creates an array to hold the data accessed in the foreach loop
    foreach($activities as $activity) { // iterates through the entire activities array
        $activityCheck[$activity['userId']] = $activity['activity']; 
        // ^ creates an associative array, mapping the userId to an activity so it can be matched with the user later
    }

    foreach($users as $user) { // par36 - 10/16/24: iterates through the entire users array
        if(isset($activityCheck[$user['userId']])) { // checks for the userId variable in the array
            $joined[] = ['userId' => $user['userId'], 'name' => $user['name'], 'age' => $user['age'], 'activity' => $activityCheck[$user['userId']]];
            // ^ gets all of the relevant fields from the user array and adds the activity check to map the activity to the user via ID match
        }
    }
    // End edits
    echo "<pre>" . var_export($joined, true) . "</pre>";
}

echo "Problem 3: Joining Arrays on userId<br>";
?>
<table>
    <thead>
        <tr>
            <th>A1</th>
            <th>A2</th>
            <th>A3</th>
            <th>A4</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php joinArrays($a1_users, $a1_activities); ?>
            </td>
            <td>
                <?php joinArrays($a2_users, $a2_activities); ?>
            </td>
            <td>
                <?php joinArrays($a3_users, $a3_activities); ?>
            </td>
            <td>
                <?php joinArrays($a4_users, $a4_activities); ?>
            </td>
        </tr>
    </tbody>
</table>
<style>
    table {
        border-spacing: 2em 3em;
        border-collapse: separate;
    }

    td {
        border-right: solid 1px black;
        border-left: solid 1px black;
    }
</style>