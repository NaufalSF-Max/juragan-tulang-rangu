<?php
session_start();
require_once __DIR__ . '/../../../database/db.php';

$cartId = $_POST['cart_id'] ?? null;
$quantity = (int) ($_POST['quantity'] ?? 0);

if (!$cartId || $quantity < 1 || $quantity > 10) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
$stmt->bind_param("ii", $quantity, $cartId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}