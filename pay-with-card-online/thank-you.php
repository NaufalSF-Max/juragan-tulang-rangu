<?php
session_start();
use JuraganTulangRangu\Config;

session_start();
require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/../database/db.php';

$invoiceId = $_GET['invoice_id'] ?? $_SESSION['last_invoice_id'] ?? null;
unset($_SESSION['last_invoice_id']); // agar tidak diakses ulang

if (!$invoiceId) {
    echo "<h2>Transaksi tidak ditemukan</h2>";
    exit;
}

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
    <meta name="description" content="Guide to ordering food with online payment">
    <meta name="author" content="Juragan Tulang Rangu Karawang">
    <title>Juragan Tulang Rangu Karawang</title>

    <!-- Favicon -->
    <link href="../img/logo.svg" rel="shortcut icon">

    <!-- Google Fonts - Jost -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" >

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
    <link href="./../css/responsive.css" rel="stylesheet">
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
                            <h1><a href="../index.php" title="logo">Juragan Tulang Rangu</a></h1>
                        </div>
                    </div>
                    <div class="col-lg-9 col-6">
                        <ul id="menuIcons">
							<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'customer'): ?>
							<li class="nav-item dropdown d-flex align-items-center">
                                <a class="nav-link dropdown-toggle d-flex align-items-center gap-3" 
                                    href="#" 
                                    id="userDropdown" 
                                    role="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false"
                                    style="font-weight: 500; font-size: 16px; color: #800040;">
                                    <i class="fas fa-user me-1 margin-right: 12px;" style="font-size: 18px;"></i><?= htmlspecialchars($_SESSION['user']['username']) ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="../database/profil.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="../database/logout.php">Logout</a></li>
                                </ul>
							</li>
							<?php else: ?>
								<li><a href="../database/login.php"><i class="fas fa-user"></i></a></li>
							<?php endif; ?>
						</ul>
                        <!-- Menu -->
                        <nav id="menu" class="main-menu">
                            <ul>
                                <li><span><a href="../index.php">Home</a></span></li>
                                <li>
                                    <span><a href="#">Order <i class="fa fa-chevron-down"></i></a></span>
                                    <ul>
                                        <li>
                                            <a href="../pay-with-card-online/index.php">Pay online</a>                                        
                                        </li>
                                        <li>
                                            <a href="../pay-with-cash-on-delivery/index.php">Pay with cash</a>                                            
                                        </li>
                                    </ul>
                                </li>
                                <li><span><a href="../faq.php">Faq</a></span></li>
                                <li><span><a href="../contacts.php">Contacts</a></span></li>
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
                            <p class="mb-0"><a href="../" class="btn-2">Back to Home</a></p>
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
							<li><i class="fa fa-angle-right"></i> <a href="../index.php" class="footer-link">Home</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="../faq.php" class="footer-link">FAQ</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="../contacts.php" class="footer-link">Contacts</a></li>
						</ul>
					</div>
					<div class="col-md-3">
						<h5 class="footer-heading">Order</h5>
						<ul class="list-unstyled nav-links">
							<li><i class="fa fa-angle-right"></i> <a href="../pay-with-card-online/index.php" class="footer-link">Pay online</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="../pay-with-cash-on-delivery/index.php" class="footer-link">Pay with cash on delivery</a></li>
						</ul>
					</div>
					<div class="col-md-4">
						<h5 class="footer-heading">Contacts</h5>
						<ul class="list-unstyled contact-links">
							<li><i class="icon icon-map-marker"></i><a href="https://maps.app.goo.gl/3kMUttsyy6Fy6rXi8" class="footer-link" target="_blank">Address: Stadion Singaperbangsa, Karawang</a></li>
							<li><i class="icon icon-envelope3"></i><a href="mailto:tulangrangukarawang@gmail.com" class="footer-link">Mail: tulangrangukarawang@gmail.com</a></li>
							<li><i class="icon icon-phone2"></i><a href="tel:+6285817128530" class="footer-link">Phone: +6285817128530</a></li>
						</ul>
					</div>
					<div class="col-md-2">
						<h5 class="footer-heading">Find Us On</h5>
						<ul class="list-unstyled social-links">
							<li><a href="https://www.facebook.com/share/18uqwzb3FC/" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a></li>
							<li><a href="https://wa.me/6285817128530" class="social-link" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
							<li><a href="https://instagram.com/tulangrangu_karawang" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a></li>
							<li><a href="https://tiktok.com/@tulangrangu_karawangg" class="social-link" target="_blank"><i class="fab fa-tiktok"></i></a></li>
						</ul>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-8">
						<ul id="subFooterLinks">
							<li><a href="../img/kelompok2.jpg" target="_blank">With <i class="fa fa-heart pulse"></i> by Kelompok 2</a></li>
							<li><a href="../pdf/terms.pdf" target="_blank">Terms and conditions</a></li>
						</ul>
					</div>
					<div class="col-md-4">
						<div id="copy">© 2025 Juragan Tulang Rangu Karawang
						</div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>