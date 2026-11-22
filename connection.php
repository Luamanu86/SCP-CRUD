<?php

include "credentials.php";

// Database connection
$connection = new mysqli(
    'localhost',
    $user,
    $password,
    $database
);

if ($connection->connect_error) {
    die("Database connection failed.");
}

$connection->set_charset("utf8mb4");

?>