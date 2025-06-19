<?php
session_start();
include '../../database/db.php';

// Cek autentikasi admin atau karyawan
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'employee'])) {
    header("Location: ../../login.php");
    exit();
}

// Handle tambah varian produk
if (isset($_POST['add_variant'])) {
    $productId = $_POST['product_id'];
    $variant = trim($_POST['variant']);
    $category = $_POST['category']; // 'size' atau 'extra'
    $price = $_POST['price'];

    // Cegah duplikasi berdasarkan product_id + variant + category
    $stmt = $conn->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = ? AND LOWER(variant) = LOWER(?) AND category = ?");
    $stmt->bind_param("iss", $productId, $variant, $category);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        header("Location: ../views/product_variants_manage.php?error=exists");
        exit();
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO product_variants (product_id, variant, category, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $productId, $variant, $category, $price);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/product_variants_manage.php?success=1");
    exit();
}

// Handle update varian produk (BAGIAN BARU)
if (isset($_POST['update_variant'])) {
    $variantId = $_POST['variant_id'];
    $productId = $_POST['product_id'];
    $variant = trim($_POST['variant']);
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Validasi data
    if (empty($variant) || empty($price)) {
        header("Location: ../views/product_variants_manage.php?error=empty");
        exit();
    }

    // Cek duplikasi (kecuali untuk record ini)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM product_variants 
                          WHERE product_id = ? AND LOWER(variant) = LOWER(?) AND category = ? AND id != ?");
    $stmt->bind_param("issi", $productId, $variant, $category, $variantId);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        header("Location: ../views/edit_variant.php?id=$variantId&error=exists");
        exit();
    }

    // Update data
    $stmt = $conn->prepare("UPDATE product_variants 
                          SET product_id = ?, variant = ?, category = ?, price = ? 
                          WHERE id = ?");
    $stmt->bind_param("issii", $productId, $variant, $category, $price, $variantId);
    
    if ($stmt->execute()) {
        header("Location: ../views/product_variants_manage.php?success=updated");
    } else {
        header("Location: ../views/edit_variant.php?id=$variantId&error=update");
    }
    
    $stmt->close();
    exit();
}

// Handle hapus varian (BAGIAN BARU)
if (isset($_POST['delete_variant'])) {
    $variantId = $_POST['variant_id'];
    
    $stmt = $conn->prepare("DELETE FROM product_variants WHERE id = ?");
    $stmt->bind_param("i", $variantId);
    
    if ($stmt->execute()) {
        header("Location: ../views/product_variants_manage.php?success=deleted");
    } else {
        header("Location: ../views/product_variants_manage.php?error=delete");
    }
    
    $stmt->close();
    exit();
}
?>