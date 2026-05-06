<?php
session_start();
include 'koneksi.php';

$queryStok = mysqli_query($conn, "SELECT * FROM stok LIMIT 1");
$dataStok = mysqli_fetch_assoc($queryStok);

$queryBeritaIndex = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal_post DESC");
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pangkalan Minyak - Okita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .glass-nav { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="text-slate-900 leading-relaxed">

    <nav class="glass-nav shadow-sm sticky top-0 z-50 border-b border-slate-100">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-serif font-bold tracking-tighter text-blue-900">
                PANGKALAN<span class="text-blue-500 underline decoration-blue-200">OKITA</span>
            </a>
            <div class="hidden md:flex space-x-8 font-medium items-center text-xs uppercase tracking-widest">
                <a href="#beranda" class="hover:text-blue-600 transition">Beranda</a>
                <a href="#tentang" class="hover:text-blue-600 transition">Tentang</a>
                <a href="#produk" class="hover:text-blue-600 transition">Harga</a>
                <a href="#pemesanan" class="hover:text-blue-600 transition">Pesan</a>
                <a href="#berita" class="hover:text-blue-600 transition">Berita</a>
                <a href="#kontak" class="hover:text-blue-600 transition">Kontak</a>
                <?php if (isset($_SESSION['login'])): ?>
                    <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full font-bold italic"><?= strtoupper($_SESSION['username']) ?></span>
                    <a href="logout.php" class="text-red-500 font-bold border-l pl-4 border-slate-200">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-blue-600 text-white px-8 py-2.5 rounded-full font-bold shadow-lg hover:bg-blue-700 transition">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header id="beranda" class="pt-20 pb-32 bg-blue-600 text-white">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-20 items-center">
                <div>
                    <h1 class="text-7xl font-serif font-bold leading-[1.1] mb-8">Energi Bersih untuk<br><span class="text-blue-200 italic">Rumah Tangga.</span></h1>
                    <p class="text-lg text-blue-100 mb-10 border-l-4 border-white pl-6">
                        Tampilan utama pangkalan dengan informasi singkat. Distribusi terpercaya dengan standar pelayanan premium.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#produk" class="bg-white text-blue-600 px-10 py-4 rounded-xl font-bold uppercase text-xs tracking-widest shadow-xl hover:bg-blue-50 transition">Lihat Produk</a>
                    </div>
                </div>
                <div class="relative" style="border-radius: 15px; overflow: hidden;">
                    <img src="1.jpeg">
                </div>
            </div>
        </div>
    </header>

    <section id="tentang" class="py-32 bg-amber-50">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1">
                    <img src="2.jpeg" class="rounded-[2rem] shadow-lg grayscale hover:grayscale-0 transition duration-700">
                </div>
                <div class="order-1 md:order-2">
                    <span class="text-amber-600 font-black text-xs uppercase tracking-[0.3em]">Profil Pangkalan</span>
                    <h2 class="text-4xl font-serif font-bold mt-4 mb-6 text-slate-800">Sejarah & Tujuan Pelayanan</h2>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Didirikan untuk memberikan kemudahan akses bahan bakar bagi masyarakat. Kami berkomitmen menjaga transparansi harga dan keakuratan takaran di setiap liternya.
                    </p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center text-amber-700 font-bold">01</div>
                        <p class="font-bold text-slate-700">Solusi energi rumah tangga: Tepat sasaran, selalu tersedia.</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center text-amber-700 font-bold">02</div>
                        <p class="font-bold text-slate-700">Jujur dalam Timbangan, Unggul dalam Pelayanan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="produk" class="py-32 bg-emerald-900 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-serif font-bold mb-4">Informasi Produk & Harga</h2>
            <p class="text-emerald-200/60 mb-16 max-w-2xl mx-auto italic">Banner harga terbaru dan ketentuan pembelian maksimal per orang.</p>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-10 bg-emerald-800 rounded-[2.5rem] border border-emerald-700 shadow-xl">
                    <h4 class="text-sm font-black uppercase tracking-widest text-emerald-400 mb-2">Harga Per Liter</h4>
                    <h3 class="text-5xl font-black mb-4">Rp <?= number_format($dataStok['harga_per_liter']) ?></h3>
                    <p class="text-xs text-emerald-300">Update Harga Hari Ini</p>
                </div>
                <div class="p-10 bg-white text-emerald-900 rounded-[2.5rem] shadow-2xl">
                    <h4 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-2">Stok Tersedia</h4>
                    <h3 class="text-5xl font-black mb-4"><?= number_format($dataStok['liter_tersedia']) ?> <span class="text-xl">Ltr</span></h3>
                    <p class="text-xs font-bold text-emerald-600">Siap Distribusi</p>
                </div>
                <div class="p-10 bg-emerald-800 rounded-[2.5rem] border border-emerald-700 text-left">
                    <h4 class="text-sm font-black uppercase tracking-widest text-emerald-400 mb-6">Ketentuan Pembelian</h4>
                    <ul class="space-y-4 text-sm">
                        <li><i class="fas fa-check-circle mr-2 text-emerald-400"></i> Maksimal 200 Liter per orang</li>
                        <li><i class="fas fa-check-circle mr-2 text-emerald-400"></i> Membawa Derom/Jerigen</li>
                        <li><i class="fas fa-check-circle mr-2 text-emerald-400"></i> Mengikuti aturan yang berlaku</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'user'): ?>
    <section id="pemesanan" class="py-20 bg-blue-50">
        <div class="max-w-xl mx-auto bg-white p-10 rounded-[2rem] shadow-lg border border-blue-100">
            <h2 class="text-3xl font-bold mb-6 text-center text-blue-900">Form Pemesanan</h2>
            
            <form action="proses_pesan.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                
                <div>
                    <label class="block text-xs font-bold uppercase mb-2 text-slate-500">Nama Lengkap (Sesuai KTP)</label>
                    <input type="text" name="nama_lengkap" required class="w-full p-4 bg-slate-50 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                
                <div>
                    <label class="block text-xs font-bold uppercase mb-2 text-slate-500">Nomor HP / WhatsApp</label>
                    <input type="number" name="nomor_hp" required class="w-full p-4 bg-slate-50 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase mb-2 text-slate-500">Jumlah Liter</label>
                        <input type="number" name="jumlah" min="1" max="800" required class="w-full p-4 bg-slate-50 border rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase mb-2 text-slate-500">Lampiran Foto KTP</label>
                        <input type="file" name="foto_ktp" accept="image/*" required class="w-full p-3 bg-slate-50 border rounded-2xl text-xs">
                    </div>
                </div>

                <button type="submit" name="submit_pesan" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition-all active:scale-95 shadow-lg shadow-blue-200">
                    Konfirmasi Pesanan
                </button>
            </form>
        </div>
    </section>
    <?php endif; ?>
    <section id="berita" class="py-32 bg-purple-50">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <span class="text-purple-600 font-black text-xs uppercase tracking-widest">Update Informasi</span>
                    <h2 class="text-4xl font-serif font-bold mt-2">Jadwal & Pengumuman</h2>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <?php if(mysqli_num_rows($queryBeritaIndex) > 0): ?>
                    <?php while($b = mysqli_fetch_assoc($queryBeritaIndex)): ?>
                    <div class="bg-white p-8 rounded-3xl shadow-sm border-b-8 border-purple-500 hover:transform hover:-translate-y-2 transition duration-300">
                        <i class="<?= $b['kategori'] ?> text-3xl text-purple-600 mb-6"></i>
                        <h3 class="font-bold text-xl mb-4 text-slate-800"><?= $b['judul'] ?></h3>
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-3">
                            <?= $b['isi'] ?>
                        </p>
                        <div class="mt-6 pt-6 border-t border-slate-50 flex justify-between items-center">
                            <span class="text-[10px] text-slate-400 uppercase font-black tracking-widest">
                                <?= date('d M Y', strtotime($b['tanggal_post'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-10 text-slate-400 italic">
                        Belum ada berita atau pengumuman saat ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="kontak" class="py-32 bg-slate-900 text-white">
        <div class="container mx-auto px-6">
            <div class="max-w-5xl mx-auto">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-4xl font-serif font-bold mb-8">Informasi Kontak <br>& Lokasi</h2>
                        <div class="space-y-8">
                            <div class="flex items-center space-x-6">
                                <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-blue-500/20">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-black uppercase">Hubungi WhatsApp</p>
                                    <p class="text-xl font-bold">+62 812-3760-3050</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center text-2xl">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-black uppercase">Alamat Pangkalan</p>
                                    <p class="text-lg opacity-80">Lunggaria, Kec.Ndori, Kab.Ende</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-[2.5rem] overflow-hidden h-[400px] shadow-2xl border-4 border-slate-800">
                       <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4688.891290979138!2d121.92432983688377!3d-8.798105808671751!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dad4140a4fe7229%3A0x8127e486e2ca647b!2sUD.%20OKITA!5e0!3m2!1sen!2sid!4v1773629432728!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-12 bg-black text-center text-slate-600 text-[10px] font-black uppercase tracking-[0.5em]">
        © 2026 Pangkalan Minyak Okita 
    </footer>
</body>
</html>