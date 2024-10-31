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
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #A1C4FD, #C2E9FB); /* Light background for better contrast */
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #07257F; /* Nav background color */
            padding: 10px 20px;
            width: 100%; /* Full width */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional shadow */
        }

        nav img {
            height: 50px; /* Logo height */
            width: auto; /* Maintain aspect ratio */
            margin-right: 20px; /* Space between logo and links */
            border-radius: 5px; /* Rounded corners for logo */
        }

        nav h1 {
            color: white; /* Title color */
            margin: 0; /* Remove default margin */
            font-size: 1.5em; /* Adjust size */
        }

        nav a {
            color: white; /* Link color */
            text-decoration: none; /* Remove underline */
            padding: 10px 15px; /* Padding for links */
            border-radius: 5px; /* Rounded corners for links */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        nav a:hover {
            background-color: #0056b3; /* Hover effect */
        }

        .reset-container {
            width: 100%;
            max-width: 400px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px; /* Add space above the container */
        }
        
        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #007bff;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            display: block;
            text-align: left; /* Align labels to the left */
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
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
            margin-top: 15px; /* Add margin for better spacing */
        }

        footer {
            background-color: #07257F; /* Match with the navbar color */
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            margin-top: auto; /* Footer sticks to the bottom */
            width: 100%; /* Full width */
            position: relative;
        }

        footer a {
            color: white;
            text-decoration: none;
            padding: 0 10px;
        }

        footer a:hover {
            color: #ffcc00; /* Highlight on hover */
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
        <a href="login.php" style="background: white; border: none; color: black; cursor: pointer; 
            padding: 14px 20px;margin-right:30px; text-decoration: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 24px; font-size: 20px; transition: background-color 0.3s ease;"
            onmouseover="this.style.backgroundColor='#7D7F86'; this.style.color = 'white';" 
            onmouseout="this.style.backgroundColor='white'; this.style.color = 'black';">
            Sign In
        </a>
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
