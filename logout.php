<?php
session_start(); // starts session so it can be destroyed
session_destroy(); // destroys the session, logging the user out
header("Location: index.php"); // redirects to login page after logout
exit(); // stops further execution of the page
?>