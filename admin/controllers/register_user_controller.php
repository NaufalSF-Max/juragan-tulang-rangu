<?php
session_start();
include '../../database/db.php';

// protect the admin panel 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../../login.php");
  exit();
}

$message = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = htmlspecialchars(trim($_POST['username']));
  $email = htmlspecialchars(trim($_POST['email']));
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $phone = htmlspecialchars(trim($_POST['phone']));
  $address = htmlspecialchars(trim($_POST['address']));
  $role = $_POST['role'];

  if (!in_array($role, ['admin', 'employee'])) {
    $message = "Invalid role selection.";
    $type = "error";
  } elseif (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone) || empty($address)) {
    $message = "All fields are required.";
    $type = "error";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "Invalid email format.";
    $type = "error";
  } elseif ($password !== $confirm_password) {
    $message = "Password confirmation does not match.";
    $type = "error";
  } else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $message = "Username or email already exists.";
      $type = "error";
    } else {
      $stmt->close();
      $prefix = ($role === 'admin') ? 'ADMNJTR' : 'EMPJTR';
      $stmt = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = '$role'");
      $row = $stmt->fetch_assoc();
      $next_id = $row['total'] + 1;
      $custom_id = $prefix . $next_id;

      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (id, username, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssss", $custom_id, $username, $email, $hashed_password, $phone, $address, $role);

      if ($stmt->execute()) {
        $newUserId = $conn->insert_id;
    
        // Ambil data user yang baru diregistrasi
        $getUser = $conn->prepare("SELECT id, username, email, phone, address, role FROM users WHERE id = ?");
        $getUser->bind_param("i", $newUserId);
        $getUser->execute();
        $result = $getUser->get_result();
        $newUser = $result->fetch_assoc();
    
        // Simpan ke session
        $_SESSION['user'] = [
          'id' => $newUser['id'],
          'username' => $newUser['username'],
          'email' => $newUser['email'],
          'phone' => $newUser['phone'],
          'address' => $newUser['address'],
          'role' => $newUser['role']
        ];
        
    
        // Set pesan sukses
        $message = ucfirst($role) . " successfully registered.";
        $type = "success";
    
        // echo "<pre>";
        // print_r($_SESSION['user']);
        // echo "</pre>";
        // exit;

        // Opsional: redirect ke halaman profil
        header("Location: admin/views/profil.php");
        exit();
      } else {
        $message = "Database error: " . $stmt->error;
        $type = "error";
      }     
    }
    $stmt->close();
  }
}