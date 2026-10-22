<?php

// Start the session so we can identify the logged-in support agent
session_start();

// Make sure a support agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: support_login.php");
    exit();
}

// Connect to the database
require_once "config.php";

// Get the logged-in support agent's ID from the session
$agent_id = $_SESSION['agent_id'];

// Get the support agent's details
$sql = "SELECT full_name, support_level
        FROM support_agents
        WHERE agent_id = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Pass the agent ID safely to the query
$stmt->bind_param("i", $agent_id);

// Execute the query
$stmt->execute();

// Get the query result
$result = $stmt->get_result();

// Get the agent's information
$agent = $result->fetch_assoc();

// Close the statement
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Support Agent Dashboard</title>

</head>

<body>

<div class="container">

    <!-- Top-right logout -->
    <div class="top-navigation">

        <a href="logout.php" class="top-logout">
            Logout
        </a>

    </div>


    <!-- Dashboard header -->
    <div class="dashboard-header">

        <h1>Support Agent Dashboard</h1>

        <?php if ($agent): ?>

            <p>
                Welcome,
                <?php echo htmlspecialchars($agent['full_name']); ?>!
            </p>

            <p>
                Support Level:
                <strong>
                    <?php echo htmlspecialchars($agent['support_level']); ?>
                </strong>
            </p>

        <?php endif; ?>

    </div>


    <!-- Support Area -->
    <h2>Support Area</h2>

    <div class="card-container">

        <div class="card">

            <h3>Search Open Tickets</h3>

            <p>
                Search for open support tickets and take action
                to resolve or elevate them.
            </p>

            <a href="search_tickets.php" class="dashboard-link">
                Search Open Tickets
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

<?php

// Close the database connection
$conn->close();

?>