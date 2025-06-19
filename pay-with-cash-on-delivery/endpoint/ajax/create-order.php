<?php
session_start();
require_once __DIR__ . '/../../../database/db.php';

// DEBUG log file (letakkan paling atas)
define('DEBUG', true);
define('LOG_FILE', __DIR__ . '/order-log.txt');

// Utility: log error
function log_error($msg) {
	if (DEBUG) file_put_contents(LOG_FILE, '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

// Baru boleh panggil log_error
log_error('DEBUG: $_SESSION[user][id] = ' . ($_SESSION['user']['id'] ?? 'NULL'));

// Cek login user
if (!isset($_SESSION['user']['id'])) {
	http_response_code(401);
	echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
	log_error('Unauthorized access');
	exit;
}

// Validasi POST
$required = ['name', 'email', 'phone', 'address', 'payment_method'];
foreach ($required as $field) {
	if (empty($_POST[$field])) {
		echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
		exit;
	}
}

// Data dari form
$userId = $_SESSION['user']['id'];
$name = htmlspecialchars(trim($_POST['name']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = htmlspecialchars(trim($_POST['phone']));
$address = htmlspecialchars(trim($_POST['address']));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));
$rawMethod = $_POST['shipping_method'] ?? 'Delivery';
$delivery_method = ucfirst(strtolower(trim($rawMethod))); // Delivery atau Pickup
$delivery_zone_id = isset($_POST['delivery_zone']) ? (int)$_POST['delivery_zone'] : null;
$payment_method = $_POST['payment_method'];

// Ambil fee zona dari DB
$delivery_fee = 0;

if ($delivery_method === 'Delivery' && $delivery_zone_id) {
	$stmt = $conn->prepare("SELECT fee, name, city FROM delivery_zones WHERE id = ?");
	$stmt->bind_param("i", $delivery_zone_id);
	$stmt->execute();
	$stmt->bind_result($delivery_fee, $zone_name, $zone_city);

	if ($stmt->fetch()) {
		$address .= " - Zone: $zone_name $zone_city";
		log_error("✅ Zona ditemukan: $zone_name, $zone_city, Fee: $delivery_fee");
	} else {
		log_error("❌ Gagal fetch zona ID $delivery_zone_id");
	}
	$stmt->close();
} else if ($delivery_method === 'Pickup') {
    $delivery_fee = 0;
    $delivery_zone_id = null; // ✅ NULL supaya tidak melanggar foreign key
    log_error("🛍️ Pickup dipilih, fee di-set 0 dan zone_id diabaikan");
}

// Ambil isi cart
$stmt = $conn->prepare("
	SELECT c.product_id, c.option_id, c.extra_ids, c.quantity,
		   p.name AS product_name, v.price AS base_price
	FROM carts c
	JOIN product_variants v ON c.option_id = v.id
	JOIN products p ON c.product_id = p.id
	WHERE c.user_id = ?
");
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total_products = 0;

while ($row = $result->fetch_assoc()) {
	$base = (int)$row['base_price'];
	$qty = (int)$row['quantity'];
	$extraPrice = 0;
	$extraLabels = [];

	if (!empty($row['extra_ids'])) {
		$extraIds = explode(',', $row['extra_ids']);
		$placeholders = implode(',', array_fill(0, count($extraIds), '?'));
		$types = str_repeat('i', count($extraIds));

		$sqlExtra = "SELECT variant, price FROM product_variants WHERE id IN ($placeholders)";
		$stmtExtra = $conn->prepare($sqlExtra);
		$stmtExtra->bind_param($types, ...$extraIds);
		$stmtExtra->execute();
		$resultExtra = $stmtExtra->get_result();

		while ($extra = $resultExtra->fetch_assoc()) {
			$extraPrice += (int)$extra['price'];
			$extraLabels[] = $extra['variant'];
		}

		$stmtExtra->close();
	}

	$totalPerItem = ($base + $extraPrice) * $qty;
	$total_products += $totalPerItem;

	$items[] = [
		'name' => $row['product_name'],
		'variant' => $row['option_id'],
		'extras' => $extraLabels,
		'quantity' => $qty,
		'price' => $base + $extraPrice,
		'subtotal' => $totalPerItem
	];
}

$stmt->close();

if (count($items) === 0) {
	echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
	exit;
}

// Total akhir
$total = $total_products + $delivery_fee;

// Generate invoice ID
function generateTransactionId($conn) {
    $prefix = 'INV/' . date('Ymd') . '/';
    $like = $prefix . '%';
    $sql = "SELECT id FROM transactions WHERE id LIKE ? ORDER BY id DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $lastId = null;
    if ($result && $row = $result->fetch_assoc()) {
        $lastId = $row['id'];
    }

    $lastNum = ($lastId !== null) ? (int)substr($lastId, -4) : 0;
    $newNum = $lastNum + 1;

    return $prefix . str_pad($newNum, 4, '0', STR_PAD_LEFT);
}

$transactionId = generateTransactionId($conn);

$dbName = $conn->query("SELECT DATABASE()")->fetch_row()[0];
log_error("🧪 DATABASE AKTIF: " . $dbName);

// Simpan transaksi
$stmt = $conn->prepare("
	INSERT INTO transactions (id, user_id, full_name, phone, email, delivery_address, message,
		payment_method, delivery_method, delivery_fee, total_price, delivery_zone_id)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$result = $conn->query("SELECT id FROM users WHERE id = 'CSTJTR1'");
log_error("🧪 Apakah CSTJTR1 ditemukan? " . ($result->num_rows > 0 ? 'YA' : 'TIDAK'));


$stmt->bind_param("sssssssssiii", $transactionId, $userId, $name, $phone, $email, $address, $message,
	$payment_method, $delivery_method, $delivery_fee, $total, $delivery_zone_id);

    log_error("VALUES DEBUG: " . json_encode([
        $transactionId, $userId, $name, $phone, $email, 
        $address, $message, $payment_method, $delivery_method, 
        $delivery_fee, $total, $delivery_zone_id
    ]));


if (!$stmt->execute()) {
	log_error('Insert failed: ' . $stmt->error);
	echo json_encode(['status' => 'error', 'message' => 'Failed saving transaction']);
	exit;
}
$stmt->close();

// Hapus cart
$del = $conn->prepare("DELETE FROM carts WHERE user_id = ?");
$del->bind_param("s", $userId);
$del->execute();
$del->close();

/// ================================
// Kirim WhatsApp Invoice via UltraMSG
// ================================

$buyerNote = $message;
$ongkir = $delivery_fee;
$phoneNumber = $phone;
$buyerName = $name;

// Format nomor telepon ke internasional
$phoneIntl = preg_replace('/\D/', '', $phoneNumber);
if (substr($phoneIntl, 0, 2) !== '62') {
	if (substr($phoneIntl, 0, 1) === '0') {
		$phoneIntl = '62' . substr($phoneIntl, 1);
	} else {
		$phoneIntl = '62' . $phoneIntl;
	}
}

// Format isi pesan WhatsApp
$waMessage  = "📦 *[PESANAN SEDANG DIPROSES]*\n";
$waMessage .= "Hai *$buyerName*! Terima kasih telah melakukan pemesanan 🙏\n";
$waMessage .= "Pesanan kamu saat ini sedang kami proses dengan penuh perhatian 💼✨\n\n";

$waMessage .= "🧾 *INVOICE PEMESANAN*\n";
$waMessage .= "📌 Nomor Transaksi: *$transactionId*\n\n";

$waMessage .= "👤 *Nama Pembeli:*\n$buyerName\n\n";
$waMessage .= "🏠 *Alamat Pengiriman:*\n$address\n\n";
$waMessage .= "💬 *Catatan Pembeli:*\n\"$buyerNote\"\n\n";
$waMessage .= "🛍️ *Metode Pembayaran:* $payment_method\n";
$waMessage .= "🚚 *Metode Pengiriman:* $delivery_method\n\n";

$waMessage .= "🍱 *Detail Pesanan:*\n";

$subtotal = 0;
foreach ($items as $item) {
    $productName = $item['name'];
    $qty = $item['quantity'];
    $price = $item['price']; // sudah termasuk base + extra
    $lineTotal = $qty * $price;
    $subtotal += $lineTotal;

    // Tampilkan extras jika ada
    $extrasText = '';
    if (!empty($item['extras'])) {
        $extrasText = " (Extra: " . implode(', ', $item['extras']) . ")";
    }

    $waMessage .= "- $productName x$qty$extrasText = Rp" . number_format($lineTotal, 0, ',', '.') . "\n";
}

$waMessage .= "\n💰 *Subtotal Produk:*\nRp " . number_format($subtotal, 0, ',', '.') . "\n";
$waMessage .= "📦 *Ongkos Kirim:*\nRp " . number_format($ongkir, 0, ',', '.') . "\n";
$waMessage .= "🧮 *Total Pembayaran:*\n*Rp " . number_format($subtotal + $ongkir, 0, ',', '.') . "*\n\n";

$waMessage .= "🙏 Terima kasih telah berbelanja di toko kami 💖\n";
$waMessage .= "Pesananmu akan segera kami antar sesuai informasi di atas 🚀\n\n";

$waMessage .= "📱 *Ikuti kami di sosial media:*\n";
$waMessage .= "🔍 IG: @tulangrangu_karawang\n📘 Email: tulangrangukarawang@gmail.com\n\n";

$waMessage .= "❓ Jika ada pertanyaan, silakan hubungi kami kapan saja.\n";
$waMessage .= "🌟 Semoga harimu menyenangkan dan pesananmu memuaskan! 🌟";

// Kirim ke UltraMsg
$params = array(
	// 'token' => '67h0ks2kaofqoanl', // Ganti dengan token kamu
	'to' => $phoneIntl,
	'body' => $waMessage
);

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.ultramsg.com/instance122015/messages/chat",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_SSL_VERIFYHOST => 0,
	CURLOPT_SSL_VERIFYPEER => 0,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => http_build_query($params),
	CURLOPT_HTTPHEADER => array(
		"content-type: application/x-www-form-urlencoded"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
	log_error("UltraMsg Error: " . $err);
} else {
	log_error("UltraMsg Response: " . $response);
}