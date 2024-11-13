<?php
$host = "localhost";      // Ganti dengan host MySQL Anda
$user = "root";           // Ganti dengan username MySQL Anda
$password = "";   // Ganti dengan password MySQL Anda
$database = "db_iot_bb"; // Ganti dengan nama database Anda

// Membuat koneksi
$connection = new mysqli($host, $user, $password, $database);

// Memeriksa koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}
?>
