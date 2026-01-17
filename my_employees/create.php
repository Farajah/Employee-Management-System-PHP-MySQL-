<?php

// Display PHP Errors (Development only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include Database Connection
require_once __DIR__ . "/config/database.php";

// Escape output (XSS protection)
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Initialize variables
$name = $email = $phone = $address = "";
$errorMessage = "";
$successMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trim input values
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    do {
        // -------------------------
        // Field Validation
        // -------------------------
        if ($name === '' || $email === '' || $phone === '' || $address === '') {
            $errorMessage = "All fields are required.";
            break;
        }
        // -------------------------
        // Check duplicate email
        // -------------------------
        $checkStmt = $connection->prepare(
            "SELECT id FROM employees WHERE email = ? LIMIT 1"
        );
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errorMessage = "This email already exists.";
            $checkStmt->close();
            break;
        }
        $checkStmt->close();

        // -------------------------
        // Insert employee
        // -------------------------
        $stmt = $connection->prepare(
            "INSERT INTO employees (name, email, phone, address) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $name, $email, $phone, $address);

        if (!$stmt->execute()) {
            $errorMessage = "Database error: " . $stmt->error;
            $stmt->close();
            break;
        }

        $stmt->close();

        // Redirect after success (PRG pattern)
        header("Location: /my_employees/index.php");
        exit;

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Card wrapper -->
                <div class="card shadow-sm">
                    <div class="card-body">

                        <!-- Page title -->
                        <h3 class="mb-4">New Employee</h3>

                        <!-- Error message -->
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-warning alert-dismissible fade show">
                                <?= e($errorMessage) ?>
                                <button class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Create form -->
                        <form method="post">

                            <!-- CSRF token (security) -->
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <!-- Name -->
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?= e($name) ?>">
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($email) ?>">
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= e($phone) ?>">
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="<?= e($address) ?>">
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="/my_employees/index.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS (placed at bottom for faster page load) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>