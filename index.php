<?php
require_once 'config.php';
require_once 'function.php';

require_once 'header.php';
?>

<div class="container">
    <section class="card">
        <h1>Selamat Datang di Sistem Rental Kendaraan</h1>
        <p>Silakan pilih menu yang tersedia:</p>
        <ul>
            <li><a href="kendaraan.php">Kelola Data Kendaraan</a></li>
            <li><a href="penyewa.php">Kelola Data Penyewa</a></li>
            <li><a href="rental.php">Kelola Data Rental</a></li>
        </ul>
    </section>
</div>

<?php require_once 'footer.php'; ?>