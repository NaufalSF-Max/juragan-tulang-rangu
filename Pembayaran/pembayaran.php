<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Form Pembayaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    
    .payment-card {
      transition: all 0.3s ease;
    }

    .payment-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .payment-card.selected {
      border-color: #3b82f6;
      background-color: #eff6ff;
    }
  </style>
</head>
<body class="bg-gray-100 font-[Poppins]">
  <div class="max-w-md mx-auto my-8 bg-white rounded-xl shadow-md overflow-hidden">
    
    <!-- Header -->
    <div class="bg-blue-600 py-4 px-6">
      <h1 class="text-xl font-bold text-white">Detail Pembeli</h1>
    </div>

    <!-- FORM SECTION -->
    <div id="form-section" class="p-6">
      <!-- Input Fields -->
      <div class="mb-6">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Aktif</label>
        <input type="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="contoh@gmail.com" required>
      </div>

      <div class="mb-6">
        <label for="nickname" class="block text-sm font-medium text-gray-700 mb-1">Nama Panggilan</label>
        <input type="text" id="nickname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama panggilan" required>
      </div>

      <div class="mb-8">
        <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">No WhatsApp</label>
        <input type="tel" id="whatsapp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nomor WhatsApp" required>
      </div>

      <!-- Payment Methods -->
      <hr class="my-6 border-gray-200">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">Metode Pembayaran</h2>

      <!-- QRIS -->
      <div class="payment-card border rounded-lg p-4 mb-4 cursor-pointer selected" onclick="selectPayment('qris')">
        <div class="flex items-center">
          <div class="bg-blue-100 p-2 rounded-lg mr-4">
            <img src="https://images.seeklogo.com/logo-png/39/1/quick-response-code-indonesia-standard-qris-logo-png_seeklogo-391791.png" class="w-10 h-10" alt="QRIS">
          </div>
          <div class="flex-grow">
            <h3 class="font-medium text-gray-800">QRIS</h3>
            <p class="text-sm text-gray-500">Scan kode QR untuk pembayaran</p>
          </div>
          <div class="w-5 h-5 rounded-full border border-blue-500 flex items-center justify-center">
            <div class="w-3 h-3 rounded-full bg-blue-500 payment-radio"></div>
          </div>
        </div>
        <div class="mt-3 payment-details">
          <img src="https://images.seeklogo.com/logo-png/39/1/quick-response-code-indonesia-standard-qris-logo-png_seeklogo-391791.png" class="mx-auto" alt="QR Code">
        </div>
      </div>

      <!-- Tambahan metode lain (Bank & E-wallet bisa kamu tambahkan seperti sebelumnya) -->

      <!-- Submit Button -->
      <button onclick="submitDonation()" class="w-full mt-8 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg shadow-md transition duration-300">
        Lanjutkan Pembayaran
      </button>
    </div>

    <!-- INVOICE SECTION -->
    <div id="invoice" class="hidden p-6">
      <h2 class="text-lg font-bold text-gray-800 mb-4">Invoice Pembayaran</h2>
      <p class="mb-2"><strong>Email:</strong> <span id="invoice-email"></span></p>
      <p class="mb-2"><strong>Nama Panggilan:</strong> <span id="invoice-nickname"></span></p>
      <p class="mb-2"><strong>No WhatsApp:</strong> <span id="invoice-whatsapp"></span></p>
      <p class="mb-4"><strong>Metode Pembayaran:</strong> <span id="invoice-method"></span></p>
      <hr class="my-4">
      <p class="mb-4 font-semibold text-green-700">Terima kasih telah melakukan pembayaran!</p>

      <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg mr-2 mb-4">Cetak Invoice</button>
      <button onclick="sendToWhatsApp()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Kirim ke WhatsApp</button>
    </div>
  </div>

  <script>
    function selectPayment(method) {
      document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('selected');
        card.querySelector('.payment-radio').classList.remove('bg-blue-500');
        card.querySelector('.payment-radio').classList.add('bg-white');
        const detail = card.querySelector('.payment-details');
        if (detail) detail.classList.add('hidden');
      });

      const selectedCard = event.currentTarget;
      selectedCard.classList.add('selected');
      selectedCard.querySelector('.payment-radio').classList.remove('bg-white');
      selectedCard.querySelector('.payment-radio').classList.add('bg-blue-500');
      const detail = selectedCard.querySelector('.payment-details');
      if (detail) detail.classList.remove('hidden');
    }

    function submitDonation() {
      const email = document.getElementById('email').value;
      const nickname = document.getElementById('nickname').value;
      const whatsapp = document.getElementById('whatsapp').value;

      if (!email || !nickname || !whatsapp) {
        alert('Harap lengkapi semua data donatur');
        return;
      }

      const selectedPayment = document.querySelector('.payment-card.selected h3').textContent;

      alert(`Pembayaran berhasil diproses!\n\nEmail: ${email}\nNama: ${nickname}\nWhatsApp: ${whatsapp}\nMetode Pembayaran: ${selectedPayment}`);

      // Tampilkan invoice
      document.getElementById('form-section').classList.add('hidden');
      document.getElementById('invoice').classList.remove('hidden');

      document.getElementById('invoice-email').textContent = email;
      document.getElementById('invoice-nickname').textContent = nickname;
      document.getElementById('invoice-whatsapp').textContent = whatsapp;
      document.getElementById('invoice-method').textContent = selectedPayment;
    }

    function sendToWhatsApp() {
      const email = document.getElementById('invoice-email').textContent;
      const nickname = document.getElementById('invoice-nickname').textContent;
      const whatsapp = document.getElementById('invoice-whatsapp').textContent;
      const method = document.getElementById('invoice-method').textContent;

      const message = `Halo ${nickname},\n\nBerikut detail pembayaran kamu:\nEmail: ${email}\nWhatsApp: ${whatsapp}\nMetode: ${method}\n\nTerima kasih telah melakukan pembayaran!`;
      const phoneNumber = whatsapp.replace(/\D/g, '').replace(/^0/, '62');
      const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;

      window.open(url, '_blank');
    }
  </script>
</body>
</html>



