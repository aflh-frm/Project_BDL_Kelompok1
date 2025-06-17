<?php
require_once 'config.php';
require_once 'function.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

$errors = [];
$messages = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';
    try {
        if ($form_type === 'kendaraan_create') {
            $brand = clean_input($_POST['brand']);
            $model = clean_input($_POST['model']);
            $year = clean_input($_POST['year']);
            $vehicle_id = clean_input($_POST['vehicle_id']);
            $daily_price = clean_input($_POST['daily_price']);
            $status = clean_input($_POST['status']);
            
            $stmt = $conn->prepare("INSERT INTO kendaraan (vehicle_id, brand, model, year, daily_price, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssids", $vehicle_id, $brand, $model, $year, $daily_price, $status);
            $stmt->execute();
            $stmt->close();
            $messages[] = "Kendaraan berhasil ditambahkan.";
            
        } elseif ($form_type === 'kendaraan_update') {
            $vehicle_id = clean_input($_POST['vehicle_id']);
            $brand = clean_input($_POST['brand']);
            $model = clean_input($_POST['model']);
            $year = clean_input($_POST['year']);
            $daily_price = clean_input($_POST['daily_price']);
            $status = clean_input($_POST['status']);
            
            $stmt = $conn->prepare("UPDATE kendaraan SET brand=?, model=?, year=?, daily_price=?, status=? WHERE vehicle_id=?");
            $stmt->bind_param("ssidss", $brand, $model, $year, $daily_price, $status, $vehicle_id);
            $stmt->execute();
            $stmt->close();
            $messages[] = "Kendaraan berhasil diperbarui.";
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM kendaraan WHERE vehicle_id=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->close();
        $messages[] = "Kendaraan berhasil dihapus.";
    } catch (Exception $e) {
        $errors[] = "Gagal menghapus: ".$e->getMessage();
    }
}

// Ambil data kendaraan
$kendaraan_rows = $conn->query("SELECT * FROM kendaraan ORDER BY brand ASC");

// Untuk form edit
$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM kendaraan WHERE vehicle_id=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

require_once 'header.php';
?>

<div class="container">
    <?php if ($messages): ?>
        <?php foreach($messages as $msg): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($errors): ?>
        <?php foreach($errors as $err): ?>
            <div class="message error"><?php echo $err; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <section id="kendaraan-section" class="card">
        <h2>Kendaraan</h2>
        <button onclick="location.href='kendaraan.php?action=create'">Tambah Kendaraan</button>
        <table aria-label="Daftar Kendaraan">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Tahun</th>
                    <th>Harga Sewa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $kendaraan_rows->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['vehicle_id'] ?></td>
                    <td><?= $row['brand'] ?></td>
                    <td><?= $row['model'] ?></td>
                    <td><?= $row['year'] ?></td>
                    <td><?= number_format($row['daily_price'], 0, ',', '.') ?></td>
                    <td><?= $row['status'] ?></td>
                    <td class="actions">
                        <a href="kendaraan.php?action=edit&id=<?= $row['vehicle_id'] ?>">Edit</a>
                        <a href="kendaraan.php?action=delete&id=<?= $row['vehicle_id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <?php if ($action === 'create' || $action === 'edit'): ?>
    <section id="create-section" class="card">
        <h2><?= ($action === 'create') ? 'Tambah' : 'Edit' ?> Kendaraan</h2>
        <form method="POST">
            <input type="hidden" name="form_type" value="kendaraan_<?= ($action === 'create') ? 'create' : 'update' ?>">
            
            <div class="form-group">
                <label for="vehicle_id">ID Kendaraan</label>
                <input type="text" name="vehicle_id" id="vehicle_id" 
                       value="<?= isset($edit_data['vehicle_id']) ? $edit_data['vehicle_id'] : '' ?>" 
                       <?= ($action === 'edit') ? 'readonly' : 'required' ?>>
            </div>
            
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" name="brand" id="brand" 
                       value="<?= isset($edit_data['brand']) ? $edit_data['brand'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" 
                       value="<?= isset($edit_data['model']) ? $edit_data['model'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="year">Tahun</label>
                <input type="number" name="year" id="year" 
                       value="<?= isset($edit_data['year']) ? $edit_data['year'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="daily_price">Harga Sewa per Hari</label>
                <input type="number" name="daily_price" id="daily_price" 
                       value="<?= isset($edit_data['daily_price']) ? $edit_data['daily_price'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="Tersedia" <?= isset($edit_data['status']) && $edit_data['status'] == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="Maintenance" <?= isset($edit_data['status']) && $edit_data['status'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    <option value="Sedang disewa" <?= isset($edit_data['status']) && $edit_data['status'] == 'Sedang disewa' ? 'selected' : '' ?>>Sedang disewa</option>
                </select>
            </div>
            
            <button type="submit">Simpan</button>
            <button type="button" onclick="location.href='kendaraan.php'">Batal</button>
        </form>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>