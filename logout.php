<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect the user to the confirmation page
header("Location: logout_confirmation.php");
exit; // Ensure that no further code is executed after redirection
?>
