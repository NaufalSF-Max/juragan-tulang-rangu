<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Guide to ordering food with online payment">
	<meta name="author" content="Juragan Tulang Rangu Karawang">
	<title>FAQ - Juragan Tulang Rangu Karawang</title>

	<!-- Favicon -->
	<link href="img/logo.svg" rel="shortcut icon">

	<!-- Google Fonts - Jost -->
	<link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<!-- Font Awesome CSS -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" >
	
	<!-- Custom Font Icons -->
	<link href="vendor/icomoon/css/iconfont.min.css" rel="stylesheet">

	<!-- Vendor CSS -->
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="vendor/dmenu/css/menu.css" rel="stylesheet">
	<link href="vendor/hamburgers/css/hamburgers.min.css" rel="stylesheet">
	<link href="vendor/mmenu/css/mmenu.min.css" rel="stylesheet">
	<link href="vendor/magnific-popup/css/magnific-popup.css" rel="stylesheet">
	<link href="vendor/float-labels/css/float-labels.min.css" rel="stylesheet">

	<!-- Main CSS -->
	<link href="css/responsive.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

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
							<h1><a href="index.php" title="FoodBoard">Juragan Tulang Rangu</a></h1>
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
								<li><a class="dropdown-item" href="database/profil.php">Profile</a></li>
								<li><a class="dropdown-item" href="database/logout.php">Logout</a></li>
							</ul>
							</li>
							<?php else: ?>
								<li><a href="database/login.php"><i class="fas fa-user"></i></a></li>
							<?php endif; ?>
						</ul>
						<!-- Menu -->
						<nav id="menu" class="main-menu">
							<ul>
								<li><span><a href="index.php">Home</a></span></li>
								<li>
									<span><a href="#">Order <i class="fa fa-chevron-down"></i></a></span>
									<ul>
										<li>
											<a href="pay-with-card-online/index.php">Pay online</a>											
										</li>
										<li>
											<a href="pay-with-cash-on-delivery/index.php">Pay with cash</a>											
										</li>
									</ul>
								</li>
								<li><span><a href="faq.php">Faq</a></span></li>
								<li><span><a href="contacts.php">Contacts</a></span></li>
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
				<h1>Frequently Asked Questions</h1>
			</div>
		</div>
		<!-- Sub Header End -->

		<!-- Main -->
		<main>
			<!-- Faq -->
			<div class="faq">
				<div class="container">
					<div class="row">
						<!-- Faq Cards -->
						<div class="col-lg-12">
							<!-- Topic 1 -->
							<div class="main-title">
								<span><em></em></span>
								<h2>Introduction</h2>
							</div>
							<div role="tablist" class="faq-accordion" id="topic1">
								<!-- Card 1 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a data-toggle="collapse" href="#subTopic1" aria-expanded="true"><i class="indicator icon icon-minus"></i>Apa itu Tulang Rangu?</a>
										</h4>
									</div>
									<div id="subTopic1" class="collapse show" role="tabpanel" data-parent="#topic1">
										<div class="card-body">
											<p>
												Tulang Rangu adalah camilan khas berbahan dasar tulang ayam yang diolah dengan bumbu rempah-rempah pilihan, lalu digoreng hingga renyah. Cocok untuk teman makan nasi atau dijadikan camilan.
											</p>
										</div>
									</div>
								</div>
								<!-- Card 2 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic2" aria-expanded="false"><i class="indicator icon icon-plus"></i>Apakah tersedia varian rasa dan produk lain?</a>
										</h4>
									</div>
									<div id="subTopic2" class="collapse" role="tabpanel" data-parent="#topic1">
										<div class="card-body">
											<p>
												Varian rasa: Original, Chili Oil, Sambal Hijau.
												<br>Produk lain: Dimsum dan Ceker Dakball.
											</p>
										</div>
									</div>
								</div>
								<!-- Card 3 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic3" aria-expanded="false"><i class="indicator icon icon-plus"></i>Apakah produk ini halal dan tahan lama?</a>
										</h4>
									</div>
									<div id="subTopic3" class="collapse" role="tabpanel" data-parent="#topic1">
										<div class="card-body">
											<p>
												Produk menggunakan bahan halal dan higienis. Tahan 2 hari di suhu ruang, bisa lebih lama jika disimpan di kulkas.
											</p>										
										</div>
									</div>
								</div>
							</div>
							<!-- Topic 2 -->
							<div class="main-title">
								<span><em></em></span>
								<h2>Processing order</h2>
							</div>
							<div role="tablist" class="faq-accordion" id="topic2">
								<!-- Card 4 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a data-toggle="collapse" href="#subTopic4" aria-expanded="true"><i class="indicator icon icon-plus"></i>Apakah bisa pesan dalam jumlah besar?</a>
										</h4>
									</div>
									<div id="subTopic4" class="collapse" role="tabpanel" data-parent="#topic2">
										<div class="card-body">
											<p>
												Tentu. Kami melayani pemesanan untuk acara, hajatan, atau reseller. Silakan hubungi kami minimal H-3 sebelum pengiriman.
											</p>
										</div>
									</div>
								</div>
								<!-- Card 5 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic5" aria-expanded="false"><i class="indicator icon icon-plus"></i>Bagaimana cara pemesanan?</a>
										</h4>
									</div>
									<div id="subTopic5" class="collapse" role="tabpanel" data-parent="#topic2">
										<div class="card-body">
											<p>
												Pemesanan bisa dilakukan melalui:
												<br>• WhatsApp: 085817128530
												<br>• Instagram: @tulangrangu_karawang
												<br>• Website: (dapat ditambahkan jika sudah tersedia)
											</p>
										</div>
									</div>
								</div>
								<!-- Card 6 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic6" aria-expanded="false"><i class="indicator icon icon-plus"></i>Apakah bisa pre-order?</a>
										</h4>
									</div>
									<div id="subTopic6" class="collapse" role="tabpanel" data-parent="#topic2">
										<div class="card-body">
											<p>
												Ya, kami menyediakan layanan pre-order. Pesan maksimal H-2 via kontak atau media sosial kami
											</p>
										</div>
									</div>
								</div>
							</div>
							<!-- Topic 3 -->
							<div class="main-title">
								<span><em></em></span>
								<h2>Payment</h2>
							</div>
							<div role="tablist" class="faq-accordion" id="topic3">
								<!-- Card 7 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a data-toggle="collapse" href="#subTopic7" aria-expanded="true"><i class="indicator icon icon-plus"></i>Metode pembayaran apa saja yang disediakan?</a>
										</h4>
									</div>
									<div id="subTopic7" class="collapse" role="tabpanel" data-parent="#topic3">
										<div class="card-body">
											<p>
												Kami menerima:
												<br>• Cash (tunai)
												<br>• E-money (seperti OVO, DANA, GoPay)
											</p>											
										</div>
									</div>
								</div>
								<!-- Card 8 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic8" aria-expanded="false"><i class="indicator icon icon-plus"></i>Apakah bisa membayar menggunakan kartu credit / debit?</a>
										</h4>
									</div>
									<div id="subTopic8" class="collapse" role="tabpanel" data-parent="#topic3">
										<div class="card-body">
											<p>
												Ya, Anda dapat melakukan pembayaran menggunakan kartu kredit atau debit, namun saat ini metode tersebut hanya tersedia untuk transaksi langsung di tempat (on-site).
												<br>Kami menyediakan mesin EDC untuk memproses pembayaran kartu. Untuk pemesanan online melalui website, pembayaran hanya dapat dilakukan melalui metode lain seperti transfer bank atau e-wallet.
											</p>											
										</div>
									</div>
								</div>
								<!-- Card 9 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic9" aria-expanded="false"><i class="indicator icon icon-plus"></i>Apakah bisa membatalkan pembayaran?</a>
										</h4>
									</div>
									<div id="subTopic9" class="collapse" role="tabpanel" data-parent="#topic3">
										<div class="card-body">
											<p>
												Setelah payment berhasil, maka tidak ada refund 
											</p>											
										</div>
									</div>
								</div>
							</div>
							<!-- Topic 4 -->
							<div class="main-title">
								<span><em></em></span>
								<h2>Delivery</h2>
							</div>
							<div role="tablist" class="faq-accordion" id="topic4">
								<!-- Card 10 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a data-toggle="collapse" href="#subTopic10" aria-expanded="true"><i class="indicator icon icon-plus"></i>Apakah ada layanan pengiriman?</a>
										</h4>
									</div>
									<div id="subTopic10" class="collapse" role="tabpanel" data-parent="#topic4">
										<div class="card-body">
											<p>
												• COD (Cash on Delivery) untuk area lokal Karawang
												<br>• Pengiriman luar kota menggunakan jasa ekspedisi (ditambah ongkos kirim sesuai  daerah tujuan)
											</p>											
										</div>
									</div>
								</div>
								<!-- Card 11 -->
								<div class="card">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic11" aria-expanded="false"><i class="indicator icon icon-plus"></i>Apakah ada ongkir untuk delivery?</a>
										</h4>
									</div>
									<div id="subTopic11" class="collapse" role="tabpanel" data-parent="#topic4">
										<div class="card-body">
											<p>
												Ya, besarnya ongkir tergantung lokasi delivery yang dituju. untuk sekitar karawang Rp10.000 dan jika diluar itu akan di infokan lewat chat.
											</p>											
										</div>
									</div>
								</div>
								<!-- Card 12 -->
								<div class="card mb-0">
									<div class="card-header" role="tab">
										<h4 class="mb-0">
											<a class="collapsed" data-toggle="collapse" href="#subTopic12" aria-expanded="false"><i class="indicator icon icon-plus"></i>Lokasi mana aja yang dapat dijangkau dengan delivery?</a>
										</h4>
									</div>
									<div id="subTopic12" class="collapse" role="tabpanel" data-parent="#topic4">
										<div class="card-body">
											<p>
												Kami melakukan delivery instan(same-day delivery) untuk lokasi di Karawang, jika memesan di luar Karawang, maka estimasi sampai  tergantung jarak lokasi delivery.
											</p>											
										</div>
									</div>
								</div>
							</div>
							<!-- Topics End  -->
						</div>
						<!-- Faq Cards End -->
					</div>
				</div>
			</div>
			<!-- Faq End -->
		</main>
		<!-- Main End -->

		<!-- Footer -->
		<footer class="main-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<h5 class="footer-heading">Menu Links</h5>
						<ul class="list-unstyled nav-links">
							<li><i class="fa fa-angle-right"></i> <a href="index.php" class="footer-link">Home</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="faq.php" class="footer-link">FAQ</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="contacts.php" class="footer-link">Contacts</a></li>
						</ul>
					</div>
					<div class="col-md-3">
						<h5 class="footer-heading">Order</h5>
						<ul class="list-unstyled nav-links">
							<li><i class="fa fa-angle-right"></i> <a href="pay-with-card-online/index.php" class="footer-link">Pay online</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="pay-with-cash-on-delivery/index.php" class="footer-link">Pay with cash on delivery</a></li>
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
							<li><a href="img/kelompok2.jpg" target="_blank">With <i class="fa fa-heart pulse"></i> by Kelompok 2</a></li>
							<li><a href="pdf/terms.pdf" target="_blank">Terms and conditions</a></li>
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

	<!-- Back to top button -->
	<div id="toTop"><i class="icon icon-chevron-up"></i></div>

	<!-- Vendor Javascript Files -->
	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/easing/js/easing.min.js"></script>
	<script src="vendor/parsley/js/parsley.min.js"></script>
	<script src="vendor/nice-select/js/jquery.nice-select.min.js"></script>
	<script src="vendor/price-format/js/jquery.priceformat.min.js"></script>
	<script src="vendor/theia-sticky-sidebar/js/ResizeSensor.min.js"></script>
	<script src="vendor/theia-sticky-sidebar/js/theia-sticky-sidebar.min.js"></script>
	<script src="vendor/mmenu/js/mmenu.min.js"></script>
	<script src="vendor/magnific-popup/js/jquery.magnific-popup.min.js"></script>
	<script src="vendor/float-labels/js/float-labels.min.js"></script>
	<script src="vendor/jquery-wizard/js/jquery-ui-1.8.22.min.js"></script>
	<script src="vendor/jquery-wizard/js/jquery.wizard.js"></script>
	<script src="vendor/isotope/js/isotope.pkgd.min.js"></script>
	<script src="vendor/scrollreveal/js/scrollreveal.min.js"></script>
	<script src="vendor/lazyload/js/lazyload.min.js"></script>
	<script src="vendor/sticky-kit/js/sticky-kit.min.js"></script>

	<!-- Main Javascript File -->
	<script src="js/scripts.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>