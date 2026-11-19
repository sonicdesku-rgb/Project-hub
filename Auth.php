<?php
session_start(); // starts the session so $_SESSION is available

if (!isset($_SESSION['username'])) { // checks if user is logged in
    header("Location: index.php");  // if not, redirects to login page
    exit();                         // stops further execution of the page
}
?>