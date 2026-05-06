<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$queryStok = mysqli_query($conn, "SELECT * FROM stok LIMIT 1");
$dataStok = mysqli_fetch_assoc($queryStok);

$today = date('Y-m-d');
$queryCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(waktu) = '$today'");
$countData = mysqli_fetch_assoc($queryCount);
$totalHariIni = $countData['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama - Pangkalan Minyak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-blue-900 text-white hidden md:block flex-shrink-0">
            <div class="p-6 text-2xl font-bold border-b border-blue-800">
                <i class="fas fa-gas-pump mr-2"></i> Admin Panel
            </div>
            <nav class="mt-6 px-4">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                
                function isActive($page, $current_page) {
                    return $page == $current_page 
                        ? 'bg-blue-800 rounded-lg' 
                        : 'hover:bg-blue-800 rounded-lg transition';
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

        <main class="flex-1">
            <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <h2 class="text-xl font-bold text-slate-700 uppercase tracking-tight">Dashboard Ringkasan</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-slate-400"><?= date('l, d F Y') ?></span>
                    <div class="h-8 w-[1px] bg-slate-200"></div>
                    <span class="text-sm font-bold text-blue-600">Admin Utama</span>
                </div>
            </header>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Stok Tersedia</p>
                        <h3 class="text-3xl font-black text-slate-800">
                            <?= number_format($dataStok['liter_tersedia'] ?? 0, 0, ',', '.') ?> 
                            <span class="text-lg font-normal text-slate-400">Ltr</span>
                        </h3>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Harga Aktif</p>
                        <h3 class="text-3xl font-black text-blue-600">
                            Rp <?= number_format($dataStok['harga_per_liter'] ?? 0, 0, ',', '.') ?>
                        </h3>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Transaksi Hari Ini</p>
                        <h3 class="text-3xl font-black text-green-600">
                            <?= $totalHariIni ?>
                        </h3>
                    </div>
                </div>

                <div class="bg-blue-50 border-2 border-dashed border-blue-200 rounded-3xl p-12 text-center">
                    <i class="fas fa-chart-line text-blue-300 text-5xl mb-4"></i>
                    <p class="text-blue-600 font-medium">Selamat datang kembali! Pilih menu di samping untuk mengelola operasional pangkalan.</p>
                </div>

            </div> 
        </main>
    </div>
</body>
</html>