<?php 

include "../templates/header.php";
include "../database/db.php";

$location = '../views/marketers.php';

$username = $_SESSION['name'];
$period = isset($_GET['period']) ? $_GET['period'] : 0;
$duration = isset($_GET['duration']) ? $_GET['duration'] : '';

$marketerredirect = '';

$id = $_GET['id'];
$sql = "SELECT * FROM leads WHERE id = '$id';";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        
    $id = $row['id'];
    $clientName = $row['clientName'];
    $clienttype = $row['clienttype'];
    $marketer = $row['marketer']; 
    $marketerredirect = $row['marketer']; 
    $intouch = $row['intouch']; 
    $phone = $row['phone']; 
    $location = $row['location']; 
    $product = $row['product']; 
    $status = $row['status']; 
    $project = $row['project']; 
    $possibility = $row['possibility']; 
    $duration = $row['duration']; 
    $amount = $row['amount']; 
    $connectivity = $row['connectivity']; 
    $task = $row['task']; 
    $marketernote = $row['marketernote']; 
    $created_at = $row['created_at']; 
    }
} else {
    
}

$backUrl = "../views/marketers.php?username=" . urlencode($marketerredirect) . "&period=" . urlencode($period);
if ($duration == 'long') {
    $backUrl .= "&duration=long";
}

?>

