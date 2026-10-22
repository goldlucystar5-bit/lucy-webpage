<?php

// Include the database connection file.
require_once "config.php";

// Start a session so we can remember the logged-in user.
session_start();

// Variable used to display login messages.
$message = "";


// Check if the login form has been submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the username entered by the user.
    $username = trim($_POST["username"]);

    // Get the password entered by the user.
    $password = $_POST["password"];


    // Check that both fields have been completed.
    if (empty($username) || empty($password)) {

        $message = "Please enter your username and password.";

    } else {

        // Find the user by username.
        $sql = "SELECT user_id, username, password, full_name
                FROM users
                WHERE username = ?";

        // Prepare the SQL statement.
        $stmt = $conn->prepare($sql);

        // Bind the username to the SQL query.
        $stmt->bind_param("s", $username);

        // Execute the query.
        $stmt->execute();

        // Get the result returned by MySQL.
        $result = $stmt->get_result();


        // Check whether the username exists.
        if ($result->num_rows == 1) {

            // Get the user's details.
            $user = $result->fetch_assoc();


            // Check the entered password against the stored password hash.
            if (password_verify($password, $user["password"])) {

                // Store the user's information in the session.
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["full_name"] = $user["full_name"];


                // Send the user to the dashboard.
                header("Location: user_dashboard.php");
                exit();

            } else {

                // Password did not match.
                $message = "Incorrect username or password.";
            }

        } else {

            // Username was not found.
            $message = "Incorrect username or password.";
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link rel="stylesheet" href="style.css">

    <title>User Login</title>

</head>

<body>

<div class="container">


    <!-- Page header -->
    <div class="dashboard-header">

        <h1>User Login</h1>

        <p>
            Sign in to manage your support tickets.
        </p>

    </div>


    <!-- Login form -->
    <div class="form-card">

        <h2>Sign In</h2>


        <!-- Display an error message if one exists -->
        <?php if ($message != ""): ?>

            <div class="error">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="login.php">


            <!-- Username -->
            <div class="form-group">

                <label for="username">
                    Username
                </label>

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

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <!-- Login button -->
            <button
                type="submit"
                class="primary-button"
            >
                Login
            </button>

        </form>

    </div>


    <!-- Navigation -->
    <div class="page-navigation">

        <a
            href="index.php"
            class="secondary-link"
        >
            Back to Home
        </a>

        <a
            href="register.php"
            class="secondary-link"
        >
            Create an Account
        </a>

    </div>


    <!-- Footer -->
    <div class="footer">

        <p>Support Ticketing System</p>

    </div>

</div>

</body>

</html>