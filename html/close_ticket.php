<?php

// Start the session so we can identify the logged-in user.
session_start();

// Only logged-in users can close their tickets.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Connect to the database.
require_once "config.php";

// Get the ID of the currently logged-in user.
$user_id = $_SESSION["user_id"];

// Check that a ticket ID was provided in the URL.
if (!isset($_GET["id"])) {
    die("No ticket ID was provided.");
}

// Convert the ticket ID to an integer.
$ticket_id = (int) $_GET["id"];

/*
 * Close the ticket only when:
 *
 * 1. The ticket belongs to the currently logged-in user.
 * 2. The ticket is currently Resolved.
 *
 * This prevents a user from closing another user's ticket
 * and prevents an Open ticket from being closed directly.
 */

$sql = "UPDATE tickets
        SET status = 'Closed'
        WHERE ticket_id = ?
        AND user_id = ?
        AND status = 'Resolved'";

// Prepare the SQL statement.
$stmt = $conn->prepare($sql);

// Bind the ticket ID and user ID.
$stmt->bind_param("ii", $ticket_id, $user_id);

// Execute the update.
if ($stmt->execute()) {

    // Check whether a ticket was actually changed.
    if ($stmt->affected_rows == 1) {

        $message = "Ticket closed successfully.";
        $message_class = "success";

    } else {

        $message = "The ticket could not be closed. It may not be resolved or may not belong to you.";
        $message_class = "error";
    }

} else {

    $message = "Unable to close the ticket.";
    $message_class = "error";
}

// Close the statement.
$stmt->close();

// Close the database connection.
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Close Ticket</title>

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

        <h1>Close Ticket</h1>

        <p>
            Update the status of your resolved support ticket.
        </p>

    </div>


    <!-- Result message -->
    <div class="<?php echo $message_class; ?>">

        <strong>
            <?php echo htmlspecialchars($message); ?>
        </strong>

    </div>


    <!-- Information card -->
    <div class="form-card">

        <h2>Ticket Status</h2>

        <?php if ($message_class == "success"): ?>

            <div class="info">

                <p>
                    Your support ticket has now been successfully closed.
                </p>

                <p>
                    <strong>New Status:</strong>

                    <span class="status-closed">
                        Closed
                    </span>
                </p>

            </div>

        <?php else: ?>

            <div class="info">

                <p>
                    The ticket was not closed.
                </p>

                <p>
                    A ticket can only be closed when it has been resolved
                    and belongs to the logged-in user.
                </p>

            </div>

        <?php endif; ?>

    </div>


    <!-- Navigation -->
    <div class="page-navigation">

        <a
            href="my_tickets.php"
            class="secondary-link"
        >
            Back to My Tickets
        </a>

        <a
            href="user_dashboard.php"
            class="secondary-link"
        >
            Back to User Dashboard
        </a>

    </div>


    <!-- Footer -->
    <div class="footer">

        <p>Support Ticketing System</p>

    </div>

</div>

</body>

</html>