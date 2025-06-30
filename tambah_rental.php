<?php include 'functions.php';

// Handle form submission for new rental
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_rental') {
    $renter_id = $_POST['renter_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Validasi input
    if (empty($renter_id) || empty($vehicle_id) || empty($start_date) || empty($end_date)) {
        $error = "Semua field harus diisi!";
    } else {
        // Calculate total days and price
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $days = $end->diff($start)->days + 1;
        
        if ($start > $end) {
            $error = "Tanggal selesai harus setelah tanggal mulai!";
        } else {
            // Get vehicle daily price
            $vehicle_query = query("SELECT daily_price FROM kendaraan WHERE vehicle_id = '$vehicle_id' AND status = 'Tersedia'");
            if (mysqli_num_rows($vehicle_query) == 0) {
                $error = "Kendaraan tidak tersedia atau tidak ditemukan!";
            } else {
                $vehicle = mysqli_fetch_assoc($vehicle_query);
                $total_price = $days * $vehicle['daily_price'];
                
                // Cek apakah penyewa ada
                $renter_check = query("SELECT renter_id FROM penyewa WHERE renter_id = '$renter_id'");
                if (mysqli_num_rows($renter_check) == 0) {
                    $error = "Penyewa tidak ditemukan!";
                } else {
                    // Insert new rental
                    $result = query("SELECT MAX(rental_id) as max_id FROM rental");
                    $row = mysqli_fetch_assoc($result);
                    $new_rental_id = $row['max_id'] + 1;

                    $insert_query = "INSERT INTO rental (rental_id, renter_id, vehicle_id, start_date, end_date, total_price, status)
                                    VALUES ('$new_rental_id', '$renter_id', '$vehicle_id', '$start_date', '$end_date', $total_price, 'Aktif')";
                    
                    if (query($insert_query)) {
                        // Redirect ke halaman yang sama dengan parameter success
                        header("Location: tambah_rental.php?success=add");
                        exit();
                    } else {
                        $error = "Gagal menambahkan rental: " . mysqli_error($conn);
                    }
                }
            }
        }
    }
}

// Handle rental completion
if (isset($_GET['action']) && $_GET['action'] == 'complete' && isset($_GET['id'])) {
    $rental_id = $_GET['id'];
    
    // Get vehicle ID first
    $rental_query = query("SELECT vehicle_id FROM rental WHERE rental_id = '$rental_id'");
    $rental = mysqli_fetch_assoc($rental_query);
    $vehicle_id = $rental['vehicle_id'];
    
    // Update rental status to 'Selesai'
    $update_query = "UPDATE rental SET status = 'Selesai' WHERE rental_id = '$rental_id'";
    
    if (query($update_query)) {
        // Update vehicle status back to 'Tersedia'
        query("UPDATE kendaraan SET status = 'Tersedia' WHERE vehicle_id = '$vehicle_id'");
        
        header("Location: tambah_rental.php?success=complete");
        exit();
    } else {
        $error = "Gagal menyelesaikan rental";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Rental - CarRent</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <script>
        function showSuccessMessage() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const action = urlParams.get('success');
                
                if (action === 'add') {
                    alert('Rental berhasil ditambahkan!');
                } 
                else if (action === 'complete') {
                    alert('Rental berhasil diselesaikan!');
                } 
            }
        }
        
        function confirmComplete(id) {
            if (confirm('Apakah Anda yakin ingin menyelesaikan rental ini?')) {
                window.location.href = `tambah_rental.php?action=complete&id=${id}`;
            }
        }
        
        window.onload = function() {
            showSuccessMessage();
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>CarRent Admin</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="kendaraan.php">Kelola Kendaraan</a></li>
            <li><a href="rental.php">History Rental</a></li>
            <li><a href="tambah_rental.php" class="active">Kelola Rental</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Kelola Rental</h1>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </header>

        <div class="crud-actions">
            <button onclick="document.getElementById('addModal').style.display='block'">Tambah Rental Baru</button>
        </div>

        <div class="recent-rentals">
            <h2>Rental Aktif</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Rental</th>
                        <th>Penyewa</th>
                        <th>Kendaraan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rentals = query("SELECT r.rental_id, p.name, k.brand, k.model, 
                                    r.start_date, r.end_date, r.total_price
                                    FROM rental r
                                    JOIN penyewa p ON r.renter_id = p.renter_id
                                    JOIN kendaraan k ON r.vehicle_id = k.vehicle_id
                                    WHERE r.status = 'Aktif'
                                    ORDER BY r.start_date");
                    
                    while ($row = mysqli_fetch_assoc($rentals)) {
                        echo "<tr>
                            <td>{$row['rental_id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['brand']} {$row['model']}</td>
                            <td>{$row['start_date']}</td>
                            <td>{$row['end_date']}</td>
                            <td>" . rupiah($row['total_price']) . "</td>
                            <td>
                                <button class='edit' onclick=\"confirmComplete('{$row['rental_id']}')\">Selesaikan</button>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Rental -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                <h2>Tambah Rental Baru</h2>
                <form action="tambah_rental.php" method="POST">
                    <input type="hidden" name="action" value="add_rental">
                    
                    <label for="renter_id">Penyewa:</label>
                    <select name="renter_id" required>
                        <option value="">Pilih Penyewa</option>
                        <?php
                        $customers = query("SELECT * FROM penyewa");
                        while ($customer = mysqli_fetch_assoc($customers)) {
                            echo "<option value='{$customer['renter_id']}'>{$customer['name']} - {$customer['phone']}</option>";
                        }
                        ?>
                    </select>
                    <label for="vehicle_id">Kendaraan:</label>
                    <select name="vehicle_id" required>
                        <option value="">Pilih Kendaraan</option>
                        <?php
                        $vehicles = query("SELECT * FROM kendaraan WHERE status = 'Tersedia'");
                        while ($vehicle = mysqli_fetch_assoc($vehicles)) {
                            echo "<option value='{$vehicle['vehicle_id']}'>{$vehicle['brand']} {$vehicle['model']} ({$vehicle['year']}) - " . rupiah($vehicle['daily_price']) . "/hari</option>";
                        }
                        ?>
                    </select>
                    
                    <label for="start_date">Tanggal Mulai:</label>
                    <input type="date" name="start_date" required>
                    
                    <label for="end_date">Tanggal Selesai:</label>
                    <input type="date" name="end_date" required>
                    
                    <button type="submit">Simpan Rental</button>
                    <br>
                    <br>
                    <p style="text-align: center;">penyewa tidak ada? <a href="tambah_penyewa.php">tambah data</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>