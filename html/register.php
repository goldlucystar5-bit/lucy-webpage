<?php

// Connect to the database.
require_once "config.php";

// Variable used to display registration messages.
$message = "";
$message_class = "";


// Check whether the registration form has been submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and clean the submitted form values.
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);


    // Check that all required fields have been completed.
    if (
        empty($username) ||
        empty($password) ||
        empty($full_name) ||
        empty($email)
    ) {

        $message = "Please fill in all fields.";
        $message_class = "error";

    } else {

        // Hash the password before storing it in the database.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the users table.
        $sql = "INSERT INTO users (username, password, full_name, email)
                VALUES (?, ?, ?, ?)";

        // Prepare the SQL statement.
        $stmt = $conn->prepare($sql);

        // Bind the four values to the SQL statement.
        $stmt->bind_param(
            "ssss",
            $username,
            $hashed_password,
            $full_name,
            $email
        );


        // Execute the registration.
        if ($stmt->execute()) {

            $message = "Registration successful. You can now log in.";
            $message_class = "success";

        } else {

            $message = "Registration failed. Username or email may already exist.";
            $message_class = "error";
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

    <title>User Registration</title>

</head>

<body>

<div class="container">


    <!-- Page header -->
    <div class="dashboard-header">

        <h1>User Registration</h1>

        <p>
            Create an account to submit and manage your support tickets.
        </p>

    </div>


    <!-- Registration form -->
    <div class="form-card">

        <h2>Create Your Account</h2>

        <?php if ($message != ""): ?>

            <div class="<?php echo $message_class; ?>">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="register.php">

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


            <!-- Full Name -->
            <div class="form-group">

                <label for="full_name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    required
                >

            </div>


            <!-- Email -->
            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    required
                >

            </div>


            <!-- Submit button -->
            <button
                type="submit"
                class="primary-button"
            >
                Register
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
            href="login.php"
            class="secondary-link"
        >
            Already Have an Account? Login
        </a>

    </div>


    <!-- Footer -->
    <div class="footer">

        <p>Support Ticketing System</p>

    </div>

</div>

</body>

</html>