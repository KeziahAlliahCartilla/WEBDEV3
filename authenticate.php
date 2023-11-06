<?php
session_start();
include_once("included/dbaccess/DBUtil.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["Username"];
    $password = $_POST["Password"];
    
    
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // User authentication successful
        $row = $result->fetch_assoc();
        
        // Store user information in session variables
        $_SESSION["UserID"] = $row["UserID"];
        $_SESSION["Username"] = $row["Email"];
        $_SESSION["Role"] = $row["Role"];

        // Redirect the user based on their role
        if ($row["Role"] == "admin") {
            header("Location: admin.php");
        } else {
            header("Location: user.php");
        }
        
        // Always exit after redirection
        exit();
    } else {
        // Invalid credentials
        header("Location: index.html"); // Redirect to a login error page or display an error message
        exit();
    }
    
    // Close the database connection
    closeConnection();
}
