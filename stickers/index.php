<?php
include '../db.php';

// Get statistics
$total_stickers_query = "SELECT COUNT(*) as total FROM stickers";
$total_stickers_result = $conn->query($total_stickers_query);
$total_stickers = $total_stickers_result->fetch_assoc()['total'];

$car_stickers_query = "SELECT COUNT(*) as total FROM stickers WHERE category = 'Car'";
$car_stickers_result = $conn->query($car_stickers_query);
$car_stickers = $car_stickers_result->fetch_assoc()['total'];

$motorcycle_stickers_query = "SELECT COUNT(*) as total FROM stickers WHERE category = 'Motorcycle'";
$motorcycle_stickers_result = $conn->query($motorcycle_stickers_query);
$motorcycle_stickers = $motorcycle_stickers_result->fetch_assoc()['total'];

$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT s.id, s.userid, u.firstname, u.lastname, s.category, s.fees 
        FROM stickers s 
        JOIN users u ON s.userid = u.id
        WHERE 1=1";

if ($filter_category) {
    $sql .= " AND s.category = '$filter_category'";
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
    <title>Vehicle Stickers Management</title>
    <link rel="icon" type="image/x-icon" href="../image.png">
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
        .stat-card.issued {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(6, 95, 70, 0.1));
            border-color: rgba(16, 185, 129, 0.2);
        }
        .stat-card.pending {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(146, 64, 14, 0.1));
            border-color: rgba(245, 158, 11, 0.2);
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
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .status-issued {
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
                    <h6><i class="fas fa-ticket-alt"></i> Total Stickers</h6>
                    <h3><?= $total_stickers ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card cars">
                    <h6><i class="fas fa-car"></i> Car Stickers</h6>
                    <h3><?= $car_stickers ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card motorcycles">
                    <h6><i class="fas fa-motorcycle"></i> Motorcycle Stickers</h6>
                    <h3><?= $motorcycle_stickers ?></h3>
                </div>
            </div>
        </div>

        <div class="filter-buttons">
            <a href="?category=" class="btn <?= !$filter_category ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
            <a href="?category=Car" class="btn <?= $filter_category === 'Car' ? 'btn-success' : 'btn-outline-success' ?>">Cars</a>
            <a href="?category=Motorcycle" class="btn <?= $filter_category === 'Motorcycle' ? 'btn-danger' : 'btn-outline-danger' ?>">Motorcycles</a>
            <a href="?category=E-bike" class="btn <?= $filter_category === 'E-bike' ? 'btn-info' : 'btn-outline-info' ?>">E-bikes</a>
            <a href="create.php" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> Add New Sticker
            </a>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search by owner name...">
        </div>

        <div class="table-container">
            <table class="table table-hover" id="stickersTable">
                <thead>
                    <tr>
                        <th>Owner Name</th>
                        <th>Vehicle Type</th>
                        <th>Fees</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['firstname'] . ' ' . $row['lastname'] ?></td>
                                <td>
                                    <span class="category-badge category-<?= strtolower($row['category']) ?>">
                                        <?= $row['category'] ?>
                                    </span>
                                </td>
                                <td class="fees">₱<?= number_format($row['fees'], 2) ?></td>
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
                                <p class="mb-0">No vehicle stickers found</p>
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
            const table = document.getElementById('stickersTable');
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
            if (confirm('Are you sure you want to delete this sticker record?')) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>
