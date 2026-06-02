<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$queryStok = mysqli_query($conn, "SELECT * FROM stok LIMIT 1");
$data = mysqli_fetch_assoc($queryStok);

$queryLog = mysqli_query($conn, "SELECT t.waktu, t.jumlah, t.total_harga, u.nama_lengkap FROM transaksi t LEFT JOIN users u ON t.user_id = u.id WHERE t.status = 'Diterima' ORDER BY t.waktu DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok - Pangkalan Minyak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 font-sans text-slate-900">
    <div class="flex min-h-screen relative overflow-x-hidden">
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

        <aside id="sidebar" class="fixed md:sticky top-0 left-0 h-screen w-64 bg-blue-900 text-white transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-30 flex-shrink-0">
            <div class="p-6 text-2xl font-bold border-b border-blue-800 flex justify-between items-center">
                <span><i class="fas fa-gas-pump mr-2"></i> Admin Panel</span>
                <button onclick="toggleSidebar()" class="md:hidden text-white focus:outline-none"><i class="fas fa-times"></i></button>
            </div>
            <nav class="mt-6 px-4">
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                function isActive($page, $current_page) {
                    return $page == $current_page ? 'bg-blue-800 rounded-lg' : 'hover:bg-blue-800 rounded-lg transition';
                }
                ?>
                <a href="admin_dashboard.php" class="flex items-center p-3 mb-2 <?= isActive('admin_dashboard.php', $current_page) ?>"><i class="fas fa-home mr-3 w-5"></i> Dashboard</a>
                <a href="admin_stok.php" class="flex items-center p-3 mb-2 <?= isActive('admin_stok.php', $current_page) ?>"><i class="fas fa-boxes mr-3 w-5"></i> Kelola Stok</a>
                <a href="admin_berita.php" class="flex items-center p-3 mb-2 <?= isActive('admin_berita.php', $current_page) ?>"><i class="fas fa-newspaper mr-3 w-5"></i> Update Berita</a>
                <a href="proses_pesan.php" class="flex items-center p-3 mb-2 <?= isActive('proses_pesan.php', $current_page) ?>"><i class="fas fa-shopping-cart mr-3 w-5"></i> Pesanan Masuk</a>
                <a href="data_user.php" class="flex items-center p-3 mb-2 <?= isActive('data_user.php', $current_page) ?>"><i class="fas fa-fw fa-users mr-3 w-5"></i> Data User</a>
                <div class="border-t border-blue-800 my-4"></div>
                <a href="logout.php" class="flex items-center p-3 text-red-300 hover:text-red-100 transition"><i class="fas fa-sign-out-alt mr-3 w-5"></i> Logout</a>
            </nav>
        </aside>

       <main class="flex-1 min-w-0 flex flex-col">
            <header class="bg-white shadow-sm px-4 md:px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-700 text-xl focus:outline-none p-1">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-base md:text-xl font-bold text-slate-700 uppercase tracking-tight truncate">Manajemen Stok</h2>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <span class="text-xs md:text-sm text-slate-400 hidden sm:inline"><?= date('l, d F Y') ?></span>
                    <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>
                    <span class="text-xs md:text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full md:bg-transparent md:p-0">Admin Utama</span>
                </div>
            </header>

            <div class="p-4 md:p-8 space-y-8 flex-1">
                
                <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
                        <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                        <span class="text-sm font-medium">Data inventaris tangki penampungan dan harga eceran berhasil diperbarui!</span>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    
                    <div class="lg:col-span-2 bg-white rounded-3xl shadow-md border border-slate-200/60 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Form Pembaruan Volume</h3>
                                <p class="text-xs text-slate-400">Pastikan angka akurat sesuai stock opname fisik gudang</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                        </div>
                        
                        <form action="proses_update.php" method="POST" class="p-6 md:p-8 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Volume Tangki Tersedia</label>
                                    <div class="relative group">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-blue-500 transition"><i class="fas fa-tint"></i></span>
                                        <input type="number" name="stok" id="inputStok" value="<?= $data['liter_tersedia'] ?>" min="0" class="w-full pl-12 pr-16 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none font-bold text-lg text-slate-800 transition" required>
                                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-slate-400 uppercase">Liter</span>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Harga Retail Per Liter</label>
                                    <div class="relative group">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold group-focus-within:text-blue-500 transition">Rp</span>
                                        <input type="number" name="harga" value="<?= $data['harga_per_liter'] ?>" min="0" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none font-bold text-lg text-blue-600 transition" required>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3.5 rounded-xl font-bold text-sm tracking-wide shadow-lg shadow-blue-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i> Terapkan Data Baru
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-3xl shadow-md border border-slate-200/60 p-6 space-y-6">
                        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Status Tangki Saat Ini</h4>
                        
                        <div id="statusBox" class="p-6 rounded-2xl transition-all duration-300">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-white shadow-sm" id="statusBadge">Memuat...</span>
                                <i class="fas fa-boxes text-xl opacity-40"></i>
                            </div>
                            <div class="space-y-1">
                                <p class="text-2xl font-black" id="previewLiter">0 Ltr</p>
                                <p class="text-[11px] opacity-70">Kalkulasi Batas Aman Operasional</p>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-4 space-y-3">
                            <div class="flex justify-between text-xs font-medium text-slate-500">
                                <span>Nilai Asset Stok Aktif:</span>
                                <span class="font-bold text-slate-800">Rp <?= number_format(($data['liter_tersedia'] * $data['harga_per_liter']), 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-xs font-medium text-slate-500">
                                <span>Batas Minimum Aman:</span>
                                <span class="text-red-500 font-bold">200 Liter</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">5 Transaksi Terakhir Terverifikasi (Pengurang Stok Otomatis)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold uppercase text-slate-400 tracking-wider">
                                    <th class="p-4 pl-6">Waktu Transaksi</th>
                                    <th class="p-4">Nama Pelanggan</th>
                                    <th class="p-4 text-center">Volume Pengeluaran</th>
                                    <th class="p-4 pr-6 text-right">Nilai Transaksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-xs text-slate-600">
                                <?php while($log = mysqli_fetch_assoc($queryLog)): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4 pl-6 font-medium text-slate-400"><?= date('d/m/Y H:i', strtotime($log['waktu'])) ?></td>
                                    <td class="p-4 font-bold text-slate-800"><?= htmlspecialchars($log['nama_lengkap'] ?? 'Pembelian Offline') ?></td>
                                    <td class="p-4 text-center font-bold text-red-500">- <?= $log['jumlah'] ?> Liter</td>
                                    <td class="p-4 pr-6 text-right font-semibold text-slate-700">Rp <?= number_format($log['total_harga'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endwhile; ?>
                                
                                <?php if(mysqli_num_rows($queryLog) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-400 italic">Belum terjadi pergeseran log volume penjualan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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

        const inputStok = document.getElementById('inputStok');
        const statusBox = document.getElementById('statusBox');
        const statusBadge = document.getElementById('statusBadge');
        const previewLiter = document.getElementById('previewLiter');

        function updateWidgetIndicator() {
            const val = parseInt(inputStok.value) || 0;
            previewLiter.innerText = val.toLocaleString('id-ID') + ' Ltr';
            
            if (val <= 200) {
                statusBox.className = "p-6 rounded-2xl bg-rose-50 text-rose-700 border border-rose-200 shadow-sm";
                statusBadge.className = "text-[10px] font-bold uppercase px-3 py-1 rounded-full bg-white shadow-sm text-rose-600";
                statusBadge.innerText = "Stok Kritis";
            } else if (val <= 500) {
                statusBox.className = "p-6 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 shadow-sm";
                statusBadge.className = "text-[10px] font-bold uppercase px-3 py-1 rounded-full bg-white shadow-sm text-amber-600";
                statusBadge.innerText = "Stok Menipis";
            } else {
                statusBox.className = "p-6 rounded-2xl bg-blue-50 text-blue-700 border border-blue-200 shadow-sm";
                statusBadge.className = "text-[10px] font-bold uppercase px-3 py-1 rounded-full bg-white shadow-sm text-blue-600";
                statusBadge.innerText = "Tangki Aman";
            }
        }

        inputStok.addEventListener('input', updateWidgetIndicator);
        window.addEventListener('DOMContentLoaded', updateWidgetIndicator);
    </script>
</body>
</html>