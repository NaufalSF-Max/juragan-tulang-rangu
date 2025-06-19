<?php
include '../../database/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Ambil path gambar terlebih dahulu
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    // Hapus file gambar dari folder uploads jika ada
    if (!empty($imagePath)) {
        $fullPath = '../../' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath); // Menghapus file dari server
        }
    }

    // Hapus data produk dari database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Redirect balik ke halaman daftar produk
    header("Location: ../views/view_add_product.php");
    exit();
}
