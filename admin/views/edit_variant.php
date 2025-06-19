<?php
session_start();
include '../../db/db.php';
include '../includes/auth_check.php';

if (!isset($_GET['id'])) {
    header("Location: product_variants_manage.php");
    exit();
}

$variantId = intval($_GET['id']);

// Ambil data varian yang akan diedit
$stmt = $conn->prepare("SELECT v.*, p.name AS product_name 
                       FROM product_variants v
                       JOIN products p ON v.product_id = p.id
                       WHERE v.id = ?");
$stmt->bind_param("i", $variantId);
$stmt->execute();
$result = $stmt->get_result();
$variant = $result->fetch_assoc();
$stmt->close();

if (!$variant) {
    header("Location: product_variants_manage.php?error=notfound");
    exit();
}

$products = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product Variant</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            width: calc(100% - 250px);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <h2 class="mb-4">Edit Product Variant</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    if ($_GET['error'] == 'exists') {
                        echo "Varian dengan nama dan kategori tersebut sudah ada untuk produk ini!";
                    } elseif ($_GET['error'] == 'update') {
                        echo "Gagal mengupdate varian. Silakan coba lagi.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form action="../controllers/product_variants_controller.php" method="POST">
                <input type="hidden" name="variant_id" value="<?= $variant['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Choose Product --</option>
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $variant['product_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variant Name</label>
                    <input type="text" name="variant" class="form-control" 
                           value="<?= htmlspecialchars($variant['variant']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">-- Choose Category --</option>
                        <option value="size" <?= $variant['category'] === 'size' ? 'selected' : '' ?>>Size</option>
                        <option value="extra" <?= $variant['category'] === 'extra' ? 'selected' : '' ?>>Extra</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (Rp)</label>
                    <input type="number" name="price" class="form-control" 
                           value="<?= $variant['price'] ?>" required>
                </div>

                <button type="submit" name="update_variant" class="btn btn-primary">Update</button>
                <a href="product_variants_manage.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>