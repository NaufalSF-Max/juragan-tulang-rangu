<?php
include 'db.php';

$message = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitasi input
  $username = htmlspecialchars(trim($_POST['username']));
  $email = htmlspecialchars(trim($_POST['email']));
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $phone = htmlspecialchars(trim($_POST['phone']));
  $address = htmlspecialchars(trim($_POST['address']));
  $role = 'customer';

  // Validasi dasar
  if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone) || empty($address)) {
    $message = "Semua field harus diisi.";
    $type = "error";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "Format email tidak valid.";
    $type = "error";
  } elseif ($password !== $confirm_password) {
    $message = "Konfirmasi password tidak sesuai.";
    $type = "error";
  } else {
    // Cek apakah username atau email sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $message = "Username atau email sudah digunakan.";
      $type = "error";
    } else {
      $stmt->close();

      // Generate ID otomatis
      $prefix = "CSTJTR";
      $stmt = $conn->prepare("SELECT id FROM users WHERE id LIKE CONCAT(?, '%') ORDER BY id DESC LIMIT 1");
      $stmt->bind_param("s", $prefix);
      $stmt->execute();
      $stmt->bind_result($lastId);
      $stmt->fetch();
      $stmt->close();

      $nextNumber = 1;
      if ($lastId) {
        $lastNumber = intval(substr($lastId, strlen($prefix)));
        $nextNumber = $lastNumber + 1;
      }
      $newId = $prefix . $nextNumber;

      // Simpan ke database
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (id, username, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssss", $newId, $username, $email, $hashed_password, $phone, $address, $role);

      if ($stmt->execute()) {
        $message = "Registrasi berhasil! Mengarahkan ke login...";
        $type = "success";
        echo "<script>
          setTimeout(function() {
            window.location.href = 'login.php';
          }, 2000);
        </script>";
      } else {
        $message = "Kesalahan saat menyimpan ke database: " . $stmt->error;
        $type = "error";
      }
      $stmt->close();
    }
  }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>SignUp - Juragan Tulang Rangu Karawang</title>
  <link href="../img/logo.svg" rel="shortcut icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Roboto", sans-serif;
    }

    body {
      width: 100%;
      height: 100vh;
      background-color: #ddd;
    }

    .form-section {
      width: 100%;
      min-height: 100vh; /* biar tinggi menyesuaikan isi */
      display: flex;
      justify-content: center;
      align-items: flex-start; /* dari center ke atas */
      padding: 80px 20px 40px; /* atas 80px, samping 20px, bawah 40px */
      background-color: transparent;
    }

    .form-wrapper {
      position: relative;
      border-radius: 8px;
      padding: 40px 30px;
      width: 100%;
      max-width: 450px;
      background: rgba(0, 0, 0, 0.75);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    }

    .form-wrapper h2 {
      color: #fff;
      font-size: 2rem;
      margin-bottom: 20px;
    }

    form {
      margin-bottom: 30px;
    }

    .form-control {
      height: 50px;
      position: relative;
      margin-bottom: 20px;
    }

    .form-control input {
      height: 100%;
      width: 100%;
      background: #333;
      border: none;
      outline: none;
      border-radius: 4px;
      color: #fff;
      font-size: 1rem;
      padding: 14px 20px 0;
    }

    .form-control label {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
      color: #8c8c8c;
      transition: all 0.1s ease;
      pointer-events: none;
    }

    .form-control input:focus + label,
    .form-control input:valid + label {
      font-size: 0.75rem;
      transform: translateY(-160%);
    }

    button {
      width: 100%;
      padding: 16px 0;
      font-size: 1rem;
      background: #e50914;
      color: #fff;
      font-weight: 500;
      border-radius: 4px;
      border: none;
      margin-top: 15px;
      cursor: pointer;
    }

    button:hover {
      background: #c40812;
    }

    .form-wrapper p, .form-wrapper small {
      color: #b3b3b3;
    }

    .form-wrapper p a, .form-wrapper small a {
      color: #fff;
      text-decoration: none;
    }

    .form-wrapper small a {
      color: #0071eb;
    }

    .popup {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      padding: 15px 25px;
      border-radius: 4px;
      color: #fff;
      font-size: 1rem;
      opacity: 1;
      transition: opacity 0.5s ease;
    }

    .popup.success {
      background-color: rgba(0, 200, 0, 0.85);
    }

    .popup.error {
      background-color: rgba(200, 0, 0, 0.85);
    }
  </style>
</head>
<body>

<?php if (!empty($message)): ?>
  <div id="popup" class="popup <?= $type ?>">
    <p><?= htmlspecialchars($message) ?></p>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const popup = document.getElementById("popup");
      if (popup) {
        popup.style.display = "block";
        setTimeout(() => {
          popup.style.opacity = "0";
          setTimeout(() => popup.style.display = "none", 500);
        }, 2500);
      }
    });
  </script>
<?php endif; ?>

<section class="form-section">
  <div class="form-wrapper">
    <h2>Sign Up</h2>
    <form method="POST" action="register.php">
      <div class="form-control">
        <input type="text" name="username" id="username" required autofocus>
        <label for="username">Username</label>
      </div>
      <div class="form-control">
        <input type="text" name="address" id="address" required>
        <label for="address">Address</label>
      </div>
      <div class="form-control">
        <input type="text" name="phone" id="phone" required>
        <label for="phone">Phone Number</label>
      </div>
      <div class="form-control">
        <input type="email" name="email" id="email" required>
        <label for="email">Email</label>
      </div>
      <div class="form-control">
        <input type="password" name="password" id="password" required>
        <label for="password">Password</label>
      </div>
      <div class="form-control">
        <input type="password" name="confirm_password" id="confirm_password" required>
        <label for="confirm_password">Confirm Password</label>
      </div>
      <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="../database/login.php">Sign In</a></p>
    <small>This page is protected by reCAPTCHA. <a href="#">Learn more.</a></small>
  </div>
</section>

</body>
</html>