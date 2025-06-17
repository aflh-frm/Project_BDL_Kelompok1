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
        if ($form_type === 'penyewa_create') {
            $name = clean_input($_POST['name']);
            $phone = clean_input($_POST['phone']);
            $email = clean_input($_POST['email']);
            
            $stmt = $conn->prepare("INSERT INTO penyewa (name, phone, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $phone, $email);
            $stmt->execute();
            $stmt->close();
            $messages[] = "Penyewa berhasil ditambahkan.";
            
        } elseif ($form_type === 'penyewa_update') {
            $renter_id = clean_input($_POST['renter_id']);
            $name = clean_input($_POST['name']);
            $phone = clean_input($_POST['phone']);
            $email = clean_input($_POST['email']);
            
            $stmt = $conn->prepare("UPDATE penyewa SET name=?, phone=?, email=? WHERE renter_id=?");
            $stmt->bind_param("sssi", $name, $phone, $email, $renter_id);
            $stmt->execute();
            $stmt->close();
            $messages[] = "Penyewa berhasil diperbarui.";
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Handle delete
if ($action === 'delete' && $id) {
    try {
        $stmt = $conn->prepare("DELETE FROM penyewa WHERE renter_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $messages[] = "Penyewa berhasil dihapus.";
    } catch (Exception $e) {
        $errors[] = "Gagal menghapus: ".$e->getMessage();
    }
}

// Ambil data penyewa
$penyewa_rows = $conn->query("SELECT * FROM penyewa ORDER BY name ASC");

// Untuk form edit
$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM penyewa WHERE renter_id=?");
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

    <section id="penyewa-section" class="card">
        <h2>Penyewa</h2>
        <button onclick="location.href='penyewa.php?action=create'">Tambah Penyewa</button>
        <table aria-label="Daftar Penyewa">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $penyewa_rows->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['renter_id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td class="actions">
                        <a href="penyewa.php?action=edit&id=<?= $row['renter_id'] ?>">Edit</a>
                        <a href="penyewa.php?action=delete&id=<?= $row['renter_id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <?php if ($action === 'create' || $action === 'edit'): ?>
    <section id="create-section" class="card">
        <h2><?= ($action === 'create') ? 'Tambah' : 'Edit' ?> Penyewa</h2>
        <form method="POST">
            <input type="hidden" name="form_type" value="penyewa_<?= ($action === 'create') ? 'create' : 'update' ?>">
            <input type="hidden" name="renter_id" value="<?= isset($edit_data['renter_id']) ? $edit_data['renter_id'] : '' ?>">
            
            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" name="name" id="name" 
                       value="<?= isset($edit_data['name']) ? $edit_data['name'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Telepon</label>
                <input type="text" name="phone" id="phone" 
                       value="<?= isset($edit_data['phone']) ? $edit_data['phone'] : '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" 
                       value="<?= isset($edit_data['email']) ? $edit_data['email'] : '' ?>" required>
            </div>
            
            <button type="submit">Simpan</button>
            <button type="button" onclick="location.href='penyewa.php'">Batal</button>
        </form>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>