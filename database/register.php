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
	<meta name="description" content="Guide to ordering food with online payment">
	<meta name="author" content="UWS">
  <title>SignUp - Juragan Tulang Rangu Karawang</title>

  <!-- Favicon -->
  <link href="../img/logo.svg" rel="shortcut icon">

  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap");
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Roboto", sans-serif; }
    body {
      background: #000;
    }
    body::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0.5;
      width: 100%;
      height: 100%;
      background: url("../img/bg/bg-1.svg") center/cover no-repeat;
    }
    nav {
      position: fixed;
      padding: 25px 60px;
      z-index: 1;
    }
    nav a img {
      width: 167px;
    }
    .form-wrapper {
      position: absolute;
      left: 50%;
      top: 50%;
      border-radius: 4px;
      padding: 70px;
      width: 450px;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.75);
      z-index: 2;
    }
    .form-wrapper h2 {
      color: #fff;
      font-size: 2rem;
    }
    form {
      margin: 25px 0 65px;
    }
    .form-control {
      height: 50px;
      position: relative;
      margin-bottom: 16px;
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
      padding: 0 20px;
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
      transform: translateY(-130%);
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
      margin: 25px 0 10px;
      cursor: pointer;
    }
    button:hover {
      background: #c40812;
    }
    .form-help {
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
      color: #b3b3b3;
    }
    .form-help a {
      color: #b3b3b3;
      text-decoration: none;
    }
    .form-help a:hover {
      text-decoration: underline;
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

<div class="form-wrapper">
  <h2>Sign Up</h2>
  <form method="POST" action="register.php">
    <div class="form-control">
      <input type="text" name="username" id="username" required>
      <label for="username">Username</label>
    </div>
    <div class="form-control">
      <input type="email" name="email" id="email" required>
      <label for="email">Email</label>
    </div>
    <div class="form-control">
      <input type="text" name="phone" id="phone" required>
      <label for="phone">Phone</label>
    </div>
    <div class="form-control">
      <input type="text" name="address" id="address" required>
      <label for="address">Address</label>
    </div>
    <div class="form-control">
      <input type="password" name="password" id="password" required>
      <label for="password"> Password</label>
    </div>
    <div class="form-control">
      <input type="password" name="confirm_password" id="confirm_password" required>
      <label for="confirm_password">Confirm Password</label>
    </div>
    <button type="submit">Sign Up</button>
    <div class="form-help">
      <div class="remember-me">
        <input type="checkbox" id="remember-me">
        <label for="remember-me">Remember me</label>
      </div>
      <a href="#">Need help?</a>
    </div>
  </form>
  <p>Have to Account? <a href="../database/login.php">Sign In</a></p>
  <small>This page is protected by reCAPTCHA. <a href="#">Learn more.</a></small>
</div>
</body>
</html>
