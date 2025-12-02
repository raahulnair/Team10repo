<?php
// employee_schedule.php - show logged-in employee's schedule

session_start();
require_once "db.php";

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

// Get schedule rows
$stmt = $mysqli->prepare(
    "SELECT start_time,
            end_time,
            location,
            notes
     FROM schedules
     WHERE empid = ?
     ORDER BY start_time"
);
$stmt->bind_param("i", $empid);
$stmt->execute();
$result = $stmt->get_result();
$shifts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Schedule</title>
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

    <h1>My Schedule</h1>
    <p>Logged in as: <strong><?php echo htmlspecialchars($email); ?></strong></p>

    <?php if (count($shifts) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Location</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($shifts as $shift): ?>
                <tr>
                    <td><?php echo htmlspecialchars($shift['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($shift['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($shift['location']); ?></td>
                    <td class="note"><?php echo htmlspecialchars($shift['notes']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No scheduled shifts found.</p>
    <?php endif; ?>
</body>
</html>
