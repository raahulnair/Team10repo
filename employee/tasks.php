<?php
$tasks = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasks — Employee Portal</title>
    <link rel="stylesheet" href="../css/styles.css">
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
                        <h3>My Tasks</h3>
                    </div>

                    <div class="card-body task-list">

                        <div class="task-item">
                            <div>
                                <strong>Prepare weekly report</strong>
                                <p class="text-muted">Due: Jan 28</p>
                            </div>
                            <span class="task-status in-progress">In Progress</span>
                        </div>

                        <div class="task-item">
                            <div>
                                <strong>Team meeting overview</strong>
                                <p class="text-muted">Due: Jan 30</p>
                            </div>
                            <span class="task-status not-started">Not Started</span>
                        </div>

                        <div class="task-item">
                            <div>
                                <strong>Client follow-up email</strong>
                                <p class="text-muted">Due: Jan 25</p>
                            </div>
                            <span class="task-status completed">Completed</span>
                        </div>

                    </div>
                </section>

            </div>
        </div>
    </main>

</div>

</body>
</html>
