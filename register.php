<?php
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $check_sql = "SELECT * FROM admin WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            // Insert new admin
            $sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            
            if ($stmt->execute()) {
                $success = "Admin account created successfully!";
            } else {
                $error = "Error creating account: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
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
        .register-container {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
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
        }
        .form-label {
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .form-control::placeholder {
            color: #64748b;
        }
        .password-toggle {
            position: relative;
        }
        .password-toggle .form-control {
            padding-right: 2.5rem;
        }
        .password-toggle-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            transition: color 0.3s ease;
        }
        .password-toggle-btn:hover {
            color: #e2e8f0;
        }
        .password-strength {
            margin-top: 0.5rem;
        }
        .strength-meter {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin-top: 0.25rem;
        }
        .strength-meter div {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
            width: 0;
        }
        .strength-meter.weak div {
            width: 33.33%;
            background: #ef4444;
        }
        .strength-meter.medium div {
            width: 66.66%;
            background: #f59e0b;
        }
        .strength-meter.strong div {
            width: 100%;
            background: #10b981;
        }
        .strength-text {
            color: #94a3b8;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #94a3b8;
        }
        .login-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .login-link a:hover {
            color: #60a5fa;
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
        }
        .alert i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus fa-3x mb-3" style="color: #3498db;"></i>
            <h2>Register Admin Account</h2>
            <p class="text-muted">Create a new administrator account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <i class="fas fa-user input-icon"></i>
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock input-icon"></i>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-meter" id="strengthMeter"></div>
                    <span class="strength-text" id="strengthText"></span>
                </div>
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <i class="fas fa-lock input-icon"></i>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus mr-2"></i>
                Register Account
            </button>

            <a href="dashboard.php" class="login-link">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </form>
    </div>

    <script>
        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (username.length < 3) {
                alert('Username must be at least 3 characters long.');
                return false;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters long.');
                return false;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }

            return true;
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');
            
            // Reset strength indicator
            strengthMeter.className = 'strength-meter';
            strengthMeter.style.width = '0%';
            strengthText.textContent = '';

            if (password.length === 0) {
                return;
            }

            // Check password strength
            let score = 0;
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            // Update strength indicator
            if (score <= 2) {
                strengthMeter.classList.add('weak');
                strengthText.textContent = 'Weak';
            } else if (score === 3) {
                strengthMeter.classList.add('medium');
                strengthMeter.style.width = '66.66%';
                strengthText.textContent = 'Medium';
            } else {
                strengthMeter.classList.add('strong');
                strengthMeter.style.width = '100%';
                strengthText.textContent = 'Strong';
            }
        });

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
