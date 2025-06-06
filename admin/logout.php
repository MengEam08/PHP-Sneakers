<?php
session_start();
session_unset();
session_destroy();

// Redirect to login page inside admin folder (relative path)
header("Location: login.php");
exit();
?>
