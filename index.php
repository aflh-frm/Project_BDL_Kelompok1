<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - CarRent</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="sidebar">
        <h2>CarRent Admin</h2>
        <ul>
            <li><a href="index.php" class="active">Dashboard</a></li>
            <li><a href="kendaraan.php">Kelola Kendaraan</a></li>
            <li><a href="rental.php">History Rental</a></li>
            <li><a href="tambah_rental.php">Kelola Rental</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
        </header>
        <div class="cards">
            <div class="card">
                <h3>Total Kendaraan</h3>
                <p>
                    <?php
                    // query untuk menghitung jumlah kendaraan - Aggregate (Multi Row Function)
                    $result = query("SELECT COUNT(*) FROM kendaraan");
                    echo mysqli_fetch_array($result)[0];
                    ?>
                </p>
            </div>
            <div class="card">
                <h3>Rental Aktif</h3>
                <p>
                    <?php
                    // query untuk mendapatkan jumlah rental aktif - Aggregate (Multi Row Function)
                    $result = query("SELECT COUNT(rental_id) FROM v_rental_baru");
                    echo mysqli_fetch_array($result)[0];
                    ?>
                </p>
            </div>
            <div class="card">
                <h3>Pendapatan Total Keseluruhan</h3>
                <p>
                    <?php
                    // query untuk mendapatkan total pendapatan - Aggregate (Multi Row Function)
                    $result = query("SELECT SUM(total_price) FROM rental");
                    echo rupiah(mysqli_fetch_array($result)[0]);
                    ?>
                </p>
            </div>
            <div class="card">
        <h3>Harga Rental Tertinggi</h3>
        <p>
            <?php
            // query untuk mendapatkan harga rental tertinggi - Aggregate (Multi Row Function)
            $result = query("SELECT MAX(daily_price) FROM kendaraan");
            echo rupiah(mysqli_fetch_array($result)[0]);
            ?>
        </p>
        </div>
        <div class="card">
            <h3>Harga Rental Terendah</h3>
            <p>
                <?php
                // query untuk mendapatkan harga rental terendah - Aggregate (Multi Row Function)
                $result = query("SELECT MIN(daily_price) FROM kendaraan");
                echo rupiah(mysqli_fetch_array($result)[0]);
                ?>
            </p>
        </div>
    </div>

        <div class="recent-rentals">
            <h2>Rental Aktif</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kendaraan</th>
                        <th>Penyewa</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query VIEW
                    $rentals = query("SELECT * FROM v_rental_baru");
                    while ($row = mysqli_fetch_assoc($rentals)) {
                        echo "<tr>
                            <td>{$row['rental_id']}</td>
                            <td>{$row['brand']} {$row['model']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['start_date']} s/d {$row['end_date']}</td>
                            <td>" . rupiah($row['total_price']) . "</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>