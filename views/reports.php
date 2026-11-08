<?php
include "../templates/header.php";
include "../database/db.php";
include "../components/colorcodes.php";

$username = isset($_GET['username']) ? $_GET['username'] : '';
$canExport = $admindesc || (isset($_SESSION['name']) && $_SESSION['name'] === $username);

// Fetch all leads for the specified marketer
$sql_all_leads = "SELECT * FROM leads WHERE marketer = '$username' ORDER BY updated_at DESC";
$result_all_leads = mysqli_query($conn, $sql_all_leads);

// Fetch successful leads (status: bought)
$sql_successful_leads = "SELECT * FROM leads WHERE marketer = '$username' AND status IN ('bought', 'partialy') ORDER BY updated_at DESC";
$result_successful_leads = mysqli_query($conn, $sql_successful_leads);

// Fetch failed leads (status: failed)
$sql_failed_leads = "SELECT * FROM leads WHERE marketer = '$username' AND status = 'failed' ORDER BY updated_at DESC";
$result_failed_leads = mysqli_query($conn, $sql_failed_leads);

// Calculate total number of leads
$total_leads_count = mysqli_num_rows($result_all_leads);

// Calculate number of successful leads
$successful_leads_count = mysqli_num_rows($result_successful_leads);

// Calculate number of failed leads
$failed_leads_count = mysqli_num_rows($result_failed_leads);

// Calculate total amount made
$total_amount_made = 0;

// Loop through the result set
while ($row = mysqli_fetch_assoc($result_all_leads)) {
    // Check if status is 'bought' or 'partialy'
    if ($row['status'] == 'bought' || $row['status'] == 'partialy') {
        // Add amount to total amount made
        $total_amount_made += $row['amount'];
    }
}

if (isset($_GET['q'])) {
    $q = $_GET['q'];
    $sql = "SELECT * FROM leads WHERE marketer = '$username' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);

} elseif (!isset($_GET['q'])) {
    $sql = "SELECT * FROM leads WHERE marketer = '$username' ORDER BY updated_at DESC";
    $result = mysqli_query($conn, $sql);
}

if (isset($_GET['from_date'], $_GET['to_date']) && $_GET['from_date'] !== '' && $_GET['to_date'] !== '') {
    $fromDate = $_GET['from_date'];
    $toDate = $_GET['to_date'];

    // Sanitize input to prevent SQL injection
    $fromDate = mysqli_real_escape_string($conn, $fromDate);
    $toDate = mysqli_real_escape_string($conn, $toDate);

    // Construct the SQL query with date range filter
    $sql = "SELECT * FROM leads WHERE marketer = '$username' AND created_at >= '$fromDate' AND created_at <= '$toDate' ORDER BY updated_at DESC";

    $result = mysqli_query($conn, $sql);

    // Recalculate metrics based on the filtered result set
    $total_leads_count = mysqli_num_rows($result);

    $successful_leads_count = 0;
    $failed_leads_count = 0;
    $total_amount_made = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['status'] == 'bought' || $row['status'] == 'partialy') {
            $successful_leads_count++;
        } elseif ($row['status'] == 'failed') {
            $failed_leads_count++;
        }
        $total_amount_made += $row['amount'];
    }

} else {
    // Default query without date filter
    $sql = "SELECT * FROM leads WHERE marketer = '$username' ORDER BY updated_at DESC";
}

// Execute the query
$result = mysqli_query($conn, $sql);
?>


