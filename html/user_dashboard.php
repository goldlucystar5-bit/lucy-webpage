<?php

// Start the session so we can check if a user is logged in.
session_start();

// If the user is not logged in, send them to the login page.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Get the user's name from the session.
$full_name = $_SESSION["full_name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Use the shared stylesheet -->
    <link rel="stylesheet" href="style.css">

    <title>User Dashboard</title>

</head>

<body>

    <div class="container">

        <div class="top-navigation">
        <a href="logout.php" class="top-logout">Logout</a>
        </div>

        <!-- Dashboard header -->
        <div class="dashboard-header">

            <h1>User Dashboard</h1>

            <p>
                Welcome, <?php echo htmlspecialchars($full_name); ?>!
            </p>

            <p>
                Manage your support requests from the options below.
            </p>

        </div>


        <!-- User functions -->
        <h2>User Area</h2>

        <div class="card-container">

            <!-- Open Ticket -->
            <div class="card">

                <h3>Open a Ticket</h3>

                <p>
                    Submit a new support request and provide
                    details about the problem you are experiencing.
                </p>

                <a href="open_ticket.php" class="dashboard-link">
                    Open Ticket
                </a>

            </div>


            <!-- My Tickets -->
            <div class="card">

                <h3>My Tickets</h3>

                <p>
                    View your submitted tickets, their current
                    status and any resolution details.
                </p>

                <a href="my_tickets.php" class="dashboard-link">
                    View My Tickets
                </a>

            </div>

        </div>

        <!-- Footer -->
        <div class="footer">

            <p>Support Ticketing System</p>


        </div>

    </div>

</body>

</html>