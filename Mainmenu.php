<?php 
require 'Auth.php'; // checks login — redirects to index.php if not logged in
require 'dblogin.php';  // loads database connection

?>

<h2>Main Menu</h2>

<a href="list.php">List Records</a><br>
<a href="add.php">Add Record</a><br>
<a href="search.php">Search</a><br>
<a href="logout.php">Logout</a>