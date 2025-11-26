<?php
session_start();

// Include koneksi yang sudah ada auto-setup
include 'koneksi.php';

// Cek jika user sudah login, redirect ke dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Proses login
if(isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Debug info
    error_log("Login attempt: username=$username");
    
    // Cek user di database
    $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    
    if($query->execute()) {
        $result = $query->get_result();
        
        if($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            error_log("User found: " . $user['username']);
            
            // Verifikasi password
            if(password_verify($password, $user['password'])) {
                error_log("Password verified for user: " . $user['username']);
                
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                
                error_log("Session set, redirecting to dashboard");
                
                // Redirect ke dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Password salah!";
                error_log("Password verification failed for user: " . $user['username']);
            }
        } else {
            $error = "Username tidak ditemukan!";
            error_log("User not found: $username");
        }
    } else {
        $error = "Error dalam proses login!";
        error_log("Query execution failed: " . $query->error);
    }
}

// Cek apakah ada user di database
$check_users = $conn->query("SELECT username FROM users LIMIT 1");
$default_users = $check_users->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }
        
        .login-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control-custom:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            background: white;
        }
        
        .input-group-custom {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }
        
        .input-with-icon {
            padding-left: 45px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 12px 15px;
            font-weight: 500;
        }
        
        .demo-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .demo-info h6 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        
        .forgot-password a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: var(--primary);
            text-decoration: underline;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Animasi untuk input */
        .form-control-custom {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                border-radius: 15px;
            }
            
            .login-body {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="bi bi-cart-check me-2"></i>KASIR PRO</h2>
            <p>Sistem Manajemen Toko Alat Tulis</p>
        </div>
        
        <div class="login-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(!$default_users): ?>
                <div class="alert alert-info alert-custom">
                    <i class="bi bi-info-circle me-2"></i>
                    Sistem sedang membuat database dan user default...
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="username" class="form-control form-control-custom input-with-icon" 
                               placeholder="Masukkan username" required autofocus value="admin">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" class="form-control form-control-custom input-with-icon" 
                               placeholder="Masukkan password" required value="admin123">
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>MASUK KE DASHBOARD
                </button>
            </form>
            
            <div class="demo-info">
                <h6><i class="bi bi-key me-2"></i>Akun Demo (Auto-generated):</h6>
                <div class="row">
                    <div class="col-6">
                        <strong>Admin:</strong><br>
                        <small>Username: <code>admin</code></small><br>
                        <small>Password: <code>admin123</code></small>
                    </div>
                    <div class="col-6">
                        <strong>Kasir:</strong><br>
                        <small>Username: <code>kasir</code></small><br>
                        <small>Password: <code>admin123</code></small>
                    </div>
                </div>
            </div>
            
            <div class="forgot-password">
                <a href="#">Lupa password?</a>
            </div>
            
            <div class="login-footer">
                <p>&copy; 2024 Kasir Pro. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi tambahan untuk form
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control-custom');
            inputs.forEach((input, index) => {
                input.style.animationDelay = (index * 0.1) + 's';
            });
            
            // Auto-fill demo credentials
            document.querySelector('input[name="username"]').value = 'admin';
            document.querySelector('input[name="password"]').value = 'admin123';
            
            // Focus pada username field
            document.querySelector('input[name="username"]').focus();
            
            // Efek saat submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                const btn = document.querySelector('.btn-login');
                btn.innerHTML = '<i class="bi bi-arrow-repeat spinner me-2"></i>Login & Redirect...';
                btn.disabled = true;
            });
        });
        
        // Style untuk spinner
        const style = document.createElement('style');
        style.textContent = `
            .spinner {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>