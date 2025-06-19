<?php

use Foodboard\Config;

require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/../database/db.php'; 

// Ambil semua nama produk unik yang sudah punya varian 'medium' dan harga valid
$namaProdukList = [];
$sqlNamaProduk = "
	SELECT DISTINCT p.name
	FROM products p
	JOIN product_variants pv
		ON pv.product_id = p.id
	WHERE pv.category = 'size'
		AND LOWER(pv.variant) = 'medium'
		AND pv.price IS NOT NULL AND pv.price > 0
";

$resultNamaProduk = $conn->query($sqlNamaProduk);
if ($resultNamaProduk && $resultNamaProduk->num_rows > 0) {
	while ($row = $resultNamaProduk->fetch_assoc()) {
		$namaProdukList[] = $row['name'];
	}
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
							<h1><a href="../index.php" title="Tulang Rangu">Juragan Tulang Rangu</a></h1>
						</div>
					</div>
					<div class="col-lg-9 col-6">
						<ul id="menuIcons">
							<li><a href="../database/login.php"><i class="fas fa-user"></i></a></li>
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
				<h1>Pay with cash on delivery</h1>
			</div>
		</div>
		<!-- Sub Header End -->

		<!-- Main -->
		<main>
			<!-- Order -->
			<div class="order">
				<!-- Container -->
				<div class="container">
					<!-- Row -->
					<div class="row">
						<!-- Left Sidebar -->
						<div class="col-lg-8" id="mainContent">
							<!-- Filter Area -->
							<div class="row filter-box filters">
								<div class="filter-box-header">
									<h3>Filters</h3>
									<span class="filter-box-link isotope-reset">Reset Filters</span>
								</div>
								<div class="col-md-6 col-sm-6">
									<select id="category" class="wide price-list" name="category">
										<option value="">Show all</option>
										<?php foreach ($namaProdukList as $nama): ?>
											<?php
												$slugNama = strtolower(preg_replace('/\s+/', '', $nama));
											?>
											<option value=".<?= $slugNama ?>"><?= htmlspecialchars($nama) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-6 col-sm-6">
									<div class="search-wrap">
										<input id="search" type="text" class="form-control" placeholder="Search..." />
										<i class="icon icon-search"></i>
									</div>
								</div>
							</div>
							<!-- Filter Area End -->
							<!-- Grid Items -->
							<div class="row grid">
								<?php
								require_once __DIR__ . '/../database/db.php';

								// Ambil semua produk dan salah satu varian size pertama (jika ada)
								$sql = "
								SELECT
									p.id, p.name, p.description, p.image_path, p.label, p.stock,
									pv.id as option_id,
									pv.variant,
									pv.price
								FROM products p
								LEFT JOIN product_variants pv
									ON pv.product_id = p.id AND pv.category = 'size' AND LOWER(pv.variant) = 'medium'
								ORDER BY p.created_at DESC
								";

								$result = $conn->query($sql);
								$index = 1;
								$basePath = dirname($_SERVER['SCRIPT_NAME'], 2) . '/uploads/';

								if ($result && $result->num_rows > 0):
									while ($row = $result->fetch_assoc()):
										// Lewatkan produk jika tidak ada varian atau harga
										if (empty($row['variant']) || $row['price'] === null || $row['price'] == 0) {
											continue;
										}
										
										$id = $row['id'];
										$name = htmlspecialchars($row['name']);
										$desc = htmlspecialchars($row['description']);
										$image = trim(str_replace('uploads/', '', $row['image_path']));
										$imageUrl = $basePath . $image;
										$label = htmlspecialchars($row['label']);
										$stock = (int)$row['stock'];
										$size = $row['variant'] ?? 'Medium';
										$price = number_format((int)($row['price'] ?? 0), 0, ',', '.');
										$slug = strtolower(preg_replace('/\s+/', '', $name));
								?>

									<div id="gridItem<?= str_pad($index, 2, '0', STR_PAD_LEFT) ?>" class="col-xl-6 col-lg-6 col-md-6 col-sm-6 isotope-item <?= $slug ?>">
										<div class="item-body">
											<figure>
												<img src="<?= $imageUrl ?>" class="img-fluid" alt="<?= $name ?>">
												<a href="#modalDetailsItem<?= $id ?>" class="item-body-link modal-opener">
													<?php if (!empty($label)): ?>
														<?php if (strtolower($label) === 'hot'): ?>
															<small class="red"><?= $label ?></small>
														<?php elseif (strtolower($label) === 'new'): ?>
															<small><?= $label ?></small>
														<?php else: ?>
															<small><?= $label ?></small>
														<?php endif; ?>
													<?php endif; ?>
													<div class="item-title">
														<h3><?= $name ?></h3>
														<small><?= $desc ?></small>
														<div class="mt-1">
															<span style="color: #fff;">Stok: <?= $stock ?></span>
														</div>
													</div>
												</a>

												<div class="ribbon-size px-2">
													<span>Size: <?= htmlspecialchars($size) ?></span>
												</div>
											</figure>
											<ul>
												<li>
													<a href="#modalOptionsItem<?= $id ?>" class="item-size modal-opener">Options</a>
												</li>
												<li>
													<span class="item-price format-price">Rp <?= $price ?></span>
												</li>
												<li>
													<a href="javascript:;" 
													class="add-options-item-to-cart"
													data-product-id="<?= $id ?>"
													data-option-id="<?= $row['option_id'] ?>"
													data-name="<?= $name ?>">
													<i class="icon icon-shopping-cart"></i>
													</a>
												</li>
											</ul>
										</div>
									</div>

									<!-- Modal Details Start -->
									<div id="modalDetailsItem<?= $id ?>" class="modal-popup zoom-anim-dialog mfp-hide">
										<div class="small-dialog-header">
											<h3><?= $name ?></h3>
										</div>
										<div class="content pb-1">
											<figure>
												<img src="<?= $imageUrl ?>" alt="<?= $name ?>" class="img-fluid">
											</figure>
											<h6 class="mb-1">Varian</h6>
											<p><?= $desc ?></p>
										</div>
										<div class="footer">
											<div class="row">
												<div class="col-4 pr-0">
													<button type="button" class="btn-modal-close">Close</button>
												</div>
											</div>
										</div>
									</div>
									<!-- Modal Details End -->

								<?php
									$index++;
									endwhile;
								else:
									echo "<p class='text-center'>Produk belum tersedia.</p>";
								endif;
								?>
							</div>
							<!-- Grid Items End -->

							<!-- Modal Options Start -->
							<?php
							require_once __DIR__ . '/../database/db.php';

							$sql = "SELECT * FROM product_variants ORDER BY product_id, category, id";
							$result = $conn->query($sql);
							$variants = [];

							if ($result && $result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									$category = $row['category'];
									$productId = $row['product_id'];
									$variants[$productId][$category][] = $row;
								}
							}
							?>

							<?php foreach ($variants as $productId => $productVariants): ?>
							<div id="modalOptionsItem<?= $productId ?>" class="modal-popup zoom-anim-dialog mfp-hide">
								<div class="small-dialog-header">
									<h3>Opsi Produk</h3>
									<div class="addedToCartMsgInModal">Item Added to cart</div>
									<div class="alreadyInCartMsgInModal">Item Already in cart</div>
								</div>
								<div class="content">

									<!-- Size (Radio Buttons) -->
									<?php if (!empty($productVariants['size'])): ?>
										<div class="row"><div class="col-12"><strong>Ukuran:</strong></div></div>
										<?php foreach ($productVariants['size'] as $var): ?>
										<div class="row">
											<div class="col-md-12 col-sm-12">
												<label class="cbx radio-wrapper">
													<input 
														type="radio"
														class="size-variant"
														name="size-options-item-<?= $productId ?>"
														data-product-id="<?= $productId ?>"
														value="<?= $var['id'] ?>"
														data-price="<?= $var['price'] ?>"
														<?= strtolower($var['variant']) === 'medium' ? 'checked' : '' ?>>
													<span class="checkmark"></span>
													<span class="radio-caption"><?= htmlspecialchars($var['variant']) ?></span>
													<span class="option-price format-price">Rp <?= number_format($var['price'], 0, ',', '.') ?></span>
												</label>
											</div>
										</div>
										<?php endforeach; ?>
									<?php endif; ?>

									<!-- Extra (Checkboxes) -->
									<?php if (!empty($productVariants['extra'])): ?>
										<div class="row"><div class="col-12"><strong>Tambahan:</strong></div></div>
										<?php foreach ($productVariants['extra'] as $var): ?>
										<div class="row">
											<div class="col-md-12 col-sm-12">
												<input 
													type="checkbox"
													id="item<?= $productId ?>Extra<?= $var['id'] ?>"
													class="inp-cbx extra-variant"
													name="extra-options-item-<?= $productId ?>[]"
													value="<?= $var['id'] ?>"
													data-price="<?= $var['price'] ?>" />
												<label class="cbx mb-0" for="item<?= $productId ?>Extra<?= $var['id'] ?>">
													<span>
														<svg width="12px" height="10px" viewbox="0 0 12 10">
															<polyline points="1.5 6 4.5 9 10.5 1"></polyline>
														</svg>
													</span>
													<span><?= htmlspecialchars($var['variant']) ?></span>
													<span class="option-price format-price">Rp <?= number_format($var['price'], 0, ',', '.') ?></span>
												</label>
											</div>
										</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
								<div class="footer">
									<div class="row">
										<div class="col-4 pr-0">
											<button type="button" class="btn-modal-close">Close</button>
										</div>
										<div class="col-8">
											<button type="button" class="btn-modal add-options-item-to-cart" data-product-id="<?= $productId ?>">Add to Cart</button>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
							<!-- Modal Options End -->
						</div>
						<!-- Left Sidebar End -->
						<!-- Right Sidebar -->
						<div class="col-lg-4" id="sidebar">
							<!-- Order Container -->
							<div id="orderContainer" class="theiaStickySidebar">
								<!-- Form -->
								<form method="POST" id="orderForm" name="orderForm" onsubmit="return confirmGuestOrder(event);">

									<!-- Step 1: Order Summary -->
									<div id="#orderSummaryStep" class="step">
										<div class="order-header">
											<h3>Order Summary 1/2</h3>
										</div>

										<div class="order-body">
											<!-- Cart Items -->
											<div class="row">
												<div class="col-md-12">
													<div class="order-list">
														<ul id="itemList">
															<!-- Cart Items / will be generated by js -->
														</ul>
													</div>
												</div>
											</div>
											<!-- Cart Items End -->
											<!-- Shipping Method -->
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="shippingMethod">Shipping Method</label>
														<select id="shippingMethod" name="shipping_method" class="form-control" required>
															<option value="delivery" selected>Delivery</option>
															<option value="pickup">Pickup (Take Away)</option>
														</select>
													</div>
												</div>
											</div>
											<!-- shipping Method -->
											<!-- Total -->
											<div class="row total-container">
												<div class="col-md-12 p-0">
													<span class="totalTitle">Total</span><span class="totalValue format-price float-right">0</span>
													<input type="hidden" id="totalOrderSummary" class="total format-price" name="total" value="" data-parsley-errors-container="#totalError" data-parsley-empty-order="" disabled />
												</div>
											</div>
											<div id="totalError"></div>
											<!-- Total End -->
											<!-- Forward Button -->
											<div class="row">
												<div class="col-md-12">
													<button type="button" name="forward" class="btn-form-func forward">
														<span class="btn-form-func-content">Continue Order</span>
														<span class="icon"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
													</button>
												</div>
											</div>
											<!-- Forward Button End -->
										</div>
									</div>
									<!-- Step 1: Order Summary End -->

									<!-- Step 2: Checkout -->
									<div class="step">
										<div class="order-header">
											<h3>Order Summary 2/2</h3>
										</div>
										<div id="personalDetails" data-return-url='<?php echo Config::THANKYOU_URL; ?>' data-currency='<?php echo Config::CURRENCY; ?>'>
											<div class="order-body">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="userNameCashPayment">Full Name</label>
															<input id="userNameCashPayment" class="form-control" name="username" type="text" data-parsley-pattern="^[a-zA-Z\s.]+$" required />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="phoneCashPayment">Phone (62)</label>
															<input id="phoneCashPayment" class="form-control" name="phone" type="text" data-parsley-pattern="^62[0-9]+$" required />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="emailCashPayment">Email</label>
															<input id="emailCashPayment" class="form-control" name="email" type="email" required />
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12 col-sm-6">
														<div class="form-group">
															<label for="addressCashPayment">Delivery Address</label>
															<input id="addressCashPayment" class="form-control" name="address" type="text" data-parsley-pattern="^[,.a-zA-Z0-9\s.]+$" required />
														</div>
													</div>
												</div>
												<!-- Delivery Zone -->
												<div class="row" id="zoneContainer">
													<div class="col-md-12 col-sm-6">
														<div class="form-group">
															<label for="deliveryZone">Delivery Zone</label>
															<select id="deliveryZone" name="delivery_zone" class="form-control" required>
																<option value="">-- Select Zone --</option>
																<!-- Options dimuat dari database -->
															</select>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label for="messageCashPayment">Message</label>
															<input id="messageCashPayment" class="form-control" name="message" type="text" data-parsley-pattern="^[a-zA-Z0-9\s.:,!?']+$" />
														</div>
													</div>
												</div>
												<div class="row total-container">
													<div class="col-md-12 p-0">
														<span class="totalTitle">Total</span><span class="totalValue format-price float-right">0</span>
													</div>
												</div>
												<div class="row">
													<div class="col-6 pr-0">
														<div class="form-group">
															<input type="checkbox" id="cbxCashPayment" class="inp-cbx" name="terms" value="yes" required />
															<label class="cbx terms" for="cbxCashPayment">
																<span>
																	<svg width="12px" height="10px" viewbox="0 0 12 10">
																		<polyline points="1.5 6 4.5 9 10.5 1"></polyline>
																	</svg>
																</span>
																<span>Accept<a href="../pdf/terms.pdf" class="terms-link" target="_blank">Terms</a>.</span>
															</label>
														</div>
													</div>
													<div class="col-6 pl-0">
														<a href="javascript:;" class="modify-order backward">Modify Order</a>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<input type="hidden" name="payment_method" value="COD"> 
														<button type="submit" name="submit" id="submitOrder" class="btn-form-func">
															<span class="btn-form-func-content">Submit</span>
															<span class="icon"><i class="fa fa-check" aria-hidden="true"></i></span>
														</button>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<button type="button" name="backward" class="btn-form-func btn-form-func-alt-color backward">
															<span class="btn-form-func-content">Back</span>
															<span class="icon"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
														</button>
													</div>
												</div>
												<div class="row footer">
													<div class="col-md-12 text-center">
														<small>© 2025 Juragan Tulang Rangu Karawang</small>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- Step 2: Checkout End -->

								</form>
								<!-- Form End -->
							</div>
							<!-- Order Container End -->
						</div>
						<!-- Right Sidebar End -->
					</div>
					<!-- Row End -->
				</div>
				<!-- Container End -->
			</div>
			<!-- Order End -->

		</main>
		<!-- Main End -->

		<!-- Footer -->
		<footer class="main-footer">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<h5 class="footer-heading">Menu Links</h5>
						<ul class="list-unstyled nav-links">
							<li><i class="fa fa-angle-right"></i> <a href="index.html" class="footer-link">Home</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="faq.html" class="footer-link">FAQ</a></li>
							<li><i class="fa fa-angle-right"></i> <a href="contacts.html" class="footer-link">Contacts</a></li>
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
							<li><i class="icon icon-map-marker"></i><a href="https://maps.app.goo.gl/3kMUttsyy6Fy6rXi8" class="footer-link" target="_blank">Address: Stadion Singaperbangsa, Karawang, Indonesia</a></li>
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

		<!-- Notification Messages -->
		<div class="addedToCartMsg">Item Added to cart</div>
	</div>
	<!-- Page End -->

	<!-- Modal Warning Generic -->
	<div id="modalWarningGeneric" class="modal-popup zoom-anim-dialog mfp-hide">
		<div class="small-dialog-header">
			<h3>Warning</h3>
		</div>
		<div class="content">
			<h6 class="mb-0 warning-text">Terjadi kesalahan.</h6>
		</div>
		<div class="footer">
			<div class="row">
				<div class="col-4 pr-0">
					<button type="button" class="btn-modal-close">Got it</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Warning Generic End -->


	<!-- Modal Warning Qty min. Limit -->
	<div id="modalWarningQtyMinLimit" class="modal-popup zoom-anim-dialog mfp-hide">
		<div class="small-dialog-header">
			<h3>Warning</h3>
		</div>
		<div class="content">
			<h6 class="mb-0">Quantity minimum limit is: 1 !</h6>
		</div>
		<div class="footer">
			<div class="row">
				<div class="col-4 pr-0">
					<button type="button" class="btn-modal-close">Got it</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Warning Qty min. Limit End -->

	<!-- Modal Warning Qty max. Limit -->
	<div id="modalWarningQtyMaxLimit" class="modal-popup zoom-anim-dialog mfp-hide">
		<div class="small-dialog-header">
			<h3>Warning</h3>
		</div>
		<div class="content">
			<h6 class="mb-0">Quantity maximum limit is: 10 !</h6>
		</div>
		<div class="footer">
			<div class="row">
				<div class="col-4 pr-0">
					<button type="button" class="btn-modal-close">Got it</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Warning Qty max. Limit End -->

	<!-- Modal Confirm Delete -->
	<div id="modalConfirmDeleteCart" class="modal-popup zoom-anim-dialog mfp-hide">
		<div class="small-dialog-header">
			<h3>Konfirmasi</h3>
		</div>
		<div class="content">
			<h6 class="mb-0">Yakin ingin menghapus item ini dari keranjang?</h6>
		</div>
		<div class="footer">
			<div class="row">
				<div class="col-6 pr-0">
					<button type="button" class="btn-modal-close">Batal</button>
				</div>
				<div class="col-6 pl-0">
					<button type="button" class="btn-confirm-delete">Hapus</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal Confirm Delete End -->

	<!-- Back to top button -->
	<div id="toTop"><i class="icon icon-chevron-up"></i></div>

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
</body>

</html>