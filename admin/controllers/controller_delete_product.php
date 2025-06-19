<?php
include '../../database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Hapus data berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redirect balik ke halaman daftar produk
    header("Location: ../views/view_add_product.php");
    exit();
}
