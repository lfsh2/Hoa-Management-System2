<?php
include '../db.php'; 

$search = isset($_GET['search']) ? $_GET['search'] : '';
$street_filter = isset($_GET['street']) ? $_GET['street'] : '';

$total_query = "SELECT COUNT(*) as total FROM users";
$total_result = $conn->query($total_query);
$total_homeowners = $total_result->fetch_assoc()['total'];

$streets_query = "SELECT DISTINCT street FROM users ORDER BY street";
$streets_result = $conn->query($streets_query);

$sql = "SELECT id, firstname, lastname, mi, block, lot, street FROM users WHERE 1=1";

if ($search) {
    $sql .= " AND (firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR block LIKE '%$search%' OR lot LIKE '%$search%')";
}

if ($street_filter) {
    $sql .= " AND street = '$street_filter'";
}

$sql .= " ORDER BY lastname, firstname";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image.png">
    <title>Members List</title>
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
        .name-link {
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .name-link:hover {
            color: #60a5fa;
            text-decoration: none;
        }
        .location-info {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .location-info i {
            margin-right: 5px;
        }
        .street-badge {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
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
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $total_homeowners ?></div>
                <div class="stat-label">Total Homeowners</div>
            </div>
        </div>

        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name, block, or lot...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-control" id="streetFilter" onchange="filterByStreet(this.value)">
                        <option value="">All Streets</option>
                        <?php while($street = $streets_result->fetch_assoc()): ?>
                            <option value="<?= $street['street'] ?>" <?= $street_filter === $street['street'] ? 'selected' : '' ?>>
                                <?= $street['street'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 text-right">
                    <a href="create.php" class="btn btn-success btn-block">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="table table-hover" id="homeownersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Street</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <a href="../membership/profile.php?userid=<?= $row['id'] ?>" class="name-link">
                                        <?= $row['lastname'] . ', ' . $row['firstname'] ?> <?= $row['mi'] ? $row['mi'] . '.' : '' ?>
                                    </a>
                                </td>
                                <td class="location-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Block <?= $row['block'] ?>, Lot <?= $row['lot'] ?>
                                </td>
                                <td>
                                    <span class="street-badge">
                                        <?= $row['street'] ?>
                                    </span>
                                </td>
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
                                <p class="mb-0">No homeowners found</p>
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
            const table = document.getElementById('homeownersTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const name = rows[i].cells[0].textContent.toLowerCase();
                const location = rows[i].cells[1].textContent.toLowerCase();
                rows[i].style.display = name.includes(searchValue) || location.includes(searchValue) ? '' : 'none';
                if (name.includes(searchValue) || location.includes(searchValue)) {
                    rows[i].style.animation = 'fadeIn 0.3s ease-out';
                }
            }
        });

        // Street filter functionality
        function filterByStreet(street) {
            window.location.href = '?street=' + street;
        }

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
            if (confirm('Are you sure you want to delete this homeowner?')) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>
