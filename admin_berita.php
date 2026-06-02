<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$queryBerita = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal_post DESC");

if (isset($_POST['simpan_berita'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = date('Y-m-d');

    $insert = mysqli_query($conn, "INSERT INTO berita (judul, isi, tanggal_post, kategori) VALUES ('$judul', '$isi', '$tanggal', '$kategori')");
    header("Location: admin_berita.php?status=" . ($insert ? "success" : "failed"));
    exit;
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM berita WHERE id = '$id'");
    header("Location: admin_berita.php?status=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Berita - Admin Panel</title>
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
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-700 text-xl focus:outline-none p-1"><i class="fas fa-bars"></i></button>
                    <h2 class="text-base md:text-xl font-bold text-slate-700 uppercase tracking-tight truncate">Update Berita</h2>
                </div>
                <span class="text-xs md:text-sm text-slate-400 hidden sm:inline"><?= date('l, d F Y') ?></span>
            </header>

            <div class="p-4 md:p-8 flex-1">
                <?php if(isset($_GET['status'])): ?>
                    <div class="mb-4 p-4 rounded-xl bg-emerald-100 text-emerald-800 text-xs md:text-sm font-bold">Aksi berhasil diproses!</div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg border border-blue-50 h-fit">
                        <h3 class="text-base md:text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter">Buat Berita Baru</h3>
                        <form action="" method="POST" class="space-y-5">
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase">Judul Berita</label>
                                <input type="text" name="judul" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase">Kategori</label>
                                <select name="kategori" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="fas fa-boxes">Info Stok</option>
                                    <option value="fas fa-tags">Update Harga</option>
                                    <option value="fas fa-bullhorn">Pengumuman</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase">Isi Berita</label>
                                <textarea name="isi" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                            </div>
                            <button type="submit" name="simpan_berita" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-all text-sm md:text-base">Publish Berita</button>
                        </form>
                    </div>

                    <div class="lg:col-span-2 bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-base md:text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter">Daftar Berita Terbaru</h3>
                        <div class="space-y-4">
                            <?php while($rowBerita = mysqli_fetch_assoc($queryBerita)): ?>
                            <div class="p-4 border border-slate-100 rounded-2xl hover:bg-slate-50 transition">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[9px] font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded uppercase mb-1 inline-block">
                                            <i class="<?= $rowBerita['kategori'] ?> mr-1"></i> Berita
                                        </span>
                                        <h4 class="font-bold text-slate-800 text-base md:text-lg truncate"><?= htmlspecialchars($rowBerita['judul']) ?></h4>
                                        <p class="text-[11px] text-slate-400 mb-2"><?= date('d F Y', strtotime($rowBerita['tanggal_post'])) ?></p>
                                        <p class="text-xs md:text-sm text-slate-600 line-clamp-2"><?= htmlspecialchars($rowBerita['isi']) ?></p>
                                    </div>
                                    <a href="?hapus=<?= $rowBerita['id'] ?>" onclick="return confirm('Hapus berita ini?')" class="text-red-400 hover:text-red-600 p-2 flex-shrink-0">
                                        <i class="fas fa-trash text-sm"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            
                            <?php if(mysqli_num_rows($queryBerita) == 0): ?>
                                <p class="text-center text-slate-400 py-10 text-sm">Belum ada berita yang diposting.</p>
                            <?php endif; ?>
                        </div>
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
    </script>
</body>
</html>