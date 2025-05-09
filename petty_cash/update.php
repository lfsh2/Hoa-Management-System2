<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $details = $_POST['details'];
    $date = $_POST['date'];

    $sql = "INSERT INTO financial_transactions (transaction_type, amount, description, details, transaction_date) 
            VALUES (?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsss", $type, $amount, $description, $details, $date);
    
    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Transaction</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: rgba(15, 23, 42, 0.95);
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 800px;
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
        .form-container {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
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
        .transaction-type-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .transaction-type-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .transaction-type-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }
        .transaction-type-btn.active {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            color: #3b82f6;
        }
        .transaction-type-btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
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
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
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
        .back-link {
            display: inline-flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            color: #e2e8f0;
            text-decoration: none;
        }
        .back-link i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-plus-circle"></i> New Transaction</h2>
                <p class="text-muted">Enter the transaction details below</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="transactionForm" onsubmit="return validateForm()">
                <div class="form-group">
                    <label>Transaction Type</label>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <button type="button" class="transaction-type-btn active income" onclick="setTransactionType('Income')">
                                <i class="fas fa-arrow-up"></i> Income
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="transaction-type-btn" onclick="setTransactionType('Expense')">
                                <i class="fas fa-arrow-down"></i> Expense
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="transactionType" value="Income">
                </div>

                <div class="form-group">
                    <label for="amount">Amount (₱)</label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>

                <div class="form-group">
                    <label for="details">Additional Details</label>
                    <textarea class="form-control" id="details" name="details" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="date">Transaction Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-check mr-2"></i>Save Transaction
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-cancel ml-2">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Transaction type toggle
        function setTransactionType(type) {
            document.getElementById('transactionType').value = type;
            const buttons = document.querySelectorAll('.transaction-type-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active', 'income', 'expense');
            });
            
            const activeButton = type === 'Income' ? buttons[0] : buttons[1];
            activeButton.classList.add('active', type.toLowerCase());
        }

        // Form validation
        function validateForm() {
            const amount = document.getElementById('amount').value;
            const description = document.getElementById('description').value;
            const date = document.getElementById('date').value;

            if (amount <= 0) {
                alert('Please enter a valid amount.');
                return false;
            }

            if (description.trim() === '') {
                alert('Please enter a description.');
                return false;
            }

            if (date === '') {
                alert('Please select a date.');
                return false;
            }

            return true;
        }

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