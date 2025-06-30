<?php include 'functions.php'; 
// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete_query = "DELETE FROM kendaraan WHERE vehicle_id = '$id'";
    if (query($delete_query)) {
        header("Location: kendaraan.php?success=delete");
        exit();
    } else {
        $error = "Gagal menghapus kendaraan";
    }
}

// Handle edit form submission
if (isset($_POST['action']) && $_POST['action'] == 'edit_vehicle') {
    $id = $_POST['vehicle_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    
    $update_query = "UPDATE kendaraan SET 
                    brand = '$brand',
                    model = '$model',
                    year = $year,
                    daily_price = $price,
                    status = '$status'
                    WHERE vehicle_id = '$id'";
    
    if (query($update_query)) {
        header("Location: kendaraan.php?success=edit");
        exit();
    } else {
        $error = "Gagal mengupdate kendaraan";
    }
}

// Handle add form submission
if (isset($_POST['action']) && $_POST['action'] == 'add_vehicle') {
    // Get the highest existing ID
    $result = query("SELECT vehicle_id FROM kendaraan ORDER BY vehicle_id DESC LIMIT 1");
    $last_id = 'V000'; // Default value if no vehicles exist
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_id = $row['vehicle_id'];
    }
    
    // Extract the numeric part and increment
    $num = (int) substr($last_id, 1);
    $num++;
    $id = 'V' . str_pad($num, 3, '0', STR_PAD_LEFT);
    
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    
    $insert_query = "INSERT INTO kendaraan (vehicle_id, brand, model, year, daily_price, status)
                    VALUES ('$id', '$brand', '$model', $year, $price, '$status')";
    
    if (query($insert_query)) {
        header("Location: kendaraan.php?success=add");
        exit();
    } else {
        $error = "Gagal menambahkan kendaraan";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kendaraan - CarRent</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <script>
        // Function to show success message
        function showSuccessMessage() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const action = urlParams.get('success');
                
                if (action === 'add') confirm('Kendaraan berhasil ditambahkan!');
                else if (action === 'edit') confirm('Kendaraan berhasil diupdate!');
                else if (action === 'delete') confirm('Kendaraan berhasil dihapus!');
            }
        }
        
        // Function to confirm deletion
        function confirmDelete(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')) {
                window.location.href = `kendaraan.php?action=delete&id=${id}`;
            }
        }
        
        // Function to open edit modal
        function openEditModal(id, brand, model, year, price, status) {
            document.getElementById('editVehicleId').value = id;
            document.getElementById('editBrand').value = brand;
            document.getElementById('editModel').value = model;
            document.getElementById('editYear').value = year;
            document.getElementById('editPrice').value = price;
            document.getElementById('editStatus').value = status;
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Close modals when clicking X
        window.onload = function() {
            showSuccessMessage();
            
            const modals = document.querySelectorAll('.modal');
            const closeButtons = document.querySelectorAll('.close');
            
            closeButtons.forEach(button => {
                button.onclick = function() {
                    modals.forEach(modal => {
                        modal.style.display = 'none';
                    });
                }
            });
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>CarRent Admin</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="kendaraan.php" class="active">Kelola Kendaraan</a></li>
            <li><a href="rental.php">History Rental</a></li>
            <li><a href="tambah_rental.php">Kelola Rental</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <h1>Kelola Kendaraan</h1>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </header>
        <div class="crud-actions">
            <button onclick="document.getElementById('addModal').style.display='block'">Tambah Kendaraan</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Merk</th>
                    <th>Model</th>
                    <th>Tahun</th>
                    <th>Harga/Hari</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $kendaraan = query("SELECT * FROM kendaraan");
                while ($row = mysqli_fetch_assoc($kendaraan)) {
                    echo "<tr>
                        <td>{$row['vehicle_id']}</td>
                        <td>{$row['brand']}</td>
                        <td>{$row['model']}</td>
                        <td>{$row['year']}</td>
                        <td>" . rupiah($row['daily_price']) . "</td>
                        <td>{$row['status']}</td>
                        <td>
                            <button class='edit' onclick=\"openEditModal('{$row['vehicle_id']}', '{$row['brand']}', '{$row['model']}', '{$row['year']}', '{$row['daily_price']}', '{$row['status']}')\">Edit</button>
                            <button class='delete' onclick=\"confirmDelete('{$row['vehicle_id']}')\">Hapus</button>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- Modal Tambah Kendaraan -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Tambah Kendaraan Baru</h2>
                <form action="kendaraan.php" method="POST">
                    <input type="hidden" name="action" value="add_vehicle">
                    <input type="text" name="brand" placeholder="Merk" required>
                    <input type="text" name="model" placeholder="Model" required>
                    <input type="number" name="year" placeholder="Tahun" required>
                    <input type="number" name="price" placeholder="Harga/Hari" required>
                    <select name="status">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                    <button type="submit">Simpan</button>
                </form>
            </div>
        </div>
        
        <!-- Modal Edit Kendaraan -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Kendaraan</h2>
                <form action="kendaraan.php" method="POST">
                    <input type="hidden" name="action" value="edit_vehicle">
                    <input type="hidden" id="editVehicleId" name="vehicle_id">
                    <input type="text" id="editBrand" name="brand" placeholder="Merk" required>
                    <input type="text" id="editModel" name="model" placeholder="Model" required>
                    <input type="number" id="editYear" name="year" placeholder="Tahun" required>
                    <input type="number" id="editPrice" name="price" placeholder="Harga/Hari" required>
                    <select id="editStatus" name="status">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>