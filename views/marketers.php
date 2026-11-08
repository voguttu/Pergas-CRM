<?php
include "../templates/header.php";
include "../database/db.php";
include "../components/colorcodes.php";
include "../components/leads_table.php";

$username = isset($_GET['username']) ? $_GET['username'] : '';
$period = isset($_GET['period']) ? $_GET['period'] : 0;
$duration = isset($_GET['duration']) ? $_GET['duration'] : '';

if ($username === '') {
    header('Location: home.php');
    exit;
}

$threeMonthsAgo = date('Y-m-d', strtotime('-2 months'));
$sixMonthsAgo = date('Y-m-d', strtotime('-4 months'));

$sqlThreeMonths = "SELECT lead_id, MIN(statuscreated_at) AS oldest_status, MAX(statuscreated_at) AS latest_status
        FROM status
        WHERE statuscreated_at <= '$threeMonthsAgo' AND statuscreated_at > '$sixMonthsAgo'
        GROUP BY lead_id";

$sqlSixMonths = "SELECT lead_id, MIN(statuscreated_at) AS oldest_status, MAX(statuscreated_at) AS latest_status
        FROM status
        WHERE statuscreated_at <= '$sixMonthsAgo'
        GROUP BY lead_id";

$resultThreeMonths = mysqli_query($conn, $sqlThreeMonths);
$resultSixMonths = mysqli_query($conn, $sqlSixMonths);

while ($row = mysqli_fetch_assoc($resultThreeMonths)) {
    $lead_id = $row['lead_id'];
    $updateSql = "UPDATE leads SET period = '2' WHERE id = '$lead_id'";
    mysqli_query($conn, $updateSql);
}

while ($row = mysqli_fetch_assoc($resultSixMonths)) {
    $lead_id = $row['lead_id'];
    $updateSql = "UPDATE leads SET period = '4' WHERE id = '$lead_id'";
    mysqli_query($conn, $updateSql);
}

if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $sql = "SELECT * FROM leads WHERE marketer = '$username' AND clientName LIKE '%$q%' AND duration != 'long' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
    $activeFilter = 'Search: ' . htmlspecialchars($_GET['q']);
} elseif (isset($_GET['period']) && $_GET['period'] == '2') {
    $sql = "SELECT * FROM leads WHERE marketer = '$username' AND period = '2' AND duration != 'long' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
    $activeFilter = '2 Month Leads';
} elseif (isset($_GET['period']) && $_GET['period'] == '4') {
    $sql = "SELECT * FROM leads WHERE marketer = '$username' AND period = '4' AND duration != 'long' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
    $activeFilter = '4 Month Leads';
} elseif (isset($_GET['duration']) && $_GET['duration'] == 'long') {
    $sql = "SELECT * FROM leads WHERE marketer = '$username' AND duration = 'long' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
    $activeFilter = 'Long Period Leads';
} else {
    $sql = "SELECT * FROM leads WHERE marketer = '$username' AND period = '0' AND duration != 'long' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
    $activeFilter = 'All Active Leads';
}

$leadCount = mysqli_num_rows($result);
$encodedUsername = urlencode($username);
$isLongFilter = ($duration === 'long');
$isPeriod2 = (isset($_GET['period']) && $_GET['period'] == '2');
$isPeriod4 = (isset($_GET['period']) && $_GET['period'] == '4');
$isAllLeads = !$isLongFilter && !$isPeriod2 && !$isPeriod4 && !isset($_GET['q']);
?>

