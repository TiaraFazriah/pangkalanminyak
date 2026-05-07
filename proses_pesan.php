<?php
session_start();
include 'koneksi.php';

if (isset($_POST['submit_pesan'])) {
    if (!isset($_SESSION['login']) || !isset($_SESSION['user_id'])) {
        echo "<script>alert('Sesi habis atau Anda belum login. Silakan login kembali.'); window.location='login.php';</script>";
        exit;
    }

    $user_id    = $_SESSION['user_id']; 
    $nomor_hp   = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $jumlah     = mysqli_real_escape_string($conn, $_POST['jumlah']);
    
    // Ambil data produk dan harga dari tabel stok
    $queryStok  = mysqli_query($conn, "SELECT * FROM stok LIMIT 1");
    $dataStok   = mysqli_fetch_assoc($queryStok);
    
    if (!$dataStok) {
        die("Data stok tidak ditemukan.");
    }

    $produk_id   = $dataStok['produk_id'];
    $total_harga = (int)$jumlah * (int)$dataStok['harga_per_liter'];

    // 3. Proses Upload Gambar
    $foto_ktp   = $_FILES['foto_ktp']['name'];
    $tmp_name   = $_FILES['foto_ktp']['tmp_name'];
    $ekstensi   = pathinfo($foto_ktp, PATHINFO_EXTENSION);
    $nama_file  = time() . "_" . $user_id . "." . $ekstensi;

    if (move_uploaded_file($tmp_name, "uploads/" . $nama_file)) {
        // 4. Query INSERT dengan memastikan user_id adalah angka (tanpa tanda kutip jika perlu, 
        // tapi dalam PHP mysqli biasanya aman jika variabelnya berisi angka)
        $query = "INSERT INTO transaksi (waktu, user_id, nomor_hp, produk_id, jumlah, total_harga, foto_ktp, status) 
                  VALUES (NOW(), $user_id, '$nomor_hp', '$produk_id', $jumlah, $total_harga, '$nama_file', 'Sukses')";
        
        if (mysqli_query($conn, $query)) {
            // Update stok di dua tabel sesuai struktur SQL Anda
            mysqli_query($conn, "UPDATE stok SET liter_tersedia = liter_tersedia - $jumlah WHERE produk_id = '$produk_id'");
            mysqli_query($conn, "UPDATE produk SET stok_sekarang = stok_sekarang - $jumlah WHERE id = '$produk_id'");
            
            echo "<script>alert('Pesanan Berhasil!'); window.location='index.php';</script>";
            exit;
        } else {
            // Menampilkan pesan eror jika query SQL gagal
            die("Gagal simpan transaksi: " . mysqli_error($conn));
        }
    } else {
        echo "<script>alert('Gagal mengunggah foto KTP.'); window.history.back();</script>";
    }
}

// PROTEKSI HALAMAN ADMIN
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// AMBIL DATA TRANSAKSI UNTUK DITAMPILKAN
$queryTransaksi = mysqli_query($conn, "
    SELECT t.*, u.nama_lengkap 
    FROM transaksi t
    LEFT JOIN users u ON t.user_id = u.id 
    ORDER BY t.waktu DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-blue-900 text-white hidden md:block flex-shrink-0">
            <div class="p-6 text-2xl font-bold border-b border-blue-800">
                <i class="fas fa-gas-pump mr-2"></i> Admin Panel
            </div>
            <nav class="mt-6 px-4">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                function isActive($page, $current_page) {
                    return ($page == $current_page) ? 'bg-blue-800 rounded-lg' : 'hover:bg-blue-800 rounded-lg transition';
                }
                ?>
                <a href="admin_dashboard.php" class="flex items-center p-3 mb-2 <?= isActive('admin_dashboard.php', $current_page) ?>">
                    <i class="fas fa-home mr-3 w-5"></i> Dashboard
                </a>
                <a href="admin_stok.php" class="flex items-center p-3 mb-2 <?= isActive('admin_stok.php', $current_page) ?>">
                    <i class="fas fa-boxes mr-3 w-5"></i> Kelola Stok
                </a>
                <a href="admin_berita.php" class="flex items-center p-3 mb-2 <?= isActive('admin_berita.php', $current_page) ?>">
                    <i class="fas fa-newspaper mr-3 w-5"></i> Update Berita
                </a>
                <a href="proses_pesan.php" class="flex items-center p-3 mb-2 <?= isActive('proses_pesan.php', $current_page) ?>">
                    <i class="fas fa-shopping-cart mr-3 w-5"></i> Pesanan Masuk
                </a>
                <div class="border-t border-blue-800 my-4"></div>
                <a href="logout.php" class="flex items-center p-3 text-red-300 hover:text-red-100 transition">
                    <i class="fas fa-sign-out-alt mr-3 w-5"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center mb-6 rounded-xl">
                <h2 class="text-xl font-semibold text-gray-700">Daftar Pesanan Masuk</h2>
                <span class="text-sm font-bold text-blue-600">Admin Utama</span>
            </header>

            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="p-4">Waktu</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">No. HP</th>
                            <th class="p-4 text-center">Jumlah</th>
                            <th class="p-4">Total Harga</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($queryTransaksi)) : ?>
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-4"><?= date('d/m/y H:i', strtotime($row['waktu'])) ?></td>
                            <td class="p-4 font-bold"><?= htmlspecialchars($row['nama_lengkap'] ?? 'User') ?></td>
                            <td class="p-4"><?= $row['nomor_hp'] ?></td>
                            <td class="p-4 text-center"><?= $row['jumlah'] ?> L</td>
                            <td class="p-4 font-bold text-blue-600">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td class="p-4">
                                <a href="uploads/<?= $row['foto_ktp'] ?>" target="_blank" class="bg-blue-100 text-blue-600 px-3 py-1 rounded-lg text-xs">Lihat KTP</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>