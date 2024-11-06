<?php
session_start();

require_once 'db_connection.php'; // Include your database connection file

$message = "";


// Password validation
function isValidPassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
}

$username = ""; // Initialize the username variable

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    $username = $_POST['username']; // Get username from POST data
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    try {
        // Prepare a SQL statement to select the user by username
        $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]); // Execute with the username parameter

        // Fetch the user data
        $user = $stmt->fetch();

        // Check if user exists
        if ($user) {
            // Check if passwords match
            if ($newPassword !== $confirmNewPassword) {
                $message = "Passwords do not match.";
            } elseif (!isValidPassword($newPassword)) {
                $message = "Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one number.";
            } else {
                // Update password in the database
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
                $updateStmt = $pdo->prepare("UPDATE user SET password = ? WHERE username = ?");
                $updateStmt->execute([$hashedPassword, $username]); // Execute update

                $message = "Password reset successfully! You can now log in with your new password.";
            }
        } else {
            $message = "User not found.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

            /* Body Styles */
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(to right, #A1C4FD, #C2E9FB);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                color: #333;
            }

        /* Sticky Navbar */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #07257F;
            color: white;
            padding: 10px 20px;
            z-index: 1000;
        }

        nav img {
            height: 50px;
            border-radius: 10px;
        }

        nav h1 {
            font-size: 24px;
            margin-left: 10px;
        }

        nav a {
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            margin: 0 10px;
            border-radius: 14px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #91A7BF;
            text-decoration:none;
            color:blue;
        }

        .reset-container {
            width: 100%;
            max-width: 500px; /* Wider for better usability */
            background-color: #ffffff;
            padding: 50px; /* Increased padding for spaciousness */
            border-radius: 12px; /* Softer corners */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin: 100px auto; /* Centering the container */
            position: relative; /* Allow for decorative circles */
            overflow: hidden;
            font-size:18px; /* Ensures circles don't overflow */
        }

        /* Decorative Circles */
        .reset-container::before, .reset-container::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: #A1C4FD;
            opacity: 0.15; /* Lighter for subtlety */
        }
        .reset-container::before {
            width: 150px; /* Slightly larger for better effect */
            height: 150px;
            top: -60px;
            left: -60px;
        }
        .reset-container::after {
            width: 100px;
            height: 100px;
            bottom: -50px;
            right: -50px;
        }

        h2 {
            font-size: 2em; /* Larger for emphasis */
            margin-bottom: 20px;
            color: #007bff;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            display: block;
            text-align: left;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px; /* Increased padding for comfort */
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px; /* Softer borders */
            font-size: 1em;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px; /* Increased padding */
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 6px; /* Softer corners */
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        p {
            font-size: 0.9em;
            color: #666;
            margin-top: 15px;
        }

        footer {
            background-color: #07257F; 
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            margin-top: auto; /* Ensure footer stays at bottom */
            width: 100%;
        }
        footer p{
            color:white;
        }

        footer a {
            color: white;
            font-size:16px;
            text-decoration: none;
            padding: 10px;
        }

        footer a:hover {
            color: #ffcc00;
            text-decoration: underline; 
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav h1 {
                font-size: 1.5em;
            }

            .reset-container {
                padding: 30px;
                margin: 15px; /* Adjust for smaller screens */
            }

            footer {
                font-size: 14px;
            }

            nav a {
                font-size: 14px; /* Smaller for better fit */
            }
        }
    </style>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo">
            <h1>Event Management System</h1>
        </div>
        <div>
        <a href="login.php">Sign In</a>
        </div>
    </nav>
    <div class="reset-container">
        <h2>Password Reset</h2>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Password Reset Form -->
        <form method="POST">
            <label for="username">UserName:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required>
            <label for="confirmNewPassword">Confirm New Password:</label>
            <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
            <button type="submit" name="reset">Reset Password</button>
        </form>
        <p>Remembered your password? <a href="login.php">Sign In</a></p>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>
