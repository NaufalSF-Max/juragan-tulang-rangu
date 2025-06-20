<?php
include '../../database/db.php';

$query = "
  SELECT t.id, t.full_name, t.payment_method, t.delivery_method, 
         t.total_price, dz.name AS delivery_zone, t.created_at
  FROM transactions t
  LEFT JOIN delivery_zones dz ON t.delivery_zone_id = dz.id
  ORDER BY t.created_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Riwayat Transaksi</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="p-4" style="margin-left:250px; width:100%;">

    <div class="container mt-5">
    <h2>Riwayat Transaksi</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
        <tr>
            <th>ID Transaksi</th>
            <th>Nama</th>
            <th>Metode</th>
            <th>Pengiriman</th>
            <th>Zona</th>
            <th>Total</th>
            <th>Waktu</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= $row['payment_method'] ?></td>
            <td><?= $row['delivery_method'] ?></td>
            <td><?= $row['delivery_zone'] ?? 'Tidak Ada' ?></td>
            <td>Rp<?= number_format($row['total_price'], 0, ',', '.') ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</body>
</html>
