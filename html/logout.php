<?php

// Start the current session.
session_start();

// Remove all session data.
session_unset();

// Destroy the session completely.
session_destroy();

// Send the user back to the home page.
header("Location: index.php");
exit();

?>