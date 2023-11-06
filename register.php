<?php
session_start();
include_once("included/dbaccess/DBUtil.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST["Fullname"];
    $email = $_POST["Email"];
    $password = password_hash($_POST["Password"], PASSWORD_BCRYPT); // Hash the password
    $role = $_POST["Role"];
    $status = $_POST["Status"];
    $gender = $_POST["Gender"];

    // input validation
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $_SESSION["registration_error"] = "Invalid email address.";
        header("Location: registration_error.php");
        exit();
    }

    // Check if the email already exists in the database
    $conn = getConnection();
    $check_email_sql = "SELECT COUNT(*) AS email_count FROM users WHERE Email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row["email_count"] > 0) {
        $_SESSION["registration_error"] = "Email address is already registered.";
        header("Location: registration_error.php");
        exit();
    } else {
        // Insert user data into the database using prepared statements
        $insert_sql = "INSERT INTO users (Fullname, Email, Password, Role, Status, Gender) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssssss", $fullname, $email, $password, $role, $status, $gender);

        if ($stmt->execute()) {
            // Registration successful, redirect the user to a success page
            header("Location: index.html");
            exit();
        } else {
            // Registration failed, redirect the user to an error page
            header("Location: registration_error.php");
            exit();
        }
    }

    closeConnection();
} else {
    header("Location: registration_error.php");
    exit();
}
