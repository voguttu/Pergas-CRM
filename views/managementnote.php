<?php 

include "../templates/header.php";  // Load the header.php template. 

include "../database/db.php";

$sql = "SELECT id, note, created_at FROM managementnotes ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

?>

<div class="container mt-5">

<?php
  if (!empty($_GET['message'])) {
    $message = $_GET['message'];
    if ($message == 'noteadded') {
      echo '<h3 class="alert alert-success" id="success-message">Note Added successfully</h3>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    } else if($message == 'deleted') {
      echo '<h3 class="alert alert-danger" id="success-message">Note deleted successfully</h3>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    } else if($message == 'updated') {
      echo '<h3 class="alert alert-success" id="success-message">Note updated successfully</h3>';
      echo '<script>
              setTimeout(function(){
                document.getElementById("success-message").style.display = "none";
              }, 5000);
            </script>';
    }
  }
  ?>


  <?php if($admindesc): ?>
    <div>
    <h5>Add Note</h5>
      <hr />

      <form class='row' action="../models/addnote.php" method="post">
          <div class='col'>
              <textarea class='form-control' name="note" id="note" cols="30" rows="5"></textarea>
          </div>
          <div class="">
              <button type='submit' class='btn btn-success mt-3'>Add Note</button>
          </div>
      </form>
    </div>
  <?php endif; ?>

    <div class='mt-3'>  
    <h6>Notes</h6>
    <hr />
    <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">Date</th>
        <th scope="col">Note</th>
        <?php if($admindesc): ?>
          <th scope="col">Actions</th> <!-- Add this column header -->
        <?php endif; ?>
      </tr>
    </thead>
      <tbody>
        <?php
          $rowNumber = 1;
          if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
              $id = $row['id'];
              echo "
              <tr>
                <td>" . $rowNumber . "</td>
                <td>".($row['created_at'])."</td>
                <td>".($row['note'])."</td>";
                if($admindesc) {
                  echo "<td>
                          <a class='btn btn-warning btn-sm' href='../views/editnote.php?id=$id' >Edit</a>
                          <a class='btn btn-danger btn-sm' onclick='return DeleteConfirm()' href='../models/deletenote.php?id=$id' >Delete</a>
                        </td>";
                }
              echo "</tr>";
              $rowNumber++;
            }
          } else {
            echo "<tr><td>"."Zero Notes available"."</td></tr>";
          }
        
        ?>
      </tbody>
    </table>
    </div>
</div>

<script>
    function DeleteConfirm() {
        if (confirm("Are you sure you want to delete note")) {
            return true; // Proceed with deletion
        } else {
            return false; // Cancel deletion
        }
    }
</script>

<?php include "../templates/footer.php";  // Load the header.php template. ?>
