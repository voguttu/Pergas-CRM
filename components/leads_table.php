<?php

function renderLeadsTable($result, $admindesc, $period, $duration, $enableExport = false)
{
    $colCount = $enableExport ? 20 : 19;

    echo "<div class='leads-table-scroll mt-3' id='leadsTableScroll'>
            <div class='leads-table-scroll__controls'>
                <button type='button' class='btn btn-outline-secondary btn-sm leads-table-scroll__btn' id='scrollTableLeft' title='Scroll left' aria-label='Scroll table left'>
                    <i class='fa-solid fa-chevron-left'></i>
                </button>
                <button type='button' class='btn btn-outline-secondary btn-sm leads-table-scroll__btn' id='scrollTableRight' title='Scroll right' aria-label='Scroll table right'>
                    <i class='fa-solid fa-chevron-right'></i>
                </button>
                <span class='leads-table-scroll__hint'>Scroll sideways using the bar below, arrow buttons, Shift + mouse wheel, or click-drag on the table.</span>
            </div>
            <div class='leads-table-scroll__top' id='leadsTableScrollTop' aria-hidden='true'>
                <div class='leads-table-scroll__top-inner' id='leadsTableScrollTopInner'></div>
            </div>
            <div class='table-container leads-table-wrap' id='leadsTableScrollWrap'>
            <table class='leads-table' id='nameTable'>
                <thead>
                    <tr>";

    if ($enableExport) {
        echo "<th scope='col' class='leads-table__check-col' title='Select for export'>
                <label class='lead-checkbox lead-checkbox--header' title='Select all visible leads for export'>
                    <input type='checkbox' id='selectAllExport' class='lead-checkbox__input'>
                    <span class='lead-checkbox__box'></span>
                </label>
              </th>";
    }

    echo "          <th scope='col' class='leads-table__col--number'>#</th>
                        <th scope='col' class='leads-table__check-col' title='Mark lead'>Mark</th>
                    <th scope='col' class='leads-table__col--marketer'>Marketer</th>
                    <th scope='col' class='leads-table__col--client'>Client</th>
                    <th scope='col' class='leads-table__col--client-type'>Client Type</th>
                    <th scope='col' class='leads-table__col--phone'>Phone</th>
                    <th scope='col' class='leads-table__col--location'>Location</th>
                    <th scope='col' class='leads-table__col--intouch'>Intouch</th>
                    <th scope='col' class='leads-table__col--project'>Project</th>
                    <th scope='col' class='leads-table__col--product'>Product</th>
                    <th scope='col' class='leads-table__col--status'>Status</th>
                    <th scope='col' class='leads-table__col--possibility'>Possibility</th>
                    <th scope='col' class='leads-table__col--duration'>Duration</th>
                    <th scope='col' class='leads-table__col--amount'>Amount</th>
                    <th scope='col' class='leads-table__col--connectivity'>Connectivity</th>
                    <th scope='col' class='leads-table__col--date'>Date</th>
                    <th scope='col' class='leads-table__col--date'>Created Date</th>
                    <th scope='col' class='leads-table__col--date'>Updated Date</th>
                    <th scope='col' class='leads-table__col--actions'>Actions</th>
                    </tr>
                </thead>
                <tbody>";

    $rowNumber = 1;
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = (int) $row['id'];
            $status = $row['status'];
            $marketer = htmlspecialchars($row['marketer'], ENT_QUOTES, 'UTF-8');
            $clientNameValue = (string) $row['clientName'];
            $clientName = htmlspecialchars($clientNameValue, ENT_QUOTES, 'UTF-8');
            $clientSearchName = htmlspecialchars(strtolower($clientNameValue), ENT_QUOTES, 'UTF-8');
            $leadDate = date('Y-m-d', strtotime($row['date']));
            $amount = htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8');
            $isMarked = ($row['radio_status'] === '1');

            $statusClasses = array(
                'visited' => 'visited',
                'revisited' => 'revisited',
                'bought' => 'bought',
                'quoted' => 'quoted',
                'partialy' => 'partialy',
                'showroom' => 'showroom',
                'failed' => 'failed',
            );
            $class = isset($statusClasses[$status]) ? $statusClasses[$status] : '';

            echo "<tr class='table-$class' data-lead-status='$class' data-lead-date='" . htmlspecialchars($leadDate, ENT_QUOTES, 'UTF-8') . "'>";

            if ($enableExport) {
                echo "<td class='leads-table__check-col'>
                        <label class='lead-checkbox' title='Select for export'>
                            <input type='checkbox' class='lead-checkbox__input export-lead-checkbox' value='$id' data-lead-id='$id'>
                            <span class='lead-checkbox__box'></span>
                        </label>
                      </td>";
            }

            echo "      <td>$rowNumber</td>
                        <td class='leads-table__check-col'>
                            <form action='../models/update_radio_status.php' method='post' class='lead-radio-form'>
                                <input type='hidden' name='id' value='$id'>
                                <input type='hidden' name='marketer' value='$marketer'>
                                <label class='lead-checkbox lead-checkbox--mark' title='Mark lead'>
                                    <input type='checkbox' name='radio_status' value='1' class='lead-checkbox__input'" . ($isMarked ? ' checked' : '') . " onchange='this.form.submit()'>
                                    <span class='lead-checkbox__box'></span>
                                </label>
                            </form>
                        </td>
                        <td>$marketer</td>
                        <td class='lead-client-name' data-client-name='$clientSearchName'>$clientName</td>
                        <td>" . htmlspecialchars($row['clienttype']) . "</td>
                        <td>" . htmlspecialchars($row['phone']) . "</td>
                        <td>" . htmlspecialchars($row['location']) . "</td>
                        <td>" . htmlspecialchars($row['intouch']) . "</td>
                        <td>" . htmlspecialchars($row['project']) . "</td>
                        <td>" . htmlspecialchars($row['product']) . "</td>
                        <td>
                            <form action='../models/update_status.php' method='post'>
                                <input type='hidden' name='id' value='$id'>
                                <input type='hidden' name='marketer' value='$marketer'>
                                <select style='width: 10rem' class='form-control status-dropdown' name='status' onchange='this.form.submit()'>
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
                        <td>" . htmlspecialchars($row['possibility']) . "</td>
                        <td>" . htmlspecialchars($row['duration']) . "</td>
                        <td>
                            <form action='../models/update_amount.php' method='post'>
                                <input type='hidden' name='id' value='$id'>
                                <input type='hidden' name='marketer' value='$marketer'>
                                <input style='width: 10rem' type='number' class='form-control amount-input' name='amount' value='$amount'>
                            </form>
                        </td>
                        <td>" . htmlspecialchars($row['connectivity']) . "</td>
                        <td>$leadDate</td>
                        <td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>
                        <td>" . date('Y-m-d', strtotime($row['updated_at'])) . "</td>
                        <td>
                            <a class='btn btn-info btn-sm' href='../views/viewlead.php?id=$id'>View</a>";

            if ($admindesc) {
                echo "      <div class='d-flex'>
                                <a class='btn btn-warning btn-sm mx-1' href='../views/editlead.php?id=$id&period=$period&duration=$duration'>Edit</a>
                                <a class='btn btn-danger btn-sm mx-1' onclick='return DeleteConfirm()' href='../models/deletelead.php?id=$id&marketer=$marketer'>Delete</a>
                            </div>";
            }

            echo "      </td>
                    </tr>";
            $rowNumber++;
        }
    } else {
        echo "<tr><td colspan='$colCount'>Zero Leads available</td></tr>";
    }

    echo "      </tbody>
            </table>
            </div>
        </div>";
}

?>
