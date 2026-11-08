<?php include "../templates/header.php";

include "../database/db.php";

$location = '../views/pending.php';

$id = $_GET['id'];
$sql = "SELECT * FROM managementnotes WHERE id = '$id';";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        
    $id = $row['id'];
    $note = $row['note'];   
    }
} else {
    
}

?>

<div class="container mt-5">
    <?php
        if(!empty($_GET['message'])) {
            $message = $_GET['message'];
            if($message == 'success'){
             echo '<h3 class="alert alert-danger">Deleted deleted succesfully</h3>';
            } else {
             echo '<h3 class="alert alert-success">Deleted Updated succesfully</h3>';
            }
        } else {
    
        }
    ?>
    <h5>Edit Note</h5>
    <hr />

    <form class='row' action="../models/editnote.php" method="post">
        <input type="number" class='form-control d-none' value="<?php echo $id ?>" name="id" id="id" required>
        <div class='col'>
            <textarea class='form-control' name="note" id="note" cols="30" rows="5"><?php echo $note ?></textarea>
        </div>
        <div class="">
            <button type='submit' class='btn btn-warning mt-3'>Update Note</button>
        </div>
    </form>


<?php include "../templates/footer.php";  ; // Load the footer.php template. ?>