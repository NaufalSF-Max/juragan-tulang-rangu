<?php
session_start();
require_once __DIR__ . '/../../../database/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId    = $_SESSION['user']['id'];
$productId = $_POST['product_id'] ?? null;
$optionId  = $_POST['option_id'] ?? null;
$quantity  = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$rawExtraIds = $_POST['extra_ids'] ?? [];
if (!is_array($rawExtraIds)) {
    $rawExtraIds = [$rawExtraIds]; // konversi string jadi array jika perlu
}
$extraIds = array_filter($rawExtraIds);
sort($extraIds);
$extraIdsStr = implode(',', $extraIds);

// Ambil harga size
$sql = "SELECT price FROM product_variants WHERE id = ? AND product_id = ? AND category = 'size'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $optionId, $productId);
$stmt->execute();
$variant = $stmt->get_result()->fetch_assoc();

if (!$variant) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product variant']);
    exit;
}

$basePrice = (int)$variant['price'];
$totalExtraPrice = 0;

if (!empty($extraIds)) {
    $placeholders = implode(',', array_fill(0, count($extraIds), '?'));
    $types = str_repeat('i', count($extraIds));
    $sqlExtras = "SELECT price FROM product_variants WHERE id IN ($placeholders) AND product_id = ? AND category = 'extra'";
    $stmtExtras = $conn->prepare($sqlExtras);
    $params = array_merge($extraIds, [$productId]);
    $stmtExtras->bind_param($types . 'i', ...$params);
    $stmtExtras->execute();
    $resExtras = $stmtExtras->get_result();
    while ($row = $resExtras->fetch_assoc()) {
        $totalExtraPrice += (int)$row['price'];
    }
}

$finalPrice = $basePrice + $totalExtraPrice;

// Cek apakah item dengan kombinasi sama sudah ada
$sqlCheck = "SELECT id, quantity, extra_ids FROM carts WHERE user_id = ? AND product_id = ? AND option_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("sii", $userId, $productId, $optionId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

$cartIdToUpdate = null;

while ($row = $resultCheck->fetch_assoc()) {
    $dbExtraIds = array_filter(explode(',', $row['extra_ids'] ?? ''));
    sort($dbExtraIds);
    if (implode(',', $dbExtraIds) === $extraIdsStr) {
        $cartIdToUpdate = $row['id'];
        $currentQty = (int)$row['quantity'];
        break;
    }
}

if ($cartIdToUpdate) {
    $newQty = $currentQty + $quantity;
    if ($newQty > 10) {
        echo json_encode(['status' => 'error', 'message' => 'Quantity maximum limit is: 10 !']);
        exit;
    }

    $updateStmt = $conn->prepare("UPDATE carts SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $updateStmt->bind_param("ii", $newQty, $cartIdToUpdate);
    $updateStmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
} else {
    $insertStmt = $conn->prepare("INSERT INTO carts (user_id, product_id, option_id, extra_ids, quantity, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $insertStmt->bind_param("siisi", $userId, $productId, $optionId, $extraIdsStr, $quantity);
    $insertStmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
}