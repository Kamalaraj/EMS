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
    <style>
        /* General Reset */
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

        .auth-container {
            width: 90%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            line-height:1;
            margin:100px auto;
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

        input[type="text"], input[type="password"],select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            outline: none;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus , select:focus{
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
            background-color: #07257F;
            color: white;
            text-align: center;
            font-size:10px;
            padding: 15px;
            font-size: 16px;
            width: 100%;
            position: sticky;
            bottom: 0;
        }
        footer p{
            color:white;
        }

        footer a {
            color: white;
            text-decoration: none;
            font-size:15px;
        }

        footer a:hover {
            color: #ffcc00;
            font-size:16px;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav h1 {
                font-size: 1.2em;
            }

            .auth-container {
                padding: 20px;
                margin: 10px;
            }

            footer {
                font-size: 14px;
            }
        }
    </style>
    <script src="scripts.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo">
            <h1>Event Management System</h1>
        </div>
        <div>
            <a href="login.php">Sign In</a>
        </div>
    </nav>

    <!-- Signup Container -->
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

        <p>Already have an account? <a href="login.php">Sign In</a></p>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>

    <script>
        function toggleFormFields() {
            const userType = document.getElementById('signupUserType').value;
            document.getElementById('organizerFields').style.display = userType === 'organizer' ? 'block' : 'none';
            document.getElementById('studentFields').style.display = userType === 'student' ? 'block' : 'none';
        }
    </script>
</body>
</html>
