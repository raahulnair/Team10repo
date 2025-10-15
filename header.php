<?php
// Commit 2: Header partial (accessible)
?>
<header class="dashboard-header" role="banner" aria-label="Application header">
  <button class="sidebar-toggle" aria-label="Toggle sidebar" type="button">
    ☰
  </button>

  <h1 class="app-title">EMS</h1>

  <div class="header-actions" aria-label="Header actions">
    <!-- Placeholder actions; wire up later if needed -->
    <form action="setting.php" method="get" style="display:inline">
      <button class="btn ghost" type="submit" aria-label="Open settings">
        Settings
      </button>
    </form>
  </div>
</header>
