<?php
$payroll = true;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Payroll — ABC Corporation</title>
  <link rel="stylesheet" href="../css/styles.css" />
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
                    <header class="topbar">
                        
                        

                    <section class="content container">
            
                    </header>
                        <div class="payroll-summary">
                            <div class="payroll-card">
                                <div class="text-muted">Total Profits</div>
                                <div style="font-weight:800;font-size:22px">$2,512,901</div>
                            </div>
                            <div class="payroll-card">
                                <div class="text-muted">Expenses</div>
                                <div style="font-weight:800;font-size:22px">$823,600</div>
                            </div>
                            <div class="payroll-card">
                                <div class="text-muted">Payroll</div>
                                <div style="font-weight:800;font-size:22px">$750,290</div>
                            </div>
                        </div>
                        <div style="height:12px"></div>
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Actions</div>
                            </div>
                            <div class="card-body" style="display:flex;gap:10px;flex-wrap:wrap">
                                <button class="btn">Run Payroll</button>
                                <button class="btn ghost">Export CSV</button>
                                <button class="btn ghost">View Reports</button>
                            </div>
                        </div>
                    </section>
                    </main>

                   

                 


                   
                   
                </div>
            </div>
        </main>
    </div>
  

  <script>
    document.getElementById('menuToggle')?.addEventListener('click',()=>document.getElementById('sidebar').classList.toggle('open'));
  </script>
</body>
</html>
