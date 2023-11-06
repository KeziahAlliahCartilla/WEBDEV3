<?php
session_start();

if ($_SESSION["Role"] === null || $_SESSION["Role"] !== "admin") {
    // Redirect unauthorized access
    header("Location: admin.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sd208";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'];
    $newValue = $_POST['value'];
    $userId = $_POST['userId'];

    // Validate and sanitize input
    $field = mysqli_real_escape_string($conn, $field);
    $newValue = mysqli_real_escape_string($conn, $newValue);
    $userId = (int)$userId;

    // Define the SQL update statement based on the field
    $updateSql = "UPDATE users SET $field = '$newValue' WHERE UserID = $userId";

    if ($conn->query($updateSql) === TRUE) {
        echo "User data updated successfully";
    } else {
        echo "Error updating user data: " . $conn->error;
    }
}

$conn->close();
?>
