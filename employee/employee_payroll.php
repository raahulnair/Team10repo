<?php
// employee_payroll.php - show logged-in employee's payroll

session_start();
require_once "db.php";

// Assumes you store logged in email in $_SESSION['email']
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo "Not logged in.";
    exit;
}

$email = $_SESSION['email'];

// Get empid for this email (only employees)
$stmt = $mysqli->prepare(
    "SELECT empid FROM users WHERE email = ? AND role = 'employee'"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($empid);
$stmt->fetch();
$stmt->close();

if (!$empid) {
    http_response_code(403);
    echo "You are not linked to an employee record.";
    exit;
}

// Get payroll rows for this empid
$stmt = $mysqli->prepare(
    "SELECT period_month,
            gross_pay,
            taxes_withheld,
            deductions,
            net_pay,
            issued_at,
            notes
     FROM payroll
     WHERE empid = ?
     ORDER BY period_month DESC"
);
$stmt->bind_param("i", $empid);
$stmt->execute();
$result      = $stmt->get_result();
$payrollRows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Payroll</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .note { color: #555; font-size: 0.9em; }
        .nav { margin-bottom: 15px; }
        .nav a { margin-right: 10px; text-decoration: none; color: #0366d6; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="employee_payroll.php">My Payroll</a>
        <a href="employee_schedule.php">My Schedule</a>
    </div>

    <h1>My Payroll</h1>
    <p>Logged in as: <strong><?php echo htmlspecialchars($email); ?></strong></p>

    <?php if (count($payrollRows) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Gross Pay</th>
                    <th>Taxes</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Issued At</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($payrollRows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['period_month']); ?></td>
                    <td><?php echo htmlspecialchars($row['gross_pay']); ?></td>
                    <td><?php echo htmlspecialchars($row['taxes_withheld']); ?></td>
                    <td><?php echo htmlspecialchars($row['deductions']); ?></td>
                    <td><?php echo htmlspecialchars($row['net_pay']); ?></td>
                    <td><?php echo htmlspecialchars($row['issued_at']); ?></td>
                    <td class="note"><?php echo htmlspecialchars($row['notes']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payroll records found.</p>
    <?php endif; ?>
</body>
</html>
