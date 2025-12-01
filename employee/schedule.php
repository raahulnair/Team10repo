<?php
$schedule = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule — Employee Portal</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/employee.js" defer></script>
</head>
<body>

<div class="dashboard-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="dashboard-content">
            <div class="content-grid">

                <section class="card">
                    <div class="card-header">
                        <h3>My Schedule</h3>
                    </div>

                    <div class="calendar-widget">
                        <div id="calendarContainer"></div>
                    </div>
                </section>

            </div>
        </div>
    </main>

</div>

</body>
</html>
