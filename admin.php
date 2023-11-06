<?php
session_start();

// Checking the user's role for authorization
if ($_SESSION["Role"] === null) {
    header("Location: index.html");
    exit;
} elseif ($_SESSION["Role"] !== "admin") {
    header("Location: admin.php");
    exit;
}

// Establishing a connection to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sd208";

$conn = new mysqli($servername, $username, $password, $dbname);

// Handling connection errors if any
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching user data from the database
$sql = "SELECT Fullname, Email, Role, Status, Gender, UserID FROM users";
$result = $conn->query($sql);

// Fetching gender data for the pie chart
$genderSql = "SELECT gender, COUNT(*) as count FROM users WHERE gender IN ('male', 'female') GROUP BY gender";
$genderResult = $conn->query($genderSql);
$genderData = array();

while ($row = $genderResult->fetch_assoc()) { 
    $genderData[] = $row;
}

// Fetching activity counts by month for the bar chart
// Initializing an array to store activity counts by month
$activityCountsByMonth = array_fill(1, 12, 0);

$activityCountSql = "
SELECT 
    MONTH(date) AS month, 
    COUNT(*) AS count 
FROM 
    activities 
WHERE 
    DATE_FORMAT(date, '%Y-%m') BETWEEN '2023-01' AND '2023-12'
GROUP BY 
    MONTH(date)
";

// Retrieving the result set from the database based on the SQL query
$activityCountResult = $conn->query($activityCountSql);

// Iterating through each row of the result set
while ($row = $activityCountResult->fetch_assoc()) {
    // Extracting the month and the count from the current row
    $month = $row['month'];
    $count = $row['count'];
    
    // Storing the count in the respective month's index in the array
    $activityCountsByMonth[$month] = $count;
}
// Initializing an array to store the data in the desired format for the chart
$activityCountData = array();

// Iterating through each month and storing the month and corresponding count in the array
for ($month = 1; $month <= 12; $month++) {
    $activityCountData[] = array(
        'month' => $month,
        'count' => $activityCountsByMonth[$month],
    );
}


