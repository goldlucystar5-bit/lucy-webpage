<?php

// Start the session so we can check whether the user is logged in.
session_start();

// If the user is not logged in, send them to the login page.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Connect to the database.
require_once "config.php";

// Get the ID of the logged-in user.
$user_id = $_SESSION["user_id"];

// This variable will hold success or error messages.
$message = "";

// Check whether the form has been submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and clean the information entered by the user.
    $subject = trim($_POST["subject"]);
    $description = trim($_POST["description"]);

    // Check that both fields contain information.
    if (empty($subject) || empty($description)) {

        $message = "Please enter both a subject and a description.";

    } else {

        // Insert the new ticket into the database.
        // The ticket status will automatically be set to Open
        // because of the database default value.
        $sql = "INSERT INTO tickets (user_id, subject, description)
                VALUES (?, ?, ?)";

        // Prepare the SQL statement.
        $stmt = $conn->prepare($sql);

        // Safely pass the user ID, subject and description.
        $stmt->bind_param("iss", $user_id, $subject, $description);

        // Execute the database query.
        if ($stmt->execute()) {

            $message = "Ticket submitted successfully. Your ticket is now Open.";

        } else {

            $message = "Unable to submit the ticket.";

        }

        // Close the prepared statement.
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Use the shared stylesheet -->
    <link rel="stylesheet" href="style.css">

    <title>Open a Support Ticket</title>

</head>

<body>

    <div class="container">

        <!-- Page header -->
        <div class="dashboard-header">

            <h1>Open a Support Ticket</h1>

            <p>
                Please provide details about the issue you need help with.
            </p>

        </div>


        <!-- Display success or error message -->
        <?php if ($message != ""): ?>

            <div class="message">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <!-- Ticket form -->
        <div class="form-card">

            <h2>Ticket Details</h2>

            <form method="POST" action="open_ticket.php">

               <!-- Subject field -->
<div class="form-group">

    <label for="subject">
        Subject
    </label>

    <input
        type="text"
        id="subject"
        name="subject"
        maxlength="150"
        placeholder="Enter a short description of the problem"
        required
    >

</div>


<!-- Ticket description field -->
<div class="form-group">

    <label for="description">
        Ticket Description
    </label>

    <textarea
        id="description"
        name="description"
        rows="8"
        placeholder="Please explain the problem in detail..."
        required
    ></textarea>

</div>
                <!-- Submit button -->
                <button type="submit" class="primary-button">
                    Submit Ticket
                </button>

            </form>

        </div>


        <!-- Navigation -->
        <div class="page-navigation">

            <a href="user_dashboard.php" class="secondary-link">
                Back to Dashboard
            </a>

            <a href="my_tickets.php" class="secondary-link">
                View My Tickets
            </a>

        </div>


        <!-- Footer -->
        <div class="footer">

            <p>Support Ticketing System</p>

        </div>

    </div>

</body>

</html>