<?php
session_start();

if ($_SESSION["Role"] === null) {
    header("Location: index.html");
    exit;
} elseif ($_SESSION["Role"] !== "admin") {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement_id'])) {
    $announcementId = $_POST['announcement_id'];
    $editedTitle = $_POST['edited_title'];
    $editedContent = $_POST['edited_content'];

    // Prepare and execute the update query
    $updateSql = "UPDATE announcements SET title=?, content=? WHERE id=?";
    $stmt = $conn->prepare($updateSql);

    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        exit;
    }

    $stmt->bind_param("ssi", $editedTitle, $editedContent, $announcementId);

    if ($stmt->execute()) {
        echo "Announcement updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
