<?php
require_once __DIR__ . '/../utils/permissions.php';
if (!canManageMarketers()) {
    http_response_code(403);
    exit('You are not authorized to manage marketers.');
}

include "../templates/header.php";
include "../database/db.php";

$sql = "SELECT id, username, email, department, designation, created_at
        FROM login
        ORDER BY username ASC, id ASC";
$result = mysqli_query($conn, $sql);
$userCount = mysqli_num_rows($result);
$searchTerm = trim((string) ($_GET['search'] ?? ''));
?>

<div class="container mt-5">
    <?php
    $messages = [
        'success' => ['alert-danger', 'Marketer deleted successfully'],
        'updated' => ['alert-warning', 'Marketer updated successfully'],
        'userexists' => ['alert-warning', 'Username already exists. Search the list below to find the existing account.'],
        'useradded' => ['alert-success', 'Marketer added successfully'],
    ];
    $message = $messages[$_GET['message'] ?? ''] ?? null;
    if ($message !== null) {
        echo '<h3 class="alert ' . $message[0] . '" id="success-message">' . $message[1] . '</h3>';
        echo '<script>
            setTimeout(function(){
              document.getElementById("success-message").style.display = "none";
            }, 5000);
          </script>';
    }
    ?>

    <h4>Marketers &amp; Users</h4>
    <p>All CRM accounts are listed below, including admin, MD, and user accounts.</p>
    <hr />

    <form class='row' action="../models/addmarketer.php" method="post">
        <div class='col-md-2 mt-2'>
            <input class='form-control' type="text" name="username" placeholder="username" id="username" required>
        </div>

        <div class='col-md-2 mt-2'>
            <select class='form-control' name="department" id="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="Sales">Sales</option>
                <option value="Marketing">Marketing</option>
                <option value="Finance">Finance</option>
            </select>
        </div>

        <div class='col-md-2 mt-2'>
            <input class='form-control' type="email" name="email" placeholder="email" id="email" required>
        </div>

        <div class='col-md-2 mt-2'>
            <select class='form-control' name="designation" id="designation" required>
                <option value="" disabled selected>Select Designation</option>
                <option value="admin">Admin</option>
                <option value="md">MD</option>
                <option value="user">User</option>
            </select>
        </div>

        <div class='col-md-2 mt-2'>
            <div class="input-group">
                <input class='form-control' type="password" name="password" placeholder="password" id="password" required>
                <div class="input-group-append">
                    <span class="input-group-text" id="toggle-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-2 mt-2">
            <button type='submit' class='btn btn-success' value='Adduser'>Add Marketer</button>
        </div>
    </form>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
        <h5 class="mb-0">All CRM users</h5>
        <label class="visually-hidden" for="user-search">Search CRM users</label>
        <input
            class="form-control"
            style="max-width: 22rem"
            type="search"
            id="user-search"
            placeholder="Search users by name, email, department, or role"
            value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
        >
    </div>
    <p class="text-muted mt-2" aria-live="polite">
        Showing <span id="visible-user-count"><?php echo $userCount; ?></span> of
        <span id="total-user-count"><?php echo $userCount; ?></span> users
    </p>

    <div class='table-container mt-3'>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Department</th>
                    <th scope="col">Designation</th>
                    <th scope="col">Created</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($userCount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id = (int) $row['id'];
                        echo "
                        <tr data-user-row>
                            <td>" . htmlspecialchars($row['username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($row['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($row['department'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($row['designation'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($row['created_at'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>
                            <td>
                                <a class='btn btn-warning btn-sm' href='../views/editmarketer.php?id=$id' >Edit</a>
                                <a class='btn btn-danger btn-sm' onclick='return DeleteConfirm()' href='../models/deleteuser.php?id=$id' >Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo '<tr><td colspan="6">No CRM users found.</td></tr>';
                }
                ?>
                <tr id="no-user-matches" hidden>
                    <td colspan="6">No users match your search.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function DeleteConfirm() {
        if (confirm("Are you sure you want to delete user")) {
            return true; // Proceed with deletion
        } else {
            return false; // Cancel deletion
        }
    }

    document.getElementById("toggle-password").addEventListener("click", function () {
        var passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    });

    const userSearch = document.getElementById("user-search");
    const userRows = Array.from(document.querySelectorAll("[data-user-row]"));
    const visibleUserCount = document.getElementById("visible-user-count");
    const noUserMatches = document.getElementById("no-user-matches");

    function filterUsers() {
        const search = userSearch.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        userRows.forEach(function (row) {
            const matches = row.textContent.toLocaleLowerCase().includes(search);
            row.hidden = !matches;
            if (matches) {
                visibleCount++;
            }
        });

        visibleUserCount.textContent = visibleCount;
        noUserMatches.hidden = visibleCount > 0 || userRows.length === 0;
    }

    userRows.forEach(function (row) {
        row.dataset.userRow = "";
    });
    userSearch.addEventListener("input", filterUsers);
    filterUsers();
</script>

<?php include "../templates/footer.php";  ; // Load the footer.php template. ?>
