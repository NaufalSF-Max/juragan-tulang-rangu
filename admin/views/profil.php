<?php
session_start();
include '../../database/db.php';

// Cek apakah user sudah login dan dia adalah admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

$userId = $_SESSION['user']['id'];

// Ambil data user dari database
$stmt = $conn->prepare("SELECT username, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $address);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profil Admin</title>
  <!-- <link rel="stylesheet" href="../css/admin_profile.css">  -->
</head>
<body>



  <div class="profile-container">
    <h2>Profil Admin</h2>
    <form method="POST" action="update_profil.php">
      <label>Username:</label>
      <input type="text" value="<?= htmlspecialchars($username) ?>" readonly>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

      <label>Nomor Telepon:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>

      <label>Alamat:</label>
      <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" required>

      <button type="submit">Simpan Perubahan</button>
    </form>
    <!-- Tambahkan tombol ubah password -->
    <a href="change_password.php" class="password-link"> Ubah Password</a>
    <a href="view_add_product.php">← Kembali ke Dashboard</a>
  </div>
</body>
</html>

<style>
.profile-container {
  max-width: 500px;
  margin: auto;
  background: #1c1c1c;
  padding: 30px;
  color: white;
  border-radius: 8px;
}
.profile-container input {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  background: #333;
  color: white;
  border: none;
  border-radius: 4px;
}
button {
  width: 100%;
  background: #e50914;
  color: white;
  padding: 12px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
}
a {
  display: block;
  margin-top: 15px;
  color: #ccc;
  text-align: center;
}
</style>