<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);

if ( isset($_GET["id"])) {
    $id = $_GET["id"];

    //Connection to the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "my_employees";

    //Create a connection to the database
    $connection = new mysqli($servername, $username, $password, $database);
    
    $sql = "DELETE FROM employees WHERE id=$id";
    $connection->query($sql);
}

header("location: /my_employees/index.php");
exit;
?>
