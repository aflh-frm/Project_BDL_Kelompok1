<?php include 'functions.php'; 
// Handle search
$search_brand = isset($_GET['search_brand']) ? trim($_GET['search_brand']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>History Rental - CarRent</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>CarRent Admin</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="kendaraan.php">Kelola Kendaraan</a></li>
            <li><a href="rental.php" class="active">History Rental</a></li>
            <li><a href="tambah_rental.php">Kelola Rental</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>History Rental</h1>
        </header>

        <div class="search-box">
            <form method="GET" action="rental.php">
                <input 
                    type="text" 
                    name="search_brand" 
                    placeholder="Cari nama brand..."
                    value="<?= htmlspecialchars($search_brand) ?>"
                >
                <button type="submit">Cari</button>
                <?php if (!empty($search_brand)): ?>
                    <a href="rental.php" class="reset-btn">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="recent-rentals">
            <h2>Semua Transaksi Rental</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Rental</th>
                        <th>Penyewa</th>
                        <th>Kendaraan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query dasar
                    $query = "SELECT r.rental_id, p.name, k.brand, k.model, 
                             r.start_date, r.end_date, r.total_price, r.status 
                             FROM rental r
                             JOIN penyewa p ON r.renter_id = p.renter_id
                             JOIN kendaraan k ON r.vehicle_id = k.vehicle_id";
                    
                    // Filter berdasarkan nama penyewa jika ada
                    if (!empty($search_brand)) {
                        $query .= " WHERE k.brand LIKE '" . mysqli_real_escape_string($conn, $search_brand) . "%'";
                    }
                    
                    $query .= " ORDER BY r.start_date DESC";
                    
                    $rentals = query($query);
                    
                    if (mysqli_num_rows($rentals) > 0) {
                        while ($row = mysqli_fetch_assoc($rentals)) {
                            echo "<tr>
                                <td>{$row['rental_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['brand']} {$row['model']}</td>
                                <td>{$row['start_date']}</td>
                                <td>{$row['end_date']}</td>
                                <td>" . rupiah($row['total_price']) . "</td>
                                <td>{$row['status']}</td>
                            </tr>";
                        }
                    } else {
                        echo '<tr><td colspan="7" style="text-align: center;">';
                        echo empty($search_brand) 
                            ? 'Belum ada data rental' 
                            : 'brand "' . htmlspecialchars($search_brand) . '" tidak ditemukan';
                        echo '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>