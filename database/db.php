<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'juragan_tulang_rangu'; // ganti sesuai nama database

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
