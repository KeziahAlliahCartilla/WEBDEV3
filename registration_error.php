<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <!-- Include necessary CSS and JS libraries -->
    <!-- ... -->
</head>
<body>
    <div class="container">
        <h1>Registration Error</h1>
        <?php
        session_start();
        if (isset($_SESSION["registration_error"])) {
            echo '<p>' . $_SESSION["registration_error"] . '</p>';
            unset($_SESSION["registration_error"]); // Clear the error message
        }
        ?>
        <p>Please go back to the <a href="index.html">registration page</a> and try again.</p>
    </div>
</body>
</html>
