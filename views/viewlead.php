<?php 
include "../templates/header.php";
include "../database/db.php";
include "../components/leads_table.php";

$location = '../views/marketers.php';
$username = $_SESSION['name'];
$id = $_GET['id'];

$sql = "SELECT * FROM leads WHERE id = '$id';";

$result = mysqli_query($conn, $sql);

?>

<main class="container-fluid py-4 lead-details-page">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-1">CRM</p>
            <h1 class="h4 mb-0">Lead Details</h1>
        </div>
        <button class="btn btn-sm btn-outline-primary" type="button" onclick="goBack()">Back</button>
    </div>

    <div class="table-container lead-details-table-wrap mt-3">
        <table class="table table-bordered table-striped table-hover align-middle lead-details-table" id="nameTable">
        <caption class="visually-hidden">Details and editable fields for this lead</caption>
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Client</th>
            <th scope="col">Client Type</th>
            <th scope="col">Phone</th>
            <th scope="col">Location</th>
            <th scope="col">Intouch</th>
            <th scope="col">Project</th>
            <th scope="col">Product</th>
            <th scope="col">Status</th>
            <th scope="col">Possibility</th>
            <th scope="col">Duration</th>
            <th scope="col">Amount</th>
            <th scope="col">Connectivity</th>
            <th scope="col">Your Note</th>
        </tr>
        </thead>
        <tbody>
    <?php
    $rowNumber = 1;

    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $status = $row['status'];
            $duration = $row['duration'];
            $marketer = $row['marketer'];
            $marketernote = (string) $row['marketernote'];
            $task = (string) $row['task'];
            $amount = (string) $row['amount'];
            $product = $row['product'];
            $escape = static function ($value): string {
                return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            };
            // Define corresponding CSS class for each status
            $statusClasses = array(
                'visited' => 'success',
                'revisited' => 'info',
                'bought' => 'primary',
                'quoted' => 'dark',
                'partialy' => 'warning',
                'showroom' => 'secondary',
                'failed' => 'danger',
            );
            
            // Set default class if status not found in the array
            $class = isset($statusClasses[$status]) ? $statusClasses[$status] : '';
            
            echo "
                <tr class='table-$class lead-details-row'>
                    <td data-label='#'><span class='lead-details-row-tools'>" . $rowNumber . "<button type='button' class='lead-details-row-toggle' data-lead-details-toggle aria-expanded='false' aria-label='Show additional lead details'><span aria-hidden='true'>+</span><span class='lead-details-visually-hidden'>Show additional lead details</span></button></span></td>
                    <td data-label='Client'>" . $escape($row['clientName']) . "</td>
                    <td data-label='Client Type'>" . $escape($row['clienttype']) . "</td>
                    <td data-label='Phone'>" . $escape($row['phone']) . "</td>
                    <td data-label='Location'>" . $escape($row['location']) . "</td>
                    <td data-label='Intouch'>" . $escape($row['intouch']) . "</td>
                    <td data-label='Project'>" . $escape($row['project']) . "</td>
                    <td data-label='Product'>
                        <form action='../models/update_product.php' method='post'>
                            <input type='hidden' name='id' value='" . (int) $id . "'>
                            <input type='hidden' name='marketer' value='" . $escape($marketer) . "'>
                            <select class='form-select product-dropdown' name='product[]' multiple>
                                <option value='iron' " . ($product == 'iron' ? 'selected' : '') . ">Iron</option>
                                <option value='tiles' " . ($product == 'tiles' ? 'selected' : '') . ">Tiles</option>
                                <option value='paint' " . ($product == 'paint' ? 'selected' : '') . ">Paint</option>
                                <option value='other' " . ($product == 'other' ? 'selected' : '') . ">Other</option>
                            </select>
                            <button type='submit' class='btn btn-success btn-sm'>Submit</button>
                        </form>
                    </td>
                    <td data-label='Status'>
                        <form action='../models/update_status.php' method='post'>
                            <input type='hidden' name='id' value='" . (int) $id . "'>
                            <input type='hidden' name='marketer' value='" . $escape($marketer) . "'>
                            <select class='form-select status-dropdown' name='status' onchange='this.form.submit()'>
                                <option value='visited' " . ($status == 'visited' ? 'selected' : '') . ">Visited</option>
                                <option value='revisited' " . ($status == 'revisited' ? 'selected' : '') . ">Re-visited</option>
                                <option value='bought' " . ($status == 'bought' ? 'selected' : '') . ">Bought</option>
                                <option value='quoted' " . ($status == 'quoted' ? 'selected' : '') . ">Quoted</option>
                                <option value='partialy' " . ($status == 'partialy' ? 'selected' : '') . ">Partially bought</option>
                                <option value='showroom' " . ($status == 'showroom' ? 'selected' : '') . ">Visit Showroom</option>
                                <option value='failed' " . ($status == 'failed' ? 'selected' : '') . ">Failed</option>
                            </select>
                        </form>
                    </td>
                    <td data-label='Possibility'>" . $escape($row['possibility']) . "</td>
                    <td data-label='Duration'>
                        <form action='../models/update_duration.php' method='post'>
                            <input type='hidden' name='id' value='" . (int) $id . "'>
                            <input type='hidden' name='marketer' value='" . $escape($marketer) . "'>
                            <select class='form-select duration-dropdown' name='duration' onchange='this.form.submit()'>
                                <option value='short' " . ($duration == 'short' ? 'selected' : '') . ">Short</option>
                                <option value='mid' " . ($duration == 'mid' ? 'selected' : '') . ">Mid</option>
                                <option value='long' " . ($duration == 'long' ? 'selected' : '') . ">Long</option>
                            </select>
                        </form>
                    </td>
                    <td data-label='Amount'>
                        <form action='../models/update_amount.php' method='post' id='amountForm'>
                            <input type='hidden' name='id' value='" . (int) $id . "'>
                            <input type='hidden' name='marketer' value='" . $escape($marketer) . "'>
                            <input type='number' class='form-control amount-input' name='amount' value='" . $escape($amount) . "'>
                        </form>
                    </td>
                    <td data-label='Connectivity'>" . $escape($row['connectivity']) . "</td>
                    <td data-label='Your Note' class='lead-details-note-cell'>
                        <form action='../models/update_marketernote.php' method='post' id='marketernoteForm'>
                            <input type='hidden' name='id' value='" . (int) $id . "'>
                            <input type='hidden' name='marketer' value='" . $escape($marketer) . "'>
                            <textarea class='form-control lead-details-note' name='marketernote' id='marketernote' data-autosize-note>" . $escape($marketernote) . "</textarea>
                        </form>
                    </td>
                </tr>
                <tr class='lead-details-expanded-row' data-lead-details-row hidden>
                    <td colspan='14'>
                        <div class='lead-details-expanded-content'>
                            <div class='lead-details-expanded-item'><span>Marketer</span><strong>" . $escape($row['marketer']) . "</strong></div>
                            <div class='lead-details-expanded-item lead-details-expanded-task'><span>Task</span><div>" . nl2br($escape($task)) . "</div></div>
                            <div class='lead-details-expanded-item'><span>Date</span><strong>" . $escape(date('Y-m-d', strtotime($row['date']))) . "</strong></div>
                            <div class='lead-details-expanded-item'><span>Created Date</span><strong>" . $escape(date('Y-m-d', strtotime($row['created_at']))) . "</strong></div>
                            <div class='lead-details-expanded-item'><span>Updated Date</span><strong>" . $escape(date('Y-m-d', strtotime($row['updated_at']))) . "</strong></div>
                        </div>
                    </td>
                </tr>";
            $rowNumber++;
        }           
    } else {
        echo "<tr><td colspan='14'>No lead details found.</td></tr>";
    }
    ?>
        </tbody>
        </table>
    </div>

    
    <hr />
    <div class="row">
    <a href="../views/todo.php">Write a Reminder</a>
        <div class="col-md-6">
            <h5>Status Details</h5>
            <ol>
                <?php 
                    // Query to fetch amount details
                    $sql_status = "SELECT status, statuscreated_at FROM status WHERE lead_id = '$id'";
                    $result_status = mysqli_query($conn, $sql_status);

                    // Loop through the result to display amount details along with creation date
                    while($row_status = mysqli_fetch_assoc($result_status)) {
                        echo "<li>Status: " . $row_status['status'] . " (Created at: " . date('Y-m-d', strtotime($row_status['statuscreated_at'])) . ")</li>";
                    }
                ?>
            </ol>
        </div>
        <div class="col-md-6">
            <h5>Amount Details</h5>
            <ol>
                <?php 
                    // Query to fetch amount details
                    $sql_amount = "SELECT amount, amountcreated_at FROM amount WHERE lead_id = '$id'";
                    $result_amount = mysqli_query($conn, $sql_amount);

                    // Loop through the result to display amount details along with creation date
                    while($row_amount = mysqli_fetch_assoc($result_amount)) {
                        echo "<li>Amount: ksh:/" . $row_amount['amount'] . " (Created at: " . date('Y-m-d', strtotime($row_amount['amountcreated_at'])) . ")</li>";
                    }
                ?>
            </ol>
        </div>
    </div>
