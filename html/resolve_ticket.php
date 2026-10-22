<?php

// Start the session so we can identify the logged-in support agent.
session_start();

// Only logged-in support agents can resolve tickets.
if (!isset($_SESSION["agent_id"])) {
    header("Location: support_login.php");
    exit();
}

// Connect to the database.
require_once "config.php";

// Get the logged-in support agent's ID.
$agent_id = $_SESSION["agent_id"];

// Get the ticket ID from the URL.
$ticket_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

// If no valid ticket ID was provided, return to the search page.
if ($ticket_id <= 0) {
    header("Location: search_tickets.php");
    exit();
}

$message = "";
$ticket = null;


// --------------------------------------------------
// PROCESS THE RESOLUTION FORM
// --------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the resolution details entered by the support agent.
    $resolution_details = trim($_POST["resolution_details"]);

    // Make sure resolution details were entered.
    if (empty($resolution_details)) {

        $message = "Please enter the resolution details.";

    } else {

        /*
         * Only resolve a ticket if it is currently Open.
         */
        // Update the ticket only if it is currently open.
        $sql = "UPDATE tickets
                SET status = 'Resolved',
                    resolution_details = ?,
                    resolved_by = ?
                WHERE ticket_id = ?
                AND status = 'Open'";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sii",
            $resolution_details,
            $agent_id,
            $ticket_id
        );

        if ($stmt->execute()) {

            // Check whether a ticket was actually updated.
            if ($stmt->affected_rows == 1) {

                $message = "Ticket resolved successfully.";

            } else {

                $message = "This ticket could not be resolved because it may no longer be Open.";
            }

        } else {

            $message = "Unable to resolve the ticket.";
        }

        $stmt->close();
    }
}


// --------------------------------------------------
// GET THE TICKET DETAILS
// --------------------------------------------------

$sql = "SELECT
            tickets.ticket_id,
            tickets.subject,
            tickets.description,
            tickets.status,
            tickets.date_submitted,
            tickets.resolution_details,
            users.username
        FROM tickets
        INNER JOIN users
            ON tickets.user_id = users.user_id
        WHERE tickets.ticket_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $ticket_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $ticket = $result->fetch_assoc();

} else {

    $message = "Ticket not found.";
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Resolve Ticket</title>

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

        <h1>Resolve Ticket</h1>

        <p>
            Review the ticket details and provide a resolution.
        </p>

    </div>


    <?php if ($message != ""): ?>

        <div class="message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if ($ticket): ?>

        <!-- Ticket details -->
        <div class="form-card">

            <h2>Ticket Details</h2>


            <div class="info">

                <p>
                    <strong>Ticket ID:</strong>
                    <?php echo htmlspecialchars($ticket["ticket_id"]); ?>
                </p>

                <p>
                    <strong>Username:</strong>
                    <?php echo htmlspecialchars($ticket["username"]); ?>
                </p>

                <p>
                    <strong>Subject:</strong>
                    <?php echo htmlspecialchars($ticket["subject"]); ?>
                </p>

                <p>
                    <strong>Description:</strong>
                    <?php echo htmlspecialchars($ticket["description"]); ?>
                </p>

                <p>
                    <strong>Status:</strong>

                    <?php if ($ticket["status"] == "Open"): ?>

                        <span class="status-open">
                            Open
                        </span>

                    <?php elseif ($ticket["status"] == "Resolved"): ?>

                        <span class="status-resolved">
                            Resolved
                        </span>

                    <?php elseif ($ticket["status"] == "Closed"): ?>

                        <span class="status-closed">
                            Closed
                        </span>

                    <?php endif; ?>

                </p>

                <p>
                    <strong>Date Submitted:</strong>
                    <?php echo htmlspecialchars($ticket["date_submitted"]); ?>
                </p>

            </div>

        </div>


        <?php if ($ticket["status"] == "Open"): ?>

            <!-- Resolution form -->
            <div class="form-card">

                <h2>Enter Resolution</h2>

                <form
                    method="POST"
                    action="resolve_ticket.php?id=<?php echo $ticket_id; ?>"
                >

                    <div class="form-group">

                        <label for="resolution_details">
                            Resolution Details
                        </label>

                        <textarea
                            id="resolution_details"
                            name="resolution_details"
                            rows="7"
                            placeholder="Enter details of how the issue was resolved..."
                            required
                        ></textarea>

                    </div>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Resolve Ticket
                    </button>

                </form>

            </div>

        <?php else: ?>

            <!-- Ticket is no longer open -->
            <div class="info">

                <p>
                    This ticket is no longer Open.
                </p>

                <?php if ($ticket["resolution_details"] !== null): ?>

                    <p>
                        <strong>Resolution Details:</strong>
                    </p>

                    <p>
                        <?php echo htmlspecialchars($ticket["resolution_details"]); ?>
                    </p>

                <?php endif; ?>

            </div>

        <?php endif; ?>

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

<?php

// Close the database connection.
$conn->close();

?>