<div class="container mt-5">
<?php
  if (!empty($_GET['message'])) {
    $message = $_GET['message'];
    if ($message == 'success') {
      echo '<h3 class="alert alert-success" id="success-message">Lead Added successfully</h3>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    } else if($message == 'deleted') {
      echo '<h3 class="alert alert-danger" id="success-message">Lead deleted successfully</h3>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    }
  }
  ?>

    <form class="d-flex float-end gap-2" id="reportDateFilter">
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
        <div class='me-2'>
            <label class='form-label' for="reportFromDate">From</label>
            <input class="form-control" id="reportFromDate" name="from_date" type="date" aria-label="From date" value="<?php echo isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : ''; ?>">
        </div>
        <div class='me-2'>
            <label class='form-label' for="reportToDate">To</label>
            <input class="form-control" id="reportToDate" name="to_date" type="date" aria-label="To date" value="<?php echo isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : ''; ?>">
        </div>
    </form>

    <h1 class="marketer-report-title"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?> Report</h1>
    <hr />

    <section class="marketers-panel marketer-report-filter-panel">
        <div class="marketers-panel__header">
            <h2 class="marketers-panel__title">Filter report leads</h2>
            <p class="marketers-panel__subtitle">Search by client name and select one or more status colors.</p>
        </div>
        <div class="marketer-report-controls">
            <label class="marketer-report-search">
                <span>Client name</span>
                <input class="form-control form-control-sm" id="reportClientSearch" type="search" placeholder="Search client name..." aria-label="Search leads by client name">
            </label>
            <div class="marketer-report-statuses">
                <span class="marketers-status-filter__label">Filter by status</span>
                <?php renderColorCodes(true, 'Show all'); ?>
            </div>
        </div>
        <?php if ($canExport): ?>
            <div class="marketers-export-bar">
                <div class="marketers-export-bar__info">
                    <span id="reportExportCount" class="marketers-export-count">0 selected</span>
                    <span class="text-muted marketers-export-hint">Downloads an Excel-compatible CSV file.</span>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="reportExportButton">
                    <i class="fa-solid fa-file-excel me-1"></i> Export selected to Excel
                </button>
            </div>
        <?php endif; ?>
        <p class="marketer-report-visible-count" data-status-filter-count aria-live="polite"></p>
    </section>

    <div class="row text-center">
        <!-- Display various metrics -->
        <div class="col-md-3 mt-2">
            <div class="card marketer">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_leads_count ?></h5>
                    <p class="card-text">Total Leads</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mt-2">
            <div class="card pipeline">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $successful_leads_count ?></h5>
                    <p class="card-text">Successful Leads</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mt-2">
            <div class="card failedleads">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $failed_leads_count ?></h5>
                    <p class="card-text">Failed Leads</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mt-2">
            <div class="card analytics">
                <div class="card-body">
                    <h5 class="card-title"><?php echo number_format($total_amount_made) ?></h5>
                    <p class="card-text">Amount Made</p>
                </div>
            </div>
        </div>
    </div>
    <hr />

    <?php if ($canExport): ?>
        <form id="reportExportForm" action="../models/exportleads.php" method="post"></form>
        <input type="hidden" id="reportExportMarketer" value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <div class="table-container mt-3 marketer-report-table-wrap">
        <table class="marketer-report-table" id="nameTable">
        <thead>
        <tr>
            <?php if ($canExport): ?>
                <th scope="col" class="report-select-column">
                    <label class="lead-checkbox lead-checkbox--header" title="Select all visible leads">
                        <input type="checkbox" id="reportSelectVisible" class="lead-checkbox__input" aria-label="Select all visible leads for export">
                        <span class="lead-checkbox__box"></span>
                    </label>
                </th>
            <?php endif; ?>
            <th scope="col" class="report-number-column">#</th>
            <th scope="col">Marketer</th>
            <th scope="col">Client name</th>
            <th scope="col">Phone number</th>
            <th scope="col">Location</th>
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
        $statusLabels = array(
            'visited' => 'Visited',
            'revisited' => 'Revisited',
            'partialy' => 'Partially bought',
            'bought' => 'Bought',
            'showroom' => 'Showroom',
            'quoted' => 'Quoted',
            'failed' => 'Failed',
        );
        $columnCount = $canExport ? 15 : 14;

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $id = (int) $row['id'];
                $status = (string) $row['status'];
                $escapedStatus = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
                $statusLabel = isset($statusLabels[$status]) ? $statusLabels[$status] : ucfirst($status);
                $clientName = (string) $row['clientName'];
                $escapedClientName = htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8');
                $searchClientName = htmlspecialchars(strtolower($clientName), ENT_QUOTES, 'UTF-8');
                $safe = function ($value) {
                    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
                };

                echo "<tr class='table-$escapedStatus' data-lead-status='$escapedStatus' data-client-name='$searchClientName'>";
                if ($canExport) {
                    echo "<td class='report-select-column'>
                            <label class='lead-checkbox' title='Select lead for export'>
                                <input type='checkbox' class='lead-checkbox__input report-export-checkbox' value='$id' aria-label='Select lead $rowNumber for export'>
                                <span class='lead-checkbox__box'></span>
                            </label>
                          </td>";
                }
                echo "<td class='report-number-column'>$rowNumber</td>
                      <td>" . $safe($row['marketer']) . "</td>
                      <td class='report-client-name'>$escapedClientName</td>
                      <td>" . $safe($row['phone']) . "</td>
                      <td>" . $safe($row['location']) . "</td>
                      <td>" . $safe($row['project']) . "</td>
                      <td>" . $safe($row['product']) . "</td>
                      <td>" . $safe($statusLabel) . "</td>
                      <td>" . $safe($row['possibility']) . "</td>
                      <td>" . number_format((float) $row['amount']) . "</td>
                      <td>" . $safe($row['connectivity']) . "</td>
                      <td>" . $safe($row['date']) . "</td>
                      <td>" . $safe($row['created_at']) . "</td>
                      <td>" . $safe($row['updated_at']) . "</td>
                      </tr>";
                $rowNumber++;
            }
        } else {
            echo "<tr><td colspan='$columnCount' class='report-empty-state'>No leads found for this marketer.</td></tr>";
        }
        ?>

        </tbody>
        </table>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var dateForm = document.getElementById('reportDateFilter');
        var fromDateInput = document.getElementById('reportFromDate');
        var toDateInput = document.getElementById('reportToDate');
        var searchInput = document.getElementById('reportClientSearch');
        var statusButtons = Array.from(document.querySelectorAll('[data-status-filter]'));
        var visibleCount = document.querySelector('[data-status-filter-count]');
        var selectVisible = document.getElementById('reportSelectVisible');
        var exportCount = document.getElementById('reportExportCount');
        var exportButton = document.getElementById('reportExportButton');

        function getVisibleExportCheckboxes() {
            return Array.from(document.querySelectorAll('.report-export-checkbox')).filter(function (checkbox) {
                var row = checkbox.closest('tr');
                return row && row.style.display !== 'none';
            });
        }

        function updateExportSelection() {
            var checkboxes = Array.from(document.querySelectorAll('.report-export-checkbox'));
            var selectedCount = checkboxes.filter(function (checkbox) {
                return checkbox.checked;
            }).length;
            var visibleCheckboxes = getVisibleExportCheckboxes();

            if (exportCount) {
                exportCount.textContent = selectedCount + ' selected';
            }
            if (selectVisible) {
                selectVisible.checked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(function (checkbox) {
                    return checkbox.checked;
                });
                selectVisible.indeterminate = visibleCheckboxes.some(function (checkbox) {
                    return checkbox.checked;
                }) && !selectVisible.checked;
            }
        }

        function applyReportFilters() {
            var query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            var activeStatuses = statusButtons.filter(function (button) {
                return button.getAttribute('aria-pressed') === 'true';
            }).map(function (button) {
                return button.getAttribute('data-status-filter');
            });
            var shown = 0;
            var total = 0;

            document.querySelectorAll('#nameTable tbody tr[data-lead-status]').forEach(function (row) {
                total++;
                var clientName = row.getAttribute('data-client-name') || '';
                var status = row.getAttribute('data-lead-status') || '';
                var matches = clientName.indexOf(query) !== -1 && activeStatuses.indexOf(status) !== -1;
                row.style.display = matches ? '' : 'none';
                if (matches) {
                    shown++;
                }
            });

            if (visibleCount) {
                visibleCount.textContent = shown + ' of ' + total + ' leads shown';
            }
            updateExportSelection();
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyReportFilters);
        }
        statusButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var isActive = button.getAttribute('aria-pressed') === 'true';
                button.setAttribute('aria-pressed', isActive ? 'false' : 'true');
                applyReportFilters();
            });
        });

        var showAllButton = document.querySelector('[data-lead-filter-reset]');
        if (showAllButton) {
            showAllButton.addEventListener('click', function () {
                var hadDateFilter = (fromDateInput && fromDateInput.value) || (toDateInput && toDateInput.value);
                statusButtons.forEach(function (button) {
                    button.setAttribute('aria-pressed', 'true');
                });
                if (searchInput) {
                    searchInput.value = '';
                }
                if (fromDateInput) {
                    fromDateInput.value = '';
                }
                if (toDateInput) {
                    toDateInput.value = '';
                }

                if (hadDateFilter && dateForm) {
                    dateForm.submit();
                    return;
                }
                applyReportFilters();
            });
        }

        [fromDateInput, toDateInput].forEach(function (input) {
            if (input) {
                input.addEventListener('change', function () {
                    if (fromDateInput.value && toDateInput.value) {
                        dateForm.submit();
                    }
                });
            }
        });

        if (selectVisible) {
            selectVisible.addEventListener('change', function () {
                getVisibleExportCheckboxes().forEach(function (checkbox) {
                    checkbox.checked = selectVisible.checked;
                });
                updateExportSelection();
            });
        }
        document.querySelectorAll('.report-export-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateExportSelection);
        });

        if (exportButton) {
            exportButton.addEventListener('click', function () {
                var selectedCheckboxes = document.querySelectorAll('.report-export-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Select at least one lead to export.');
                    return;
                }

                var form = document.getElementById('reportExportForm');
                form.innerHTML = '';
                var marketerInput = document.createElement('input');
                marketerInput.type = 'hidden';
                marketerInput.name = 'marketer';
                marketerInput.value = document.getElementById('reportExportMarketer').value;
                form.appendChild(marketerInput);

                selectedCheckboxes.forEach(function (checkbox) {
                    var leadInput = document.createElement('input');
                    leadInput.type = 'hidden';
                    leadInput.name = 'lead_ids[]';
                    leadInput.value = checkbox.value;
                    form.appendChild(leadInput);
                });
                form.submit();
            });
        }

        applyReportFilters();
    });

    function DeleteConfirm() {
        if (confirm("Are you sure you want to delete Lead")) {
            return true; // Proceed with deletion
        } else {
            return false; // Cancel deletion
        }
    }
</script>

<?php include "../templates/footer.php";  ; // Load the header.php template. ?>
