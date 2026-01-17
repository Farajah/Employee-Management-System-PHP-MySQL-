<?php
session_start();

// -------------------------
// Error reporting (dev only)
// -------------------------
ini_set('display_errors', 1);
error_reporting(E_ALL);

// -------------------------
// Database connection
// -------------------------
require_once __DIR__ . "/config/database.php";



// -------------------------
// Escape output (XSS protection)
// -------------------------
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// -------------------------
// Initialize variables
// -------------------------
$id = $name = $email = $phone = $address = "";
$errorMessage = "";

// -------------------------
// CSRF token
// -------------------------
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// -------------------------
// GET: Load employee data
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: /my_employees/index.php");
        exit;
    }
    $id = (int) $_GET['id'];

    $stmt = $connection->prepare(
        "SELECT name, email, phone, address FROM employees WHERE id = ? LIMIT 1"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        header("Location: /my_employees/index.php");
        exit;
    }
    // Populate form fields
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
    $address = $row['address'];
}
// -------------------------
// POST: Update employee
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF validation
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $id = (int) $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Validation
    if (!$name || !$email || !$phone || !$address) {
        $errorMessage = "All fields are required.";
    } else {
        // Update record
        $stmt = $connection->prepare(
            "UPDATE employees SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?"
        );
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);

        if (!$stmt->execute()) {
            $errorMessage = "Database error: " . $stmt->error;
            $stmt->close();
        } else {
            $stmt->close();
            header("Location: /my_employees/index.php");
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-sm">
                    <div class="card-body">

                        <h3 class="mb-4">Edit Employee</h3>

                        <!-- Error message -->
                        <?php if ($errorMessage): ?>
                            <div class="alert alert-warning alert-dismissible fade show">
                                <?= e($errorMessage) ?>
                                <button class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post">

                            <!-- Hidden fields -->
                            <input type="hidden" name="id" value="<?= e($id) ?>">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <!-- Name -->
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="<?= e($name) ?>">
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" name="email" value="<?= e($email) ?>">
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input class="form-control" name="phone" value="<?= e($phone) ?>">
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input class="form-control" name="address" value="<?= e($address) ?>">
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary">Update</button>
                                <a href="/my_employees/index.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS (bottom for performance) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>