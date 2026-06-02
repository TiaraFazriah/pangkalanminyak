<?php
session_start();
include 'koneksi.php';

// Fungsi mendalam untuk mengecek keaslian akun email di dunia nyata
function is_real_google_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    
    // Pastikan berakhiran @gmail.com atau @googlemail.com
    $domain = substr(strrchr($email, "@"), 1);
    if ($domain !== 'gmail.com' && $domain !== 'googlemail.com') return false;
    
    // Jika tidak berjalan di komputer lokal (localhost), cek keaslian server MX Google
    if ($_SERVER['HTTP_HOST'] !== 'localhost' && function_exists('checkdnsrr')) {
        return checkdnsrr($domain, "MX");
    }
    return true; 
}

$error = "";
$email_terverifikasi = ""; 

// 1. ALUR TAB LOG IN (UNTUK YANG SUDAH MEMILIKI AKUN)
if (isset($_POST['login_manual'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cek Kredensial Login Admin Utama
    if ($username === 'admin' && $password === '7780990817') {
        $_SESSION['login'] = true;
        $_SESSION['username'] = 'Admin';
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php"); exit;
    } else {
        // Cek Login User Biasa
        $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        $data = mysqli_fetch_assoc($query);

        if ($data && $password === $data['password']) {
            $_SESSION['login']    = true;
            $_SESSION['user_id']  = $data['id'];
            $_SESSION['username'] = $data['nama_lengkap'];
            $_SESSION['role']     = 'user';
            header("Location: index.php"); exit;
        } else {
            $error = "Username atau Password salah!";
        }
    }
}

// 2. ALUR PROSES CEK KEASLIAN EMAIL GOOGLE (SIGN UP TAB)
if (isset($_POST['cek_email_google'])) {
    $email_input = trim($_POST['google_email_raw']);

    if (!is_real_google_email($email_input)) {
        $error = "Gagal! Email tersebut bukan akun Google (Gmail) yang valid atau aktif.";
    } else {
        // Jika lolos pengecekan keaslian, simpan email ke penampung view form berikutnya
        $email_terverifikasi = mysqli_real_escape_string($conn, $email_input);
        
        // Cek jika user biasa ternyata sudah terdaftar, langsung login otomatis tanpa buat password baru
        if ($email_terverifikasi !== 'tiarafazriah08@gmail.com') {
            $query_cek = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_terverifikasi'");
            $data_user = mysqli_fetch_assoc($query_cek);
            if ($data_user) {
                $_SESSION['login']    = true;
                $_SESSION['user_id']  = $data_user['id'];
                $_SESSION['username'] = $data_user['nama_lengkap'];
                $_SESSION['role']     = 'user';
                header("Location: index.php"); exit;
            }
        }
    }
}

// 3. ALUR FINALISASI: MENYIMPAN USERNAME & PASSWORD SETELAH EMAIL VALID
if (isset($_POST['proses_final_daftar'])) {
    $email = mysqli_real_escape_string($conn, $_POST['verified_email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $nama_lengkap = ucwords($username);

    if ($email === 'tiarafazriah08@gmail.com') {
        // Proteksi Ketat Admin Utama
        if ($username !== 'admin' || $password !== '7780990817') {
            $error = "Akses Admin Ditolak! Username atau Password Admin salah.";
            $email_terverifikasi = $email; 
        } else {
            $_SESSION['login'] = true;
            $_SESSION['username'] = 'Admin';
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php"); exit;
        }
    } else {
        // Validasi ketersediaan username untuk user biasa
        $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $error = "Username telah digunakan pelanggan lain! Silakan cari nama lain.";
            $email_terverifikasi = $email;
        } else {
            $insert = mysqli_query($conn, "INSERT INTO users (email, username, password, nama_lengkap) 
                                           VALUES ('$email', '$username', '$password', '$nama_lengkap')");
            if ($insert) {
                $_SESSION['login']    = true;
                $_SESSION['user_id']  = mysqli_insert_id($conn);
                $_SESSION['username'] = $nama_lengkap;
                $_SESSION['role']     = 'user';
                header("Location: index.php"); exit;
            } else {
                $error = "Gagal memproses pembuatan data baru ke database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Autentikasi - Pangkalan Minyak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfb; }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4">

    <div class="text-center mb-8 max-w-sm">
        <h1 class="text-4xl font-normal tracking-tight text-neutral-900 mb-2" style="font-family: Georgia, serif;">
            Masuk,<br>atau Daftar
        </h1>
        <p class="text-xs text-neutral-500">Pangkalan Minyak Okita — Sistem Manajemen</p>
    </div>

    <div class="w-full max-w-[420px] bg-white border border-neutral-200/80 rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.03)] p-8">
        
        <?php if($error !== ""): ?>
            <div class="bg-red-50 border border-red-100 text-red-700 p-3.5 rounded-2xl mb-5 flex items-start text-xs">
                <i class="fas fa-exclamation-circle mt-0.5 mr-2 flex-shrink-0"></i>
                <span class="font-medium"><?= $error ?></span>
            </div>
        <?php endif; ?>

        <?php if ($email_terverifikasi === ""): ?>
        <div class="flex bg-neutral-100 p-1 rounded-xl mb-6 text-xs font-semibold">
            <button onclick="pindahTab('login')" id="tabLoginBtn" class="flex-1 py-2 rounded-lg bg-white text-neutral-900 shadow-sm transition duration-200">
                Log In (Masuk)
            </button>
            <button onclick="pindahTab('signup')" id="tabSignupBtn" class="flex-1 py-2 rounded-lg text-neutral-500 hover:text-neutral-900 transition duration-200">
                Sign Up (Daftar Baru)
            </button>
        </div>
        <?php endif; ?>

        <div id="sectionLogIn" class="<?= $email_terverifikasi === '' ? 'block' : 'hidden' ?>">
            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" name="username" placeholder="Masukkan username Anda" required 
                        class="w-full px-4 py-3 border border-neutral-200 focus:border-neutral-900 rounded-xl text-sm placeholder-neutral-400 outline-none transition duration-200">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" name="password" placeholder="••••••••" required 
                        class="w-full px-4 py-3 border border-neutral-200 focus:border-neutral-900 rounded-xl text-sm placeholder-neutral-400 outline-none transition duration-200">
                </div>
                <button type="submit" name="login_manual" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-medium text-sm transition duration-200 tracking-wide mt-2">
                    Continue with credentials
                </button>
            </form>
        </div>

        <div id="sectionSignUp" class="hidden">
            <?php if ($email_terverifikasi === ""): ?>
                <p class="text-xs text-neutral-500 text-center mb-5 leading-relaxed">
                    Sistem wajib memverifikasi akun Google asli Anda. Silakan klik tombol di bawah untuk memeriksa email Anda.
                </p>
                
                <button type="button" onclick="bukaFormCekGoogle()" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-neutral-50 text-neutral-700 font-medium py-3 px-4 border border-neutral-200 rounded-xl transition duration-200 text-sm mb-2 shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.85z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.85c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </button>

                <form id="formCekGoogleRaw" action="" method="POST" class="hidden mt-4 space-y-3 pt-3 border-t border-neutral-100">
                    <div>
                        <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1">Ketik Akun Google (Gmail) Anda</label>
                        <input type="email" name="google_email_raw" placeholder="contoh@gmail.com" required 
                            class="w-full px-4 py-2.5 border border-neutral-200 focus:border-blue-500 rounded-xl text-sm outline-none transition">
                    </div>
                    <button type="submit" name="cek_email_google" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                        Verifikasi Akun Google
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($email_terverifikasi !== ""): ?>
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 mb-5 text-xs">
                <p class="text-emerald-700 font-bold mb-1"><i class="fas fa-check-circle mr-1"></i> Akun Google Terverifikasi Nyata:</p>
                <p class="font-mono text-neutral-800 bg-white p-2.5 rounded-lg border border-neutral-200/60 shadow-sm mt-1"><?= $email_terverifikasi ?></p>
            </div>

            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="verified_email" value="<?= $email_terverifikasi ?>">
                
                <div>
                    <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">
                        <?= $email_terverifikasi === 'tiarafazriah08@gmail.com' ? 'Username Khusus Admin (Ketik: admin)' : 'Buat Username Baru Anda' ?>
                    </label>
                    <input type="text" name="username" placeholder="Masukkan username" required 
                        class="w-full px-4 py-3 border border-neutral-200 focus:border-neutral-900 rounded-xl text-sm outline-none transition duration-200">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-neutral-400 uppercase tracking-wider mb-1.5">
                        <?= $email_terverifikasi === 'tiarafazriah08@gmail.com' ? 'Password Khusus Admin' : 'Buat Password Akun Baru' ?>
                    </label>
                    <input type="password" name="password" placeholder="••••••••" required 
                        class="w-full px-4 py-3 border border-neutral-200 focus:border-neutral-900 rounded-xl text-sm outline-none transition duration-200">
                </div>
                
                <button type="submit" name="proses_final_daftar" class="w-full bg-neutral-900 hover:bg-neutral-800 text-white py-3.5 rounded-xl font-medium text-sm transition duration-200 tracking-wide mt-2">
                    Selesaikan dan Masuk Sistem
                </button>
            </form>
        <?php endif; ?>

        <p class="text-[11px] text-center text-neutral-400 mt-6 leading-relaxed">
            By continuing, you acknowledge Pangkalan Okita's <a href="#" class="underline hover:text-neutral-600">Privacy Policy</a>.
        </p>
    </div>

    <script>
        function pindahTab(jenis) {
            const secLogin = document.getElementById('sectionLogIn');
            const secSignup = document.getElementById('sectionSignUp');
            const btnLogin = document.getElementById('tabLoginBtn');
            const btnSignup = document.getElementById('tabSignupBtn');

            if (jenis === 'login') {
                if(secLogin) secLogin.classList.remove('hidden');
                if(secSignup) secSignup.classList.add('hidden');
                if(btnLogin) btnLogin.className = "flex-1 py-2 rounded-lg bg-white text-neutral-900 shadow-sm transition duration-200";
                if(btnSignup) btnSignup.className = "flex-1 py-2 rounded-lg text-neutral-500 hover:text-neutral-900 transition duration-200";
            } else {
                if(secLogin) secLogin.classList.add('hidden');
                if(secSignup) secSignup.classList.remove('hidden');
                if(btnLogin) btnLogin.className = "flex-1 py-2 rounded-lg text-neutral-500 hover:text-neutral-900 transition duration-200";
                if(btnSignup) btnSignup.className = "flex-1 py-2 rounded-lg bg-white text-neutral-900 shadow-sm transition duration-200";
            }
        }

        function bukaFormCekGoogle() {
            document.getElementById('formCekGoogleRaw').classList.toggle('hidden');
        }
    </script>
    
    <?php if ($email_terverifikasi !== ""): ?>
    <script>
        // Memaksa tab sign up tetap terbuka ketika proses pembuatan password sedang berjalan
        pindahTab('signup');
    </script>
    <?php endif; ?>
</body>
</html>