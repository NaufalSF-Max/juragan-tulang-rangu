<?php
session_start();
include '../../database/db.php';
include '../includes/auth_check.php';

$product_result = $conn->query("SELECT id, name FROM products");

$existing_variants = [];
$variant_check = $conn->query("SELECT product_id, variant, category FROM product_variants");
while ($row = $variant_check->fetch_assoc()) {
    $existing_variants[] = $row['product_id'] . '|' . strtolower($row['variant']) . '|' . $row['category'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Product Variants</title>
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
            <h2 class="mb-4">Add Product Variants</h2>
            <form action="../controllers/product_variants_controller.php" method="POST" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label class="form-label">Select Product</label>
                    <select id="product_id" name="product_id" class="form-select" required>
                        <option value="">-- Choose --</option>
                        <?php while ($product = $product_result->fetch_assoc()): ?>
                            <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variants Name</label>
                    <input type="text" id="variant" name="variant" class="form-control" placeholder="e.g. Medium, Extra Spicy" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <option value="size">Size</option>
                        <option value="extra">Extra</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (Rp)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <button type="submit" name="add_variant" class="btn btn-primary">Save</button>
            </form>

            <hr class="my-5">

            <h4>Varian yang Ada</h4>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Varian</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Action</th> 
                    </tr>
                </thead>
          <tbody>
            <?php
            $variant_query = "
            SELECT v.id, p.name AS product_name, v.variant, v.category, v.price
            FROM product_variants v
            JOIN products p ON v.product_id = p.id
            ORDER BY v.product_id, v.category, v.id
            ";
            $variants = $conn->query($variant_query);
            while ($v = $variants->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($v['product_name']) ?></td>
                <td><?= htmlspecialchars($v['variant']) ?></td>
                <td><?= htmlspecialchars($v['category']) ?></td>
                <td>Rp <?= number_format($v['price'], 0, ',', '.') ?></td>
                <td>
                    <a href="edit_variant.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="../controllers/product_variants_controller.php" method="POST" style="display:inline;">
                        <input type="hidden" name="variant_id" value="<?= $v['id'] ?>">
                        <button type="submit" name="delete_variant" class="btn btn-sm btn-danger" 
                        onclick="return confirm('Yakin ingin menghapus varian ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
             </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>

    <script>
        const existingVariants = <?= json_encode($existing_variants) ?>;

        function validateForm() {
            const productId = document.getElementById('product_id').value;
            const variant = document.getElementById('variant').value.trim().toLowerCase();
            const category = document.getElementById('category').value;
            const key = productId + '|' + variant + '|' + category;

            if (existingVariants.includes(key)) {
                alert('Varian tersebut sudah tersedia untuk produk ini.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>