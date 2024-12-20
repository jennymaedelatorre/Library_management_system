<?php
$host = "localhost";
$port = "5432";
$dbname = "IT108_LibraryMS";
$user = "postgres"; 
$password = "may142004"; 

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Error: Unable to connect to the database. " . pg_last_error());
}
?>
