<?php
include '../db.php'; 

$filter_month = '';
if (isset($_GET['filter_month']) && !empty($_GET['filter_month'])) {
    $filter_month = $_GET['filter_month'];
    $sql = "SELECT md.id, u.firstname, u.lastname, md.funds, md.month_paid 
            FROM monthly_dues md 
            JOIN users u ON md.userid = u.id
            WHERE DATE_FORMAT(md.month_paid, '%Y-%m') = '$filter_month'";
} else {
    $sql = "SELECT md.id, u.firstname, u.lastname, md.funds, md.month_paid 
            FROM monthly_dues md 
            JOIN users u ON md.userid = u.id";
}
$result = $conn->query($sql);

// Get statistics
$total_dues_query = "SELECT SUM(funds) as total FROM monthly_dues";
$total_dues_result = $conn->query($total_dues_query);
$total_dues = $total_dues_result->fetch_assoc()['total'] ?? 0;

$paid_members_query = "SELECT COUNT(DISTINCT userid) as count FROM monthly_dues WHERE month_paid = CURRENT_DATE";
$paid_members_result = $conn->query($paid_members_query);
$paid_members = $paid_members_result->fetch_assoc()['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image.png">

    <title>Manage Monthly Dues</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: rgba(15, 23, 42, 0.95);
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .page-header h2 {
            color: #e2e8f0;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .stat-icon {
            font-size: 2rem;
            color: #3b82f6;
            margin-bottom: 1rem;
        }
        .search-box {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .table-container {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
            color: #e2e8f0;
        }
        .table th {
            background: rgba(15, 23, 42, 0.95);
            color: #94a3b8;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
        }
        .table td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            vertical-align: middle;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .status-badge.paid {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        .status-badge.unpaid {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        .status-badge i {
            margin-right: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        .btn-action {
            padding: 0.5rem;
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
        }
        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        .alert-success {
            border-color: rgba(16, 185, 129, 0.2);
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">₱<?= number_format($total_dues, 2) ?></div>
                <div class="stat-label">Total Dues Collected</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $paid_members ?></div>
                <div class="stat-label">Paid Members (This Month)</div>
            </div>
        </div>

        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name...">
                    </div>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="mb-0">
                        <div class="input-group">
                            <input type="month" name="filter_month" id="filter_month" class="form-control" value="<?= $filter_month ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 text-right">
                    <a href="create.php" class="btn btn-success btn-block">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover" id="duesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Month Paid</th>
                        <th>Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['firstname'] . ' ' . $row['lastname'] ?></td>
                                <td><?= date('F Y', strtotime($row['month_paid'])) ?></td>
                                <td>₱<?= number_format($row['funds'], 2) ?></td>
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
                                <p class="mb-0">No monthly dues found for the selected period</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('duesTable');
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
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>
