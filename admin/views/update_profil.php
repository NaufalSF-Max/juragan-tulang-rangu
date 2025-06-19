<?php
session_start();
include '../../db/db.php';

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
  $_SESSION['user']['email'] = $email;
  $_SESSION['user']['phone'] = $phone;
  $_SESSION['user']['address'] = $address;
  header("Location: profile.php?success=1");
} else {
  echo "Gagal update profil";
}
$stmt->close();
?>
