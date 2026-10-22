<?php

// Start the session so we can check whether a support agent is logged in.
session_start();

// If no support agent is logged in, send them back to the support login page.
if (!isset($_SESSION["agent_id"])) {
    header("Location: support_login.php");
    exit();
}

// Connect to the database.
require_once "config.php";

// Get the search keyword from the form.
// If no keyword was entered, use an empty string.
$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

/*
 * Search for tickets that are currently Open.
 *
 * The search can match:
 * - Ticket subject
 * - Ticket description
 * - Username
 */
// Prepare a query to find open tickets matching the search term.
$sql = "SELECT 
            tickets.ticket_id,
            tickets.subject,
            tickets.description,
            tickets.status,
            tickets.date_submitted,
            tickets.support_level,
            users.username
        FROM tickets
        INNER JOIN users ON tickets.user_id = users.user_id
        WHERE tickets.status = 'Open'
        AND (
            tickets.subject LIKE ?
            OR tickets.description LIKE ?
            OR users.username LIKE ?
        )
        ORDER BY tickets.date_submitted DESC";

// Prepare the SQL statement.
$stmt = $conn->prepare($sql);

// Add % around the search word so partial matches can be found.
$search_term = "%" . $search . "%";

// Bind the same search term to all three placeholders.
$stmt->bind_param("sss", $search_term, $search_term, $search_term);

// Execute the query.
$stmt->execute();

// Get the results.
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Search Open Tickets</title>

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

        <h1>Open Tickets Search</h1>

        <p>
            Search for open support tickets by subject,
            description or username.
        </p>

    </div>


    <!-- Search form -->
    <div class="form-card">

        <h2>Ticket Search</h2>

        <form method="GET" action="search_tickets.php">

            <div class="form-group">

                <label for="search">
                    Search Tickets
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Subject, description or username"
                >

            </div>

            <button type="submit" class="primary-button">
                Search Open Tickets
            </button>

        </form>

    </div>


    <!-- Open tickets -->
    <?php if ($result->num_rows > 0): ?>

        <div class="ticket-table-card">

            <h2>Open Tickets</h2>

            <div class="table-wrapper">

                <table>

                    <tr>

                        <th>Ticket ID</th>

                        <th>Username</th>

                        <th>Subject</th>

                        <th>Description</th>

                        <th>Status</th>

                        <th>Date Submitted</th>

                        <th>Support Level</th>

                        <th>Action</th>

                    </tr>


                    <?php while ($ticket = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($ticket["ticket_id"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($ticket["username"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($ticket["subject"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($ticket["description"]); ?>
                            </td>

                            <td>

                                <span class="status-open">
                                    <?php echo htmlspecialchars($ticket["status"]); ?>
                                </span>

                            </td>

                            <td>
                                <?php echo htmlspecialchars($ticket["date_submitted"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($ticket["support_level"]); ?>
                            </td>

                            <td>

                                <a
                                    href="resolve_ticket.php?id=<?php echo $ticket['ticket_id']; ?>"
                                    class="action-link"
                                >
                                    Resolve
                                </a>

                                <br><br>

                                <a
                                    href="elevate_ticket.php?id=<?php echo $ticket['ticket_id']; ?>"
                                    class="action-link"
                                >
                                    Elevate
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </table>

            </div>

        </div>

    <?php else: ?>

        <div class="info">

            <p>
                No open tickets were found.
            </p>

        </div>

    <?php endif; ?>


    <!-- Navigation -->
    <div class="page-navigation">

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

// Close the prepared statement.
$stmt->close();

// Close the database connection.
$conn->close();

?>