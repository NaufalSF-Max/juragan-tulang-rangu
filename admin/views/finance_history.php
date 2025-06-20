<?php
// Koneksi database
include '../../database/db.php';

// Ambil data keuangan
$query = "SELECT * FROM daily_financial_records ORDER BY record_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pencatatan Keuangan Harian</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .container { margin-top: 40px; }
    h2 { margin-bottom: 20px; }
    .table th, .table td { vertical-align: middle; }
  </style>
</head>
<body class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="p-4" style="margin-left:250px; width:100%;">

    <div class="container">
    <h2 class="text-center">📊 Pencatatan Keuangan Harian</h2>
    
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Tanggal</th>
            <th>Pendapatan Produk</th>
            <th>Pendapatan Ongkir</th>
            <th>Total Pendapatan</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
            <td><?= htmlspecialchars($row['record_date']) ?></td>
            <td>Rp<?= number_format($row['product_income'], 0, ',', '.') ?></td>
            <td>Rp<?= number_format($row['delivery_income'], 0, ',', '.') ?></td>
            <td><strong>Rp<?= number_format($row['total_income'], 0, ',', '.') ?></strong></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
            <td colspan="4" class="text-center">Belum ada data keuangan.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

</body>
</html>
