<?php
session_start();

require_once 'db_connection.php';// Include your database connection file

// Password validation
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    // Login handling
    try {
        // Prepare a SQL statement to select the user by username
        $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]); // Execute with the username parameter

        // Fetch the user data
        $user = $stmt->fetch();


        // Check if the user exists and verify the password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['currentUser'] = $user;

            // Redirect based on user type
            if ($user['usertype'] === 'student') {
                header('Location: student-dashboard.php');
            } elseif ($user['usertype'] === 'organizer') {
                header('Location: organizer-dashboard.php');
            }
            exit();
        } else {
            $message = "Invalid username or password.";
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
    <title>Login</title>
    <style> /* General Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Body Styles */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #A1C4FD, #C2E9FB);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items:center;
            height:100vh;
            color: #333;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            color:white;
            align-items: center;
            background-color: #07257F; /* Semi-transparent blue */
            padding: 10px 20px;
            width: 100%; /* Make sure the nav takes full width */
        }

        nav img {
            height: 60px; /* Increase logo height */
            width: auto; /* Maintain aspect ratio */
            margin-right: 20px; /* Space between logo and links */
            border-radius: 10px;
        }

        nav a {
            color: white;
            padding: 10px 20px; /* Reduce padding for better alignment */
            text-decoration: none;
            margin: 0 10px;
            border-radius: 14px;
            font-size: 18px; /* Slightly smaller font size for better spacing */
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #0056b3;
        }

        button[type="submit"] {
            background: none; 
            border: none; 
            color: black; 
            cursor: pointer; 
            padding: 10px 20px; /* Same padding as the other links */
            text-decoration: none; 
            margin: 0 10px; 
            border-radius: 25px; 
            font-size: 18px; /* Match the font size of the other links */
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        button[type="submit"]:hover {
            background-color: #dc3545; 
            color: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Container */
        .auth-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            margin-top:60px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            line-height:1.2;
        }

        /* Form Titles */
        h2 { font-size: 2em; margin-bottom: 20px; color: #007bff; }

        /* Form Styles */
        form { display: flex; flex-direction: column; align-items: flex-start; }

        label { margin-bottom: 5px; font-weight: bold; color: #555; }

        input[type="text"], input[type="password"], select {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border: 1px solid #ccc; border-radius: 5px; font-size: 1em;
        }

        button[type="submit"] {
            width: auto; /* Adjust width so buttons don't stretch */
            padding: 10px 20px; /* Adjust padding for better spacing */
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            display: inline-flex; /* This ensures buttons stay inline */
            align-items: center; /* Vertically center text */
            margin-right: 10px; /* Adds space between buttons */
        }


        button[type="submit"]:hover { background-color: #0056b3; }

        /* Link Styles */
        p { font-size: 0.9em; color: #666; }
        a { color: #007bff; text-decoration: none; }
        a:hover { color: #0056b3; }

        /* Footer Styles */
        footer {
            background-color: #07257F; /* Match with the navbar color */
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            margin-top: auto; /* Ensures footer stays at the bottom */
            width: 100%;
            position: relative;
        }

        footer a {
            color: white;
            text-decoration: none;
            padding: 0 10px;
        }

       
        footer a:hover {
            color: #ffcc00; /* Highlight on hover */
            text-decoration: underline; /* Underline on hover */
        }
    </style>
    <script src="scripts.js"></script> <!-- Assuming scripts.js contains your JS functions -->
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo">
            <h1>Event Management System</h1>
        </div>
        <div>
        <a href="signup.php" style="background: white; border: none; color: black; cursor: pointer; 
            padding: 14px 20px;margin-right:30px; text-decoration: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 24px; font-size: 20px; transition: background-color 0.3s ease;"
            onmouseover="this.style.backgroundColor='#7D7F86'; this.style.color = 'white';" 
            onmouseout="this.style.backgroundColor='white'; this.style.color = 'black';">
            Sign Up
        </a>
        </div>
    </nav>

    <div class="auth-container">
        <h2>Sign In</h2>

        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="loginUsername">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="loginPassword">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Sign In</button>
        </form>

        <p>
            Don't have an account? <a href="signup.php">Sign Up</a>
        </p>
        <p>Forgot your password? <a href="password-reset.php">Reset Password</a></p>
    </div>

    <footer>
        <p>&copy; 2024 Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>
