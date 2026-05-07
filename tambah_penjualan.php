<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryProduk = mysqli_query($conn, "SELECT id FROM produk LIMIT 1");
    $produk = mysqli_fetch_assoc($queryProduk);
    $produk_id = $produk['id'];

    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);
    $total = mysqli_real_escape_string($conn, $_POST['total']);
    
    $insert = mysqli_query($conn, "INSERT INTO transaksi (user_id, produk_id, jumlah, total_harga, status) 
                                   VALUES (NULL, '$produk_id', '$jumlah', '$total', 'Sukses')");

    if ($insert) {
        mysqli_query($conn, "UPDATE stok SET liter_tersedia = liter_tersedia - $jumlah WHERE produk_id = '$produk_id'");
        mysqli_query($conn, "UPDATE produk SET stok_sekarang = stok_sekarang - $jumlah WHERE id = '$produk_id'");
        
        header("Location: admin_dashboard.php?status=success_transaksi");
    } else {
        header("Location: admin_dashboard.php?status=failed");
    }
}
?>