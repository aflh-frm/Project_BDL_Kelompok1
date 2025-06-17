<?php
require_once 'config.php';
require_once 'function.php';

// Query view rental aktif
$rental_aktif = $conn->query("SELECT * FROM v_rental_aktif");

// Query agregat: Kendaraan paling populer
$kendaraan_populer = $conn->query("
    SELECT k.brand, k.model, COUNT(r.rental_id) AS total_rental
    FROM kendaraan k
    LEFT JOIN rental r ON k.vehicle_id = r.vehicle_id
    GROUP BY k.vehicle_id
    ORDER BY total_rental DESC
    LIMIT 1
")->fetch_assoc();

require_once 'header.php';
?>

<div class="container">
    <section class="card">
        <h2>Histori Rental</h2>
        <table>
            <thead>
                <tr>
                    <th>Kendaraan</th>
                    <th>Penyewa</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $rental_aktif->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['brand'] ?> <?= $row['model'] ?></td>
                    <td><?= $row['penyewa'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['start_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['end_date'])) ?></td>
                    <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Statistik</h2>
        <p>Kendaraan paling populer: 
            <strong><?= $kendaraan_populer['brand'] ?> <?= $kendaraan_populer['model'] ?></strong> 
            (disewa <?= $kendaraan_populer['total_rental'] ?> kali)
        </p>
    </section>
</div>

<?php require_once 'footer.php'; ?>