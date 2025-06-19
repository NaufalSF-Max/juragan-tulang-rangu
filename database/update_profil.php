<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $user_id = $_SESSION['user']['id'];
  $email = htmlspecialchars(trim($_POST['email']));
  $phone = htmlspecialchars(trim($_POST['phone']));
  $address = htmlspecialchars(trim($_POST['address']));

  $stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, address = ? WHERE id = ?");
  $stmt->bind_param("ssss", $email, $phone, $address, $user_id);
  
  if ($stmt->execute()) {
    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href = 'profil.php';</script>";
  } else {
    echo "Gagal update: " . $stmt->error;
  }

  $stmt->close();
}
?>
