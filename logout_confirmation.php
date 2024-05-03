<style>
    .container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin:20%;

    }
    
    h2 {
        color: #273a89;
    }

    .container a {
        display: inline-block;
        margin:20px;
        background-color: #273a89;
        color: white;
        padding: 14px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
    }

    .container > div:first-child {
        background-color: gray;
    }
    .container a:hover {
        background-color: #45a049;
    }
    
    div.container, form {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }
</style>
<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    
    // User is logged in, display confirmation message
    echo "<div class='container'><h2>Are you sure you want to logout?</h2>";
    echo "<div><a href='logout.php'>Yes, Logout</a>";
    echo "<a href='dashboard.php'>No, Go Back to Dashboard</a></div></div>";
} else {
    // User is not logged in, redirect to login page
    header("Location: signin.php");
    exit;
}
?>