// Handling different POST requests based on form submissions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_announcement'])) {
        $announcementTitle = $_POST['announcement_title'];
        $announcementContent = $_POST['announcement_content'];

        $insertSql = "INSERT INTO announcements (title, content) VALUES ('$announcementTitle', '$announcementContent')";
        if ($conn->query($insertSql) === TRUE) {
            header("Location: admin.php"); 
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif (isset($_POST['edit_announcement'])) {
        $announcementId = $_POST['announcement_id'];
        $editedTitle = $_POST['edited_title'];
        $editedContent = $_POST['edited_content'];
    
        // Preparing an SQL statement to update the 'announcements' table with the new title and content for the provided announcement ID
        $updateSql = "UPDATE announcements SET title=?, content=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $editedTitle, $editedContent, $announcementId);
    
        if ($stmt->execute()) {
            header("Location: admin.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    
        $stmt->close();
    } elseif (isset($_POST['delete_announcement'])) {
        $announcementId = $_POST['announcement_id'];

        $deleteSql = "DELETE FROM announcements WHERE id=$announcementId";
        if ($conn->query($deleteSql) === TRUE) {
            header("Location: admin.php"); 
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];

        // Preparing an SQL statement to delete the user with the given ID from the 'users' table
        $deleteSql = "DELETE FROM users WHERE ID=?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            header("Location: admin.php"); 
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500;1,600&display=swap" rel="stylesheet">
<style>
    body{
        display:flex;
        flex-direction:row;
    }
    .table{
        width:100%;
    }
    .canvas{
        border-radius:20px;
        width:25%;
        position:fixed;
        left:80rem;
        top:4.5rem;
        background: #FFF;
        box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;

    }
    .content{
        background:#D8D9DA;
        display:flex;
        justify-content:center;
        width:100%;
    }
    .users{
        padding:none;
        width:100%;
        height:50vh;
        max-height:100%;


    }
    .left{
        margin:1%;
        padding:2%;
        width:60%;
        box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.5) -3px -3px 6px 1px inset;
        background:#FFF;
        border-radius:20px;
    }
    .dashboard{ 
        margin:none;
        padding:0;
        width:15%;
        

    }
    .bottom a{
        color:#FFF;
        font-weight:bold;

    }
    .bottom{
        height:94vh;
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
  background-color: #B76E78;
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
    .top{
        font-family: 'Poppins', sans-serif;
        font-size:1.5rem;
        display: flex;
        justify-content:center;
        align-items:center;
        padding:1%;
        background: #1B1811;
        color:#FFF;
        border-bottom: solid 2px #B76E78;
    }
    .nav{
        background:#FFF;
        display: flex;
        justify-content:space-between;
        padding:13px;
    }
    .icn{
        display: flex;
        justify-content:center;
        flex-direction:row;
        gap:1rem;
    }
    .pic img{
        border:#2937f0 solid 2px;
        width:20px;
        border-radius:40px;
    }
    .wrap{
        width:100rem;
        padding:1%;
        display:flex;
        justify-content:center;
        align-items:center;
        flex-direction:column;
        gap:1rem;
    }
    .list {
        width:100%;
        max-width:90%;
        background-color: white;
        border: 2px solid #F4C2C1; 
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: black; /* Purple */
        color: white;
    }

    td {
        background-color: white;
        color: #000;
    }

    .delete-announcement-button {
        background-color: #B76E78; /* Purple */
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s;
        border-radius: 5px;
    }

    .delete-announcement-button:hover {
        background-color: #7b639b; /* Darker purple */
    }

    .list table {
        animation: fadeIn 0.5s ease-in-out;
    }

    /* Add a subtle animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .manage{
        border-radius:10px;
        max-width:100%;

    }
    .manageAnnouncement{
        animation: fadeIn 0.5s ease-in-out;
        width:100%;
        display: grid;
        grid-template-columns: 39rem 39rem;
        column-gap: 20px;    
    }
    .manageAnnouncement form[method="post"]{
        width:100%;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        border-radius:20px;
        border:20px;
        padding:20px;
        background:#FFF;
        box-shadow: rgb(204, 219, 232) 3px 3px 6px 0px inset, rgba(255, 255, 255, 0.
        5) -3px -3px 6px 1px inset;
    }
    .manageAnnouncement input[type="text"],textarea{
        width:100%;
        border-top:none;
        border-right:none;
        border-left:none;
        outline:none;
        text-align:center;
    }
    #addbut, #edbut{
        background-color: #B76E78; /* Purple */
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s;
        border-radius: 5px;
    }
    .delete-button:hover, #addbut:hover, #edbut:hover{
        background-color: #7b639b; /* Darker purple */
    }
    .delete-button{
        background-color: #B76E78; /* Darker purple */
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s;
        border-radius: 5px;

    }
    #activities{
        width:60%;
    }
    .x_panel {
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 20px;
            padding: 15px;
            background-color: #fff;
            transition: transform 0.2s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        /* Styles for the x_title */
        .x_title {
            background: #B76E78;
            color: #fff;
            padding: 10px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        /* Styles for the chart container */
        .x_content {
            padding: 10px;
        }

        /* CSS animations for x_panel on hover */
        .x_panel:hover {
            transform: scale(1.02);
        }
</style>
</head>
<body onload="myFunction()">
    <div class="dashboard">
        <div class="top">
            <p>DASHBOARD</p>
        </div>
        <div class="bottom">
            <a class="nav-link nav-link-ltr" onclick="user()" href="#users">Users</a>
            <a class="nav-link nav-link-ltr" onclick="announcement()" href="#announcements" >Announcement</a>
            <a class="nav-link nav-link-ltr" onclick="activities()" href="#activities" >Activities</a>
            <a class="nav-link nav-link-ltr" href="index.html" >Logout</a>
        </div>
    </div>

    <div class="content">
        <div id="users" class="users">
            <div class="nav">
                <div>
                    <div class="menu">
                    <span class="material-symbols-outlined">
                        menu
                    </span>
                    </div>
                </div>
                <div class="icn">
                    <div>
                        <span class="material-symbols-outlined">
                            notifications
                        </span>
                    </div>
                    <div>
                        <span class="material-symbols-outlined">
                            list
                        </span>
                    </div>
                    <div>
                        <span class="material-symbols-outlined">
                            mail
                        </span>
                    </div>
                    <div class="pic">
                        <img src="https://images.unsplash.com/photo-1630305131239-c8df91783f10?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y3V0ZSUyMGJhYnl8ZW58MHx8MHx8fDA%3D&w=1000&q=80" alt="Profile">
                    </div>
                </div>
            </div>

                <div class="canvas">
                    <canvas id="genderChart" width="100" height="100"></canvas>
                </div>
                

            <div class="left">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Gender</th>
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo '<td class="editable" data-field="Fullname" data-userid="' . $row["UserID"] . '">' . $row["Fullname"] . '</td>';
                                    echo '<td class="editable" data-field="Email" data-userid="' . $row["UserID"] . '">' . $row["Email"] . '</td>';
                                    echo '<td class="editable" data-field="Role" data-userid="' . $row["UserID"] . '">' . $row["Role"] . '</td>';
                                    echo '<td class="editable" data-field="Status" data-userid="' . $row["UserID"] . '">' . $row["Status"] . '</td>';
                                    echo '<td class="editable" data-field="Gender" data-userid="' . $row["UserID"] . '">' . $row["Gender"] . '</td>';
                                    echo '<td>';
                                    echo '<button class="delete-button" data-rowid="' . $row["UserID"] . '">Delete</button>';
                                    echo '</td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No members/users found.</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>



        <div id="announcements" class="announcements">
            <div class="wrap">
           
                <div class="list">
                    <?php
                    $announcementsSql = "SELECT id, title, content FROM announcements";
                    $announcementsResult = $conn->query($announcementsSql);

                    if ($announcementsResult->num_rows > 0) {
                        echo "<table>";
                        echo "<tr>";
                        echo "<th>ID</th>";
                        echo "<th>Title</th>";
                        echo "<th>Content</th>";
                        echo "<th>Action</th>";

                        echo "<th>Action</th>";

                        echo "</tr>";

                        while ($row = $announcementsResult->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["title"] . "</td>";
                            echo "<td>" . $row["content"] . "</td>";
                            echo '<td><button class="delete-announcement-button" data-announcementid="' . $row["id"] . '">Delete</button></td>';
                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "<p>No announcements found.</p>";
                    }
                    ?>
                </div>
                 <div class="manage">
                    <div class="manageAnnouncement">
                        <form method="post">
                            <label for="announcement_title">Title:</label>
                            <input type="text" name="announcement_title" id="announcement_title" required><br>
                            <label for="announcement_content">Content:</label>
                            <textarea name="announcement_content" id="announcement_content" required></textarea><br>
                            <input id="addbut" type="submit" name="add_announcement" value="Add Announcement">
                        </form>
                        <form method="post">
                            <label for="announcement_id">Announcement ID:</label>
                            <input type="text" name="announcement_id" id="announcement_id" required>
                            <label for="edited_title">Title:</label>
                            <input type="text" name="edited_title" id="edited_title" required><br>
                            <label for="edited_content">Content:</label>
                            <textarea name="edited_content" id="edited_content" required></textarea><br>
                            <input id="edbut" type="submit" name="edit_announcement" value="Edit Announcement">
                        </form>
                    </div>
                 </div>
            </div>
        </div>


        
        <div id="activities" class="x_panel">
            <div class="x_title">
                <h2>Bar Graph for Activities</h2>
                <ul class="nav navbar-right panel_toolbox" style="padding-left:5%">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li>
                    <a class="close-link"><i class="fa fa-close"></i></a>
                </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <canvas id="activityBarChart"></canvas>
            </div>
            </div>

    </div>

    <script>
        function myFunction() {
            let user = document.getElementById("users");
            let announcements = document.getElementById("announcements");
            let activities = document.getElementById("activities");

            user.style.display ="block";
            announcements.style.display ="none";
            activities.style.display ="none";
        }
        function user(){
            let user = document.getElementById("users");
            let announcements = document.getElementById("announcements");
            let activities = document.getElementById("activities");

            user.style.display ="block";
            announcements.style.display ="none";
            activities.style.display ="none";

        }
        function announcement(){
            let user = document.getElementById("users");
            let announcements = document.getElementById("announcements");
            let activities = document.getElementById("activities");

            user.style.display ="none";
            announcements.style.display ="block";
            activities.style.display ="none";

        }

        function canvas(){
            let user = document.getElementById("users");
            let announcements = document.getElementById("announcements");
            let activities = document.getElementById("activities");

            user.style.display ="none";
            announcements.style.display ="none";
            activities.style.display ="none";

        }
        function activities(){
            let user = document.getElementById("users");
            let announcements = document.getElementById("announcements");
            let activities = document.getElementById("activities");

            user.style.display ="none";
            announcements.style.display ="none";
            activities.style.display ="block";

        }

        // Creating the gender chart using the Chart.js library
        const genderLabels = <?php echo json_encode(array_column($genderData, 'gender')); ?>;
        const genderValues = <?php echo json_encode(array_column($genderData, 'count')); ?>;

        const ctx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: genderLabels, // label
                datasets: [{
                    data: genderValues, // value
                    backgroundColor: ['	#DFc5fe', '#87ceeb'], 
                }]
            },
        });

        // Creating the activity bar chart using the Chart.js library
        const activityMonths = <?php echo json_encode(array_column($activityCountData, 'month')); ?>;
        const activityCounts = <?php echo json_encode(array_column($activityCountData, 'count')); ?>;

        const activityCtx = document.getElementById('activityBarChart').getContext('2d');
        const activityBarChart = new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: activityMonths, // Labels for the x-axis
                datasets: [{
                    label: 'Activity Count', // Label for the dataset
                    data: activityCounts, // Values for the bar chart
                    backgroundColor: '#B76E78',  // Background color for the bars
                    borderColor: '#F4C2C1', // Border color for the bars
                    borderWidth: 1 // Border width for the bars
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Count'
                        }
                    }
                }
            }
        });

        // Handling the deletion of user records using an AJAX request
        document.querySelectorAll('.delete-button').forEach(function (deleteButton) {
            deleteButton.addEventListener('click', function () {
                var rowId = this.getAttribute('data-rowid');
                
                if (confirm("Are you sure you want to delete user with ID: " + rowId)) {
                    // Send an AJAX request to delete the user record
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete_user.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Successful response from the server
                            alert(xhr.responseText); 
                            var row = deleteButton.parentNode.parentNode; // Assuming a specific HTML structure
                            row.parentNode.removeChild(row);
                        }
                    };
                    xhr.send("user_id=" + rowId);
                }
            });
        });

        document.querySelectorAll('.editable').forEach((cell) => {
            cell.addEventListener('click', () => {
                const field = cell.getAttribute('data-field');
                const userId = cell.getAttribute('data-userid');

                // Make the cell content editable
                cell.contentEditable = true;
                cell.focus();

                // Save the original content for later comparison
                const originalContent = cell.textContent;

                // Listen for blur event to save changes
                cell.addEventListener('blur', () => {
                    const newValue = cell.textContent;

                    // Check if the content has changed
                    if (newValue !== originalContent) {
                        // Send an AJAX request to update the database with the new value
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_user.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                // Successful response from the server
                                alert(xhr.responseText);
                            }
                        };
                        xhr.send(`field=${field}&value=${newValue}&userId=${userId}`);
                    }

                    // Make the cell non-editable again
                    cell.contentEditable = false;
                });
            });
        });
        document.querySelectorAll('.delete-announcement-button').forEach(function (deleteButton) {
            deleteButton.addEventListener('click', function () {
                var announcementId = this.getAttribute('data-announcementid');

                if (confirm("Are you sure you want to delete announcement with ID: " + announcementId)) {
                    // Send an AJAX request to delete the announcement
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "delete_announcement.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Successful response from the server
                            alert(xhr.responseText);
                            // Optionally, remove the deleted row from the table
                            var row = deleteButton.parentNode.parentNode;
                            row.parentNode.removeChild(row);
                        }
                    };
                    xhr.send("announcement_id=" + announcementId);
                }
            });
        });

    </script>
</body>

</html>

