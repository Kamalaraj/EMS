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
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['usertype'] = $user['usertype'];

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
    <style>
        /* General Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Body Styles */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #A1C4FD, #C2E9FB);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding-top: 70px; /* Space for sticky navbar */
            padding-bottom: 60px; /* Space for sticky footer */
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

        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        /* Responsive Navbar */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                background-color: #07257F;
                position: absolute;
                top: 70px;
                right: 0;
                width: 100%;
                text-align: center;
                padding: 10px 0;
            }

            .nav-links.active {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }

        .auth-container {
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            line-height:3;
            margin:50px auto;
            font-size:20px;
        }

        /* Decorative Circles */
        .auth-container::before, .auth-container::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: #A1C4FD;
            opacity: 0.2;
        }
        .auth-container::before {
            width: 120px;
            height: 120px;
            top: -40px;
            left: -40px;
        }
        .auth-container::after {
            width: 80px;
            height: 80px;
            bottom: -30px;
            right: -30px;
        }

        /* Form Title */
        h2 { 
            font-size: 1.8em; 
            margin-bottom: 20px; 
            color: #007bff; 
            position: relative;
        }

        /* Form Message */
        p.message {
            background: #ffdddd;
            color: #b30000;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        /* Form Fields */
        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            outline: none;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        }

        /* Button */
        button[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 25px;
            font-size: 1em;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Link Styles */
        p, a {
            font-size: 0.9em;
            color: #666;
            text-decoration: none;
        }

        a {
            color: #007bff;
        }

        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Spacing for links */
        .links {
            margin-top: 20px;
        }

        /* Sticky Footer */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #07257F;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
        }
        footer p{
            color:white;
        }

        footer a {
            color: white;
            font-size:16px;
            text-decoration: none;
            padding: 0 10px;
        }

        footer a:hover {
            color: #ffcc00;
            text-decoration: underline;
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
        <a href="signup.php">Sign Up</a>
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
        <p>&copy;<?php echo date("Y"); ?> 2024 Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>
