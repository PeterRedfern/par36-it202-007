<?php
session_start();
require(__DIR__ . "/../../lib/functions.php");
reset_session(); // par36 - 11/8/24: destroys the session

flash("Successfully logged out", "success");
header("Location: login.php");