<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'admin' && $password === 'admin1') {
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit();
    }

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) || $user['password'] === $password) {
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit();
        }
    }
    
    $error = "Invalid username or password";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="image.png">
    <title>HOA System Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 64, 175, 0.95));
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }
        .login-container {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: fadeIn 0.5s ease-out;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-section img {
            width: 120px;
            height: auto;
            margin-bottom: 1rem;
        }
        .logo-section h1 {
            color: #e2e8f0;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .logo-section p {
            color: #94a3b8;
            margin-top: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .password-toggle:hover {
            color: #e2e8f0;
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h2 {
            color: #e2e8f0;
            margin: 1rem 0 0.5rem;
        }
        .login-header p {
            color: #94a3b8;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <img src="image.png" alt="HOA System Logo">
            <div class="system-name">
                <h1>HOA Management System</h1>
                <p>Homeowners association management</p>
            </div>
        </div>

        <div class="login-header">
            <i class="fas fa-user-circle fa-3x" style="color: #3498db;"></i>
            <h2>Welcome Back</h2>
            <p>Login to access your dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="alert" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" class="form-control" id="username" name="username" 
                       placeholder="Username" required autocomplete="username">
            </div>

            <div class="form-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Password" required autocomplete="current-password">
                <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Add input animations
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'scale(1.02)';
                input.parentElement.style.transition = 'transform 0.3s ease';
            });
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
