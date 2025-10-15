<?php
session_start();
$setting = true;

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Settings — ABC Corporation</title>
  <link rel="stylesheet" href="../css/styles.css" />
  <script src="../js/employee.js?v=3" defer></script>

</head>
<body>
    <div class="dashboard-container">
        <?php
        include 'sidebar.php';
        ?>
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Header -->
            <?php 
                include 'header.php'
            ;?>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="content-grid">
                    
                    
                    <main class="main">
                    

                    <section class="content container">
                        <div class="card">
                        <div class="card-header">
                            <div class="card-title">Profile</div>
                        </div>
                        <div class="card-body">
                            <div class="form-grid cols-2">
                            <div class="field">
                                <label class="label">Full Name</label>
                                <input class="input" placeholder="John Anderson" />
                            </div>
                            <div class="field">
                                <label class="label">Email</label>
                                <input class="input" type="email" placeholder="john.anderson@abc.com" />
                            </div>
                            <div class="field">
                                <label class="label">Department</label>
                                <select class="select">
                                <option>Human Resources</option>
                                <option>IT</option>
                                <option>Finance</option>
                                <option>Marketing</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label">Phone</label>
                                <input class="input" placeholder="+1 (555) 123-4567" />
                            </div>
                            </div>
                            <div class="form-actions">
                            <button class="btn">Update Profile</button>
                            <button class="btn ghost">Cancel</button>
                            </div>
                        </div>
                        </div>

                        <div style="height:16px"></div>

                        <div class="card">
                        <div class="card-header"><div class="card-title">Security</div></div>
                        <div class="card-body">
                            <div class="form-grid cols-2">
                            <div class="field">
                                <label class="label">Current Password</label>
                                <input class="input" type="password" />
                            </div>
                            <div class="field">
                                <label class="label">New Password</label>
                                <input class="input" type="password" />
                            </div>
                            <div class="field">
                                <label class="label">Two-Factor Authentication</label>
                                <select class="select">
                                <option>Disabled</option>
                                <option>Authenticator App</option>
                                <option>SMS</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="label">Session Timeout (minutes)</label>
                                <input class="input" type="number" value="30" />
                            </div>
                            </div>
                            <div class="form-actions">
                            <button class="btn">Update Security</button>
                            <button class="btn ghost">Cancel</button>
                            </div>
                        </div>
                        </div>

                        <div style="height:16px"></div>

                    <div class="card">
                        
                        <div class="card-body">
                            <button class="btn" style="color: red;" id="logoutBtn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                                    <path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"/>
                                    <path d="M16 17l5-5-5-5"/>
                                    <path d="M21 12H9"/>
                                </svg>
                                Log out
                            </button>
                    </div>
                    </section>
                    </main>

                   

                 


                   
                   
                </div>
            </div>
        </main>
    </div>
 

</body>
</html>
