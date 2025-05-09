<?php
include '../db.php'; 

// Get statistics
$total_bonds_query = "SELECT SUM(funds) as total FROM housebond";
$total_bonds_result = $conn->query($total_bonds_query);
$total_bonds = $total_bonds_result->fetch_assoc()['total'] ?? 0;

$internal_bonds_query = "SELECT COUNT(*) as count, SUM(funds) as total FROM housebond WHERE category = 'Internal Construction Bond'";
$internal_bonds_result = $conn->query($internal_bonds_query);
$internal_bonds = $internal_bonds_result->fetch_assoc();

$external_bonds_query = "SELECT COUNT(*) as count, SUM(funds) as total FROM housebond WHERE category = 'External Construction Bond'";
$external_bonds_result = $conn->query($external_bonds_query);
$external_bonds = $external_bonds_result->fetch_assoc();

$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT h.id, h.userid, u.firstname, u.lastname, h.category, h.funds 
        FROM housebond h 
        JOIN users u ON h.userid = u.id
        WHERE 1=1";

if ($filter_category) {
    $sql .= " AND h.category = '$filter_category'";
}

if ($search) {
    $sql .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%')";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image.png">
    <title>Construction Bonds Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: rgba(15, 23, 42, 0.95);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #e2e8f0;
        }
        .container {
            padding: 20px;
            animation: fadeIn 0.5s ease-out;
        }
        .stats-container {
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        .stat-card:hover::before {
            transform: translateX(100%);
        }
        .stat-card.total {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(30, 41, 59, 0.1));
            border-color: rgba(59, 130, 246, 0.2);
        }
        .stat-card.internal {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }
        .stat-card.external {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-box input {
            padding-left: 35px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            transition: all 0.3s ease;
        }
        .search-box input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            color: #ffffff;
        }
        .search-box i {
            position: absolute;
            left: 12px;
            top: 12px;
            color: #94a3b8;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .table {
            color: #e2e8f0;
        }
        .table thead th {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }
        .table td {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }
        .category-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .category-internal {
            background-color: #d4edda;
            color: #155724;
        }
        .category-external {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .status-paid {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .status-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .btn-action.btn-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        .btn-action.btn-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }
        .btn-action.btn-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        .filter-buttons {
            margin-bottom: 20px;
        }
        .filter-buttons .btn {
            margin-right: 10px;
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }
        .filter-buttons .btn:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .filter-buttons .btn-primary {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }
        .filter-buttons .btn-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }
        .filter-buttons .btn-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }
        .profile-link {
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .profile-link:hover {
            color: #60a5fa;
            text-decoration: none;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="stats-container row">
            <div class="col-md-4">
                <div class="stat-card total">
                    <h6><i class="fas fa-coins"></i> Total Bonds</h6>
                    <h3>₱<?= number_format($total_bonds, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card internal">
                    <h6><i class="fas fa-home"></i> Internal Bonds</h6>
                    <h3><?= $internal_bonds['count'] ?></h3>
                    <small>Total: ₱<?= number_format($internal_bonds['total'] ?? 0, 2) ?></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card external">
                    <h6><i class="fas fa-building"></i> External Bonds</h6>
                    <h3><?= $external_bonds['count'] ?></h3>
                    <small>Total: ₱<?= number_format($external_bonds['total'] ?? 0, 2) ?></small>
                </div>
            </div>
        </div>

        <div class="filter-buttons">
            <a href="?category=" class="btn <?= !$filter_category ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
            <a href="?category=Internal Construction Bond" class="btn <?= $filter_category === 'Internal Construction Bond' ? 'btn-success' : 'btn-outline-success' ?>">Internal</a>
            <a href="?category=External Construction Bond" class="btn <?= $filter_category === 'External Construction Bond' ? 'btn-danger' : 'btn-outline-danger' ?>">External</a>
            <a href="create.php" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> Add New Bond
            </a>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search by homeowner name...">
        </div>

        <div class="table-container">
            <table class="table table-hover" id="bondsTable">
                <thead>
                    <tr>
                        <th>Homeowner</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <a href="../membership/profile.php?userid=<?= $row['userid'] ?>" class="name-link">
                                        <?= $row['firstname'] . ' ' . $row['lastname'] ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="category-badge category-<?= strpos($row['category'], 'Internal') !== false ? 'internal' : 'external' ?>">
                                        <?= str_replace(' Construction Bond', '', $row['category']) ?>
                                    </span>
                                </td>
                                <td class="funds">₱<?= number_format($row['funds'], 2) ?></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm btn-action" onclick="confirmDelete(<?= $row['id'] ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p class="mb-0">No construction bonds found</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Search functionality with enhanced interaction
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('bondsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const name = rows[i].cells[0].textContent.toLowerCase();
                rows[i].style.display = name.includes(searchValue) ? '' : 'none';
                if (name.includes(searchValue)) {
                    rows[i].style.animation = 'fadeIn 0.3s ease-out';
                }
            }
        });

        // Add smooth transitions for status badges
        const statusBadges = document.querySelectorAll('.status-badge');
        statusBadges.forEach(badge => {
            badge.style.transition = 'all 0.3s ease';
        });

        // Add row highlight effect with smooth transition
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseover', () => {
                row.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                row.style.transition = 'background-color 0.3s ease';
            });
            row.addEventListener('mouseout', () => {
                row.style.backgroundColor = '';
            });
        });

        // Add click effect to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', () => {
                card.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    card.style.transform = '';
                }, 200);
            });
        });

        // Delete confirmation
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this construction bond record?')) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>
