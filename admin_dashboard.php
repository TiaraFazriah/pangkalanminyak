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

$queryUser = mysqli_query($conn, "SELECT COUNT(*) as total_user FROM users WHERE role = 'user'");
$userData = mysqli_fetch_assoc($queryUser);
$totalUser = $userData['total_user'] ?? 0;
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

    <div class="flex min-h-screen relative overflow-x-hidden">
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <aside id="sidebar" class="fixed md:sticky top-0 left-0 h-screen w-64 bg-blue-900 text-white transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30 flex-shrink-0">
            <div class="p-6 text-2xl font-bold border-b border-blue-800 flex justify-between items-center">
                <span><i class="fas fa-gas-pump mr-2"></i> Admin Panel</span>
                <button onclick="toggleSidebar()" class="md:hidden text-white focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
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
                <a href="data_user.php" class="flex items-center p-3 mb-2 <?= isActive('data_user.php', $current_page) ?>">
                    <i class="fas fa-fw fa-users mr-3 w-5"></i> Data User
                </a>

                <div class="border-t border-blue-800 my-4"></div>
                <a href="logout.php" class="flex items-center p-3 text-red-300 hover:text-red-100 transition">
                    <i class="fas fa-sign-out-alt mr-3 w-5"></i> Logout
                </a>
            </nav>
        </aside>

        <main class="flex-1 min-w-0 flex flex-col">
            <header class="bg-white shadow-sm px-4 md:px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-700 text-xl focus:outline-none p-1">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-base md:text-xl font-bold text-slate-700 uppercase tracking-tight truncate">Dashboard Ringkasan</h2>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <span class="text-xs md:text-sm text-slate-400 hidden sm:inline"><?= date('l, d F Y') ?></span>
                    <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>
                    <span class="text-xs md:text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full md:bg-transparent md:p-0">Admin Utama</span>
                </div>
            </header>

            <div class="p-4 md:p-8 flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 mb-10">
                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Stok Tersedia</p>
                        <h3 class="text-2xl md:text-3xl font-black text-slate-800 break-all">
                            <?= number_format($dataStok['liter_tersedia'] ?? 0, 0, ',', '.') ?> 
                            <span class="text-lg font-normal text-slate-400">Ltr</span>
                        </h3>
                    </div>

                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Harga Aktif</p>
                        <h3 class="text-2xl md:text-3xl font-black text-blue-600 break-all">
                            Rp <?= number_format($dataStok['harga_per_liter'] ?? 0, 0, ',', '.') ?>
                        </h3>
                    </div>

                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Transaksi Hari Ini</p>
                        <h3 class="text-2xl md:text-3xl font-black text-green-600">
                            <?= $totalHariIni ?>
                        </h3>
                    </div>

                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Pelanggan</p>
                        <h3 class="text-2xl md:text-3xl font-black text-purple-600">
                            <?= $totalUser ?> <span class="text-lg font-normal text-slate-400">User</span>
                        </h3>
                    </div>
                </div>

                <div class="bg-blue-50 border-2 border-dashed border-blue-200 rounded-3xl p-6 md:p-12 text-center">
                    <i class="fas fa-chart-line text-blue-300 text-4xl md:text-5xl mb-4"></i>
                    <p class="text-blue-600 font-medium text-sm md:text-base">Selamat datang kembali! Pilih menu di samping untuk mengelola operasional pangkalan.</p>
                </div>
            </div> 
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => { backdrop.classList.add('opacity-100'); }, 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                setTimeout(() => { backdrop.classList.add('hidden'); }, 300);
            }
        }
        document.getElementById('sidebarBackdrop').addEventListener('click', toggleSidebar);
    </script>
</body>
</html>