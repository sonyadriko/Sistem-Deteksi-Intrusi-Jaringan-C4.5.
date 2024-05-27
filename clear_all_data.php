<?php
include 'koneksi.php'; // Include file koneksi.php untuk menghubungkan ke database

// Query untuk menghapus semua data dari tabel handphone
$sql = "TRUNCATE TABLE data_training";

if ($conn->query($sql) === TRUE) {
    // Jika penghapusan berhasil, kembalikan respons ke klien
    header("Location: data_training.php");
    exit; 
} else {
    // Jika terjadi kesalahan, kembalikan pesan kesalahan ke klien
    echo "Error deleting data: " . $conn->error;
}

// Menutup koneksi
$conn->close();