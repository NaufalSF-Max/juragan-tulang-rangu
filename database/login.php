<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// ==== Cek Remember Me ====
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
  $token = $_COOKIE['remember_token'];
  $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE remember_token=?");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $username,$email, $phone, $address, $role);
    $stmt->fetch();

    $_SESSION['user'] = [
      'id' => $id,
      'username' => $username,
      'role' => $role,
      'address' => $address,
      'email' => $email,
      'phone' => $phone
    ];

    $redirect = ($role === 'customer') ? 'index.php' : 'admin.php';
    header("Location: $redirect");
    exit();
  }
}

// ==== Proses Login ====
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim(htmlspecialchars($_POST['username']));
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $username_db, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION['user'] = [
        'id' => $id,
        'username' => $username_db,
        'role' => $role
      ];

      if (isset($_POST['remember_me'])) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), "/");

        $update = $conn->prepare("UPDATE users SET remember_token=? WHERE id=?");
        $update->bind_param("ss", $token, $id);
        $update->execute();
      }

      $success = "Login berhasil! Mengarahkan ke dashboard...";
      $redirect = ($role === 'customer') ? '../index.php' : '../admin/views/view_add_product.php';
      echo "<script>
        setTimeout(function() {
          window.location.href = '$redirect';
        }, 2000);
      </script>";
      exit();

      

    } else {
      $error = "Password salah.";
    }
  } else {
    $error = "User tidak ditemukan.";
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
  <title>SignIn - Juragan Tulang Rangu Karawang</title>

  <!-- Favicon -->
  <link href="../img/logo.svg" rel="shortcut icon">

  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap");
    * {margin: 0; padding: 0; box-sizing: border-box; font-family: "Roboto", sans-serif; }
    body {
      background-color: #ddd;
    }
    .form-wrapper {
      position: absolute;
      left: 50%;
      top: 50%;
      border-radius: 4px;
      padding: 60px;
      width: 450px;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.75);
      z-index: 2;
      box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.5);
    }
    .form-wrapper h1 {
      padding: 0 ;
      color:rgb(255, 32, 43);
      font-size: 2.5rem;
      text-align: center;
      margin-bottom: 40px;
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
    /* === Popup Modal === */
    #popup {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: 999;
      justify-content: center;
      align-items: center;
    }

    #popup .popup-content {
      background: #222;
      color: #fff;
      padding: 30px 25px;
      border-radius: 8px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      animation: fadeInUp 0.3s ease-out;
    }

    #popup .popup-content h3 {
      font-size: 1.5rem;
      margin-bottom: 10px;
    }

    #popup .popup-content p {
      font-size: 1rem;
      margin-bottom: 20px;
    }

    #popup .popup-content button {
      padding: 10px 20px;
      font-size: 1rem;
      background: #e50914;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    #popup .popup-content button:hover {
      background: #c40812;
    }

    /* Simple animation */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @media (max-width: 768px) {
      .form-wrapper {
        width: 90%;
        padding: 40px 20px;
      }

      .form-wrapper h2 {
        font-size: 1.5rem;
        text-align: center;
      }

      .form-control input {
        font-size: 0.9rem;
        padding: 0 15px;
      }

      .form-control label {
        font-size: 0.9rem;
        left: 15px;
      }

      button {
        font-size: 0.95rem;
        padding: 14px 0;
      }

      .form-help {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        font-size: 0.85rem;
      }

      #popup .popup-content {
        width: 90%;
        padding: 25px 15px;
      }

      #popup .popup-content h3 {
        font-size: 1.25rem;
      }

      #popup .popup-content p {
        font-size: 0.95rem;
      }

      #popup .popup-content button {
        font-size: 0.95rem;
        padding: 10px 16px;
      }
    }

  </style>
</head>
<body>
<div id="popup">
  <div class="popup-content">
    <h3 id="popup-title">Pesan</h3>
    <p id="popup-message"></p>
    <button onclick="document.getElementById('popup').style.display='none'">Tutup</button>
  </div>
</div>

<div class="form-wrapper">
  <h1>Welcome back</h1>
  <h2>Sign In</h2>
  <form method="POST" action="login.php">
    <div class="form-control">
      <input type="text" name="username" id="username" required autofocus>
      <label for="username">Username</label>
    </div>
    <div class="form-control">
      <input type="password" name="password" id="password" required>
      <label for="password">Password</label>
    </div>
    <button type="submit">Sign In</button>
    <div class="form-help">
      <div class="remember-me">
        <input type="checkbox" id="remember-me">
        <label for="remember-me">Remember me</label>
      </div>
      <a href="../faq.html">Need help?</a>
    </div>
  </form>
  <p>Don't have an account? <a href="register.php">Sign up now</a></p>
  <small>This page is protected by reCAPTCHA. <a href="#">Learn more.</a></small>
</div>

<?php if (!empty($error) || !empty($success)): ?>
<script>
  window.onload = function() {
    const popup = document.getElementById("popup");
    const title = document.getElementById("popup-title");
    const message = document.getElementById("popup-message");

    <?php if (!empty($error)): ?>
      title.innerText = "Login Gagal";
      message.innerText = "<?= htmlspecialchars($error) ?>";
    <?php else: ?>
      title.innerText = "Login Berhasil";
      message.innerText = "<?= htmlspecialchars($success) ?>";
    <?php endif; ?>

    popup.style.display = "flex";
  }
</script>
<?php endif; ?>

</body>
</html>