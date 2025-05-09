<?php
include 'db.php'; 

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$membership_query = "SELECT SUM(funds) AS total_paid FROM membership WHERE status = 'Paid'";
$membership_result = $conn->query($membership_query);
$membership_data = $membership_result->fetch_assoc();
$total_paid_membership = $membership_data['total_paid'] ?? 0;

$monthly_dues_query = "SELECT DATE_FORMAT(month_paid, '%M %Y') AS month, SUM(funds) AS total_dues FROM monthly_dues GROUP BY DATE_FORMAT(month_paid, '%M %Y')";
$monthly_dues_result = $conn->query($monthly_dues_query);

$stickers_query = "SELECT category, COUNT(*) AS count FROM stickers GROUP BY category";
$stickers_result = $conn->query($stickers_query);

$homeowners_query = "SELECT COUNT(*) as total FROM users";
$homeowners_result = $conn->query($homeowners_query);
$homeowners_data = $homeowners_result->fetch_assoc();
$total_homeowners = $homeowners_data['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link rel="icon" type="image/x-icon" href="image.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgba(15, 23, 42, 0.95);
            padding: 20px;
            color: #e2e8f0;
        }
        .dashboard-header {
            margin-bottom: 2rem;
            animation: fadeIn 0.5s ease-out;
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dashboard-header h2 {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .dashboard-header p {
            color: #94a3b8;
            margin: 0;
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            animation: slideUp 0.5s ease-out;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .stats-card::before {
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
        .stats-card:hover::before {
            transform: translateX(100%);
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        .chart-container:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0.5rem 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .stat-label {
            color: #94a3b8;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }
        .stat-icon {
            float: right;
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }
        .stats-card:hover .stat-icon {
            transform: scale(1.1);
            color: rgba(255, 255, 255, 0.25);
        }
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .theme-toggle:hover {
            transform: rotate(180deg);
            background: rgba(255, 255, 255, 0.15);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .loading:after {
            content: '';
            display: block;
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
    </button>

    <div class="container">
        <div class="dashboard-header">
            <h2><i class="fas fa-chart-line"></i> Analytics Dashboard</h2>
            <p class="text-muted">Real-time overview of HOA operations</p>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-label">Total Homeowners</div>
                    <div class="stat-value" data-target="<?= $total_homeowners ?>">0</div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" style="width: 100%; background-color: #3498db;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-money-bill-wave stat-icon"></i>
                    <div class="stat-label">Total Membership Fees</div>
                    <div class="stat-value" data-target="<?= $total_paid_membership ?>">₱0</div>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar" style="width: 100%; background-color: #2ecc71;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="stats-card">
                    <h5><i class="fas fa-chart-line"></i> Monthly Dues Trend</h5>
                    <div class="chart-container">
                        <div class="loading"></div>
                        <canvas id="monthlyDuesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stats-card">
                    <h5><i class="fas fa-car"></i> Vehicle Stickers Distribution</h5>
                    <div class="chart-container">
                        <div class="loading"></div>
                        <canvas id="stickersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Animate numbers with counting effect
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const current = Math.floor(progress * (end - start) + start);
                element.innerHTML = '₱' + current.toLocaleString();
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Add hover effects to stats cards
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });

        // Animate stats on load
        document.querySelectorAll('.stat-value').forEach(element => {
            const target = parseInt(element.dataset.target);
            animateValue(element, 0, target, 2000);
        });

        // Monthly Dues Chart with enhanced interactivity
        const monthlyDuesCtx = document.getElementById('monthlyDuesChart').getContext('2d');
        new Chart(monthlyDuesCtx, {
            type: 'line',
            data: {
                labels: [<?php while($row = $monthly_dues_result->fetch_assoc()) { echo "'" . $row['month'] . "',"; } ?>],
                datasets: [{
                    label: 'Monthly Dues',
                    data: [<?php $monthly_dues_result->data_seek(0); while($row = $monthly_dues_result->fetch_assoc()) { echo $row['total_dues'] . ','; } ?>],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#60a5fa',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Stickers Chart with enhanced interactivity
        const stickersCtx = document.getElementById('stickersChart').getContext('2d');
        new Chart(stickersCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php while($row = $stickers_result->fetch_assoc()) { echo "'" . $row['category'] . "',"; } ?>],
                datasets: [{
                    data: [<?php $stickers_result->data_seek(0); while($row = $stickers_result->fetch_assoc()) { echo $row['count'] . ','; } ?>],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#94a3b8',
                            font: {
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

        // Remove loading animations
        document.querySelectorAll('.loading').forEach(loader => {
            loader.style.display = 'none';
        });

        // Theme toggling with smooth transition
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const icon = document.querySelector('.theme-toggle i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
        }
    </script>
</body>
</html>
