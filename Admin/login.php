<?php
session_start();

// Cek path koneksi
$koneksi_path = __DIR__ . '/../koneksi.php';
if (!file_exists($koneksi_path)) {
    die("File koneksi tidak ditemukan");
}

include $koneksi_path;

// Jika admin sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

// Proses login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Gunakan prepared statement untuk keamanan
        $stmt = $koneksi->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $data['password'])) {
                $_SESSION['admin'] = [
                    'id' => $data['id'],
                    'username' => $data['username'],
                    'nama_lengkap' => $data['nama_lengkap']
                ];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin PPDB SMK UMAR MAS'UD</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #004080, #0073e6);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            background: #fff;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 25px;
            color: #004080;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #004080;
            box-shadow: 0 0 0 2px rgba(0, 64, 128, 0.1);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #004080;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #003366;
        }
        .error {
            color: #d63031;
            background: #ffeaea;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #d63031;
        }
        .success {
            color: #00b894;
            background: #e8f8f5;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #00b894;
        }
        footer {
            margin-top: 25px;
            font-size: 13px;
            color: #666;
        }
        .logo {
            margin-bottom: 20px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: left;
        }
        .info-box h4 {
            margin: 0 0 10px 0;
            color: #004080;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h2 style="color: #004080; margin: 0;">SMK UMAR MAS'UD</h2>
            <p style="color: #666; margin: 5px 0 0 0; font-size: 14px;">Admin Panel PPDB</p>
        </div>
        
        <h2>Login Administrator</h2>
        
        <div class="info-box">
            <h4>Informasi Login:</h4>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> password</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_GET['logout'])): ?>
            <div class="success">Anda telah berhasil logout.</div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : 'admin'; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required 
                       value="admin123">
            </div>
            
            <button type="submit" name="login">Masuk ke Dashboard</button>
        </form>
        
        <footer>
            <p>SMK UMAR MAS'UD &copy; 2025 - Sistem PPDB Online</p>
        </footer>
    </div>
</body>
</html>