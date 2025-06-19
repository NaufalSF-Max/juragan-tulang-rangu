<?php
session_start();
require_once __DIR__ . '/../../../database/db.php';

$cartId = $_POST['cart_id'] ?? null;

if (!$cartId) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM carts WHERE id = ?");
$stmt->bind_param("i", $cartId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}