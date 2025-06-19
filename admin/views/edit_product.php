<?php
include '../../db/db.php';

if (!isset($_GET['id'])) {
    header('Location: view_add_product.php');
    exit();
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $label = $_POST['label'] ?? '';
    $stock = (int)$_POST['stock'];

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, label = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("sssii", $name, $description, $label, $stock, $id);
    $stmt->execute();

    header("Location: view_add_product.php?success_edit=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Edit Produk</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label>Label</label>
                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($product['label']) ?>">
            </div>
            <div class="mb-3">
                <label>Stok</label>
                <input type="number" name="stock" class="form-control" required value="<?= (int)$product['stock'] ?>">
            </div>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="view_add_product.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
