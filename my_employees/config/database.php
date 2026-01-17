
<?php
// Database connection Logic only
    $connection = new mysqli("localhost", "root", "", "my_employees");

    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }
?>