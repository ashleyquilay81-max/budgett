<?php
session_start();

$salary = 0;
$totalExpenses = 0;
$expenses = [];
$cutoff = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $salary = floatval($_POST["salary"] ?? 0);
    $cutoff = $_POST["cutoff"] ?? "";

    if (!empty($_POST["expense_name"])) {
        foreach ($_POST["expense_name"] as $key => $name) {
            $amount = floatval($_POST["expense_amount"][$key] ?? 0);
            if (!empty($name) && $amount > 0) {
                $expenses[] = [
                    "name" => $name,
                    "amount" => $amount
                ];
                $totalExpenses += $amount;
            }
        }
    }

    $balance = $salary - $totalExpenses;

    // Store result temporarily
    $_SESSION["result"] = [
        "salary" => $salary,
        "cutoff" => $cutoff,
        "expenses" => $expenses,
        "totalExpenses" => $totalExpenses,
        "balance" => $balance
    ];

    // Redirect to clear POST data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get result then clear session
$result = $_SESSION["result"] ?? null;
unset($_SESSION["result"]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cut-off Budgeting System</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        input, select {
            padding: 8px;
            margin: 5px 0;
            width: 100%;
        }
        button {
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .expense-row {
            display: flex;
            gap: 10px;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            background: #e9ecef;
        }
        .warning {
            color: red;
            font-weight: bold;
        }
        .good {
            color: green;
            font-weight: bold;
        }
        .live-total {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>

    <script>
        function addExpense() {
            let container = document.getElementById("expenses");
            let div = document.createElement("div");
            div.classList.add("expense-row");

            div.innerHTML = `
                <input type="text" name="expense_name[]" placeholder="Expense Name" required>
                <input type="number" step="0.01" name="expense_amount[]" placeholder="Amount" oninput="calculateTotal()" required>
            `;

            container.appendChild(div);
        }

        function calculateTotal() {
            let amounts = document.getElementsByName("expense_amount[]");
            let total = 0;

            amounts.forEach(input => {
                let value = parseFloat(input.value) || 0;
                total += value;
            });

            document.getElementById("liveTotal").innerText = total.toFixed(2);
        }
    </script>
</head>
<body>

<div class="container">
    <h2>💰 Cut-off Budgeting System</h2>

    <form method="POST">
        <label>Cut-off:</label>
        <select name="cutoff" required>
            <option value="">Select Cut-off</option>
            <option value="10">10th Cut-off</option>
            <option value="25">25th Cut-off</option>
        </select>

        <label>Salary for this Cut-off:</label>
        <input type="number" step="0.01" name="salary" required>

        <h3>Expenses</h3>
        <div id="expenses">
            <div class="expense-row">
                <input type="text" name="expense_name[]" placeholder="Expense Name" required>
                <input type="number" step="0.01" name="expense_amount[]" placeholder="Amount" oninput="calculateTotal()" required>
            </div>
        </div>

        <button type="button" onclick="addExpense()">+ Add Expense</button>

        <div class="live-total">
            Live Total Expenses: ₱<span id="liveTotal">0.00</span>
        </div>

        <button type="submit">Calculate</button>
    </form>

    <?php if ($result): ?>
        <div class="result">
            <h3>Summary (<?php echo $result["cutoff"]; ?> Cut-off)</h3>

            <p><strong>Salary:</strong> ₱<?php echo number_format($result["salary"], 2); ?></p>
            <p><strong>Total Expenses:</strong> ₱<?php echo number_format($result["totalExpenses"], 2); ?></p>
            <p><strong>Remaining Balance:</strong> ₱<?php echo number_format($result["balance"], 2); ?></p>

            <?php if ($result["balance"] < 0): ?>
                <p class="warning">⚠️ Warning: You are OVER BUDGET!</p>
            <?php elseif ($result["balance"] < ($result["salary"] * 0.2)): ?>
                <p class="warning">⚠️ Warning: Low remaining balance!</p>
            <?php else: ?>
                <p class="good">✅ Good job! You're within budget.</p>
            <?php endif; ?>

            <h4>Breakdown:</h4>
            <ul>
                <?php foreach ($result["expenses"] as $exp): ?>
                    <li><?php echo $exp["name"]; ?> - ₱<?php echo number_format($exp["amount"], 2); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

</div>

</body>
</html>