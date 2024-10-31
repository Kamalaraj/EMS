<?php
session_start();
require_once 'db_connection.php';

// Generate unique userID
function generateUserID($prefix) {
    return $prefix . strtoupper(substr(md5(uniqid(rand(), true)), 0, 9));
}

// Password validation
function isValidPassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
}

// Email validation
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = $_POST['userType'];
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Signup handling
    if ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } elseif (!isValidPassword($password)) {
        $message = "Password must be at least 8 characters long with uppercase, lowercase, and a digit.";
    } else {
        try {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $message = "Username already exists. Choose another one.";
            } else {
                // Insert into user table
                $userID = generateUserID($userType === 'organizer' ? 'ORG-' : 'STU-');
                $stmt = $pdo->prepare("INSERT INTO user (userID, username, password, usertype) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userID, $username, $hashedPassword, $userType]);

                // Additional data for organizers or students
                if ($userType === 'organizer') {
                    $committeeName = $_POST['committeeName'];
                    $chairPersonName = htmlspecialchars(trim($_POST['chairPersonName']));
                    $email = htmlspecialchars(trim($_POST['organizer_email']));
                    if (isValidEmail($email)) {
                        $stmt = $pdo->prepare("INSERT INTO organizer (userID,username, committeeName, chairPersonName, email) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$userID,$username, $committeeName, $chairPersonName, $email]);
                        $message = "Organizer signup successful!";
                    } else {
                        $message = "Invalid email format.";
                    }
                } elseif ($userType === 'student') {
                    $firstName = htmlspecialchars(trim($_POST['firstName']));
                    $lastName = htmlspecialchars(trim($_POST['lastName']));
                    $email = htmlspecialchars(trim($_POST['student_email']));
                    $registrationNumber = htmlspecialchars(trim($_POST['registrationNumber']));
                    $levelOfStudy = htmlspecialchars(trim($_POST['levelOfStudy']));
                    if (isValidEmail($email)) {
                        $stmt = $pdo->prepare("INSERT INTO student (userID,username, firstName, lastName, Reg_No, email, yearOfStudy) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$userID,$username, $firstName, $lastName, $registrationNumber, $email, $levelOfStudy]);
                        $message = "Student signup successful!";
                    } else {
                        $message = "Invalid email format.";
                    }
                }
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
    echo $message; // Display signup status
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            margin-top:20px;
            margin-bottom:10px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            line-height:1;
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
        <a href="login.php" style="background: white; border: none; color: black; cursor: pointer; 
            padding: 14px 20px;margin-right:30px; text-decoration: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 24px; font-size: 20px; transition: background-color 0.3s ease;"
            onmouseover="this.style.backgroundColor='#7D7F86'; this.style.color = 'white';" 
            onmouseout="this.style.backgroundColor='white'; this.style.color = 'black';">
            Sign In
        </a>
        </div>
    </nav>

    <div class="auth-container">
        <h2>Sign Up</h2>

        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="signupUserType">User Type:</label>
            <select id="signupUserType" name="userType" onchange="toggleFormFields()" required>
                <option value="student">Student</option>
                <option value="organizer">Organizer</option>
            </select>

            <!-- Organizer Fields -->
            <div id="organizerFields" style="display: none;">
                <label for="committeeName">Committee Name:</label>
                <select id="committeeName" name="committeeName">
                    <option value="ComSociety">ComSociety</option>
                    <option value="IEEE-StudentBranch">IEEE-StudentBranch</option>
                    <option value="IEEE-WIE">IEEE-WIE</option>
                </select>

                <label for="chairPersonName">Chair Person Name:</label>
                <input type="text" id="chairPersonName" name="chairPersonName">

                <label for="organizer_email">E-mail:</label>
                <input type="text" id="organizer_email" name="organizer_email">
            </div>

            <!-- Student Fields -->
            <div id="studentFields" style="display: block;">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName">

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName">

                <label for="student_email">E-mail:</label>
                <input type="text" id="student_email" name="student_email">

                <label for="registrationNumber">Registration Number:</label>
                <input type="text" id="registrationNumber" name="registrationNumber">

                <label for="levelOfStudy">Level Of Study:</label>
                <select name="levelOfStudy" id="levelOfStudy">
                <option value="">Select here</option>
                <option value="Level 1">Level 1</option>
                <option value="Level 2">Level 2</option>
                <option value="Level 3">Level 3</option>
                <option value="Level 4">Level 4</option>
            </select>
            </div>

            <!-- Common Fields -->
            <label for="signupUsername">Username:</label>
            <input type="text" id="signupUsername" name="username" required>
            <label for="signupPassword">Password:</label>
            <input type="password" id="signupPassword" name="password" required>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <button type="submit">Sign Up</button>
        </form>

        <p>
            Already have an account? <a href="login.php">Sign In</a>
        </p>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>

    <script>
        function toggleFormFields() {
            const userType = document.getElementById('signupUserType').value;
            if (userType === 'organizer') {
                document.getElementById('organizerFields').style.display = 'block';
                document.getElementById('studentFields').style.display = 'none';
            } else {
                document.getElementById('organizerFields').style.display = 'none';
                document.getElementById('studentFields').style.display = 'block';
            }
        }
    </script>
</body>
</html>
