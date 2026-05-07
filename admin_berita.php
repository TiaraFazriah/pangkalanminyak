<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$queryBerita = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal_post DESC");
$row = mysqli_fetch_assoc($queryBerita);

if (isset($_POST['simpan_berita'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = date('Y-m-d');

    $insert = mysqli_query($conn, "INSERT INTO berita (judul, isi, tanggal_post, kategori) VALUES ('$judul', '$isi', '$tanggal', '$kategori')");
    
    if ($insert) {
        header("Location: admin_berita.php?status=success");
    } else {
        header("Location: admin_berita.php?status=failed");
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM berita WHERE id = '$id'");
    header("Location: admin_berita.php?status=deleted");
}
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
                <h2 class="text-xl font-bold text-slate-700 uppercase tracking-tight">Update Berita</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-slate-400"><?= date('l, d F Y') ?></span>
                    <div class="h-8 w-[1px] bg-slate-200"></div>
                    <span class="text-sm font-bold text-blue-600">Admin Utama</span>
                </div>
            </header>

            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-3xl shadow-lg border border-blue-50">
                        <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter">Buat Berita Baru</h3>
                        <form action="" method="POST" class="space-y-5">
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase">Judul Berita</label>
                                <input type="text" name="judul" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase">Kategori</label>
                                <select name="kategori" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Info Stok">Info Stok</option>
                                    <option value="Harga">Update Harga</option>
                                    <option value="Pengumuman">Pengumuman</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase">Isi Berita</label>
                                <textarea name="isi" rows="5" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                            </div>
                            <button type="submit" name="simpan_berita" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition-all">Publish Berita</button>
                        </form>
                    </div>

                    <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter">Daftar Berita Terbaru</h3>
                        <div class="space-y-4">
                            <?php while($row = mysqli_fetch_assoc($queryBerita)): ?>
                            <div class="p-5 border border-slate-100 rounded-2xl hover:bg-slate-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-[10px] font-bold bg-blue-100 text-blue-600 px-2 py-1 rounded uppercase mb-2 inline-block">
                                            <?= $row['kategori'] ?>
                                        </span>
                                        <h4 class="font-bold text-slate-800 text-lg"><?= $row['judul'] ?></h4>
                                        <p class="text-xs text-slate-400 mb-3"><?= date('d F Y', strtotime($row['tanggal_post'])) ?></p>
                                        <p class="text-sm text-slate-600 line-clamp-2"><?= $row['isi'] ?></p>
                                    </div>
                                    <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus berita ini?')" class="text-red-400 hover:text-red-600 p-2">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            
                            <?php if(mysqli_num_rows($queryBerita) == 0): ?>
                                <p class="text-center text-slate-400 py-10">Belum ada berita yang diposting.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>