<?php
require_once __DIR__ . '/../utils/permissions.php';

if (!canManageMarketers()) {
    http_response_code(403);
    exit('You are not authorized to add marketers.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed.');
}

$username = trim($_POST['username'] ?? '');
$department = $_POST['department'] ?? '';
$email = trim($_POST['email'] ?? '');
$designation = strtolower(trim($_POST['designation'] ?? ''));
$password = $_POST['password'] ?? '';
$departments = ['Sales', 'Marketing', 'Finance'];
$designations = ['admin', 'md', 'user'];

if (
    $username === ''
    || !in_array($department, $departments, true)
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !in_array($designation, $designations, true)
    || $password === ''
) {
    http_response_code(400);
    exit('Please provide valid marketer details.');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if ($hashedPassword === false) {
    throw new RuntimeException('Unable to secure the marketer password.');
}

require_once __DIR__ . '/../database/db.php';

$check = $conn->prepare('SELECT id FROM login WHERE username = ?');
$check->bind_param('s', $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    header('Location: ../views/addmarketer.php?message=userexists&search=' . rawurlencode($username));
    exit;
}
$check->close();

$insert = $conn->prepare(
    'INSERT INTO login (username, department, designation, email, password, updated_at) VALUES (?, ?, ?, ?, ?, ?)'
);
$updatedAt = '';
$insert->bind_param('ssssss', $username, $department, $designation, $email, $hashedPassword, $updatedAt);
$insert->execute();
$insert->close();

$conn->close();
header('Location: ../views/addmarketer.php?message=useradded');
exit;
