<?php
if (isset($_POST['announcement_id'])) {
    $announcementId = $_POST['announcement_id'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sd208";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sd208";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $deleteSql = "DELETE FROM announcements WHERE id=?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $announcementId);

    if ($stmt->execute()) {
        echo "Announcement with ID: " . $announcementId . " has been deleted successfully.";
    } else {
        echo "Error deleting announcement: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Announcement ID not provided in the request.";
}
?>
