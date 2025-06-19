<?php
$currentPage = basename($_SERVER['PHP_SELF']);
function isActive($page) {
    global $currentPage;
    return $currentPage === $page ? 'active text-white bg-primary' : 'text-dark';
}
?>

<!-- Sidebar -->
<div class="d-flex flex-column flex-shrink-0 p-3 bg-light border-end shadow-sm" style="width: 250px; height: 100vh; position: fixed;">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none text-dark">
        <span class="fs-4 fw-bold">Admin Panel</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="../../index.php" class="nav-link <?= isActive('index.php') ?>">
                <i class="fas fa-home"></i> Main Page
            </a>
        </li>
        <li class="nav-item">
            <a href="view_add_product.php" class="nav-link <?= isActive('admin.php') ?>">
                <i class="fas fa-box me-2"></i> Menu
            </a>
        </li>
        <li>
            <a href="product_variants_manage.php" class="nav-link <?= isActive('product_option.php') ?>">
                <i class="fas fa-tags me-2"></i> Product Variants
            </a>
        </li>
        <li>
            <a href="financial_record.php" class="nav-link <?= isActive('financial_record.php') ?>">
                <i class="fas fa-file-invoice-dollar me-2"></i> Pencatatan Keuangan
            </a>
        </li>
        <li>
            <a href="transaction_history.php" class="nav-link <?= isActive('transaction_history.php') ?>">
                <i class="fas fa-history me-2"></i> History Transaksi
            </a>
        </li>
        <li>
            <a href="register_user_view.php" class="nav-link <?= isActive('register_internal.php') ?>">
                <i class="fas fa-user-plus me-2"></i> Register Karyawan
            </a>
        </li>
    </ul>
    <hr>
    <div>
        <a href="../../db/logout.php" class="btn btn-outline-danger w-100">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>
</div>