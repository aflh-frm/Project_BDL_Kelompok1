<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'rentalkendaraanbaru';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk eksekusi query dengan error handling dan logging
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    
    // Error handling
    if (!$result) {
        die("Error: " . mysqli_error($conn) . "<br>Query: " . $sql);
    }
    
    // Log query yang menggunakan index kendaraan
    if (strpos($sql, 'kendaraan') !== false && strpos($sql, 'status') !== false) {
        error_log("Query optimasi index: " . $sql . " | Waktu: " . date('Y-m-d H:i:s'));
    }
    
    return $result;
}

// Fungsi untuk format Rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>