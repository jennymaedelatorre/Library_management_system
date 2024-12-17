<?php
$host = "localhost";
$port = "5432";
$dbname = "108_library";
$user = "postgres"; 
$password = "1504"; 

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Error: Unable to connect to the database. " . pg_last_error());
}
?>
