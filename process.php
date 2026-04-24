<?php
session_start();

$salary = floatval($_POST["salary"] ?? 0);
$cutoff = $_POST["cutoff"] ?? "";
$expenses = [];
$totalExpenses = 0;

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Budget Result</title>
</head>
<body>

<h2>Summary (<?php echo $cutoff; ?> Cut-off)</h2>

<p>Salary: ₱<?php echo number_format($salary, 2); ?></p>
<p>Total Expenses: ₱<?php echo number_format($totalExpenses, 2); ?></p>
<p>Balance: ₱<?php echo number_format($balance, 2); ?></p>

<?php if ($balance < 0): ?>
    <p style="color:red;">⚠️ Over Budget</p>
<?php elseif ($balance < ($salary * 0.2)): ?>
    <p style="color:orange;">⚠️ Low Balance</p>
<?php else: ?>
    <p style="color:green;">✅ Good Budget</p>
<?php endif; ?>

<h3>Expenses:</h3>
<ul>
<?php foreach ($expenses as $exp): ?>
    <li><?php echo $exp["name"]; ?> - ₱<?php echo number_format($exp["amount"], 2); ?></li>
<?php endforeach; ?>
</ul>

<br>
<a href="index.html">← Back</a>

</body>
</html>