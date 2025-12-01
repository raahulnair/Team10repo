<?php
$profile = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile — Employee Portal</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<div class="dashboard-container">

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="dashboard-content">
            <div class="content-grid">

                <section class="card profile-card">
                    <div class="card-header">
                        <h3>My Profile</h3>
                    </div>

                    <div class="card-body profile-info">

                        <img src="../assets/user.png" class="profile-photo">

                        <div>
                            <p><strong>Name:</strong> John Doe</p>
                            <p><strong>Email:</strong> john.doe@company.com</p>
                            <p><strong>Phone:</strong> (555) 555-1212</p>
                            <p><strong>Position:</strong> Software Engineer</p>
                            <p><strong>Department:</strong> Technology</p>
                            <p><strong>Hire Date:</strong> March 18, 2022</p>
                        </div>

                    </div>
                </section>

            </div>
        </div>

    </main>

</div>

</body>
</html>
