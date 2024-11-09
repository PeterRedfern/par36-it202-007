<?php
function reset_session() // par36 - 11/8/24: Shows the session being removed and destroyed
{
    session_unset();
    session_destroy();
    session_start();
}