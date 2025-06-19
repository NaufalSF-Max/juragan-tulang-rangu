<?php
include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['variant_id'])) {
    $variantId = (int)$_POST['variant_id'];
    
    $stmt = $conn->prepare("DELETE FROM product_variants WHERE id = ?");
    $stmt->bind_param("i", $variantId);
    $stmt->execute();
    
    header("Location: ../views/product_variants_manage.php");
    exit();
}

header("Location: ../views/product_variants_manage.php");
exit();
?>