<div class="container mt-5">
    <?php
        if(!empty($_GET['message'])) {
            $message = $_GET['message'];
            if($message == 'success'){
             echo '<h3 class="alert alert-danger">Lead Updated succesfully</h3>';
            } else {
             echo '<h3 class="alert alert-success">Deleted Updated succesfully</h3>';
            }
        } else {
    
        }
    ?>
    <a href="<?php echo $backUrl; ?>" class="btn btn-sm btn-primary float-end">Back</a>
    <!-- <a href="http://localhost/pergascrm/views/marketers.php?username=<?php echo $marketerredirect; ?>&period=<?php echo $period; ?>" class="btn btn-sm btn-primary float-end">Back</a> -->
    <h6>Edit Lead</h6>
    <hr />

    <form class="row" action="../models/editlead.php" method='post'>
        <input type="text" class='form-control d-none' value="<?php echo $username ?>" name="username" id="username" required>
        <input type="number" class='form-control d-none' value="<?php echo $id ?>" name="id" id="id" required>
        
        <div class="col-md-3">
            <label class="form-label">Date</label>
            <input class="form-control me-2 my-2" name="date" type="date" max="<?php echo date('Y-m-d'); ?>" placeholder="Date">
        </div>

        <div class="col-md-3">
            <label class="form-label">Client</label>
            <input class="form-control me-2 my-2" value='<?php echo $clientName ?>' name="name" type="text" placeholder="<?php echo $clientName ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Phone</label>
            <input class="form-control me-2 my-2" value='<?php echo $phone ?>' name="phone" type="number" placeholder="<?php echo $phone ?>">
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Client Type</label>
            <select class='form-control me-2 my-2' name="clienttype" place required>
                <option value="" disabled selected>Client Type</option>
                <option value="company" <?php if ($clienttype == 'company') echo 'selected'; ?>>Company</option>
                <option value="individual" <?php if ($clienttype == 'individual') echo 'selected'; ?>>Individual</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Type of Connectivity</label>
            <select class='form-control me-2 my-2' name="connectivity" place required>
                <option value="" disabled selected>Type of Connectivity</option>
                <option value="visit" <?php if ($connectivity == 'visit') echo 'selected'; ?>>Visit</option>
                <option value="referal" <?php if ($connectivity == 'referal') echo 'selected'; ?>>Referal</option>
                <option value="assigned" <?php if ($connectivity == 'assigned') echo 'selected'; ?>>Assigned</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Person in Touch</label>
            <select class='form-control me-2 my-2' name="intouch" place required>
                <option value="" disabled selected>Person in Touch</option>
                <option value="forman" <?php if ($intouch == 'forman') echo 'selected'; ?>>Forman</option>
                <option value="engineer" <?php if ($intouch == 'engineer') echo 'selected'; ?>>Engeener</option>
                <option value="owner" <?php if ($intouch == 'owner') echo 'selected'; ?>>Owner</option>
                <option value="contractor" <?php if ($intouch == 'contractor') echo 'selected'; ?>>Contactor</option>
                <option value="other" <?php if ($intouch == 'other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Project</label>
            <select class='form-control me-2 my-2' name="project" place required>
                <option value="" disabled selected>Project Type</option>
                <option value="villa" <?php if ($project == 'villa') echo 'selected'; ?>>Villa</option>
                <option value="flat" <?php if ($project == 'flat') echo 'selected'; ?>>Flat</option>
                <option value="commercial" <?php if ($project == 'commercial') echo 'selected'; ?>>Commercial</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class='form-control me-2 my-2' name="status" place required>
                <option value="" disabled selected>Status</option>
                <option value="visited" <?php if ($status == 'visited') echo 'selected'; ?>>Visited</option>
                <option value="revisited" <?php if ($status == 'revisited') echo 'selected'; ?>>Re-visited</option>
                <option value="bought" <?php if ($status == 'bought') echo 'selected'; ?>>Bought</option>
                <option value="quoted" <?php if ($status == 'quoted') echo 'selected'; ?>>Quoted</option>
                <option value="partialy" <?php if ($status == 'partialy') echo 'selected'; ?>>Partialy bought</option>
                <option value="showroom" <?php if ($status == 'showroom') echo 'selected'; ?>>Visit Showroom</option>
                <option value="failed" <?php if ($status == 'failed') echo 'selected'; ?>>Failed</option>
            </select>
        </div>

        <div class="col-md-3 d-none">
            <input class="form-control me-2 my-2" name="marketer" value='<?php echo $marketer?>' type="text" placeholder="<?php echo $marketer?>">
        </div>


        <div class="col-md-3">
            <label class="form-label">Location</label>
            <input class="form-control me-2 my-2" value='<?php echo $location ?>' name="location" type="text" placeholder="<?php echo $location ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Deal Possibility</label>
            <select class='form-control me-2 my-2' name="possibility" type="text" placeholder="Deal Possibility">
                <option value="" disabled selected>Deal Possibility</option>
                <option value="high" <?php if ($possibility == 'high') echo 'selected'; ?>>High</option>
                <option value="moderate" <?php if ($possibility == 'moderate') echo 'selected'; ?>>Moderate</option>
                <option value="low" <?php if ($possibility == 'low') echo 'selected'; ?>>Low</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Duration</label>
            <select class='form-control me-2 my-2' name="duration" type="text" required>
                <option value="" disabled selected>Duration</option>
                <option value="short" <?php if ($duration == 'short') echo 'selected'; ?>>Short</option>
                <option value="mid" <?php if ($duration == 'mid') echo 'selected'; ?>>Mid</option>
                <option value="long" <?php if ($duration == 'long') echo 'selected'; ?>>Long</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Amount</label>
            <input class="form-control me-2 my-2" value='<?php echo $amount ?>' name="amount" type="text" placeholder="value='<?php echo $amount ?>'">
        </div>

        <div class="col-md-3"> 
            <label class="form-label">Marketer Note</label>
            <textarea class="form-control me-2 my-2" value='<?php echo $marketernote ?>' name="marketernote" id="marketernote" cols="20" rows="4"> <?php echo $marketernote ?> </textarea> 
        </div>

        <?php if($admindesc): ?>
            <div class="col-md-4"> 
                <label class="form-label">Task</label>
                <textarea class="form-control me-2 my-2" value='<?php echo $task ?>' name="task" id="task" cols="20" rows="4"> <?php echo $task ?> </textarea> 
            </div>        
        <?php endif; ?>

        <div class="col-md-4">
            <label class="form-label">Product</label>
            <select class='form-control me-2 my-2' name="product[]" place required multiple>
                <option value="" disabled>Product</option>
                <option value="iron">Iron</option>
                <option value="tiles" selected>Tiles</option>
                <option value="paint">Paint</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <div class="col-md-3"><button class="btn btn-warning my-2" type="submit">Update</button></div>
    </form>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

<?php include "../templates/footer.php";  ; // Load the footer.php template. ?>