<?php include 'functions.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_penyewa') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Validasi input
    if (empty($name) || empty($phone) || empty($email)) {
        $error = "Semua data wajib diisi!";
    } else {
        // Generate renter_id baru
        $result = query("SELECT MAX(renter_id) as max_id FROM penyewa");
        $row = mysqli_fetch_assoc($result);
        $new_renter_id = $row['max_id'] + 1;

        // Insert new penyewa
        $insert_query = "INSERT INTO penyewa (renter_id, name, phone, email)
                        VALUES ('$new_renter_id', '$name', '$phone', '$email')";
        
        if (query($insert_query)) {
            header("Location: tambah_rental.php?success=add");
            exit();
        } else {
            $error = "Gagal menambahkan penyewa: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penyewa Baru - CarRent</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
    <script>
        function showSuccessMessage() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                const action = urlParams.get('success');
                
                if (action === 'add') alert('Penyewa berhasil ditambahkan!');
            }
        }
        
        function validateForm() {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            
            if (name.trim() === '' || phone.trim() === '') {
                alert('Nama dan Nomor HP wajib diisi!');
                return false;
            }
            
            return true;
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
            <li><a href="tambah_rental.php">Kelola Rental</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Tambah Penyewa Baru</h1>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
        </header>

        <div class="form-container">
            <form id="penyewaForm" action="tambah_penyewa.php" method="POST" onsubmit="return validateForm()">
                <input type="hidden" name="action" value="add_penyewa">
                
                <div class="form-group">
                    <label for="name">Nama Lengkap:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Nomor HP:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>