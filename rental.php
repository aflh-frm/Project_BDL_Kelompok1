<?php
require_once 'config.php';
require_once 'function.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

$errors = [];
$messages = [];

// Ambil data untuk dropdown
$kendaraan_list = $conn->query("SELECT vehicle_id, brand, model FROM kendaraan");
$penyewa_list = $conn->query("SELECT renter_id, name FROM penyewa");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';
    try {
        if ($form_type === 'rental_create') {
            $vehicle_id = clean_input($_POST['vehicle_id']);
            $renter_id = clean_input($_POST['renter_id']);
            $start_date = clean_input($_POST['start_date']);
            $end_date = clean_input($_POST['end_date']);
            $total_price = clean_input($_POST['total_price']);
            
            $stmt = $conn->prepare("INSERT INTO rental (vehicle_id, renter_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sissi", $vehicle_id, $renter_id, $start_date, $end_date, $total_price);
            $stmt->execute();
            $stmt->close();
            
            // Update status kendaraan
            $update_stmt = $conn->prepare("UPDATE kendaraan SET status='Sedang disewa' WHERE vehicle_id=?");
            $update_stmt->bind_param("s", $vehicle_id);
            $update_stmt->execute();
            $update_stmt->close();
            
            $messages[] = "Rental berhasil ditambahkan.";
            
        } elseif ($form_type === 'rental_update') {
            $rental_id = clean_input($_POST['rental_id']);
            $vehicle_id = clean_input($_POST['vehicle_id']);
            $renter_id = clean_input($_POST['renter_id']);
            $start_date = clean_input($_POST['start_date']);
            $end_date = clean_input($_POST['end_date']);
            $total_price = clean_input($_POST['total_price']);
            
            $stmt = $conn->prepare("UPDATE rental SET vehicle_id=?, renter_id=?, start_date=?, end_date=?, total_price=? WHERE rental_id=?");
            $stmt->bind_param("sissii", $vehicle_id, $renter_id, $start_date, $end_date, $total_price, $rental_id);
            $stmt->execute();
            $stmt->close();
            $messages[] = "Rental berhasil diperbarui.";
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    try {
        // Dapatkan vehicle_id sebelum menghapus
        $stmt = $conn->prepare("SELECT vehicle_id FROM rental WHERE rental_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rental_data = $result->fetch_assoc();
        $stmt->close();
        
        // Hapus data rental
        $stmt = $conn->prepare("DELETE FROM rental WHERE rental_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Update status kendaraan kembali ke Tersedia
        $update_stmt = $conn->prepare("UPDATE kendaraan SET status='Tersedia' WHERE vehicle_id=?");
        $update_stmt->bind_param("s", $rental_data['vehicle_id']);
        $update_stmt->execute();
        $update_stmt->close();
        
        $messages[] = "Rental berhasil dihapus.";
    } catch (Exception $e) {
        $errors[] = "Gagal menghapus: ".$e->getMessage();
    }
}

// Ambil data rental dengan join
$rental_rows = $conn->query("SELECT r.*, k.brand, k.model, p.name 
                           FROM rental r
                           LEFT JOIN kendaraan k ON r.vehicle_id = k.vehicle_id
                           LEFT JOIN penyewa p ON r.renter_id = p.renter_id
                           ORDER BY r.rental_id ASC");

// Untuk form edit
$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM rental WHERE rental_id=?");
    $stmt->bind_param("i", $id);
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

    <section id="rental-section" class="card">
        <h2>Rental</h2>
        <button onclick="location.href='rental.php?action=create'">Tambah Rental</button>
        <table aria-label="Daftar Rental">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kendaraan</th>
                    <th>Penyewa</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $rental_rows->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['rental_id'] ?></td>
                    <td><?= $row['brand'] ?> <?= $row['model'] ?> (<?= $row['vehicle_id'] ?>)</td>
                    <td><?= $row['name'] ?> (ID: <?= $row['renter_id'] ?>)</td>
                    <td><?= date('d/m/Y', strtotime($row['start_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['end_date'])) ?></td>
                    <td><?= number_format($row['total_price'], 0, ',', '.') ?></td>
                    <td class="actions">
                        <a href="rental.php?action=edit&id=<?= $row['rental_id'] ?>">Edit</a>
                        <a href="rental.php?action=delete&id=<?= $row['rental_id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <?php if ($action === 'create' || $action === 'edit'): ?>
    <section id="create-section" class="card">
        <h2><?= ($action === 'create') ? 'Tambah' : 'Edit' ?> Rental</h2>
        <form method="POST">
            <input type="hidden" name="form_type" value="rental_<?= ($action === 'create') ? 'create' : 'update' ?>">
            <input type="hidden" name="rental_id" value="<?= isset($edit_data['rental_id']) ? $edit_data['rental_id'] : '' ?>">
            
            <div class="form-group">
                <label for="vehicle_id">Kendaraan</label>
                <select name="vehicle_id" id="vehicle_id" required>
                    <option value="">Pilih Kendaraan</option>
                    <?php while($kendaraan = $kendaraan_list->fetch_assoc()): ?>
                        <option value="<?= $kendaraan['vehicle_id'] ?>" 
                            <?= isset($edit_data['vehicle_id']) && $edit_data['vehicle_id'] == $kendaraan['vehicle_id'] ? 'selected' : '' ?>>
                            <?= $kendaraan['brand'] ?> <?= $kendaraan['model'] ?> (<?= $kendaraan['vehicle_id'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="renter_id">Penyewa</label>
                <select name="renter_id" id="renter_id" required>
                    <option value="">Pilih Penyewa</option>
                    <?php while($penyewa = $penyewa_list->fetch_assoc()): ?>
                        <option value="<?= $penyewa['renter_id'] ?>" 
                            <?= isset($edit_data['renter_id']) && $edit_data['renter_id'] == $penyewa['renter_id'] ? 'selected' : '' ?>>
                            <?= $penyewa['name'] ?> (ID: <?= $penyewa['renter_id'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_date">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" 
                       value="<?= isset($edit_data['start_date']) ? $edit_data['start_date'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="end_date">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" 
                       value="<?= isset($edit_data['end_date']) ? $edit_data['end_date'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="total_price">Total Harga</label>
                <input type="number" name="total_price" id="total_price" 
                       value="<?= isset($edit_data['total_price']) ? $edit_data['total_price'] : '' ?>" required>
            </div>
            
            <button type="submit">Simpan</button>
            <button type="button" onclick="location.href='rental.php'">Batal</button>
        </form>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>