<main class="dashboard-page marketers-page">
    <div class="container py-4">

        <?php if (!empty($_GET['message'])): ?>
            <?php
            $message = $_GET['message'];
            if ($message == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Lead added successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message == 'deleted'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Lead deleted successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <section class="dashboard-hero marketers-hero">
            <div class="dashboard-hero__content">
                <p class="dashboard-hero__eyebrow">Marketer Leads</p>
                <h1 class="dashboard-hero__title"><?= htmlspecialchars($username) ?></h1>
                <p class="dashboard-hero__subtitle">Manage leads, update statuses, and export selected records for this marketer only.</p>
            </div>
            <div class="dashboard-hero__meta">
                <span class="dashboard-badge"><?= htmlspecialchars($activeFilter) ?></span>
                <span class="dashboard-date"><i class="fa-solid fa-table-list me-2"></i><?= $leadCount ?> lead<?= $leadCount === 1 ? '' : 's' ?></span>
                <a href="./home.php" class="btn btn-sm btn-outline-secondary marketers-back-btn">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to dashboard
                </a>
            </div>
        </section>

        <section class="marketers-toolbar">
            <div class="marketers-toolbar__row">
                <div class="marketers-toolbar__actions">
                    <button class="btn btn-success btn-sm" id="toggleFormBtn" type="button">Add Lead</button>

                    <div class="marketers-filter-group">
                        <a class="marketers-filter-btn <?= $isAllLeads ? 'is-active' : '' ?>" href="./marketers.php?username=<?= $encodedUsername ?>&period=0">All Leads</a>
                        <a class="marketers-filter-btn <?= $isPeriod2 ? 'is-active' : '' ?>" href="./marketers.php?username=<?= $encodedUsername ?>&period=2">2 Months</a>
                        <a class="marketers-filter-btn <?= $isPeriod4 ? 'is-active' : '' ?>" href="./marketers.php?username=<?= $encodedUsername ?>&period=4">4 Months</a>
                        <a class="marketers-filter-btn <?= $isLongFilter ? 'is-active' : '' ?>" href="./marketers.php?username=<?= $encodedUsername ?>&duration=long">Long Period</a>
                    </div>
                </div>

            </div>
        </section>

        <section class="marketers-panel marketers-filter-panel">
            <div class="marketers-panel__header">
                <h2 class="marketers-panel__title">Filter and select leads</h2>
                <p class="marketers-panel__subtitle">Combine client search, a date range, and status colors. Select rows below for export.</p>
            </div>

            <div class="marketers-table-filters">
                <form class="marketers-search" role="search">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input class="form-control" id="searchInput" name="q" type="search" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Search client name..." aria-label="Search client">
                    </div>
                </form>
                <div class="marketers-date-filter" role="group" aria-label="Filter leads by date">
                    <div>
                        <label for="leadDateFrom">From</label>
                        <input class="form-control form-control-sm" id="leadDateFrom" type="date" aria-label="Start date">
                    </div>
                    <div>
                        <label for="leadDateTo">To</label>
                        <input class="form-control form-control-sm" id="leadDateTo" type="date" aria-label="End date">
                    </div>
                </div>
            </div>

            <p class="marketers-date-filter-error" id="leadDateFilterError" role="alert" hidden>Start date must be on or before end date.</p>

            <div class="marketers-status-filter">
                <span class="marketers-status-filter__label">Filter by status</span>
                <?php renderColorCodes(true); ?>
            </div>

            <?php if ($admindesc): ?>
                <div class="marketers-export-bar">
                    <div class="marketers-export-bar__info">
                        <span id="exportCount" class="marketers-export-count">0 selected</span>
                        <span class="text-muted marketers-export-hint">Select leads in the table to export or mark.</span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="exportLeadsBtn">
                        <i class="fa-solid fa-file-export me-1"></i> Export Selected
                    </button>
                </div>
            <?php endif; ?>
        </section>

        <section class="marketers-panel marketers-form-panel" id="leadForm" style="display: none;">
            <div class="marketers-panel__header">
                <h2 class="marketers-panel__title">Add New Lead</h2>
                <p class="marketers-panel__subtitle">Fill in the client details below.</p>
            </div>
            <form class="row g-2" action="../models/addlead.php" method="post">
                <div class="col-md-3"><input class="form-control form-control-sm" name="date" required type="date" max="<?= date('Y-m-d') ?>" placeholder="Date"></div>
                <div class="col-md-3"><input class="form-control form-control-sm" name="name" required type="text" placeholder="Client Name"></div>
                <div class="col-md-3"><input class="form-control form-control-sm" name="phone" required type="number" placeholder="Phone"></div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" name="clienttype" required>
                        <option value="" disabled selected>Client Type</option>
                        <option value="company">Company</option>
                        <option value="individual">Indivudual</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" name="connectivity" required>
                        <option value="" disabled selected>Type of Connectivity</option>
                        <option value="visit">Visit</option>
                        <option value="referal">Referal</option>
                        <option value="assigned">Assigned</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" name="intouch" required>
                        <option value="" disabled selected>Person in Touch</option>
                        <option value="forman">Forman</option>
                        <option value="engineer">Engeener</option>
                        <option value="owner">Owner</option>
                        <option value="contractor">Contactor</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" name="project" required>
                        <option value="" disabled selected>Project Type</option>
                        <option value="villa">Villa</option>
                        <option value="flat">Flat</option>
                        <option value="commercial">Commercial</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control form-control-sm" name="status" required>
                        <option value="" disabled selected>Status</option>
                        <option value="visited">Visited</option>
                        <option value="revisited">Re-visited</option>
                        <option value="bought">Bought</option>
                        <option value="quoted">Quoted</option>
                        <option value="partialy">Partialy bought</option>
                        <option value="showroom">Visit Showroom</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3 d-none">
                    <input class="form-control form-control-sm" name="marketer" value="<?= htmlspecialchars($username) ?>" type="text">
                </div>
                <div class="col-md-4"><input class="form-control form-control-sm" name="location" required type="text" placeholder="Location"></div>
                <div class="col-md-4">
                    <select class="form-control form-control-sm" name="possibility" required>
                        <option value="" disabled selected>Deal Possibility</option>
                        <option value="high">High</option>
                        <option value="moderate">Moderate</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-control form-control-sm" name="duration" required>
                        <option value="" disabled selected>Duration</option>
                        <option value="short">Short</option>
                        <option value="mid">Mid</option>
                        <option value="long">Long</option>
                    </select>
                </div>
                <div class="col-md-4"><input class="form-control form-control-sm" name="amount" type="text" required placeholder="Amount"></div>
                <div class="col-md-4"><textarea class="form-control form-control-sm" name="marketernote" required rows="3" placeholder="Add Note"></textarea></div>
                <div class="col-md-4">
                    <select class="form-control form-control-sm" name="product[]" required multiple>
                        <option value="iron">Iron</option>
                        <option value="tiles">Tiles</option>
                        <option value="paint">Paint</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3"><button class="btn btn-success btn-sm" type="submit">Save Lead</button></div>
            </form>
        </section>

        <section class="marketers-panel marketers-table-panel">
            <form id="exportForm" action="../models/exportleads.php" method="post"></form>
            <input type="hidden" id="exportMarketer" value="<?= htmlspecialchars($username) ?>">
            <?php renderLeadsTable($result, $admindesc, $period, $duration, $admindesc); ?>
        </section>

    </div>
</main>

<script>
    function DeleteConfirm() {
        return confirm('Are you sure you want to delete this lead?');
    }

    function getVisibleExportCheckboxes() {
        return Array.from(document.querySelectorAll('.export-lead-checkbox')).filter(function (checkbox) {
            var row = checkbox.closest('tr');
            return row && row.style.display !== 'none';
        });
    }

    function updateExportCount() {
        var exportCount = document.getElementById('exportCount');
        if (!exportCount) {
            return;
        }

        var selectedCount = document.querySelectorAll('.export-lead-checkbox:checked').length;
        exportCount.textContent = selectedCount + ' selected';
    }

    function exportSelectedLeads() {
        var checkedBoxes = document.querySelectorAll('.export-lead-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select at least one lead to export.');
            return;
        }

        var exportForm = document.getElementById('exportForm');
        exportForm.innerHTML = '';

        var marketerInput = document.createElement('input');
        marketerInput.type = 'hidden';
        marketerInput.name = 'marketer';
        marketerInput.value = document.getElementById('exportMarketer').value;
        exportForm.appendChild(marketerInput);

        checkedBoxes.forEach(function (checkbox) {
            var leadInput = document.createElement('input');
            leadInput.type = 'hidden';
            leadInput.name = 'lead_ids[]';
            leadInput.value = checkbox.value;
            exportForm.appendChild(leadInput);
        });

        exportForm.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('searchInput');
        var selectAllExport = document.getElementById('selectAllExport');
        var exportLeadsBtn = document.getElementById('exportLeadsBtn');
        var statusFilterButtons = Array.from(document.querySelectorAll('[data-status-filter]'));
        var statusFilterCount = document.querySelector('[data-status-filter-count]');
        var dateFromInput = document.getElementById('leadDateFrom');
        var dateToInput = document.getElementById('leadDateTo');
        var dateFilterError = document.getElementById('leadDateFilterError');

        function applyLeadFilters() {
            var searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            var dateFrom = dateFromInput ? dateFromInput.value : '';
            var dateTo = dateToInput ? dateToInput.value : '';
            var isDateRangeInvalid = dateFrom !== '' && dateTo !== '' && dateFrom > dateTo;
            var activeStatuses = statusFilterButtons
                .filter(function (button) {
                    return button.getAttribute('aria-pressed') === 'true';
                })
                .map(function (button) {
                    return button.getAttribute('data-status-filter');
                });
            var visibleCount = 0;

            document.querySelectorAll('#nameTable tbody tr').forEach(function (row) {
                var clientCell = row.querySelector('.lead-client-name');
                if (!clientCell) {
                    return;
                }

                var clientName = clientCell.getAttribute('data-client-name') || '';
                var status = row.getAttribute('data-lead-status') || '';
                var leadDate = row.getAttribute('data-lead-date') || '';
                var matchesSearch = clientName.includes(searchTerm);
                var matchesStatus = activeStatuses.indexOf(status) !== -1;
                var matchesDate = !isDateRangeInvalid &&
                    (dateFrom === '' || leadDate >= dateFrom) &&
                    (dateTo === '' || leadDate <= dateTo);
                var isVisible = matchesSearch && matchesStatus && matchesDate;

                row.style.display = isVisible ? 'table-row' : 'none';
                if (isVisible) {
                    visibleCount++;
                }
            });

            if (statusFilterCount) {
                statusFilterCount.textContent = visibleCount + ' lead' + (visibleCount === 1 ? '' : 's') + ' shown';
            }
            if (dateFilterError) {
                dateFilterError.hidden = !isDateRangeInvalid;
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                applyLeadFilters();
                if (selectAllExport) {
                    selectAllExport.checked = false;
                }
                updateExportCount();
            });
        }

        statusFilterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                button.setAttribute('aria-pressed', button.getAttribute('aria-pressed') !== 'true' ? 'true' : 'false');
                applyLeadFilters();
                if (selectAllExport) {
                    selectAllExport.checked = false;
                }
                updateExportCount();
            });
        });

        var resetLeadFilters = document.querySelector('[data-lead-filter-reset]');
        if (resetLeadFilters) {
            resetLeadFilters.addEventListener('click', function () {
                statusFilterButtons.forEach(function (button) {
                    button.setAttribute('aria-pressed', 'true');
                });
                if (searchInput) {
                    searchInput.value = '';
                }
                if (dateFromInput) {
                    dateFromInput.value = '';
                }
                if (dateToInput) {
                    dateToInput.value = '';
                }
                applyLeadFilters();
                if (selectAllExport) {
                    selectAllExport.checked = false;
                }
                updateExportCount();
            });
        }

        [dateFromInput, dateToInput].forEach(function (input) {
            if (input) {
                input.addEventListener('change', function () {
                    applyLeadFilters();
                    if (selectAllExport) {
                        selectAllExport.checked = false;
                    }
                    updateExportCount();
                });
            }
        });

        if (selectAllExport) {
            selectAllExport.addEventListener('change', function () {
                getVisibleExportCheckboxes().forEach(function (checkbox) {
                    checkbox.checked = selectAllExport.checked;
                });
                updateExportCount();
            });
        }

        document.querySelectorAll('.export-lead-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if (selectAllExport) {
                    var visibleBoxes = getVisibleExportCheckboxes();
                    selectAllExport.checked = visibleBoxes.length > 0 && visibleBoxes.every(function (item) {
                        return item.checked;
                    });
                }
                applyLeadFilters();
                updateExportCount();
            });
        });

        if (exportLeadsBtn) {
            exportLeadsBtn.addEventListener('click', exportSelectedLeads);
        }

        applyLeadFilters();
        updateExportCount();
    });

    function initTableHorizontalScroll() {
        var table = document.getElementById('nameTable');
        var wrap = document.getElementById('leadsTableScrollWrap');
        var top = document.getElementById('leadsTableScrollTop');
        var topInner = document.getElementById('leadsTableScrollTopInner');
        var scrollLeftBtn = document.getElementById('scrollTableLeft');
        var scrollRightBtn = document.getElementById('scrollTableRight');

        if (!table || !wrap || !top || !topInner) {
            return;
        }

        function syncScrollWidth() {
            topInner.style.width = table.scrollWidth + 'px';
        }

        function scrollByAmount(direction) {
            wrap.scrollBy({
                left: direction * 320,
                behavior: 'smooth'
            });
        }

        wrap.addEventListener('scroll', function () {
            top.scrollLeft = wrap.scrollLeft;
        });

        top.addEventListener('scroll', function () {
            wrap.scrollLeft = top.scrollLeft;
        });

        wrap.addEventListener('wheel', function (event) {
            if (!event.shiftKey) {
                return;
            }

            event.preventDefault();
            wrap.scrollLeft += event.deltaY;
        }, { passive: false });

        var isDragging = false;
        var dragStartX = 0;
        var dragStartScroll = 0;

        wrap.addEventListener('mousedown', function (event) {
            if (event.button !== 0 || event.target.closest('input, select, button, a, textarea, label')) {
                return;
            }

            isDragging = true;
            dragStartX = event.pageX;
            dragStartScroll = wrap.scrollLeft;
            wrap.classList.add('is-dragging');
        });

        window.addEventListener('mousemove', function (event) {
            if (!isDragging) {
                return;
            }

            event.preventDefault();
            wrap.scrollLeft = dragStartScroll - (event.pageX - dragStartX);
        });

        window.addEventListener('mouseup', function () {
            isDragging = false;
            wrap.classList.remove('is-dragging');
        });

        if (scrollLeftBtn) {
            scrollLeftBtn.addEventListener('click', function () {
                scrollByAmount(-1);
            });
        }

        if (scrollRightBtn) {
            scrollRightBtn.addEventListener('click', function () {
                scrollByAmount(1);
            });
        }

        window.addEventListener('resize', syncScrollWidth);
        syncScrollWidth();
    }

    initTableHorizontalScroll();
</script>

<?php include "../templates/footer.php"; ?>
