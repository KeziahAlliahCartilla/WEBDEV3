<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sd208";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $date = $time = $location = $ootd = "";

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function show_all_activities($conn) {
    $sql = "SELECT * FROM activities ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="show">';
        
        echo '<table class="activity-records">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Date</th>';
        echo '<th>Time</th>';
        echo '<th>Location</th>';
        echo '<th>OOTD</th>';
        echo '<th>Status</th>';
        echo '<th>Action</th>';
        echo '</tr>';
    
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['date'] . '</td>';
            echo '<td>' . $row['time'] . '</td>';
            echo '<td>' . $row['location'] . '</td>';
            echo '<td>' . $row['ootd'] . '</td>';
            echo '<td>' . $row['status'] . '</td>';
            echo '<td><a href="edit_activity.php?id=' . $row['id'] . '">Edit</a></td>';

            echo '</tr>';
        }
    
        echo '</table>';
        echo '</div>';

    } else {
        echo "No activities found.";
    }
}

function get_activity_details($conn, $activity_id) {
    $activity_id = sanitize_input($activity_id);
    $sql = "SELECT * FROM activities WHERE id = $activity_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["add_activity"])) {
        $name = sanitize_input($_POST["name"]);
        $date = sanitize_input($_POST["date"]);
        $time = sanitize_input($_POST["time"]);
        $location = sanitize_input($_POST["location"]);
        $ootd = sanitize_input($_POST["ootd"]);

        $sql = "INSERT INTO activities (name, date, time, location, ootd) VALUES ('$name', '$date', '$time', '$location', '$ootd')";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Activity added successfully.");</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST["set_status"])) {
        $activity_id = sanitize_input($_POST["activity_id"]);
        $status = sanitize_input($_POST["status"]);

        $sql = "UPDATE activities SET status='$status' WHERE id=$activity_id";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Activity status updated successfully.");</script>';
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }   elseif (isset($_POST["update_activity"])) {
        $activity_id = sanitize_input($_POST["activity_id"]);
        $name = sanitize_input($_POST["name"]);
        $date = sanitize_input($_POST["date"]);
        $time = sanitize_input($_POST["time"]);
        $location = sanitize_input($_POST["location"]);
        $ootd = sanitize_input($_POST["ootd"]);

        $sql = "UPDATE activities SET 
                name='$name', date='$date', time='$time', location='$location', ootd='$ootd' 
                WHERE id=$activity_id";

        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Activity updated successfully.");</script>';
        } else {
            echo '<script>alert("Error updating activity: ' . $conn->error . '");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Life Activities Manager</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<style>
    @import "https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic&subset=latin,cyrillic";

    .right{
        width:90%;
        max-width:90%;
        display:flex;
        flex-direction:column;
    }
    .body{
        background:#FFF;
        display:flex;
        flex-direction:row;
    }

    .activity-records {
        width:100%;
    }

    table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    }
    th{
        background:#1B1811;
        color:#fff;
    }
    td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
    }

    tr:nth-child(even) {
    background-color: #fff;
    }
    .dashboard a{
    font-weight: bold;
    text-decoration: none;
    color: black;
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }
    .dashboard a:hover{
        color:#b66078;
    }
    .dashboard{
        height:auto;
        width:auto ;
        padding: 1%;
        display: flex;
        flex-direction: column;
    }
    #show{
        width:100%;
        background:#fff;

    }
    #show a{
        display:flex;
        align-items:center;
        justify-content:center;
        color:black;
        text-decoration:none;
        border-radius:10px;
        padding:2px;
        border:none;
    }

    .dashboard{ 
        margin:none;
        padding:0;
        width:15%;
        

    }
    .bottom a{
        color:#FFF;
        font-weight:300;
        display:flex;
        flex-direction:row;
        align-items:center;

    }
    .bottom{
        height:90.5vh;
        padding:3%;
        background:#1B1811;
        display: flex;
        flex-direction:column;
        width:100%;
    }
    .nav-link {
  font-weight: bold;
  font-size: 14px;
  text-transform: uppercase;
  text-decoration: none;
  color: #031D44;
  padding: 20px 0px;
  margin: 0px 20px;
  display: inline-block;
  position: relative;
  opacity: 0.75;
}

.nav-link:hover {
  opacity: 1;
}

.nav-link::before {
  transition: 300ms;
  height: 5px;
  content: "";
  position: absolute;
  background-color: #031D44;
}

.nav-link-ltr::before {
  width: 0%;
  bottom: 10px;
}

.nav-link-ltr:hover::before {
  width: 100%;
}

.nav-link-fade-up::before {
  width: 100%;
  bottom: 5px;
  opacity: 0;
}

.nav-link-fade-up:hover::before {
  bottom: 10px;
  opacity: 1;
}

.nav-link-grow-up::before {
  height: 0%;
  width: 100%;
  bottom: 0px;
}

