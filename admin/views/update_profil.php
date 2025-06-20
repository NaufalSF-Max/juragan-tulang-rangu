<?php
session_start();
include '../../database/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

$id = $_SESSION['user']['id'];
$email = htmlspecialchars(trim($_POST['email']));
$phone = htmlspecialchars(trim($_POST['phone']));
$address = htmlspecialchars(trim($_POST['address']));

// Update database
$stmt = $conn->prepare("UPDATE users SET email = ?, phone = ?, address = ? WHERE id = ?");
$stmt->bind_param("ssss", $email, $phone, $address, $id);

if ($stmt->execute()) {
  echo "<script>alert('Profil berhasil diperbarui!'); window.location.href = 'profil.php';</script>";
} else {
  echo "Gagal update profil";
}
$stmt->close();
?>
