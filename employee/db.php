<?php
// db.php - shared DB connection

$host   = "localhost";
$user   = "your_mysql_username";
$pass   = "your_mysql_password";
$dbname = "project"; // matches your project_schema_only.sql

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