</main>

<script>
    document.getElementById('nameTable').addEventListener('click', function(event) {
        var button = event.target.closest('[data-lead-details-toggle]');
        if (!button) {
            return;
        }

        var expanded = button.getAttribute('aria-expanded') !== 'true';
        var row = button.closest('tr');
        var detailsRow = row.nextElementSibling;
        var label = expanded ? 'Hide additional lead details' : 'Show additional lead details';

        detailsRow.hidden = !expanded;
        button.setAttribute('aria-expanded', String(expanded));
        button.setAttribute('aria-label', label);
        button.querySelector('.lead-details-visually-hidden').textContent = label;
        button.querySelector('[aria-hidden="true"]').textContent = expanded ? '-' : '+';
    });
</script>

<script>
    var changesMade = false;
    var textarea = document.getElementById('marketernote');
    var form = document.getElementById('marketernoteForm');

    function resizeNoteToContent() {
        var styles = window.getComputedStyle(textarea);
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        var font = styles.font || [
            styles.fontStyle,
            styles.fontVariant,
            styles.fontWeight,
            styles.fontSize,
            styles.fontFamily
        ].join(' ');
        var maxLineWidth = Math.max.apply(null, textarea.value.split(/\r?\n/).map(function (line) {
            context.font = font;
            return context.measureText(line).width;
        }));
        var horizontalPadding = parseFloat(styles.paddingLeft) + parseFloat(styles.paddingRight);
        var borders = parseFloat(styles.borderLeftWidth) + parseFloat(styles.borderRightWidth);
        var minWidth = parseFloat(styles.minWidth);
        var maxWidth = Math.min(parseFloat(styles.maxWidth), window.innerWidth * 0.48);
        var contentWidth = Math.min(maxWidth, Math.max(minWidth, maxLineWidth + horizontalPadding + borders + 24));

        textarea.style.width = contentWidth + 'px';
        textarea.style.height = 'auto';
        textarea.style.height = Math.max(textarea.scrollHeight, parseFloat(styles.minHeight)) + 'px';
    }

    resizeNoteToContent();
    window.addEventListener('resize', resizeNoteToContent);
    textarea.addEventListener('input', function() {
        changesMade = true;
        resizeNoteToContent();
    });

    document.addEventListener('click', function(event) {
        if (changesMade && event.target !== textarea && !textarea.contains(event.target)) {
            form.submit();
            changesMade = false;
        }
    });
</script>

<script>
    function goBack() {
        window.history.back();
    }
</script>


<?php include "../templates/footer.php";  // Load the footer.php template. ?>
