<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOA Management System</title>
    <link rel="icon" type="image/x-icon" href="image.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #0f1729;
            --secondary-color: #1e293b;
            --accent-color: #3b82f6;
            --accent-hover: #2563eb;
            --danger-color: #ef4444;
            --text-light: #f1f5f9;
            --text-dark: #94a3b8;
            --transition-speed: 0.3s;
            --glass-bg: rgba(15, 23, 42, 0.85);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow: hidden;
            height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e40af 100%);
            color: var(--text-light);
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-right: 1px solid var(--glass-border);
            height: 100vh;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.3);
        }

        .brand-section {
            padding: 2rem 1.5rem;
            background: rgba(15, 23, 42, 0.98);
            text-align: center;
            border-bottom: 1px solid var(--glass-border);
            backdrop-filter: blur(8px);
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        .nav-section {
            color: var(--text-dark);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.5rem 1.5rem 0.5rem;
            font-weight: 600;
        }

        .nav-item {
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all var(--transition-speed);
            border-left: 3px solid transparent;
            margin: 0.2rem 0.5rem;
            border-radius: 0.5rem;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-dark);
        }

        .nav-item:hover {
            background: rgba(59, 130, 246, 0.15);
            border-left: 3px solid var(--accent-color);
            transform: translateX(3px);
            color: var(--text-light);
        }

        .nav-item.active {
            background: rgba(59, 130, 246, 0.2);
            border-left: 3px solid var(--accent-color);
            color: var(--text-light);
            font-weight: 500;
        }

        .nav-item i {
            font-size: 1.1rem;
            min-width: 1.5rem;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .nav-item:hover i {
            transform: translateX(2px);
        }

        /* Content Area Styles */
        .content-area {
            flex: 1;
            height: 100vh;
            overflow: hidden;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            position: relative;
        }

        .content-header {
            padding: 1rem 2rem;
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            height: 70px;
        }

        .toggle-sidebar {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: var(--text-light);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all var(--transition-speed);
            backdrop-filter: blur(4px);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-sidebar:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: scale(1.05);
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-light);
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(59, 130, 246, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all var(--transition-speed);
        }

        .user-menu:hover {
            background: rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-light);
            margin: 0;
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-dark);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: calc(100% - 2rem);
            padding: 0.8rem;
            background: rgba(239, 68, 68, 0.15);
            color: var(--text-light);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all var(--transition-speed);
            backdrop-filter: blur(4px);
            text-decoration: none;
            margin: 1rem;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(239, 68, 68, 0.2);
            text-decoration: none;
            color: white;
        }

        /* iframe container styles */
        .iframe-container {
            height: calc(100vh - 70px);
            width: 100%;
            position: relative;
            background: rgba(15, 23, 42, 0.95);
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: rgba(15, 23, 42, 0.95);
        }

        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(59, 130, 246, 0.2);
            border-radius: 50%;
            border-top-color: var(--accent-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                z-index: 1000;
            }

            .sidebar.expanded {
                left: 0;
            }

            .content-area {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand-section">
            <div class="brand-logo">
                <i class="fas fa-home"></i>
            </div>
            <h1 class="brand-name">PAGSIBOL HOA</h1>
            <p class="brand-description">Management System</p>
        </div>

        <ul class="nav-menu">
            <div class="nav-section">Dashboard</div>
            <li class="nav-item active" onclick="loadPage('analystic.php', this, 'Analytics')">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </li>

            <div class="nav-section">Financial Management</div>
            <li class="nav-item" onclick="loadPage('monthly_dues/index.php', this, 'Monthly Dues')">
                <i class="fas fa-money-bill-wave"></i>
                <span>Monthly Dues</span>
            </li>
            <li class="nav-item" onclick="loadPage('house_bond/index.php', this, 'House Bond')">
                <i class="fas fa-building"></i>
                <span>House Bond</span>
            </li>
            <li class="nav-item" onclick="loadPage('petty_cash/index.php', this, 'Petty Cash')">
                <i class="fas fa-wallet"></i>
                <span>Petty Cash</span>
            </li>

            <div class="nav-section">Member Management</div>
            <li class="nav-item" onclick="loadPage('stickers/index.php', this, 'Stickers')">
                <i class="fas fa-ticket-alt"></i>
                <span>Stickers</span>
            </li>
            <li class="nav-item" onclick="loadPage('membership/index.php', this, 'Membership')">
                <i class="fas fa-users"></i>
                <span>Membership</span>
            </li>
            <li class="nav-item" onclick="loadPage('members/index.php', this, 'Members List')">
                <i class="fas fa-address-card"></i>
                <span>Members List</span>
            </li>
        </ul>

        <div class="logout-section">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar mr-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title" id="pageTitle">Analytics</h1>
            </div>
            <div class="user-menu">
                <div class="user-info">
                    <p class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p class="user-role">Administrator</p>
                </div>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=3498db&color=fff" 
                     alt="User Avatar" 
                     style="width: 40px; height: 40px; border-radius: 50%;">
            </div>
        </div>

        <div class="iframe-container">
            <iframe id="contentFrame" name="contentFrame" src="analystic.php"></iframe>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
        </div>
    </div>

    <script>
        function loadPage(url, element, title) {
            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Update active state
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            element.classList.add('active');

            // Update page title
            document.getElementById('pageTitle').textContent = title;

            // Load content in iframe
            const iframe = document.getElementById('contentFrame');
            iframe.src = url;

            // Hide loading overlay when iframe loads
            iframe.onload = function() {
                document.getElementById('loadingOverlay').style.display = 'none';
            };

            // On mobile, collapse sidebar after selection
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('expanded');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('expanded');
        }

        // Handle iframe load errors
        document.getElementById('contentFrame').onerror = function() {
            document.getElementById('loadingOverlay').style.display = 'none';
            alert('Error loading page. Please try again.');
        };

        // Initialize responsive sidebar state
        function initResponsiveSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('expanded');
            } else {
                sidebar.classList.remove('expanded');
            }
        }

        // Listen for window resize
        window.addEventListener('resize', initResponsiveSidebar);

        // Initial setup
        initResponsiveSidebar();
    </script>
</body>
</html>