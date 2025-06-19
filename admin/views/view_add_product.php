<?php
session_start();
include '../controllers/controller_add_product.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="p-4" style="margin-left:250px; width:100%;">

    <!-- dropdown profile -->
    <?php if (isset($_SESSION['user'])): ?>
    <div class="dropdown text-end mb-3">
        <button class="btn btn-outline-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
             <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user']['username']) ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="profil.php">Profil</a></li>
            <li><hr class="dropdown-divider"></li>
        </ul>
    </div>
    <?php endif; ?>

        <h2 class="mb-4">Add New Product</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Product added successfully!</div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label>Label <small>(optional)</small></label>
                <input type="text" name="label" class="form-control">
            </div>
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" required min="0" value="0">
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>

        <!-- Daftar Produk -->
        <h4>Produk Yang Tersedia</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Label</th>
                    <th>Stock</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="../../<?= htmlspecialchars($product['image_path']) ?>" width="50">
                        <?php else: ?>
                            <span>No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['label'] ?? '-') ?></td>
                    <td><?= (int)$product['stock'] ?></td>
                    <td><?= htmlspecialchars($product['created_at']) ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm me-1">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="../controllers/controller_delete_product.php" onsubmit="return confirm('Yakin ingin menghapus produk ini?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </form>
                    <td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>