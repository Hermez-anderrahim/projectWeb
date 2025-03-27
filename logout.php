<?php
// Start session if not already started
if(!isset($_SESSION)) {
    session_start();
}

// Destroy session
session_destroy();

// Redirect to home page
header('Location: index.php');
exit;
?>