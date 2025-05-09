<?php
include '../db.php';

// Get statistics
$total_balance_query = "SELECT SUM(CASE WHEN transaction_type = 'Income' THEN amount ELSE -amount END) as balance FROM financial_transactions";
$total_balance_result = $conn->query($total_balance_query);
$total_balance = $total_balance_result->fetch_assoc()['balance'] ?? 0;

$income_query = "SELECT SUM(amount) as total FROM financial_transactions WHERE transaction_type = 'Income'";
$income_result = $conn->query($income_query);
$total_income = $income_result->fetch_assoc()['total'] ?? 0;

$expense_query = "SELECT SUM(amount) as total FROM financial_transactions WHERE transaction_type = 'Expense'";
$expense_result = $conn->query($expense_query);
$total_expenses = $expense_result->fetch_assoc()['total'] ?? 0;

$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM financial_transactions WHERE 1=1";

if ($filter_type) {
    $sql .= " AND transaction_type = '$filter_type'";
}

if ($filter_date) {
    $sql .= " AND DATE(transaction_date) = '$filter_date'";
}

if ($search) {
    $sql .= " AND (description LIKE '%$search%' OR details LIKE '%$search%')";
}

$sql .= " ORDER BY transaction_date DESC, id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image.png">
    <title>Petty Cash Management</title>
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
        .type-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .type-badge.income {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        .type-badge.expense {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        .type-badge i {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="stats-container row">
            <div class="col-md-4">
                <div class="stat-card balance">
                    <h6><i class="fas fa-wallet"></i> Current Balance</h6>
                    <h3>₱<?= number_format($total_balance, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card income">
                    <h6><i class="fas fa-arrow-up"></i> Total Income</h6>
                    <h3>₱<?= number_format($total_income, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card expense">
                    <h6><i class="fas fa-arrow-down"></i> Total Expenses</h6>
                    <h3>₱<?= number_format($total_expenses, 2) ?></h3>
                </div>
            </div>
        </div>

        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search transactions...">
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="date" class="form-control" id="dateFilter" onchange="filterByDate(this.value)">
                </div>
                <div class="col-md-4">
                    <div class="filter-buttons">
                        <a href="?type=" class="btn <?= !$filter_type ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
                        <a href="?type=Income" class="btn <?= $filter_type === 'Income' ? 'btn-success' : 'btn-outline-success' ?>">Income</a>
                        <a href="?type=Expense" class="btn <?= $filter_type === 'Expense' ? 'btn-danger' : 'btn-outline-danger' ?>">Expenses</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="transaction-container">
            <table class="table table-hover" id="transactionsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="transaction-date">
                                    <?= date('M d, Y', strtotime($row['transaction_date'])) ?>
                                </td>
                                <td>
                                    <div><?= $row['description'] ?></div>
                                    <?php if ($row['details']): ?>
                                        <small class="text-muted"><?= $row['details'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="transaction-type type-<?= strtolower($row['transaction_type']) ?>">
                                        <?= $row['transaction_type'] ?>
                                    </span>
                                </td>
                                <td class="amount <?= $row['transaction_type'] === 'Income' ? 'positive' : 'negative' ?>">
                                    <?= $row['transaction_type'] === 'Income' ? '+' : '-' ?>₱<?= number_format($row['amount'], 2) ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-info btn-sm btn-action" onclick="viewDetails(<?= $row['id'] ?>)" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" onclick="editTransaction(<?= $row['id'] ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-action" onclick="confirmDelete(<?= $row['id'] ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p class="mb-0">No transactions found</p>
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
            const table = document.getElementById('transactionsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const description = rows[i].cells[1].textContent.toLowerCase();
                rows[i].style.display = description.includes(searchValue) ? '' : 'none';
                if (description.includes(searchValue)) {
                    rows[i].style.animation = 'fadeIn 0.3s ease-out';
                }
            }
        });

        // Date filter functionality
        function filterByDate(date) {
            window.location.href = '?date=' + date;
        }

        // Initialize date filter with current value
        document.getElementById('dateFilter').value = '<?= $filter_date ?>';

        // Transaction actions
        function viewDetails(id) {
            // Implement view details functionality
            alert('View details for transaction ' + id);
        }

        function editTransaction(id) {
            window.location.href = 'edit.php?id=' + id;
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this transaction?')) {
                window.location.href = 'delete.php?id=' + id;
            }
        }

        // Add smooth transitions for type badges
        const typeBadges = document.querySelectorAll('.type-badge');
        typeBadges.forEach(badge => {
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