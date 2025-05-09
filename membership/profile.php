<?php
include '../db.php'; 

$userid = $_GET['userid'];

// Get member details
$sql = "SELECT * FROM users WHERE id = $userid";
$result = $conn->query($sql);
$member = $result->fetch_assoc();

// Get membership status
$membership_query = "SELECT status, funds FROM membership WHERE userid = $userid";
$membership_result = $conn->query($membership_query);
$membership = $membership_result->fetch_assoc();

// Get monthly dues history
$dues_query = "SELECT month_paid, funds FROM monthly_dues WHERE userid = $userid ORDER BY month_paid DESC LIMIT 12";
$dues_result = $conn->query($dues_query);

// Get vehicle stickers
$stickers_query = "SELECT category, fees FROM stickers WHERE userid = $userid";
$stickers_result = $conn->query($stickers_query);

// Get construction bonds
$bonds_query = "SELECT category, funds FROM housebond WHERE userid = $userid";
$bonds_result = $conn->query($bonds_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Profile - <?= $member['firstname'] . ' ' . $member['lastname'] ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: rgba(15, 23, 42, 0.95);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            padding: 20px;
            animation: fadeIn 0.5s ease-out;
        }
        .profile-header {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #2c3e50;
        }
        .location-badge {
            background: #e9ecef;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .payment-history {
            max-height: 300px;
            overflow-y: auto;
        }
        .card-header {
            background: none;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 1rem;
        }
        .card-header h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .vehicle-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .btn-print {
            background-color: #3498db;
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            background-color: #2980b9;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>
                        <i class="fas fa-user-circle mr-2"></i>
                        <?= $member['firstname'] . ' ' . $member['lastname'] ?> <?= $member['mi'] ? $member['mi'] . '.' : '' ?>
                    </h2>
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        Block <?= $member['block'] ?>, Lot <?= $member['lot'] ?>, <?= $member['street'] ?>
                    </div>
                </div>
                <div class="col-md-4 text-md-right">
                    <button class="btn btn-print" onclick="window.print()">
                        <i class="fas fa-print mr-2"></i> Print Profile
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="profile-card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Membership Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-label">Status</div>
                        <span class="status-badge <?= $membership['status'] === 'Paid' ? 'status-paid' : 'status-pending' ?>">
                            <?= $membership['status'] ?? 'Not Registered' ?>
                        </span>
                        <?php if ($membership['funds']): ?>
                            <div class="info-label mt-3">Membership Fee</div>
                            <div class="info-value">₱<?= number_format($membership['funds'], 2) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-card">
                    <div class="card-header">
                        <h5><i class="fas fa-car"></i> Vehicle Stickers</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($stickers_result->num_rows > 0): ?>
                            <?php while($sticker = $stickers_result->fetch_assoc()): ?>
                                <div class="mb-3">
                                    <div class="info-label">
                                        <?php
                                            $icon = 'car';
                                            if (strpos(strtolower($sticker['category']), 'motorcycle') !== false) {
                                                $icon = 'motorcycle';
                                            } elseif (strpos(strtolower($sticker['category']), 'bike') !== false) {
                                                $icon = 'bicycle';
                                            }
                                        ?>
                                        <i class="fas fa-<?= $icon ?> vehicle-icon"></i>
                                        <?= $sticker['category'] ?>
                                    </div>
                                    <div class="info-value">₱<?= number_format($sticker['fees'], 2) ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No vehicle stickers registered</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="profile-card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Monthly Dues History</h5>
                    </div>
                    <div class="card-body payment-history">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($dues_result->num_rows > 0): ?>
                                    <?php while($due = $dues_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('F Y', strtotime($due['month_paid'])) ?></td>
                                            <td>₱<?= number_format($due['funds'], 2) ?></td>
                                            <td>
                                                <span class="status-badge status-paid">Paid</span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No payment history available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="profile-card">
                    <div class="card-header">
                        <h5><i class="fas fa-building"></i> Construction Bonds</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($bonds_result->num_rows > 0): ?>
                            <?php while($bond = $bonds_result->fetch_assoc()): ?>
                                <div class="mb-3">
                                    <div class="info-label"><?= $bond['category'] ?></div>
                                    <div class="info-value">₱<?= number_format($bond['funds'], 2) ?></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No construction bonds registered</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Members List
            </a>
        </div>
    </div>

    <script>
        // Add smooth scrolling to payment history
        document.querySelector('.payment-history').addEventListener('scroll', function() {
            this.classList.add('scrolling');
            clearTimeout(this.scrollTimeout);
            this.scrollTimeout = setTimeout(() => {
                this.classList.remove('scrolling');
            }, 150);
        });
    </script>
</body>
</html>
