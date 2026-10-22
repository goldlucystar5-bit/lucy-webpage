<?php
session_start();

// Make sure the user is logged in before viewing their tickets
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connect to the database
require_once "config.php";

// Get the ID of the currently logged-in user
$user_id = $_SESSION['user_id'];

// Get all tickets belonging to this user
$sql = "SELECT ticket_id, subject, description, status, date_submitted, resolution_details
        FROM tickets
        WHERE user_id = ?
        ORDER BY date_submitted DESC";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Send the user's ID safely to the SQL query
$stmt->bind_param("i", $user_id);

// Execute the query
$stmt->execute();

// Get the results
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>My Tickets</title>

    <script>
        function confirmCloseTicket() {
            return confirm("Are you sure you want to close this ticket?");
    }
</script>

</head>

<body>

<div class="container">

    <!-- Top-right navigation -->
    <div class="top-navigation">
        <a href="logout.php" class="top-logout">Logout</a>
    </div>

    <!-- Page header -->
    <div class="dashboard-header">

        <h1>My Tickets</h1>

        <p>View your submitted support tickets and their current status.</p>

    </div>


    <?php if ($result->num_rows > 0): ?>

        <div class="ticket-table-card">

            <h2>Your Support Tickets</h2>

            <div class="table-wrapper">

               <table>

                    <tr>

                        <th>Ticket ID</th>

                        <th>Subject</th>

                        <th>Description</th>

                        <th>Status</th>

                        <th>Date Submitted</th>

                        <th>Resolution Details</th>

                        <th>Action</th>

                    </tr>


                    <?php while ($ticket = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($ticket['ticket_id']); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($ticket['subject']); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($ticket['description']); ?>
                            </td>


                            <td>

                                <?php if ($ticket['status'] == 'Open'): ?>

                                    <span class="status-open">
                                        Open
                                    </span>

                                <?php elseif ($ticket['status'] == 'Resolved'): ?>

                                    <span class="status-resolved">
                                        Resolved
                                    </span>

                                <?php elseif ($ticket['status'] == 'Closed'): ?>

                                    <span class="status-closed">
                                        Closed
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($ticket['date_submitted']); ?>
                            </td>


                            <td>

                                <?php

                                if ($ticket['resolution_details'] !== null) {

                                    echo htmlspecialchars($ticket['resolution_details']);

                                } else {

                                    echo "Not yet resolved";

                                }

                                ?>

                            </td>


                            <td>

                                <?php if ($ticket['status'] == 'Resolved'): ?>

                                    <a
                                        href="close_ticket.php?id=<?php echo $ticket['ticket_id']; ?>"
                                        class="action-link"
                                        onclick="return confirmCloseTicket();"
                                    >
                                        Close Ticket
                                    </a>

                                <?php elseif ($ticket['status'] == 'Closed'): ?>

                                    <span class="status-closed">
                                        Closed
                                    </span>

                                <?php else: ?>

                                    <span>
                                        Not available
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </table>

            </div>

        </div>


    <?php else: ?>

        <div class="info">

            <p>You have not submitted any tickets yet.</p>

        </div>

    <?php endif; ?>


    <!-- Navigation boxes -->
    <div class="page-navigation">

        <a href="user_dashboard.php" class="secondary-link">
            Back to Dashboard
        </a>

        <a href="open_ticket.php" class="secondary-link">
            Open a New Ticket
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

// Close the prepared statement

$stmt->close();

// Close the database connection

$conn->close();

?>