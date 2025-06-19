<?php
require_once __DIR__ . '/../../../datase/db.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT id, name, city, fee FROM delivery_zones WHERE active = 1 ORDER BY city, name");
    $stmt->execute();
    $result = $stmt->get_result();

    $zones = [];
    while ($row = $result->fetch_assoc()) {
        $zones[] = [
            'id' => $row['id'],
            'label' => "{$row['name']} ({$row['city']})",
            'fee' => (int)$row['fee']
        ];
    }

    echo json_encode([
        'status' => 'ok',
        'data' => $zones
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}