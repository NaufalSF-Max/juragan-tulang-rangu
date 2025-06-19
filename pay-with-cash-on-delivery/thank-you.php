<?php

use Foodboard\Config;

session_start();
require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/../database/db.php';

if (!isset($_GET['invoice_id'])) {
    echo "<h2>Transaksi tidak ditemukan</h2>";
    exit;
}

$invoiceId = $_GET['invoice_id'];
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->bind_param("s", $invoiceId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>Transaksi tidak ditemukan</h2>";
    exit;
}

$trans = $result->fetch_assoc();
$stmt->close();

// Coba ambil data dari session jika ada
if (isset($_SESSION['last_transaction']) && $_SESSION['last_transaction']['id'] === $invoiceId) {
    $items = $_SESSION['last_transaction']['items'];
    $subtotal = $_SESSION['last_transaction']['total'] - $_SESSION['last_transaction']['delivery_fee'];
    $ongkir = $_SESSION['last_transaction']['delivery_fee'];
    $total = $_SESSION['last_transaction']['total'];

    // Hapus dari session agar tidak menumpuk
    unset($_SESSION['last_transaction']);
} else {
    // Tidak ada data di session, fallback manual (jika kelak ada tabel transaction_items)
    $items = []; // Atau beri pesan "tidak bisa tampilkan detail"
    $subtotal = $trans['total_price'] - $trans['delivery_fee'];
    $ongkir = $trans['delivery_fee'];
    $total = $trans['total_price'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Food order wizard with online payment">
    <meta name="author" content="UWS">
    <title>FoodBoard | Thank you</title>

    <!-- Favicon -->
    <link href="../img/favicon.png" rel="shortcut icon">

    <!-- Google Fonts - Jost -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom Font Icons -->
    <link href="../vendor/icomoon/css/iconfont.min.css" rel="stylesheet">

    <!-- Vendor CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/dmenu/css/menu.css" rel="stylesheet">
    <link href="../vendor/hamburgers/css/hamburgers.min.css" rel="stylesheet">
    <link href="../vendor/mmenu/css/mmenu.min.css" rel="stylesheet">
    <link href="../vendor/magnific-popup/css/magnific-popup.css" rel="stylesheet">
    <link href="../vendor/float-labels/css/float-labels.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link href="./../css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div>
    <!-- Preloader End -->

    <!-- Page -->
    <div id="page">

        <!-- Header -->
        <header class="main-header sticky">
            <a href="#menu" class="btn-mobile">
                <div class="hamburger hamburger--spin" id="hamburger">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div id="logo">
                            <h1><a href="https://ultimatewebsolutions.net/foodboard/" title="FoodBoard">FoodBoard</a></h1>
                        </div>
                    </div>
                    <div class="col-lg-9 col-6">
                        <ul id="menuIcons">
                            <li><a href="#"><i class="icon icon-support"></i></a></li>
                            <li><a href="#"><i class="icon icon-shopping-cart2"></i></a></li>
                        </ul>
                        <!-- Menu -->
                        <nav id="menu" class="main-menu">
                            <ul>
                                <li><span><a href="https://ultimatewebsolutions.net/foodboard/">Home</a></span></li>
                                <li>
                                    <span><a href="#">Order <i class="fa fa-chevron-down"></i></a></span>
                                    <ul>
                                        <li>
                                            <span><a href="#">Pay online</a></span>
                                            <ul>
                                                <li><a href="../pay-with-card-online/">Demo 1 - Filtering</a></li>
                                                <li><a href="../pay-with-card-online/order-2.php">Demo 2 - Sticky navigation</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                            <span><a href="#">Pay with cash</a></span>
                                            <ul>
                                                <li><a href="../pay-with-cash-on-delivery/">Demo 1 - Filtering</a></li>
                                                <li><a href="../pay-with-cash-on-delivery/order-2.php">Demo 2 - Sticky navigation</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li><span><a href="../faq.html">Faq</a></span></li>
                                <li><span><a href="../contacts.html">Contacts</a></span></li>
                            </ul>
                        </nav>
                        <!-- Menu End -->
                    </div>
                </div>
            </div>
        </header>
        <!-- Header End -->

        <!-- Sub Header -->
        <div class="sub-header">
            <div class="container">
                <h1>Order Confirmed</h1>
            </div>
        </div>
        <!-- Sub Header End -->

        <!-- Main -->
        <main>
            <div class="confirm-wrap">
                <!-- Container -->
                <div class="container">
                    <!-- Row -->
                    <div class="row">
                        <!-- Left Sidebar -->
                        <div class="col-lg-12" id="mainContent">
                            <!-- Filter Area -->
                            <h3>🎉 Terima kasih! Berikut detail pesanan Anda:</h3>
                            <table class="tbl-cart" cellpadding="10" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="text-left">Produk</th>
                                        <th class="table-th">Harga Satuan (<?php echo Config::CURRENCY_SYMBOL; ?>)</th>
                                        <th class="table-th">Jumlah</th>
                                        <th class="table-th">Subtotal (<?php echo Config::CURRENCY_SYMBOL; ?>)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="text-left">
                                            <?php
                                                // Tampilkan: Produk + (Extra: ...)
                                                $fullName = htmlspecialchars($item['name']);
                                                if (!empty($item['extras'])) {
                                                    $fullName .= ' (Extra: ' . htmlspecialchars(implode(', ', $item['extras'])) . ')';
                                                }
                                                echo $fullName;
                                            ?>
                                        </td>
                                        <td class="table-td"><?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                        <td class="table-td"><?php echo $item['quantity']; ?></td>
                                        <td class="table-td"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="sub_total">
                                    <td colspan="3" align="right"><strong>Ongkos Kirim (<?php echo Config::CURRENCY_SYMBOL; ?>)</strong></td>
                                    <td class="table-td"><?php echo number_format($ongkir ?? 0, 0, ',', '.'); ?></td>
                                </tr>
                                <tr class="sub_total">
                                    <td colspan="3" align="right"><strong>Total (<?php echo Config::CURRENCY_SYMBOL; ?>)</strong></td>
                                    <td class="table-td"><?php echo number_format($total, 0, ',', '.'); ?></td>
                                </tr>
                            </tbody>
                            </table>
                            <h3>📝 Detail Pembeli:</h3>
                            <ul>
                                <li><strong>Nama:</strong> <?php echo htmlspecialchars($trans['full_name']); ?></li>
                                <li><strong>Email:</strong> <?php echo htmlspecialchars($trans['email']); ?></li>
                                <li><strong>Telepon:</strong> <?php echo htmlspecialchars($trans['phone']); ?></li>
                                <li><strong>Alamat:</strong> <?php echo htmlspecialchars($trans['delivery_address']); ?></li>
                                <li><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($trans['payment_method']); ?></li>
                                <li><strong>Metode Pengiriman:</strong> <?php echo htmlspecialchars($trans['delivery_method']); ?></li>
                                <li><strong>Pesan:</strong> <?php echo htmlspecialchars($trans['message']); ?></li>
                            </ul>
                            <p class="mb-0"><a href="https://ultimatewebsolutions.net/foodboard/" class="btn-2">Back to Home</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Main End -->

        <!-- Footer -->
        <footer class="main-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <h5 class="footer-heading">Menu Links</h5>
                        <ul class="list-unstyled nav-links">
                            <li><i class="fa fa-angle-right"></i> <a href="https://ultimatewebsolutions.net/foodboard/" class="footer-link">Home</a></li>
                            <li><i class="fa fa-angle-right"></i> <a href="../faq.html" class="footer-link">FAQ</a></li>
                            <li><i class="fa fa-angle-right"></i> <a href="../contacts.html" class="footer-link">Contacts</a></li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h5 class="footer-heading">Order Wizard</h5>
                        <ul class="list-unstyled nav-links">
                            <li><i class="fa fa-angle-right"></i> <a href="../pay-with-card-online/" class="footer-link">Pay online</a></li>
                            <li><i class="fa fa-angle-right"></i> <a href="../pay-with-cash-on-delivery/" class="footer-link">Pay with cash on delivery</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5 class="footer-heading">Contacts</h5>
                        <ul class="list-unstyled contact-links">
                            <li><i class="icon icon-map-marker"></i><a href="https://goo.gl/maps/vKgGyZe2JSRLDnYH6" class="footer-link" target="_blank">Address: 1234 Street Name, City Name, USA</a>
                            </li>
                            <li><i class="icon icon-envelope3"></i><a href="mailto:info@yourdomain.com" class="footer-link">Mail: info@yourdomain.com</a></li>
                            <li><i class="icon icon-phone2"></i><a href="tel:+3630123456789" class="footer-link">Phone: +3630123456789</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                        <h5 class="footer-heading">Find Us On</h5>
                        <ul class="list-unstyled social-links">
                            <li><a href="https://facebook.com" class="social-link" target="_blank"><i class="icon icon-facebook"></i></a></li>
                            <li><a href="https://twitter.com" class="social-link" target="_blank"><i class="icon icon-twitter"></i></a></li>
                            <li><a href="https://instagram.com" class="social-link" target="_blank"><i class="icon icon-instagram"></i></a></li>
                            <li><a href="https://pinterest.com" class="social-link" target="_blank"><i class="icon icon-pinterest"></i></a></li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-8">
                        <ul id="subFooterLinks">
                            <li><a href="https://themeforest.net/user/ultimatewebsolutions" target="_blank">With <i class="fa fa-heart pulse"></i> by UWS</a></li>
                            <li><a href="../pdf/terms.pdf" target="_blank">Terms and conditions</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div id="copy">© 2021 FoodBoard</div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer End -->

    </div>
    <!-- Page End -->

    <!-- Vendor Javascript Files -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/easing/js/easing.min.js"></script>
    <script src="../vendor/parsley/js/parsley.min.js"></script>
    <script src="../vendor/nice-select/js/jquery.nice-select.min.js"></script>
    <script src="../vendor/price-format/js/jquery.priceformat.min.js"></script>
    <script src="../vendor/theia-sticky-sidebar/js/ResizeSensor.min.js"></script>
    <script src="../vendor/theia-sticky-sidebar/js/theia-sticky-sidebar.min.js"></script>
    <script src="../vendor/mmenu/js/mmenu.min.js"></script>
    <script src="../vendor/magnific-popup/js/jquery.magnific-popup.min.js"></script>
    <script src="../vendor/float-labels/js/float-labels.min.js"></script>
    <script src="../vendor/jquery-wizard/js/jquery-ui-1.8.22.min.js"></script>
    <script src="../vendor/jquery-wizard/js/jquery.wizard.js"></script>
    <script src="../vendor/isotope/js/isotope.pkgd.min.js"></script>
    <script src="../vendor/scrollreveal/js/scrollreveal.min.js"></script>
    <script src="../vendor/lazyload/js/lazyload.min.js"></script>
    <script src="../vendor/sticky-kit/js/sticky-kit.min.js"></script>

    <!-- Main Javascript File -->
    <script src="../js/scripts.js"></script>

    <?php unset($_SESSION['last_transaction']); ?>

</body>

</html>