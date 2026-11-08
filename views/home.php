<?php

include "../templates/header.php";
include "../database/db.php";

$sessionName = $_SESSION['name'];
$sessionDesignation = $_SESSION['designation'];
$userid = $_SESSION['id'];
$isAdmin = ($sessionDesignation === 'admin');

if ($isAdmin) {
    $sqlMarketers = "SELECT username FROM login ORDER BY username ASC";
} else {
    $sqlMarketers = "SELECT username FROM login WHERE username = '$sessionName'";
}
$resultMarketers = mysqli_query($conn, $sqlMarketers);
$marketers = [];
if ($resultMarketers) {
    while ($row = mysqli_fetch_assoc($resultMarketers)) {
        $marketers[] = $row['username'];
    }
}

$reminderSql = "SELECT COUNT(*) as cnt FROM remindernotes WHERE user = '$userid' AND status != 1";
$reminderResult = mysqli_query($conn, $reminderSql);
$pendingReminders = $reminderResult ? (int) mysqli_fetch_assoc($reminderResult)['cnt'] : 0;

if ($isAdmin) {
    $pipelineSql = "SELECT COUNT(*) as cnt FROM leads WHERE status IN ('quoted', 'partialy')";
} else {
    $pipelineSql = "SELECT COUNT(*) as cnt FROM leads WHERE marketer = '$sessionName' AND status IN ('quoted', 'partialy')";
}
$pipelineResult = mysqli_query($conn, $pipelineSql);
$pipelineCount = $pipelineResult ? (int) mysqli_fetch_assoc($pipelineResult)['cnt'] : 0;

$roleLabel = $isAdmin ? 'Administrator' : ucfirst($sessionDesignation ?? 'Marketer');
$marketerCount = count($marketers);
$todayFormatted = date('l, F j, Y');

?>

<main class="dashboard-page">
    <div class="container py-4">

        <section class="dashboard-hero">
            <div class="dashboard-hero__content">
                <p class="dashboard-hero__eyebrow">Dashboard</p>
                <h1 class="dashboard-hero__title">Welcome back, <?= htmlspecialchars($sessionName) ?></h1>
                <p class="dashboard-hero__subtitle">Choose a section below to manage leads, track progress, and stay on top of your work.</p>
            </div>
            <div class="dashboard-hero__meta">
                <span class="dashboard-badge"><?= htmlspecialchars($roleLabel) ?></span>
                <span class="dashboard-date"><i class="fa-regular fa-calendar me-2"></i><?= $todayFormatted ?></span>
            </div>
        </section>

        <section class="dashboard-stats">
            <div class="dashboard-stat">
                <div class="dashboard-stat__icon dashboard-stat__icon--pipeline">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <p class="dashboard-stat__value"><?= $pipelineCount ?></p>
                    <p class="dashboard-stat__label">Active pipeline</p>
                </div>
            </div>
            <div class="dashboard-stat">
                <div class="dashboard-stat__icon dashboard-stat__icon--todo">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div>
                    <p class="dashboard-stat__value"><?= $pendingReminders ?></p>
                    <p class="dashboard-stat__label">Pending reminders</p>
                </div>
            </div>
            <div class="dashboard-stat">
                <div class="dashboard-stat__icon dashboard-stat__icon--marketer">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="dashboard-stat__value"><?= $marketerCount ?></p>
                    <p class="dashboard-stat__label"><?= $isAdmin ? 'Team members' : 'Your profile' ?></p>
                </div>
            </div>
        </section>

        <section class="dashboard-grid" id="dashboard-sections">

            <article class="dashboard-card dashboard-card--marketer" id="marketers">
                <div class="dashboard-card__header">
                    <div class="dashboard-card__icon"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <h2 class="dashboard-card__title">Marketers</h2>
                        <p class="dashboard-card__text">View leads and activity by marketer</p>
                    </div>
                </div>
                <div class="dropdown dashboard-card__action">
                    <button class="btn btn-dashboard" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Open marketer <i class="fa-solid fa-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <?php if (empty($marketers)): ?>
                            <li><span class="dropdown-item-text text-muted">No marketers found</span></li>
                        <?php else: ?>
                            <?php foreach ($marketers as $marketerName): ?>
                                <li>
                                    <a class="dropdown-item" href="./marketers.php?username=<?= urlencode($marketerName) ?>">
                                        <i class="fa-solid fa-user me-2 text-primary"></i><?= htmlspecialchars($marketerName) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </article>

            <article class="dashboard-card dashboard-card--pipeline">
                <div class="dashboard-card__header">
                    <div class="dashboard-card__icon"><i class="fa-solid fa-diagram-project"></i></div>
                    <div>
                        <h2 class="dashboard-card__title">Pipeline</h2>
                        <p class="dashboard-card__text">Track quoted and in-progress projects</p>
                    </div>
                </div>
                <a href="./pipeline.php" class="btn btn-dashboard">View pipeline</a>
            </article>

            <article class="dashboard-card dashboard-card--management">
                <div class="dashboard-card__header">
                    <div class="dashboard-card__icon"><i class="fa-solid fa-note-sticky"></i></div>
                    <div>
                        <h2 class="dashboard-card__title">Management Notes</h2>
                        <p class="dashboard-card__text">Record internal notes and updates</p>
                    </div>
                </div>
                <a href="./managementnote.php" class="btn btn-dashboard">Write a note</a>
            </article>

            <article class="dashboard-card dashboard-card--analytics" id="analytics">
                <div class="dashboard-card__header">
                    <div class="dashboard-card__icon"><i class="fa-solid fa-chart-pie"></i></div>
                    <div>
                        <h2 class="dashboard-card__title">Analytics Reports</h2>
                        <p class="dashboard-card__text">Review performance and lead metrics</p>
                    </div>
                </div>
                <div class="dropdown dashboard-card__action">
                    <button class="btn btn-dashboard" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        View reports <i class="fa-solid fa-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <?php if (empty($marketers)): ?>
                            <li><span class="dropdown-item-text text-muted">No reports available</span></li>
                        <?php else: ?>
                            <?php foreach ($marketers as $marketerName): ?>
                                <li>
                                    <a class="dropdown-item" href="./reports.php?username=<?= urlencode($marketerName) ?>">
                                        <i class="fa-solid fa-chart-bar me-2 text-warning"></i><?= htmlspecialchars($marketerName) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </article>

            <article class="dashboard-card dashboard-card--todo">
                <div class="dashboard-card__header">
                    <div class="dashboard-card__icon"><i class="fa-solid fa-list-check"></i></div>
                    <div>
                        <h2 class="dashboard-card__title">Job To Do</h2>
                        <p class="dashboard-card__text">Manage reminders and daily tasks</p>
                    </div>
                </div>
                <a href="./todo.php" class="btn btn-dashboard">
                    Open tasks
                    <?php if ($pendingReminders > 0): ?>
                        <span class="dashboard-card__pill"><?= $pendingReminders ?></span>
                    <?php endif; ?>
                </a>
            </article>

        </section>

    </div>
</main>

<?php include "../templates/footer.php"; ?>
