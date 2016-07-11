<?php

require_once("user.php");
$USER = new User();

if ( ! $USER->authenticated ) {
        echo "Not authorized. Please login at ";
        echo '<a href="/login.php">login form</a>';
        die();
}
?>


