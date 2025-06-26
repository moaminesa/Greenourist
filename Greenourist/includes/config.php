<?php


$host = "localhost";
$user = "root";
$password = "";
$database = "user_db";
define('DB_HOST', 'localhost');
define('DB_NAME', 'user_db');
define('DB_USER', 'root');
define('DB_PASS', '');
$conn = new mysqli($host, $user, $password, $database);




if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>