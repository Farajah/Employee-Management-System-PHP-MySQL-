<?php
// Display PHP Error(Development/ Debugging only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include Database Connection
require_once __DIR__ . "/config/database.php";

// Helper Function to escape output(Prevents XSS)
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Pagination Settings

$recordsPerPage = 5; // rows per page

// Count the total rows in the table 
// check if the url has a page parameter and use that as the current page, if no default to page 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Calculate the total pages needed 
$totalResult = $connection->query("SELECT COUNT(*) AS total FROM employees");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $recordsPerPage); // Ceil rounds up, 1 extra row gets a new page

// Fetch the rows for the current page 
$sql = "SELECT id, name, email, phone, address, created_at 
        FROM employees 
        ORDER BY created_at DESC 
        LIMIT $recordsPerPage OFFSET $offset";

$result = $connection->query($sql);
if (!$result) {
    die("Query failed: " . $connection->error);
}

/*********************************************************************
 * ****************Fetch Employees from database (No Pagination)*********

// Fetch Employees from database (No Pagination)
$sql = "SELECT id, name, email, phone, address, created_at FROM employees";
$result = $connection->query($sql);

if (!$result) {
    die("Query failed: " . $connection->error);
}
*///////////////////////////////////////////////////////////////////////////
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Page header: title + action button aligned using Flexbox -->
                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <!-- Page title -->
                            <h2 class="mb-0">List of Employees</h2>

                            <!-- Navigate to create employee -->
                            <a class="btn btn-primary" href="/my_employees/create.php">
                                Add Employee
                            </a>

                        </div>


                        <!-- Employees table -->
                        <div class="table-responsive"> <!-- Mobile friendly -->
                            <table class="table table-bordered table-hover">

                                <!-- Table headers -->
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Created At</th>

                                        <!-- Fixed width for action buttons -->
                                        <th style="width: 150px;">Actions</th>
                                    </tr>
                                </thead>

                                <!-- Table body (rows rendered dynamically) -->
                                <tbody>


                                    <?php if ($result->num_rows === 0): ?>
                                        <!-- No records found -->
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                No employees found.
                                            </td>
                                        </tr>

                                    <?php else: ?>
                                        <!-- Loop through employees -->
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <!-- Safe output (XSS protection) -->
                                                <td>
                                                    <?= e($row['id']) ?>
                                                </td>
                                                <td>
                                                    <?= e($row['name']) ?>
                                                </td>
                                                <td>
                                                    <?= e($row['email']) ?>
                                                </td>
                                                <td>
                                                    <?= e($row['phone']) ?>
                                                </td>
                                                <td>
                                                    <?= e($row['address']) ?>
                                                </td>
                                                <td>
                                                    <?= e($row['created_at']) ?>
                                                </td>
                                                <td>
                                                    <!-- Action buttons -->
                                                    <?php $id = (int)$row['id']; ?>
                                                    <a class="btn btn-primary btn-sm" href="/my_employees/edit.php?id=<?= $id ?>">Edit</a>
                                                    <a class="btn btn-danger btn-sm" href="/my_employees/delete.php?id=<?= $id ?>">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination UI -->
                    <nav aria-label="Employee pagination" class="mt-3">
                        <ul class="pagination justify-content-center mt-4">

                            <!-- Previous button -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                            </li>

                            <!-- Page numbers -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next button -->
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<?php
// Free resources and close connection
$result->free();
$connection->close();
?>