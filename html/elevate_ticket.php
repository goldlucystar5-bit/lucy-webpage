<?php

// Start the session so we can check whether a support agent is logged in.
session_start();

// Make sure only logged-in support agents can elevate tickets.
if (!isset($_SESSION["agent_id"])) {
    header("Location: support_login.php");
    exit();
}

// Connect to the database.
require_once "config.php";

// Check that a ticket ID was provided in the URL.
if (!isset($_GET["id"])) {
    die("No ticket ID was provided.");
}

// Convert the ticket ID to an integer.
$ticket_id = (int) $_GET["id"];

// First, check that the ticket exists and is currently Open.
$sql = "SELECT ticket_id, subject, status, support_level
        FROM tickets
        WHERE ticket_id = ? AND status = 'Open'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    $stmt->close();
    $conn->close();

    die("This ticket does not exist or is no longer open.");
}

// Get the ticket information.
$ticket = $result->fetch_assoc();

$stmt->close();


// Increase the support level by 1.
//
// IMPORTANT:
// The ticket status remains Open.
$new_support_level = $ticket["support_level"] + 1;

$sql = "UPDATE tickets
        SET support_level = ?
        WHERE ticket_id = ? AND status = 'Open'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $new_support_level, $ticket_id);

if ($stmt->execute()) {

    // The ticket was successfully elevated.
    $message = "Ticket elevated successfully.";

} else {

    // Something went wrong while updating the ticket.
    $message = "Unable to elevate the ticket.";
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Elevate Ticket</title>

</head>

<body>

<div class="container">

    <!-- Top-right logout -->
    <div class="top-navigation">

        <a href="logout.php" class="top-logout">
            Logout
        </a>

    </div>


    <!-- Page header -->
    <div class="dashboard-header">

        <h1>Elevate Ticket</h1>

        <p>
            The support level of this ticket has been increased.
        </p>

    </div>


    <!-- Success message -->
    <div class="success">

        <?php echo htmlspecialchars($message); ?>

    </div>


    <?php if (isset($new_support_level)): ?>

        <!-- Ticket information -->
        <div class="form-card">

            <h2>Ticket Details</h2>

            <div class="info">

                <p>
                    <strong>Ticket ID:</strong>
                    <?php echo htmlspecialchars($ticket_id); ?>
                </p>

                <p>
                    <strong>Subject:</strong>
                    <?php echo htmlspecialchars($ticket["subject"]); ?>
                </p>

                <p>
                    <strong>New Support Level:</strong>
                    <?php echo htmlspecialchars($new_support_level); ?>
                </p>

                <p>
                    <strong>Status:</strong>

                    <span class="status-open">
                        Open
                    </span>

                </p>

            </div>

        </div>

    <?php endif; ?>


    <!-- Navigation -->
    <div class="page-navigation">

        <a
            href="search_tickets.php"
            class="secondary-link"
        >
            Back to Open Tickets
        </a>

        <a
            href="support_dashboard.php"
            class="secondary-link"
        >
            Back to Support Dashboard
        </a>

    </div>


    <!-- Footer -->
    <div class="footer">

        <p>Support Ticketing System</p>

    </div>

</div>

</body>

</html>