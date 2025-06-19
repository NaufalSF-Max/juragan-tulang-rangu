<?php
session_start();
include '../../database/db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['user']['username'];

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        echo " Konfirmasi password tidak cocok.";
        exit;
    }

    // Ambil password dari database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!$hashed_password) {
        echo " User tidak ditemukan.";
        exit;
    }

    // Verifikasi password lama
    if (!password_verify($current_password, $hashed_password)) {
        echo " Password lama salah.";
        exit;
    }

    // Update password baru
    $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $new_hashed, $username);
    
    if ($stmt->execute()) {
        echo " Password berhasil diganti.";
    } else {
        echo " Gagal mengganti password.";
    }
    $stmt->close();
}
?>



<h3>Ganti Password</h3>
<form method="POST" action="change_password.php">
  <label>Password Lama:</label>
  <input type="password" name="current_password" required>

  <label>Password Baru:</label>
  <input type="password" name="new_password" required>

  <label>Konfirmasi Password Baru:</label>
  <input type="password" name="confirm_password" required>

  <button type="submit">Ganti Password</button>
  <br>
  <a href="profil.php" style="display: inline-block; margin-top: 15px; text-align: center; color: white;"> Kembali ke Profil</a>

</form>


<style>
body {
  background: #1c1c1c;
  color: white;
  font-family: Arial, sans-serif;
  padding: 40px;
}
h3 {
  text-align: center;
  margin-bottom: 30px;
}
form {
  max-width: 400px;
  margin: auto;
  background: #2a2a2a;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(255,255,255,0.05);
}
label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
input[type="password"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 4px;
  border: none;
  background: #444;
  color: white;
}
button {
  width: 100%;
  padding: 12px;
  background-color: #e50914;
  color: black;
  font-weight: bold;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:hover {
  background-color: white;
}
a {
  display: block;
  margin-top: 20px;
  text-align: center;
  background: black;
  color: #00bfff;;
  padding: 10px;
  border-radius: 5px;
  text-decoration: none;
}
a:hover {
  background: #333;
}
</style>
