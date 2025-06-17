<?php
// Database config
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "rentalkendaraan";

// Connect db
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo ("Koneksi gagal: ".$conn->connect_error);
}
?>