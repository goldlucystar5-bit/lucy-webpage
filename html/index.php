<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Connect the page to the shared stylesheet -->
    <link rel="stylesheet" href="style.css">

    <title>Support Ticketing System</title>

</head>

<body>

    <div class="container">

        <!-- Main welcome header -->
        <div class="header">

            <h1>Support Ticketing System</h1>

            <p>
                Welcome to the Support Ticketing System.
                Please select an option below to continue.
            </p>

        </div>


        <!-- User Area -->
        <h2>User Area</h2>

        <div class="card-container">

            <div class="card">

                <h3>New User?</h3>

                <p>
                    Create an account to submit and manage
                    your support tickets.
                </p>

                <a href="register.php" class="dashboard-link">
                    Register
                </a>

            </div>


            <div class="card">

                <h3>Existing User</h3>

                <p>
                    Log in to open new tickets and check
                    the status of your existing tickets.
                </p>

                <a href="login.php" class="dashboard-link">
                    User Login
                </a>

            </div>

        </div>


        <!-- Support Area -->
        <h2>Support Area</h2>

        <div class="card-container">

            <div class="card">

                <h3>Support Agent</h3>

                <p>
                    Support agents can search, resolve and
                    elevate support tickets.
                </p>

                <a href="support_login.php" class="dashboard-link">
                    Support Agent Login
                </a>

            </div>

        </div>


        <!-- Footer -->
        <div class="footer">

            <p>
                Support Ticketing System
            </p>


        </div>

    </div>

</body>

</html>