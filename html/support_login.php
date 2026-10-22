<?php

// Start the session so we can keep the support agent logged in
session_start();

// Connect to the database
require_once "config.php";

// Check if the login form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the username and password entered by the support agent
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Find the support agent using the entered username
    $sql = "SELECT agent_id, username, password, full_name, support_level
            FROM support_agents
            WHERE username = ?";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Send the username safely to the SQL query
    $stmt->bind_param("s", $username);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check whether the username exists
    if ($result->num_rows == 1) {

        // Get the support agent's details
        $agent = $result->fetch_assoc();

        // Check the entered password against the stored password hash
        if (password_verify($password, $agent["password"])) {

            // Store the agent ID in the session
            $_SESSION["agent_id"] = $agent["agent_id"];

            // Store the agent's name in the session
            $_SESSION["agent_name"] = $agent["full_name"];

            // Store the agent's support level in the session
            $_SESSION["support_level"] = $agent["support_level"];

            // Send the agent to the support dashboard
            header("Location: support_dashboard.php");
            exit();

        } else {

            // Password was incorrect
            $error = "Incorrect password.";

        }

    } else {

        // Username was not found
        $error = "Support agent username not found.";
    }

    // Close the prepared statement
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">

    <title>Support Agent Login</title>

</head>

<body>

<div class="container">

    <!-- Page header -->
    <div class="dashboard-header">

        <h1>Support Agent Login</h1>

        <p>Sign in to access the support ticket management area.</p>

    </div>


    <!-- Login form -->
    <div class="form-card">

        <h2>Agent Login</h2>

        <?php if (isset($error)): ?>

            <div class="error">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <!-- Username -->
            <div class="form-group">

                <label for="username">Username</label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    required
                >

            </div>


            <!-- Password -->
            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <!-- Login button -->
            <button type="submit" class="primary-button">
                Login
            </button>

        </form>

    </div>


    <!-- Navigation -->
    <div class="page-navigation">

        <a href="index.php" class="secondary-link">
            Back to Home
        </a>

    </div>


    <!-- Footer -->
    <div class="footer">

        <p>Support Ticketing System</p>

    </div>

</div>

</body>

</html>