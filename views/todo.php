<?php 
include "../templates/header.php";  // Load the header.php template. 
include "../database/db.php";

$username = $_SESSION['name'];
$userid = $_SESSION['id'];


// Fetch data for reminders
$sql = "SELECT * FROM remindernotes WHERE user = '$userid' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Fetch client names and locations from leads table
$leads_sql = "SELECT clientName, location FROM leads WHERE marketer = '$username' ORDER BY clientName ASC";
$leads_result = mysqli_query($conn, $leads_sql);

?>

<div class="container mt-5">

<?php
  // Display messages based on the 'message' query parameter
  if (!empty($_GET['message'])) {
    $message = $_GET['message'];
    if ($message == 'noteadded') {
      echo '<h6 class="alert alert-success" id="success-message">Reminder Added successfully</h6>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    } else if($message == 'deleted') {
      echo '<h6 class="alert alert-danger" id="success-message">Reminder deleted successfully</h6>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    } else if($message == 'notemarked') {
      echo '<h6 class="alert alert-danger" id="success-message">Reminder Marked successfully</h6>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    } else if($message == 'updated') {
      echo '<h6 class="alert alert-success" id="success-message">Reminder updated successfully</h6>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    }
  }
?>

<div>
  <h5>Add Reminder</h5>
  <hr />

  <form class='row' action="../models/reminder.php" method="post">
      <div class='col-md-6'>
          <textarea class='form-control' required name="note" id="note" cols="30" rows="5"></textarea>
      </div>
      <div class='col-md-6'>
          <!-- Searchable Dropdown for Client -->
          <select class='form-control' required name='client' id="client">
              <option value="">Select Client</option>
              <?php
                // Populate dropdown with client names and locations
                while($lead = mysqli_fetch_assoc($leads_result)) {
                  $clientName = $lead['clientName'];
                  $location = $lead['location'];
                  echo "<option value='$clientName'>$clientName - $location</option>";
                }
              ?>
          </select>
          <input type="date" required class='form-control mt-3' name='reminderdate'>
          <input type="number" required class='form-control mt-3 d-none' name='user' value='<?php echo $userid?>'>
          
          <button type='submit' class='btn btn-success btn-sm mt-3'>Add Reminder</button>
      </div>
  </form>
</div>

<div class='mt-3'>  
  <h6>Reminders</h6>
  <hr />
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">Date Reminer</th>
        <th scope="col">Client</th>
        <th scope="col">Note</th>
        <th scope="col">Created</th>
        <?php if($admindesc): ?>
          <th scope="col">Actions</th> 
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php
        $rowNumber = 1;
        $today = date('Y-m-d'); // Assuming $today is in 'Y-m-d' format
        if (mysqli_num_rows($result) > 0) {
          // Output data of each row
          while($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $reminderDate = $row['reminderdate'];
            $status = $row['status'];

            // Check if the reminder date is today or in the past
            if ($status == 1) {
              $backgroundColor = 'green'; // Status 1 reminders
            } elseif ($reminderDate < $today) {
                $backgroundColor = 'blue'; // Past reminders
            } elseif ($reminderDate == $today) {
                $backgroundColor = 'red'; // Today's reminders
            } else {
                $backgroundColor = ''; // Future reminders (no color)
            }
          

            echo "
            <tr>
              <td>" . $rowNumber . "</td>
              <td>
                  <form action='../models/todostatus.php' method='post'>
                      <input type='hidden' name='id' value='$id'>
                      <input type='checkbox' name='status' value='1' onchange='this.form.submit()' " . ($row['status'] === "1" ? 'checked' : '') . ">
                  </form>
              </td>
              <td style='color: $backgroundColor;'>".($row['reminderdate'])."</td>
              <td>".($row['client'])."</td>
              <td style='color: $backgroundColor;'>".($row['note'])."</td>
              <td>".($row['created_at'])."</td>
              <td>
                <a class='btn btn-danger btn-sm' onclick='return DeleteConfirm()' href='../models/deletereminder.php?id=$id' >Delete</a>
              </td>"; 
            echo "</tr>";
            
            $rowNumber++;
          }
        } else {
          echo "<tr><td colspan='4'>Zero Reminders available</td></tr>";
        }
      ?>
    </tbody>
  </table>
</div>
</div>

<script>
  function DeleteConfirm() {
      return confirm("Are you sure you want to delete note?");
  }

  // Optionally, add Select2 or other library for searchable dropdown
  document.addEventListener("DOMContentLoaded", function() {
      const clientSelect = document.getElementById("client");
      clientSelect.addEventListener("input", function() {
          const filter = clientSelect.value.toUpperCase();
          for (let option of clientSelect.options) {
              option.style.display = option.text.toUpperCase().includes(filter) ? "" : "none";
          }
      });
  });
</script>

<?php include "../templates/footer.php";  // Load the footer.php template. ?>
