(function ($) {

	"use strict";

	// =====================================================
	// PRELOADER
	// =====================================================
	$(window).on("load", function () {
		'use strict';
		$('[data-loader="circle-side"]').fadeOut();
		$('#preloader').delay(350).fadeOut('slow');
		var $hero = $('.hero-home .content');
		var $hero_v = $('#hero_video .content ');
		$hero.find('h3, p, form').addClass('fadeInUp animated');
		$hero.find('.btn-1').addClass('fadeIn animated');
		$hero_v.find('.h3, p, form').addClass('fadeInUp animated');
		$(window).scroll();
	})

	// =====================================================
	// SCROLL ANIMATIONS
	// =====================================================	
	window.sr = ScrollReveal();

	sr.reveal('.animated-element', {
		interval: 300,
		distance: '250px'
	});

	// =====================================================
	// BACK TO TOP BUTTON
	// =====================================================
	function scrollToTop() {
		$('html, body').animate({
			scrollTop: 0
		}, 500, 'easeInOutExpo');
	}

	$(window).on('scroll', function () {
		if ($(this).scrollTop() > 100) {
			$('#toTop').fadeIn('slow');
		} else {
			$('#toTop').fadeOut('slow');
		}
	});

	$('#toTop').on('click', function () {
		scrollToTop();
		return false;
	});

	// =====================================================
	// NAVBAR
	// =====================================================
	$(window).on('scroll load', function () {

		if ($(window).scrollTop() >= 1) {
			$('.main-header').addClass('active');
		} else {
			$('.main-header').removeClass('active');
		}

	});

	// Sticky nav
	$('.sticky-nav').stick_in_parent({
		offset_top: 0
	});

	// =====================================================
	// STICKY SIDEBAR SETUP
	// =====================================================
	$('#mainContent, #sidebar').theiaStickySidebar({
		additionalMarginTop: 90,
		updateSidebarHeight: false,
	});

	// =====================================================
	// WIZARD STEPS
	// =====================================================

	$('#sidebar').wizard({
		stepsWrapper: '#orderContainer',
		submit: '.submit',

		// Prevent moving forward if total is zero
		beforeForward: function (event, state) {
			if ($('.total').val() == 'Rp 0') {
				validateTotal();
				return false; // prevent moving forward
			}
		},

		// Reset validation and remove error notifications from the form
		beforeBackward: function (event, state) {
			$('#orderForm').parsley().reset();
			$('ul.parsley-errors-list').not(':has(li)').remove();
		}
	});

	// Go to "Order Summary" step if "Modify Order" is clicked
	$('.modify-order').on('click', function () {
		$('#sidebar').wizard('select', '#orderSummaryStep');
	});

	// =====================================================
	// ISOTOPE
	// =====================================================
	if ($('.isotope-item').length > 0) {

		// Quick search regex
		var qsRegex;
		var filterValue;

		// Init Isotope
		var $grid = $('.grid').isotope({
			itemSelector: '.isotope-item',			
			filter: function () {
				var $this = $(this);
				var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
				var selectResult = filterValue ? $this.is(filterValue) : true;
				return searchResult && selectResult;
			}
		});

		// Bind filter on select change
		$('#category').on('change', function () {
			// Get filter value from option value
			filterValue = $(this).val();
			$grid.isotope();
		});

		// Use value of search field to filter
		var $quicksearch = $('#search').keyup(debounce(function () {
			qsRegex = new RegExp($quicksearch.val(), 'gi');
			$grid.isotope();
		}));

		// Debounce so filtering doesn't happen every millisecond
		function debounce(fn, threshold) {
			var timeout;
			return function debounced() {
				if (timeout) {
					clearTimeout(timeout);
				}
				function delayed() {
					fn();
					timeout = null;
				}
				setTimeout(delayed, threshold || 100);
			};
		}

		// Reset filters
		$('.isotope-reset').on('click', function () {
			qsRegex = '';
			filterValue = '';

			$('#search').val('');
			$('#category').prop('selectedIndex', 0).niceSelect('update');;

			$grid.isotope();

		});
	}

	// =====================================================
	// MOBILE MENU
	// =====================================================
	var $menu = $("nav#menu").mmenu({
		"extensions": ["pagedim-black", "theme-white"], // "theme-dark" can be changed to: "theme-dark"
		counters: true,
		keyboardNavigation: {
			enable: true,
			enhance: true
		},
		navbar: {
			title: 'MENU'
		},
		navbars: [{
			position: 'bottom',
			content: ['<a href="#">© 2025 Juragan Tulang Rangu Karawang</a>']
		}]
	}, {
		// configuration
		clone: true,
	});
	var $icon = $("#hamburger");
	var API = $menu.data("mmenu");
	$icon.on("click", function () {
		API.open();
	});
	API.bind("open:finish", function () {
		setTimeout(function () {
			$icon.addClass("is-active");
		}, 100);
	});
	API.bind("close:finish", function () {
		setTimeout(function () {
			$icon.removeClass("is-active");
		}, 100);
	});

	// =====================================================
	// NICE SCROLL
	// =====================================================
	var position;

	$('a.nice-scroll').on('click', function (e) {
		e.preventDefault();
		position = $($(this).attr('href')).offset().top - 125;
		$('body, html').animate({
			scrollTop: position
		}, 500, 'easeInOutExpo');
	});

	$('#stickyNavItems a.sticky-nice-scroll').on('click', function (e) {
		e.preventDefault();
		position = $($(this).attr('href')).offset().top - 85;
		$('body, html').animate({
			scrollTop: position
		}, 500, 'easeInOutExpo');
	});

	// =====================================================
	// FAQ ACCORDION
	// =====================================================
	function toggleChevron(e) {
		$(e.target).prev('.card-header').find('i.indicator').toggleClass('icon-minus icon-plus');
	}
	$('.faq-accordion').on('hidden.bs.collapse shown.bs.collapse', toggleChevron);

	// =====================================================
	// GALLERY
	// =====================================================	
	$('.menu-gallery').each(function () {
		$(this).magnificPopup({
			delegate: 'figure a',
			type: 'image',
			preloader: true,
			gallery: {
				enabled: true
			}
		});
	});

	// =====================================================
	// MODAL
	// =====================================================
	function resetModalOptions() {
		$(':radio[value="Medium"]').prop('checked', true);
		$('.modal-popup .inp-cbx').prop('checked', false);
	}

	$('.modal-opener').magnificPopup({
		type: 'inline',
		fixedContentPos: true,
		fixedBgPos: true,
		closeOnBgClick: false,
		overflowY: 'auto',
		closeBtnInside: true,
		preloader: false,
		midClick: true,
		removalDelay: 300,
		closeMarkup: '<button title="%title%" type="button" class="mfp-close"></button>',
		mainClass: 'my-mfp-zoom-in',
		callbacks: {
			close: function () {
			resetModalOptions();
			}
		}
	});

	$('.btn-modal-close').on('click', function () {
		$.magnificPopup.close();
	});

	// =====================================================
	// INIT DROPDOWNS
	// =====================================================
	$('#category').niceSelect();

	// =====================================================
	// FORM LABELS
	// =====================================================
	new FloatLabels('#orderForm', {
		style: 1
	});

	// =====================================================
	// FORM INPUT VALIDATION
	// =====================================================

	// Quantity inputs
	$('.qty-input').on('keypress', function (event) {
		if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
			event.preventDefault();
		}
	});

	// Add custom empty order validation
	window.Parsley.addValidator('emptyOrder', {
		validateString: function (value) {
			return value !== 'Rp 0';
		},
		messages: {
			en: 'Order is empty.'
		}
	});

	// Clear parsley empty elements
	if ($('#orderForm').length > 0) {
		$('#orderForm').parsley().on('field:success', function () {
			$('ul.parsley-errors-list').not(':has(li)').remove();
		});
	}

	// =====================================================
	// HELPER FUNCTIONS
	// =====================================================

	// Function to format item prices usign priceFormat plugin
	function formatPrice() {
		$('.format-price').priceFormat({
			prefix: 'Rp ',
			centsSeparator: ',',
			thousandsSeparator: '.',
			centsLimit: 0
		});
	}
	
	function formatRupiah(angka) {
		return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	}

	function loadCartItems() {
		$.getJSON('endpoint/ajax/get_cart_items.php', function(response) {
			const itemList = $('#itemList');
			itemList.empty();

			if (response.status === 'ok') {
				const items = response.data;

				if (items.length === 0) {
					itemList.append(`
						<li id="emptyCart">
							<div class="order-list-img"><img src="../img/bg/empty-cart-small.png" alt="Empty Cart"/></div>
							<div class="order-list-details">
								<h4>Your cart is empty<br/><small>Start adding items</small></h4>
								<div class="order-list-price format-price">Rp 0</div>
							</div>
						</li>
					`);
				} else {
					items.forEach(item => {
						// Total harga = base + semua harga extra
						let totalPrice = item.price;
						if (Array.isArray(item.extras)) {
							item.extras.forEach(extra => {
								totalPrice += extra.price;
							});
						}

						const subTotal = totalPrice * item.quantity;

						let extrasText = '';
						if (item.extras && item.extras.length > 0) {
							extrasText = '<br/><small>Extra: ' + item.extras.map(e => e.variant).join(', ') + '</small>';
						}

						itemList.append(`
							<li id="cartItem${item.cart_id}">
								<div class="order-list-img"><img src="${item.image}" alt=""></div>
								<div class="order-list-details">
									<h4>${item.name}<br/><small>Size: ${item.variant}</small>${extrasText}</h4>
									<div class="qty-buttons">
										<input type="button" value="+" class="qtyplus" data-id="${item.cart_id}" data-qty="${item.quantity}">
										<input type="text" name="qty" value="${item.quantity}" class="qty form-control" readonly>
										<input type="button" value="-" class="qtyminus" data-id="${item.cart_id}" data-qty="${item.quantity}">
									</div>
									<div class="order-list-price format-price">${formatRupiah(subTotal)}</div>
									<div class="order-list-delete"><a href="javascript:;" class="delete-cart" data-id="${item.cart_id}"><i class="icon icon-trash"></i></a></div>
								</div>
							</li>
						`);
					});
				}

				// Attach events
				attachCartEvents();
			}
		});
	}


	function attachCartEvents() {
		// Tambah qty
		$('.qtyplus').off().on('click', function () {
			const cartId = $(this).data('id');
			let currentQty = parseInt($(this).data('qty'));

			if (currentQty < 10) {
				updateCartQuantity(cartId, currentQty + 1);
			} else {
				alert('Maksimal pembelian hanya 10 per produk.');
			}
		});

		// Kurangi qty
		$('.qtyminus').off().on('click', function () {
			const cartId = $(this).data('id');
			let currentQty = parseInt($(this).data('qty'));

			if (currentQty > 1) {
				updateCartQuantity(cartId, currentQty - 1);
			} else {
				if (confirm('Apakah Anda ingin menghapus produk dari keranjang?')) {
					deleteCartItem(cartId);
				}
			}
		});

		// Hapus item langsung
		$('.delete-cart').off().on('click', function () {
			const cartId = $(this).data('id');
			if (confirm('Yakin ingin menghapus item ini dari keranjang?')) {
				deleteCartItem(cartId);
			}
		});
	}

	function updateCartQuantity(cartId, newQty) {
		$.post('endpoint/ajax/update_cart_quantity.php', {
			cart_id: cartId,
			quantity: newQty
		}, function (res) {
			if (res.status === 'success') {
				loadCartItems();
			} else {
				alert('Gagal memperbarui kuantitas.');
			}
		}, 'json');
	}

	function deleteCartItem(cartId) {
		$.post('endpoint/ajax/delete_cart_item.php', {
			cart_id: cartId
		}, function (res) {
			if (res.status === 'success') {
				loadCartItems();
			} else {
				alert('Gagal menghapus item.');
			}
		}, 'json');
	}

	$(document).ready(function() {
		loadCartItems();
	});

	// Function to reset total price
	function resetTotal() {

		$('.totalTitle').val('Total');
		$('.total').val('0');
		formatPrice();

	}

	// Function to call warning popup
	function callWarningPopup(popupId) {
		$.magnificPopup.open({
			items: {
				src: popupId
			},
			type: 'inline',
			fixedContentPos: false,
			fixedBgPos: true,
			closeOnBgClick: false,
			overflowY: 'auto',
			closeBtnInside: true,
			preloader: false,
			midClick: true,
			removalDelay: 300,
			closeMarkup: '<button title="%title%" type="button" class="mfp-close"></button>',
			mainClass: 'my-mfp-zoom-in'
		});
	}

	// Function to show a popup essage that item is added to cart
	function showItemAddedMessage() {

		// Only show this message when there is no popup opened
		if (!$.magnificPopup.instance.isOpen) {

			// Show added to cart message
			$('.addedToCartMsg').fadeIn('slow', function () {
				$('.addedToCartMsg').fadeOut();
			});

		} else if ($.magnificPopup.instance.isOpen) { // Only show this message when a popup is opened
			$('.addedToCartMsgInModal').fadeIn('slow', function () {
				$('.addedToCartMsgInModal').fadeOut();
			});
		}
	}

	// Function to show a popup message that item is added to cart
	function showItemAlreadyInCartMessage() {

		// Only show this message when there is no popup opened
		if (!$.magnificPopup.instance.isOpen) {

			// Show already in cart message
			$('.alreadyInCartMsg').fadeIn('slow', function () {
				$('.alreadyInCartMsg').fadeOut();
			});

		} else if ($.magnificPopup.instance.isOpen) { // Only show this
			// message when a popup
			// is opened
			$('.alreadyInCartMsgInModal').fadeIn('slow', function () {
				$('.alreadyInCartMsgInModal').fadeOut();
			});
		}
	}

	// Function to validate total price
	function validateTotal() {
		$('#totalOrderSummary').parsley().validate();
	}

	// =====================================================
	// CART FUNCTIONS
	// =====================================================
	var id = '';
	var rowId = '';
	var size = '';
	var thumbnailPath = '';
	var itemTitle = '';
	var description = '';
	var itemPrice = '';
	var extraTitle = '';
	var extraPrice = '';
	var extraIsChecked = false;
	var qtyInput = 0;
	var actualQty = 0;
	var maxQty = 10;
	var subSum = 0;
	var deliveryFee = 10;
	var total = 0;

	// Function to set empty cart image
	function setEmptyCart() {

		// Create the dedicated row for the empty cart element
		$('#itemList').append('<li id="emptyCart"></li>');

		// Fill the dedicated row
		$('#emptyCart').html('<div class="order-list-img"><img src="../img/bg/empty-cart-small.png" alt="Your cart is empty"/></div><div class="order-list-details"> <h4>Your cart is empty</a><br/><small>Start adding items</small></h4> <div class="order-list-price format-price">0.00</div></div>');
		formatPrice();
	}

	// Function to check if the cart is empty
	function isCartEmpty() {

		if ($('ul#itemList li').length == 0) {
			return true;
		}
	}

	// Function to update sub summary
	function updateSubSum(id, rowId, itemPrice, actualQty) {

		// Calculate subSum
		subSum = (itemPrice * 1) * (actualQty * 1);

		// Update subSum
		$('#cartItem' + id + rowId + ' .order-list-details .order-list-price').text(subSum.toFixed(2));
	}

	// Function to update total summary
	function updateTotal() {

		total = 0;

		// Update total with prices in order list
		$('.order-list-price').each(function () {

			total += ($(this).text().match(/[0-9.]+/g) * 1);

		});
		//Add delivery fee
		total = total + (deliveryFee * 1);

		// Set total
		$('.total').val(total.toFixed(2));
		$('.totalValue').text(total.toFixed(2));

		// If cart is empty do not calculate any cost
		if ($('ul#itemList li#emptyCart').length > 0) {
			total = 0;
			$('.total').val(total.toFixed(2));
			$('.totalValue').text(total.toFixed(2));
		}

		formatPrice();

	}

	// Item having options is added to cart
	$(document).on('click', '.add-options-item-to-cart', function () {
		const $btn = $(this);
		const productId = $btn.data('product-id');
		let optionId = $btn.data('option-id') || null;
		let extraIds = [];

		// Cek apakah dari modal
		const $sizeInput = $(`input[name="size-options-item-${productId}"]:checked`);
		if ($sizeInput.length > 0) {
			optionId = $sizeInput.val();
			extraIds = $(`input[name="extra-options-item-${productId}[]"]:checked`)
				.map(function () {
					return $(this).val();
				}).get();
		}

		if (!optionId) {
			alert('Ukuran produk belum dipilih!');
			return;
		}

		// Kirim request
		$.ajax({
			url: 'endpoint/ajax/add_to_cart.php',
			method: 'POST',
			data: {
				product_id: productId,
				option_id: optionId,
				extra_ids: extraIds,
				quantity: 1
			},
			traditional: true, // ⬅️ penting agar extra_ids[] tidak dikirim sebagai object nested
			dataType: 'json',
			success: function (res) {
				if (res.status === 'success' || res.status === 'ok') {
					alert(res.message || 'Produk berhasil ditambahkan ke keranjang!');
					loadCartItems();
				} else {
					alert(res.message || 'Gagal menambahkan ke keranjang');
				}
			},
			error: function (xhr, status, error) {
				console.error('AJAX Error:', xhr.responseText);
				alert('Gagal menghubungi server');
			}
		});
	});

	setEmptyCart();
	resetTotal();

})(window.jQuery);