.nav-link-grow-up:hover::before {
  height: 5px;
}

    #add{
        
        box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;
        width:70%;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        background:#FFF;
        padding:5%;
        border-radius:20px;
        background:#FAF9F6;

    }
    #add form,#setstat form{

        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        background:transparent;
        
    }
    #setstat{
        background:#FAF9F6;
        padding:5%;
        border-radius:20px;
        box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;


    }
    #add form input[type="text"],
    input[type="date"],
    input[type="time"]{
        border-left:none;
        border-right:none;
        border-top:none;
        padding:10px;
        width:100%;
        background:none;
        outline:none;
    }
    .right{
        background:transparent;
        display:flex;
        display:flex;
        flex-direction:column;
    }
    .botSide{
        width:100%;
    }
    #show{
        width:100%;
        padding:1% 0% 0 0;
        display:flex;
        flex-direction:row;
        justify-content:space-evenly;
        gap:1rem;
    }
    .show{
        scroll-behavior:smooth;
        height: 89vh;
        padding:5%;
        max-height:100vh;
        overflow:scroll;
    }
    .show form{
        width:100%;

    }
    .topSide{
        padding:10px;
        width:100%;
        background:#fff;
        display:flex;
        justify-content:flex-end;
    }
    .topSide img{
        width:50px;
        border:5px solid black;
        border-radius:40px;
    }
    #setstat form{
        display:flex;
        flex-direction:row;
        gap:2rem;
    }

    #setstat form input[type="text"],select{
        padding:10px;
    }
    #setstat form input[type="text"]{
        border-left:none;
        border-right:none;
        border-top:none;
        background:none;
        outline:none;
    }
    #setstat form input[type="submit"]{
        padding:5px;
        border-radius:10px;
        background:#B76E78;
        color:#FFF;
    }
    #setstat h2{
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }
    .acts{
        display:flex;
        flex-direction:column;
        gap:1rem;
        justify-content:center;
        align-items:center;
    }
    .uppernav{
        padding:20px;
        display:flex;
        flex-direction:row;
        justify-content:space-between;
        box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;

    }
    .notifs{
        display:flex;
        flex-direction:row;
        justify-content:space-between;
        align-items:center;
        gap:1rem;
    }
    .logo{
        display:flex;
        flex-direction:row;
        justify-content:space-between;
        width:28%;
    }
    .myLogo{
        font-size:1.5rem;
        font-weight:bold;
    }
    .notifs{
        display:flex;
        flex-direction:row;
        justify-content:space-between;
    }
    .searchIcon{
        display:flex;
        flex-direction:row;
        justify-content:space-between;
        align-items:center;
        gap:1rem;

    }
    .searchengine{
        display:flex;
        flex-direction:row;
        align-items:center;
        border:solid 2px black;
        border-radius:20px;
        font-size:11rem;
        padding:6px;
    }
    .searchengine input[type="text"]{
        font-size:20px;
        border:none;
        outline:none;
        border-radius:20px;

    }
    .userName{
        font-weight:bold;
    }
    add form{
        border:1px solid #000;
    }
    #submit-btn{
        padding:5px;
        border-radius:20px;
        background:#B76E78;
        color:#FFF;
    }
</style>
<body>
    <nav class="uppernav">
        <div class="logo">
            <div class="myLogo">
                Keziah
            </div>
            <div class="searchIcon">
                <div class="burger">
                    <span class="material-symbols-outlined">
                    menu
                    </span>
                </div>
                <div class="search">
                    <div class="searchengine">
                        <input type="text" placeholder="search...">
                        <span class="material-symbols-outlined" style>
                        search
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="notifs">
            <div class="notification">
                <span class="material-symbols-outlined">
                notifications
                </span>
            </div>
            <div class="messages">
                <span class="material-symbols-outlined">
                chat
                </span>
            </div>
            <div class="profile">
                <div class="userName">USER</div>
                <div class="userPic"></div>
            </div>
        </div>
    </nav>
    <div class="body">
    <div class="dashboard">
        <div class="bottom">
            
            <a class="nav-link nav-link-ltr" onclick="showActivities()" href="#">
                <span class="material-symbols-outlined">
                home
                </span>
                show Activities
            </a>
            <a class="nav-link nav-link-ltr" href="index.html">
                <span class="material-symbols-outlined">
                first_page
                </span>
                Logout
            </a>
        </div>
    </div>
    <div class="right">
        <div id="show">
            <?php
            show_all_activities($conn);
            ?>
            <br>
            <div class="acts">
                <div id="setstat">
                    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                        <input type="text" name="activity_id" placeholder="Activity ID" required>
                        <select name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                        <input type="submit" name="set_status" value="Set Status">
                    </form>
                </div> 
                <div id="add">
                    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                        <input type="text" placeholder="Activity Name" name="name" required><br>
                        <input type="date" placeholder="Date" name="date" required><br>
                        <input type="time" placeholder="Time" name="time" required><br>
                        <input type="text" placeholder="Location" name="location" required><br>
                        <input type="text" placeholder="OOTD" name="ootd" required><br>
                        <!-- Add a section to select users to invite using checkboxes -->
                        <input id="submit-btn" type="submit" name="add_activity" value="Add Activity">
                    </form>
                </div>  
            </div>
        
        </div>
    </div>
    </div>
    <?php $conn->close(); ?>
</body>
<script>
   
</script>
</html>
