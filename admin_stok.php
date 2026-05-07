<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$queryStok = mysqli_query($conn, "SELECT * FROM stok LIMIT 1");
$data = mysqli_fetch_assoc($queryStok);
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
<body class="bg-slate-100 font-sans">
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
                <h2 class="text-xl font-bold text-slate-700 uppercase tracking-tight">Kelola Stok</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-slate-400"><?= date('l, d F Y') ?></span>
                    <div class="h-8 w-[1px] bg-slate-200"></div>
                    <span class="text-sm font-bold text-blue-600">Admin Utama</span>
                </div>
            </header>

            <div class="p-8">
                <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
                    <div class="bg-blue-600 p-6 text-white">
                        <h3 class="text-lg font-bold uppercase tracking-widest">Update Data Inventaris</h3>
                        <p class="text-blue-100 text-sm">Sesuaikan jumlah stok gudang dan harga jual saat ini.</p>
                    </div>
                    
                    <form action="proses_update.php" method="POST" class="p-8 space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-400 mb-2">Persediaan Saat Ini (Liter)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400"><i class="fas fa-oil-can"></i></span>
                                    <input type="number" name="stok" value="<?= $data['liter_tersedia'] ?>" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-xl">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-400 mb-2">Harga Jual per Liter (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">Rp</span>
                                    <input type="number" name="harga" value="<?= $data['harga_per_liter'] ?>" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-xl text-blue-600">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">
                                Simpan Perubahan Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>