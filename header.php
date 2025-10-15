<?php
// Commit 3: Sidebar partial with active-link hook
// Usage: in your page set $activePage = 'Dashboard' (or Employees/Payroll/Attendance/Settings/Admin) before include 'sidebar.php';

$activePage = $activePage ?? '';

function navClass(string $name, string $active): string {
  return $name === $active ? 'nav-link active' : 'nav-link';
}

function ariaCurrent(string $name, string $active): string {
  return $name === $active ? ' aria-current="page"' : '';
}
?>
<aside class="sidebar" id="sidebar" role="navigation" aria-label="Sidebar">
  <nav>
    <a class="<?= navClass('Dashboard', $activePage) ?>" href="index.php"<?= ariaCurrent('Dashboard', $activePage) ?>>Dashboard</a>
    <a class="<?= navClass('Employees', $activePage) ?>" href="employee.php"<?= ariaCurrent('Employees', $activePage) ?>>Employees</a>
    <a class="<?= navClass('Payroll', $activePage) ?>" href="payroll.php"<?= ariaCurrent('Payroll', $activePage) ?>>Payroll</a>
    <a class="<?= navClass('Attendance', $activePage) ?>" href="attendance.php"<?= ariaCurrent('Attendance', $activePage) ?>>Attendance</a>
    <a class="<?= navClass('Settings', $activePage) ?>" href="setting.php"<?= ariaCurrent('Settings', $activePage) ?>>Settings</a>
    <?php // Optional admin page link; keep if you have admin.php ?>
    <a class="<?= navClass('Admin', $activePage) ?>" href="admin.php"<?= ariaCurrent('Admin', $activePage) ?>>Admin</a>
  </nav>
</aside>
