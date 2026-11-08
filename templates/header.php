<?php
include "../database/db.php";
require_once __DIR__ . '/../utils/permissions.php';
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

$canManageMarketers = canManageMarketers();
$admindesc = strtolower(trim((string) ($_SESSION['designation'] ?? ''))) === 'admin';
$currentUsername = (string) ($_SESSION['name'] ?? '');
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$pageTitles = [
    'home.php' => 'Dashboard',
    'marketers.php' => 'Marketers',
    'pipeline.php' => 'Pipeline',
    'managementnote.php' => 'Management Notes',
    'reports.php' => 'Analytics Reports',
    'todo.php' => 'Job To Do',
    'viewlead.php' => 'Lead Details',
    'editlead.php' => 'Edit Lead',
    'addmarketer.php' => 'Add Marketer',
    'editmarketer.php' => 'Edit Marketer',
    'editnote.php' => 'Edit Note',
];
$currentPageTitle = $pageTitles[$currentPage] ?? 'CRM';

$userid = $_SESSION['id'];
$today = date("Y-m-d");

// Fetch reminders for today where status is not 0
$sqlToday = "SELECT * FROM remindernotes WHERE user = '$userid' AND reminderdate = '$today' AND status != 1";
$resultToday = mysqli_query($conn, $sqlToday);
$hasTodayReminders = mysqli_num_rows($resultToday) > 0; // Check if there are any reminders for today

// Fetch reminders that are past due where status is not 0
$sqlPast = "SELECT * FROM remindernotes WHERE user = '$userid' AND reminderdate < '$today' AND status != 1";
$resultPast = mysqli_query($conn, $sqlPast);
$hasPastReminders = mysqli_num_rows($resultPast) > 0; // Check if there are any past-due reminders

$navMarketerSql = $admindesc
    ? "SELECT username FROM login ORDER BY username ASC"
    : "SELECT username FROM login WHERE username = '" . mysqli_real_escape_string($conn, $currentUsername) . "' ORDER BY username ASC";
$navMarketerResult = mysqli_query($conn, $navMarketerSql);
if (!$navMarketerResult) {
    http_response_code(500);
    exit('Unable to load marketer navigation.');
}
$navMarketers = [];
while ($navMarketerRow = mysqli_fetch_assoc($navMarketerResult)) {
    $navMarketers[] = $navMarketerRow['username'];
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pergas Group</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='../style.css' rel='stylesheet'>
  </head>
  <body>
    
  <nav class="navbar navbar-expand-xl bordernav fixed-top bg-white crm-navbar">
  <div class="container">
    <a class="navbar-brand" href="../views/home.php"> <span><img style='width: 1.5rem' src='../images/logo.png' /></span></a>

    <div class="crm-navbar-reminders">
      <a href="../views/todo.php">
        <i id="notification-bell" class="fa fa-bell" style="font-size: 1.25rem;"></i>
        
        <?php if ($hasTodayReminders): ?>
          <div class="red-dot"
            style="
              width: 8px;
              height: 8px;
              background-color: red;
              border-radius: 50%;
              position: absolute;
              margin-left: 0.8rem;
              margin-top: -2rem
            "
          ></div> <!-- Red dot -->
        <?php endif; ?>

        <?php if ($hasPastReminders): ?>
          <div class="red-dot"
            style="
              width: 8px;
              height: 8px;
              background-color: blue;
              border-radius: 50%;
              position: absolute;
              margin-left: 0rem;
              margin-top: -2rem
            "
          ></div> <!-- blue dot -->
        <?php endif; ?>
      </a>
    </div>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav crm-primary-links">
        <li class="nav-item"><a href="../views/home.php" class="nav-link">Dashboard</a></li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Marketers</a>
          <ul class="dropdown-menu">
            <?php if (empty($navMarketers)): ?>
              <li><span class="dropdown-item-text text-muted">No users available</span></li>
            <?php else: ?>
              <?php foreach ($navMarketers as $navMarketer): ?>
                <li><a class="dropdown-item" href="../views/marketers.php?username=<?= urlencode($navMarketer) ?>"><?= htmlspecialchars($navMarketer, ENT_QUOTES, 'UTF-8') ?></a></li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </li>
        <li class="nav-item"><a href="../views/pipeline.php" class="nav-link">Pipeline</a></li>
        <li class="nav-item"><a href="../views/managementnote.php" class="nav-link">Management Notes</a></li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Analytics Reports</a>
          <ul class="dropdown-menu">
            <?php if (empty($navMarketers)): ?>
              <li><span class="dropdown-item-text text-muted">No reports available</span></li>
            <?php else: ?>
              <?php foreach ($navMarketers as $navMarketer): ?>
                <li><a class="dropdown-item" href="../views/reports.php?username=<?= urlencode($navMarketer) ?>"><?= htmlspecialchars($navMarketer, ENT_QUOTES, 'UTF-8') ?></a></li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </li>
        <li class="nav-item"><a href="../views/todo.php" class="nav-link">Job To Do</a></li>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-xl-0">
        <?php if($canManageMarketers): ?>
        <li class="nav-item">
          <a href="../views/addmarketer.php" class='nav-link'>Add Marketer</a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
          <a href="../logout.php" class='nav-link'>
            <button class='btn btn-danger btn-sm'>Logout</button>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="crm-page-context">
  <div class="container">
    <div class="crm-page-context__inner">
      <div class="crm-history-controls" aria-label="Page history">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="history.back()" aria-label="Go back" title="Go back"><i class="fa-solid fa-arrow-left"></i></button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="history.forward()" aria-label="Go forward" title="Go forward"><i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <nav aria-label="Breadcrumb">
        <ol class="breadcrumb crm-breadcrumb">
          <?php if ($currentPage === 'home.php'): ?>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          <?php else: ?>
            <li class="breadcrumb-item"><a href="../views/home.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($currentPageTitle, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endif; ?>
        </ol>
      </nav>
    </div>
  </div>
</div>
