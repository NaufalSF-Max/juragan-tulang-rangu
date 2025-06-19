<?php
session_start();
include 'db.php';

// Ambil ID user dari session
$user_id = $_SESSION['user']['id'];

// Query ambil data user
$stmt = $conn->prepare("SELECT username, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $address);
$stmt->fetch();
$stmt->close();
?>


<form method="POST" action="update_profil.php">
  <div class="form-wrapper">
  <h2>Profil Saya</h2>
  <form method="POST" action="update_profile.php">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" value="<?= htmlspecialchars($username) ?>" readonly>
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="form-group">
      <label for="phone">Nomor Telepon</label>
      <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($phone) ?>" required>
    </div>
    <div class="form-group">
      <label for="address">Alamat</label>
      <input type="text" name="address" id="address" value="<?= htmlspecialchars($address) ?>" required>
    </div>
    <button type="submit">Simpan Perubahan</button>
    <div class="form-help">
      <a href="../index.php">← Kembali ke Beranda</a>
    </div>
  </form>
</div>

</form>

<style>
.form-wrapper {
  margin: 60px auto;
  padding: 40px;
  max-width: 480px;
  border-radius: 8px;
  background-color: #1c1c1c;
  color: #fff;
}

.form-wrapper h2 {
  text-align: center;
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-size: 0.95rem;
  color: #ddd;
}

.form-group input {
  width: 100%;
  padding: 12px 15px;
  border-radius: 4px;
  border: none;
  background-color: #333;
  color: #fff;
  font-size: 1rem;
}

.form-group input:focus {
  outline: 2px solid #e50914;
}

button {
  width: 100%;
  background-color: #e50914;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 14px 0;
  font-size: 1rem;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #c40812;
}

.form-help {
  margin-top: 15px;
  text-align: center;
}

.form-help a {
  color: #aaa;
  font-size: 0.9rem;
  text-decoration: none;
}

.form-help a:hover {
  text-decoration: underline;
}

</style>