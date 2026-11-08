<?php
include "../templates/header.php";
include "../database/db.php";
include "../components/colorcodes.php";

$statusCondition = "status IN ('quoted', 'partialy')";
$sql = "SELECT * FROM leads WHERE $statusCondition ORDER BY updated_at DESC";
$result = mysqli_query($conn, $sql);
$marketerResult = mysqli_query($conn, "SELECT DISTINCT marketer FROM leads WHERE $statusCondition ORDER BY marketer ASC");

if (!$result || !$marketerResult) {
    http_response_code(500);
    exit('Unable to load pipeline leads.');
}

$pipelineMarketers = [];
while ($marketerRow = mysqli_fetch_assoc($marketerResult)) {
    $pipelineMarketers[] = $marketerRow['marketer'];
}

$pipelineCount = mysqli_num_rows($result);
$canExportAll = strtolower(trim((string) ($_SESSION['designation'] ?? ''))) === 'admin';
$currentUsername = (string) ($_SESSION['name'] ?? '');
?>

<main class="dashboard-page pipeline-page">
    <div class="container py-4">
        <section class="dashboard-hero">
            <div class="dashboard-hero__content">
                <p class="dashboard-hero__eyebrow">Lead Pipeline</p>
                <h1 class="dashboard-hero__title">Partially Bought &amp; Quoted</h1>
                <p class="dashboard-hero__subtitle">Search and filter active pipeline leads by client, marketer, and status.</p>
            </div>
            <div class="dashboard-hero__meta">
                <span class="dashboard-date"><?= $pipelineCount ?> lead<?= $pipelineCount === 1 ? '' : 's' ?> total</span>
            </div>
        </section>

        <section class="marketers-panel marketer-report-filter-panel">
            <div class="marketers-panel__header">
                <h2 class="marketers-panel__title">Filter and select pipeline leads</h2>
                <p class="marketers-panel__subtitle">Combine client search, marketer selection, and status colors.</p>
            </div>
            <div class="pipeline-filter-controls">
                <label class="marketer-report-search">
                    <span>Client name</span>
                    <input class="form-control form-control-sm" id="pipelineClientSearch" type="search" placeholder="Search client name..." aria-label="Search pipeline by client name">
                </label>
                <label class="marketer-report-search">
                    <span>Marketer</span>
                    <select class="form-select form-select-sm" id="pipelineMarketerFilter" aria-label="Filter pipeline by marketer">
                        <option value="">All marketers</option>
                        <?php foreach ($pipelineMarketers as $marketerName): ?>
                            <option value="<?= htmlspecialchars(strtolower((string) $marketerName), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $marketerName, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="marketer-report-statuses">
                <span class="marketers-status-filter__label">Filter by status</span>
                <?php renderColorCodes(true, 'Show all'); ?>
            </div>
            <?php if ($canExportAll || $currentUsername !== ''): ?>
                <div class="marketers-export-bar">
                    <div class="marketers-export-bar__info">
                        <span id="pipelineExportCount" class="marketers-export-count">0 selected</span>
                        <span class="text-muted marketers-export-hint">Downloads selected leads as an Excel-compatible CSV file.</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="pipelineExportButton">
                        <i class="fa-solid fa-file-excel me-1"></i> Export selected to Excel
                    </button>
                </div>
            <?php endif; ?>
            <p class="marketer-report-visible-count" data-status-filter-count aria-live="polite"></p>
        </section>

        <?php if ($canExportAll || $currentUsername !== ''): ?>
            <form id="pipelineExportForm" action="../models/exportleads.php" method="post"></form>
            <input type="hidden" id="pipelineExportScope" value="pipeline">
        <?php endif; ?>

        <div class="table-container mt-3 marketer-report-table-wrap">
            <table class="marketer-report-table pipeline-table" id="nameTable">
                <thead>
                    <tr>
                        <?php if ($canExportAll || $currentUsername !== ''): ?>
                            <th scope="col" class="report-select-column">
                                <label class="lead-checkbox lead-checkbox--header" title="Select all visible leads">
                                    <input type="checkbox" id="pipelineSelectVisible" class="lead-checkbox__input" aria-label="Select all visible leads for export">
                                    <span class="lead-checkbox__box"></span>
                                </label>
                            </th>
                        <?php endif; ?>
                        <th scope="col" class="report-number-column">#</th>
                        <th scope="col">Marketer</th>
                        <th scope="col">Client name</th>
                        <th scope="col">Client type</th>
                        <th scope="col">Phone number</th>
                        <th scope="col">Location</th>
                        <th scope="col">Last contact</th>
                        <th scope="col">Project</th>
                        <th scope="col">Product(s)</th>
                        <th scope="col">Current status</th>
                        <th scope="col">Deal possibility</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Contact method</th>
                        <th scope="col">Lead date</th>
                        <th scope="col">Added on</th>
                        <th scope="col">Last updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rowNumber = 1;
                    $statusLabels = [
                        'visited' => 'Visited',
                        'revisited' => 'Revisited',
                        'partialy' => 'Partially bought',
                        'bought' => 'Bought',
                        'showroom' => 'Showroom',
                        'quoted' => 'Quoted',
                        'failed' => 'Failed',
                    ];
                    $columnCount = ($canExportAll || $currentUsername !== '') ? 17 : 16;
                    while ($row = mysqli_fetch_assoc($result)):
                        $status = (string) $row['status'];
                        $clientName = (string) $row['clientName'];
                        $marketerName = (string) $row['marketer'];
                        $statusLabel = $statusLabels[$status] ?? ucfirst($status);
                        $canExportRow = $canExportAll || $marketerName === $currentUsername;
                        $safe = function ($value) {
                            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
                        };
                    ?>
                        <tr class="table-<?= $safe($status) ?>" data-lead-status="<?= $safe($status) ?>" data-client-name="<?= $safe(strtolower($clientName)) ?>" data-marketer="<?= $safe(strtolower($marketerName)) ?>">
                            <?php if ($canExportAll || $currentUsername !== ''): ?>
                                <td class="report-select-column">
                                    <?php if ($canExportRow): ?>
                                        <label class="lead-checkbox" title="Select lead for export">
                                            <input type="checkbox" class="lead-checkbox__input pipeline-export-checkbox" value="<?= (int) $row['id'] ?>" aria-label="Select lead <?= $rowNumber ?> for export">
                                            <span class="lead-checkbox__box"></span>
                                        </label>
                                    <?php else: ?>
                                        <span aria-label="Export restricted to your own leads">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="report-number-column"><?= $rowNumber ?></td>
                            <td><?= $safe($marketerName) ?></td>
                            <td class="report-client-name"><?= $safe($clientName) ?></td>
                            <td><?= $safe($row['clienttype']) ?></td>
                            <td><?= $safe($row['phone']) ?></td>
                            <td><?= $safe($row['location']) ?></td>
                            <td><?= $safe($row['intouch']) ?></td>
                            <td><?= $safe($row['project']) ?></td>
                            <td><?= $safe($row['product']) ?></td>
                            <td><?= $safe($statusLabel) ?></td>
                            <td><?= $safe($row['possibility']) ?></td>
                            <td><?= number_format((float) $row['amount']) ?></td>
                            <td><?= $safe($row['connectivity']) ?></td>
                            <td><?= $safe($row['date']) ?></td>
                            <td><?= $safe($row['created_at']) ?></td>
                            <td><?= $safe($row['updated_at']) ?></td>
                        </tr>
                    <?php
                        $rowNumber++;
                    endwhile;
                    if ($pipelineCount === 0):
                    ?>
                        <tr><td colspan="<?= $columnCount ?>" class="report-empty-state">No partially bought or quoted leads found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var clientSearch = document.getElementById('pipelineClientSearch');
    var marketerFilter = document.getElementById('pipelineMarketerFilter');
    var statusButtons = Array.from(document.querySelectorAll('[data-status-filter]'));
    var count = document.querySelector('[data-status-filter-count]');
    var selectVisible = document.getElementById('pipelineSelectVisible');
    var exportCount = document.getElementById('pipelineExportCount');
    var exportButton = document.getElementById('pipelineExportButton');

    function visibleCheckboxes() {
        return Array.from(document.querySelectorAll('.pipeline-export-checkbox')).filter(function (checkbox) {
            return checkbox.closest('tr').style.display !== 'none';
        });
    }

    function updateExportControls() {
        var boxes = Array.from(document.querySelectorAll('.pipeline-export-checkbox'));
        var selected = boxes.filter(function (checkbox) { return checkbox.checked; }).length;
        var visible = visibleCheckboxes();
        if (exportCount) {
            exportCount.textContent = selected + ' selected';
        }
        if (selectVisible) {
            selectVisible.checked = visible.length > 0 && visible.every(function (checkbox) { return checkbox.checked; });
            selectVisible.indeterminate = visible.some(function (checkbox) { return checkbox.checked; }) && !selectVisible.checked;
        }
    }

    function applyFilters() {
        var query = clientSearch ? clientSearch.value.toLowerCase().trim() : '';
        var marketer = marketerFilter ? marketerFilter.value : '';
        var statuses = statusButtons.filter(function (button) {
            return button.getAttribute('aria-pressed') === 'true';
        }).map(function (button) {
            return button.getAttribute('data-status-filter');
        });
        var rows = Array.from(document.querySelectorAll('#nameTable tbody tr[data-lead-status]'));
        var shown = 0;

        rows.forEach(function (row) {
            var matches = (row.getAttribute('data-client-name') || '').indexOf(query) !== -1 &&
                (!marketer || row.getAttribute('data-marketer') === marketer) &&
                statuses.indexOf(row.getAttribute('data-lead-status')) !== -1;
            row.style.display = matches ? '' : 'none';
            if (matches) {
                shown++;
            }
        });
        if (count) {
            count.textContent = shown + ' of ' + rows.length + ' leads shown';
        }
        updateExportControls();
    }

    if (clientSearch) {
        clientSearch.addEventListener('input', applyFilters);
    }
    if (marketerFilter) {
        marketerFilter.addEventListener('change', applyFilters);
    }
    statusButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            button.setAttribute('aria-pressed', button.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
            applyFilters();
        });
    });

    var showAll = document.querySelector('[data-lead-filter-reset]');
    if (showAll) {
        showAll.addEventListener('click', function () {
            statusButtons.forEach(function (button) { button.setAttribute('aria-pressed', 'true'); });
            if (clientSearch) {
                clientSearch.value = '';
            }
            if (marketerFilter) {
                marketerFilter.value = '';
            }
            applyFilters();
        });
    }

    if (selectVisible) {
        selectVisible.addEventListener('change', function () {
            visibleCheckboxes().forEach(function (checkbox) { checkbox.checked = selectVisible.checked; });
            updateExportControls();
        });
    }
    document.querySelectorAll('.pipeline-export-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', updateExportControls);
    });

    if (exportButton) {
        exportButton.addEventListener('click', function () {
            var selected = document.querySelectorAll('.pipeline-export-checkbox:checked');
            if (selected.length === 0) {
                alert('Select at least one lead to export.');
                return;
            }
            var form = document.getElementById('pipelineExportForm');
            form.innerHTML = '';
            var pipelineInput = document.createElement('input');
            pipelineInput.type = 'hidden';
            pipelineInput.name = 'pipeline_export';
            pipelineInput.value = '1';
            form.appendChild(pipelineInput);
            selected.forEach(function (checkbox) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'lead_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
            form.submit();
        });
    }

    applyFilters();
});
</script>

<?php include "../templates/footer.php"; ?>
