<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    $check = mysqli_query($conn, "SELECT produk_id FROM stok LIMIT 1");
    $row = mysqli_fetch_assoc($check);
    
    if ($row) {
        $p_id = $row['produk_id'];
        
        mysqli_query($conn, "UPDATE stok SET liter_tersedia = '$stok', harga_per_liter = '$harga' WHERE produk_id = '$p_id'");

        mysqli_query($conn, "UPDATE produk SET harga_per_liter = '$harga', stok_sekarang = '$stok' WHERE id = '$p_id'");
        
        header("Location: admin_dashboard.php?status=success");
    } else {

        header("Location: admin_dashboard.php?status=empty_database");
    }
    exit;
}