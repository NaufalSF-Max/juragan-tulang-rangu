<?php
// session_start();
include '../../database/db.php';

// Redirect if not admin or employee
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'employee'])) {
    header("Location: ../../login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $label = $_POST['label'] ?? null;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $imagePath = '';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = '../../uploads/';
        $fileName = basename($_FILES['image']['name']);
        $targetFile = $targetDir . time() . '_' . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = str_replace('../../', '', $targetFile); // store as relative path
        }
    }

    // Insert into products table
    $stmt = $conn->prepare("INSERT INTO products (name, description, image_path, label, stock, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sss si", $name, $description, $imagePath, $label, $stock);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/view_add_product.php?success=1");
    exit();
}

// Fetch existing products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}