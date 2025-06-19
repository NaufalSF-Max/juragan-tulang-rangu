<?php
session_start();
require_once __DIR__ . '/../../../database/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user']['id'];

$sql = "
    SELECT 
        c.id AS cart_id,
        c.product_id,
        p.name,
        p.image_path,
        v.variant,
        v.price,
        c.quantity,
        c.extra_ids
    FROM carts c
    JOIN products p ON c.product_id = p.id
    JOIN product_variants v ON c.option_id = v.id
    WHERE c.user_id = ?
    AND COALESCE(c.updated_at, c.created_at) >= NOW() - INTERVAL 1 DAY
    ORDER BY COALESCE(c.updated_at, c.created_at) DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];

while ($row = $result->fetch_assoc()) {
    $item = [
        'cart_id' => $row['cart_id'],
        'name' => $row['name'],
        'image' => '/' . explode('/', $_SERVER['SCRIPT_NAME'])[1] . '/uploads/' . basename($row['image_path']),
        'variant' => $row['variant'],
        'price' => (int)$row['price'],
        'quantity' => (int)$row['quantity'],
    ];

    $extras = [];
    if (!empty($row['extra_ids'])) {
        $extraIds = explode(',', $row['extra_ids']);
        $placeholders = implode(',', array_fill(0, count($extraIds), '?'));
        $types = str_repeat('i', count($extraIds));

        $sqlExtras = "SELECT variant, price FROM product_variants WHERE id IN ($placeholders)";
        $stmtExtras = $conn->prepare($sqlExtras);
        $stmtExtras->bind_param($types, ...$extraIds);
        $stmtExtras->execute();
        $resExtras = $stmtExtras->get_result();

        while ($extra = $resExtras->fetch_assoc()) {
            $extras[] = [
                'variant' => $extra['variant'],
                'price' => (int)$extra['price'],
            ];
        }
    }

    $item['extras'] = $extras;
    $cartItems[] = $item;
}

$total_products_price = 0;
foreach ($cartItems as $item) {
    $itemPrice = $item['price'];
    if (!empty($item['extras'])) {
        foreach ($item['extras'] as $extra) {
            $itemPrice += $extra['price'];
        }
    }
    $total_products_price += $itemPrice * $item['quantity'];
}

echo json_encode([
    'status' => 'ok',
    'data' => $cartItems,
    'total_products_price' => $total_products_price
]);