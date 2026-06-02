<?php
session_start();
include 'koneksi.php';

if (isset($_POST['submit_pesan'])) {
    $user_id = $_POST['user_id'];
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nomor_hp = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $jumlah = $_POST['jumlah'];
    
    $queryStok = mysqli_query($conn, "SELECT harga_per_liter FROM stok LIMIT 1");
    $dataStok = mysqli_fetch_assoc($queryStok);
    $total_harga = $jumlah * $dataStok['harga_per_liter'];

    $foto_ktp = $_FILES['foto_ktp']['name'];
    $tmp_name = $_FILES['foto_ktp']['tmp_name'];
    $ekstensi_boleh = ['jpg', 'jpeg', 'png'];
    $x = explode('.', $foto_ktp);
    $ekstensi = strtolower(end($x));
    
    $nama_file_baru = time() . '-' . $foto_ktp; 

    if (in_array($ekstensi, $ekstensi_boleh)) {
        move_uploaded_file($tmp_name, 'uploads/' . $nama_file_baru);
        $queryInput = "INSERT INTO transaksi (user_id, nomor_hp, jumlah, total_harga, foto_ktp, status, waktu) VALUES ('$user_id', '$nomor_hp', '$jumlah', '$total_harga', '$nama_file_baru', 'Menunggu', NOW())";
        if (mysqli_query($conn, $queryInput)) {
            echo "<script>alert('Pesanan berhasil dikirim. untuk konfirmasi lanjutannya akan dihubungi melalui whatsapp!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Format gambar harus JPG/PNG'); window.location='index.php';</script>";
    }
    exit;
}

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id']);
    $aksi = $_GET['aksi'];

    if ($aksi == 'terima') {
        $queryPesanan = mysqli_query($conn, "SELECT jumlah FROM transaksi WHERE id = '$id_transaksi'");
        $dataPesanan = mysqli_fetch_assoc($queryPesanan);
        if (!$dataPesanan) die("Error: Data pesanan tidak ditemukan.");
        
        $jumlah_beli = $dataPesanan['jumlah'];
        $queryStok = mysqli_query($conn, "SELECT liter_tersedia FROM stok LIMIT 1");
        $dataStok = mysqli_fetch_assoc($queryStok);
        $stok_sekarang = $dataStok['liter_tersedia'];
        
        if ($stok_sekarang >= $jumlah_beli) {
            mysqli_query($conn, "UPDATE stok SET liter_tersedia = liter_tersedia - $jumlah_beli");
            mysqli_query($conn, "UPDATE transaksi SET status = 'Diterima' WHERE id = '$id_transaksi'");
            echo "<script>alert('Berhasil! Pesanan diterima.'); window.location='proses_pesan.php';</script>";
        } else {
            echo "<script>alert('Gagal! Stok tidak cukup.'); window.location='proses_pesan.php';</script>";
        }
        exit;
    } elseif ($aksi == 'tolak') {
        mysqli_query($conn, "UPDATE transaksi SET status = 'Ditolak' WHERE id = '$id_transaksi'");
        header("Location: proses_pesan.php");
        exit;
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id']);
    $queryFoto = mysqli_query($conn, "SELECT foto_ktp FROM transaksi WHERE id = '$id_transaksi'");
    $dataFoto = mysqli_fetch_assoc($queryFoto);
    if ($dataFoto) {
        if (file_exists('uploads/' . $dataFoto['foto_ktp'])) unlink('uploads/' . $dataFoto['foto_ktp']);
        mysqli_query($conn, "DELETE FROM transaksi WHERE id = '$id_transaksi'");
        echo "<script>alert('Data berhasil dihapus'); window.location='proses_pesan.php';</script>";
    }
    exit;
}

$queryTransaksi = mysqli_query($conn, "SELECT t.*, u.nama_lengkap FROM transaksi t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.waktu DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - Admin Panel</title>
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
                    <h2 class="text-base md:text-xl font-bold text-slate-700 uppercase tracking-tight truncate">Pesanan Masuk</h2>
                </div>
                <span class="text-xs md:text-sm text-slate-400 hidden sm:inline"><?= date('l, d F Y') ?></span>
            </header>

            <div class="p-4 md:p-8 flex-1">
                <div class="bg-white p-4 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="space-y-6">
                        <?php while($row = mysqli_fetch_assoc($queryTransaksi)) : ?>
                        <div class="p-5 md:p-6 border border-slate-100 rounded-3xl hover:bg-slate-50 transition shadow-sm">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div class="min-w-0 flex-1">
                                    <?php 
                                        $st = $row['status'] ?? 'Menunggu';
                                        $badgeColor = ($st == 'Diterima') ? "bg-green-100 text-green-600" : (($st == 'Ditolak') ? "bg-red-100 text-red-600" : "bg-amber-100 text-amber-600");
                                    ?>
                                    <span class="text-[10px] font-black <?= $badgeColor ?> px-3 py-1 rounded-md uppercase mb-2 inline-block"><?= $st ?></span>
                                    <h4 class="text-lg md:text-xl font-bold text-slate-800 truncate"><?= htmlspecialchars($row['nama_lengkap'] ?? 'User') ?></h4>
                                    <p class="text-blue-600 font-semibold text-xs md:text-sm"><?= $row['nomor_hp'] ?></p>
                                    <p class="text-[10px] md:text-[11px] text-slate-400 mt-1 uppercase"><?= date('d F Y | H:i', strtotime($row['waktu'])) ?></p>
                                </div>
                                <div class="text-left sm:text-right flex-shrink-0">
                                    <p class="text-base md:text-lg font-black text-blue-600">RP <?= number_format($row['total_harga'], 0, ',', '.') ?></p>
                                    <p class="text-xs font-bold text-slate-600"><?= $row['jumlah'] ?> LITER</p>
                                </div>
                            </div>
                            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-2.5"> 
                                <a href="uploads/<?= $row['foto_ktp'] ?>" target="_blank" class="bg-slate-100 text-slate-600 text-center py-3 rounded-xl text-[10px] font-bold uppercase hover:bg-slate-200 transition flex items-center justify-center col-span-2 md:col-span-1">
                                    <i class="fas fa-id-card mr-2"></i> Lihat KTP
                                </a>
                                
                                <?php if($st == 'Menunggu'): ?>
                                    <a href="?aksi=terima&id=<?= $row['id'] ?>" class="bg-green-600 text-white text-center py-3 rounded-xl text-[10px] font-bold uppercase hover:bg-green-700 transition flex items-center justify-center">
                                        <i class="fas fa-check mr-2"></i> Terima
                                    </a>
                                    <a href="?aksi=tolak&id=<?= $row['id'] ?>" class="bg-red-50 text-red-600 text-center py-3 rounded-xl text-[10px] font-bold uppercase border border-red-100 hover:bg-red-100 transition flex items-center justify-center">
                                        <i class="fas fa-times mr-2"></i> Tolak
                                    </a>
                                <?php endif; ?>

                                <a href="?aksi=hapus&id=<?= $row['id'] ?>" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" 
                                class="bg-slate-800 text-white text-center py-3 rounded-xl text-[10px] font-bold uppercase hover:bg-black transition flex items-center justify-center <?php echo ($st != 'Menunggu') ? 'col-span-2 md:col-span-1' : '' ?>">
                                    <i class="fas fa-trash mr-2"></i> Hapus
                                </a>
                            </div>
                        </div>
                        <?php endwhile; ?>

                        <?php if(mysqli_num_rows($queryTransaksi) == 0): ?>
                            <div class="text-center py-20">
                                <i class="fas fa-shopping-basket text-slate-200 text-5xl mb-4"></i>
                                <p class="text-slate-400 font-medium text-sm">Belum ada pesanan yang masuk.</p>
                            </div>
                        <?php endif; ?>
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