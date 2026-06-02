<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id_user = mysqli_real_escape_string($conn, $_GET['hapus']);
    $hapus = mysqli_query($conn, "DELETE FROM users WHERE id = '$id_user' AND role = 'user'");
    if ($hapus) {
        echo "<script>alert('User berhasil dihapus!'); window.location='data_user.php';</script>";
    } else {
        die("Gagal menghapus: " . mysqli_error($conn));
    }
    exit;
}

$queryUsers = mysqli_query($conn, "SELECT id, username, email, role FROM users WHERE role = 'user' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User - Admin Panel</title>
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
                    <h2 class="text-base md:text-xl font-bold text-slate-700 uppercase tracking-tight truncate">Data User Pelanggan</h2>
                </div>
                <span class="text-xs md:text-sm text-slate-400 hidden sm:inline"><?= date('l, d F Y') ?></span>
            </header>

            <div class="p-4 md:p-8 flex-1">
                <div class="bg-white p-4 md:p-8 rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-6">
                        <h3 class="text-sm md:text-lg font-black text-slate-800 uppercase tracking-tighter">Daftar Pelanggan</h3>
                        <span class="w-max bg-blue-100 text-blue-600 px-4 py-1.5 rounded-full text-xs font-bold">
                            Total: <?= mysqli_num_rows($queryUsers) ?> User
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-xs font-black uppercase text-slate-400 tracking-wider">
                                    <th class="p-4 pl-6 w-16 text-center">No</th>
                                    <th class="p-4">Username</th>
                                    <th class="p-4">Email</th>
                                    <th class="p-4">Role</th>
                                    <th class="p-4 pr-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                                <?php 
                                $no = 1;
                                while($row = mysqli_fetch_assoc($queryUsers)) : 
                                ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 pl-6 text-center font-bold text-slate-400"><?= $no++; ?></td>
                                    <td class="p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold uppercase text-xs flex-shrink-0">
                                                <?= substr($row['username'], 0, 2); ?>
                                            </div>
                                            <span class="font-bold text-slate-800 truncate max-w-[120px] sm:max-w-none"><?= htmlspecialchars($row['username']); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-medium truncate max-w-[150px] sm:max-w-none"><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 text-[11px] font-bold bg-purple-100 text-purple-600 rounded-md uppercase">
                                            <?= $row['role']; ?>
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <a href="?hapus=<?= $row['id']; ?>" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" 
                                           class="inline-flex items-center justify-center p-2 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition duration-200">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>

                                <?php if(mysqli_num_rows($queryUsers) == 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-12 text-slate-400 italic">
                                        <i class="fas fa-users-slash text-4xl mb-3 block"></i> Belum ada pengguna terdaftar.
                                    </td>
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
    </script>
</body>
</html>