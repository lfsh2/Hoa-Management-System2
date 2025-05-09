<?php
include '../db.php'; 

$search = "";
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$sql = "SELECT membership.id, users.firstname, users.lastname, users.id as userid, membership.status, membership.funds 
        FROM membership 
        JOIN users ON membership.userid = users.id
        WHERE (users.firstname LIKE '%$search%' OR users.lastname LIKE '%$search%')";

if ($status_filter) {
    $sql .= " AND membership.status = '$status_filter'";
}

$result = $conn->query($sql);

// Get statistics
$total_members_query = "SELECT COUNT(*) as total FROM membership";
$total_members_result = $conn->query($total_members_query);
$total_members = $total_members_result->fetch_assoc()['total'];

$paid_members_query = "SELECT COUNT(*) as total FROM membership WHERE status = 'Paid'";
$paid_members_result = $conn->query($paid_members_query);
$paid_members = $paid_members_result->fetch_assoc()['total'];

$pending_members_query = "SELECT COUNT(*) as total FROM membership WHERE status = 'To be Reviewed'";
$pending_members_result = $conn->query($pending_members_query);
$pending_members = $pending_members_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image.png">

    <title>Membership Management</title>
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
        .stat-card.paid {
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
                    <h6><i class="fas fa-users"></i> Total Members</h6>
                    <h3><?= $total_members ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card paid">
                    <h6><i class="fas fa-check-circle"></i> Paid Members</h6>
                    <h3><?= $paid_members ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card pending">
                    <h6><i class="fas fa-clock"></i> Pending Review</h6>
                    <h3><?= $pending_members ?></h3>
                </div>
            </div>
        </div>

        <div class="filter-buttons">
            <a href="?status=" class="btn <?= !$status_filter ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
            <a href="?status=Paid" class="btn <?= $status_filter === 'Paid' ? 'btn-success' : 'btn-outline-success' ?>">Paid</a>
            <a href="?status=To be Reviewed" class="btn <?= $status_filter === 'To be Reviewed' ? 'btn-warning' : 'btn-outline-warning' ?>">To be Reviewed</a>
            <a href="create.php" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> Add New Member
            </a>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
        </div>

        <div class="table-container">
            <table class="table table-hover" id="membersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="profile.php?userid=<?= $row['userid'] ?>" class="profile-link">
                                    <?= $row['firstname'] . ' ' . $row['lastname'] ?>
                                </a>
                            </td>
                            <td>
                                <span class="status-badge <?= $row['status'] === 'Paid' ? 'status-paid' : 'status-pending' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="update_status.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <?php if ($row['status'] == 'To be Reviewed'): ?>
                                        <button type="submit" name="status" value="Paid" class="btn btn-success btn-sm btn-action" title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="status" value="To be Reviewed" class="btn btn-warning btn-sm btn-action" title="Mark as Pending">
                                            <i class="fas fa-clock"></i>
                                        </button>
                                    <?php endif; ?>
                                </form>
                                <a href="profile.php?userid=<?= $row['userid'] ?>" class="btn btn-info btn-sm btn-action" title="View Profile">
                                    <i class="fas fa-user"></i>
                                </a>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-action" title="Delete Member" onclick="return confirm('Are you sure you want to delete this member?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Search functionality with enhanced interaction
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('membersTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const name = rows[i].cells[0].textContent.toLowerCase();
                rows[i].style.display = name.includes(searchValue) ? '' : 'none';
                if (name.includes(searchValue)) {
                    rows[i].style.animation = 'fadeIn 0.3s ease-out';
                }
            }
        });

        // Add smooth transitions for status updates
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
    </script>
</body>
</html>
