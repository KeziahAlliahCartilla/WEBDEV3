<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sd208";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$activity_id = $_GET['id'];

if (isset($_POST['update_activity'])) {
    $name = $_POST['actname'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $ootd = $_POST['ootd'];

    $sql = "UPDATE activities SET name='$name', date='$date', time='$time', location='$location', ootd='$ootd' WHERE id=$activity_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: user.php"); 
        exit;
    } else {
        echo "Error updating activity: " . $conn->error;
    }
}

$sql = "SELECT * FROM activities WHERE id = $activity_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $activity = $result->fetch_assoc();
} else {
    echo "Activity not found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Activity</title>
</head>
<style>
    *{
        padding:0;
        margin:0;
    }
    .editSec{
        display:flex;
        justify-content:center;
        align-items:center;
        flex-direction:column;
        height:100vh;
        background:#1B1811;
    }
    form{
        box-shadow: 0 13px 13px 13px;
        width:30%;
        padding:20px;
        border-radius:20px;
        background:#F4C2C1;
        display:flex;
        justify-content:center;
        align-items:center;
        flex-direction:column;
    }
    input[type="text"],input[type="time"],input[type="date"]{
        outline:none;
        padding:10px;
        border-left:none;
        border-right:none;
        border-top:none;
        background:transparent;
        color:#000;
        text-align:center;
    }
    input[type="submit"]{
        padding:8px;
        border-radius:10px;
        text-transform:uppercase;
        border:none;
        box-shadow: 0 3px 13px 1px;
        background:#B76E78;

    }
</style>
<body>
    <div class="editSec">
        <form method="post" action="edit_activity.php?id=<?php echo $activity_id; ?>">
            <input type="text" placeholder="Activity Name" name="actname" value="<?php echo $activity['name']; ?>" required><br>
            <input type="date" placeholder="Date" name="date" value="<?php echo $activity['date']; ?>" required><br>
            <input type="time" placeholder="Time" name="time" value="<?php echo $activity['time']; ?>" required><br>
            <input type="text" placeholder="Location" name="location" value="<?php echo $activity['location']; ?>" required><br>
            <input type="text" placeholder="OOTD" name="ootd" value="<?php echo $activity['ootd']; ?>" required><br>
            <input type="submit" name="update_activity" value="Update Activity">
        </form>
    </div>
</body>
</html>
