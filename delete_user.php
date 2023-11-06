<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "sd208";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        

        $deleteSql = "DELETE FROM users WHERE UserID = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            echo "User deleted.";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();
    } else {
        echo "User ID not provided.";
    }
} else {
    echo "Invalid request.";
}
?>
