<?php include "../templates/header.php";

include "../database/db.php";

$location = '../views/pending.php';

$id = $_GET['id'];
$sql = "SELECT * FROM login WHERE id = '$id';";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        
    $id = $row['id'];
    $name = $row['username'];
    $email = $row['email'];
    $department = $row['department'];   
    $designation = $row['designation'];    
    }
} else {
    
}

?>

<div class="container mt-5">
    <?php
        if(!empty($_GET['message'])) {
            $message = $_GET['message'];
            if($message == 'success'){
             echo '<h3 class="alert alert-danger">Marketer deleted succesfully</h3>';
            } else {
             echo '<h3 class="alert alert-success">Marketer Updated succesfully</h3>';
            }
        } else {
    
        }
    ?>
    <h5>Edit <?php echo $name ?> Details</h5>
    <hr />

    <form class='row' action="../models/editmarketer.php" method="post">
        <input type="number" class='form-control d-none' value="<?php echo $id ?>" name="id" id="id" required>
        <div class="col">
            <lable class='form-label'>Name</lable>
            <input type="text" class='form-control' value="<?php echo $name ?>" name="name" value="<?php echo $name ?>" placeholder="<?php echo $name ?>" id="name" required>
        </div>

        <div class="col">
            <lable class='form-label'>Email</lable>
            <input type="email" class='form-control' value="<?php echo $email ?>" name="email" value="<?php echo $email ?>"  placeholder="<?php echo $email ?>" id="email" required>
        </div>

        <div class='col'>
            <lable class='form-label'>Designation</lable>
            <select class='form-control' name="designation" id="designation" required>
                <option value="" disabled selected>Select Designation</option>
                <option value="admin" <?php if ($designation == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="md" <?php if (strtolower($designation) == 'md') echo 'selected'; ?>>MD</option>
                <option value="user" <?php if ($designation == 'user') echo 'selected'; ?>>User</option>
            </select>
        </div>

        <div class="col">
            <lable class='form-label'>Department</lable>
            <select class='form-control' name="department" id="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="Sales" <?php if ($department == 'Sales') echo 'selected'; ?>>Sales</option>
                <option value="Marketing" <?php if ($department == 'Marketing') echo 'selected'; ?>>Marketing</option>
                <option value="Finance" <?php if ($department == 'Finance') echo 'selected'; ?>>Finance</option>
            </select>
        </div>

        <div class="col">
            <lable class='form-label'>Password</lable>
            <div class="input-group">
                <input class='form-control' type="password" name="password" placeholder="password" id="password" required>
                <div class="input-group-append">
                    <span class="input-group-text" id="toggle-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col">
            <button class='btn btn-warning mt-4' type='submit'>Update</button>
        </div>

    </form>

<script>
    function DeleteConfirm() {
        confirm("Are you sure to delete the record");
    }

    document.getElementById("toggle-password").addEventListener("click", function () {
        var passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    });
</script>


<?php include "../templates/footer.php";  ; // Load the footer.php template. ?>