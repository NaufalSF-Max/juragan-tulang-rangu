<?php
session_start();
include 'db.php';

// Hapus cookie remember_token
setcookie('remember_token', '', time() - 3600, "/");

// Hapus dari database juga
if (isset($_SESSION['user']['id'])) {
  $id = $_SESSION['user']['id'];
  $stmt = $conn->prepare("UPDATE users SET remember_token=NULL WHERE id=?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
}

session_unset();
session_destroy();
header("Location: ../index.php");
exit();