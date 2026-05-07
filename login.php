<?php
session_start();
include 'koneksi.php';

function is_real_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    $domain = substr(strrchr($email, "@"), 1);
    if ($_SERVER['HTTP_HOST'] !== 'localhost' && function_exists('checkdnsrr')) {
        return checkdnsrr($domain, "MX");
    }
    return true; 
}

$error = "";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if (!is_real_email($email)) {
        $error = "Format Email tidak valid atau domain email tidak ditemukan!";
    } else {
        if ($role === 'admin') {
            if ($email !== 'tiarafazriah08@gmail.com') {
                $error = "Email Admin tidak dikenal!";
            } elseif ($username !== 'admin') {
                $error = "Username Admin salah!";
            } elseif ($password !== '7780990817') {
                $error = "Password Admin salah!";
            } else {
                $_SESSION['login'] = true;
                $_SESSION['username'] = 'Admin';
                $_SESSION['role'] = 'admin';
                header("Location: admin_dashboard.php"); exit;
            }
        } else {
            $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
            $data = mysqli_fetch_assoc($query);

            if ($data) {
                if ($username !== $data['username']) {
                    $error = "Username salah! Akun dengan email ini terdaftar dengan username lain.";
                } elseif ($password !== $data['password']) {
                    $error = "Password yang Anda masukkan salah!";
                } else {
                    $_SESSION['login']    = true;
                    $_SESSION['user_id']  = $data['id'];
                    $_SESSION['username'] = $data['nama_lengkap'];
                    $_SESSION['role']     = 'user';
                    header("Location: index.php"); exit;
                }
            } else {
                $nama_lengkap = ucwords($username); 
                $insert = mysqli_query($conn, "INSERT INTO users (email, username, password, nama_lengkap) 
                                               VALUES ('$email', '$username', '$password', '$nama_lengkap')");
                
                if ($insert) {
                    $new_id = mysqli_insert_id($conn);
                    $_SESSION['login']    = true;
                    $_SESSION['user_id']  = $new_id;
                    $_SESSION['username'] = $nama_lengkap;
                    $_SESSION['role']     = 'user';
                    header("Location: index.php"); exit;
                } else {
                    $error = "Terjadi kesalahan sistem saat membuat akun.";
                }
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
    <title>Login - Pangkalan Minyak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-[2rem] shadow-2xl p-10">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-slate-800">Login Sistem</h2>
            <p class="text-slate-400">Pangkalan Minyak Okita</p>
        </div>

        <?php if($error !== ""): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span class="text-sm font-bold"><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <select name="role" class="w-full px-4 py-3 border rounded-xl font-bold">
                <option value="user">User (Pelanggan)</option>
                <option value="admin">Admin</option>
            </select>
            
            <input type="email" name="email" placeholder="Email Asli" required 
                class="w-full px-4 py-3 border rounded-xl">
                
            <input type="text" name="username" placeholder="Username" required 
                class="w-full px-4 py-3 border rounded-xl">
                
            <input type="password" name="password" placeholder="Password" required 
                class="w-full px-4 py-3 border rounded-xl">
                
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">
                MASUK
            </button>
        </form>
    </div>

</body>
</html>