<?php
session_start();
require_once 'db_connection.php'; // Include your database connection file


$currentUserID = $_SESSION['userID'];
// Query to retrieve all records from the registration table
$query = "SELECT registrationID, eventID, organizerID,studentID,firstName, lastName, studentRegNo, levelOfStudy, registrationDate, approval FROM registration WHERE studentID = :currentUserID";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':currentUserID', $currentUserID, PDO::PARAM_STR);
$stmt->execute();
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Details</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background: linear-gradient(to right, #A1C4FD, #C2E9FB); /* Gradient background */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            margin-top:80px;
        }

        /* Navigation Bar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #07257F;
            padding: 10px 30px;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        nav img {
            height: 60px;
            border-radius: 10px;
        }

        nav a {
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            margin: 0 10px;
            border-radius: 24px;
            font-size: 20px;
            transition: background-color 0.3s ease;
            background-color: white;
        }

        nav a:hover {
            background-color: #7D7F86;
        }
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 30px 0px;
            padding : 10px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align:center;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #07257F;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        #backButton {
            background-color: #007bff;
            color: white;
            padding:10px;
            font-size: 14px;
            cursor: pointer;
            border : none;
            border-radius:10px;
        }
        #backButton:hover {
            background-color: #0056b3;
        }
        footer {
            background-color: #07257F;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            margin-top: auto;
            

        }

        footer a {
            color: white;
            text-decoration: none;
            padding: 0 10px;
        }

        footer a:hover {
            color: #ffcc00;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo"> <!-- Replace with your logo path -->
            <a href="student-dashboard.php">Dashboard</a>
        </div>
        <div>
            <a href="student-about.php">About Us</a>
            <a href="student-contactUS.php">Contact Us</a>
            <a href="student-profile.php">Profile</a>
            <a href="login.php">Sign Out</a>
        </div>
    </nav>
    <div class="container">
    <h1>Registration Details</h1>
    <table>
        <thead>
            <tr>
                <th>Registration ID</th>
                <th>Event ID</th>
                <th>Event_creator ID</th>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Student Reg No</th>
                <th>Level of Study</th>
                <th>Registration Date</th>
                <th>Approval Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($registrations)): ?>
                <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registration['registrationID']); ?></td>
                        <td><?php echo htmlspecialchars($registration['eventID']); ?></td>
                        <td><?php echo htmlspecialchars($registration['organizerID']); ?></td>
                        <td><?php echo htmlspecialchars($registration['studentID']); ?></td>
                        <td><?php echo htmlspecialchars($registration['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($registration['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($registration['studentRegNo']); ?></td>
                        <td><?php echo htmlspecialchars($registration['levelOfStudy']); ?></td>
                        <td><?php echo htmlspecialchars($registration['registrationDate']); ?></td>
                        <td><?php echo htmlspecialchars($registration['approval']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;">No registrations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div>
        <button id="backButton" onclick="goBack()">Back to Events</button>
    </div>
    </div>
    
    <script>
        function goBack() {
            window.history.back(); // Go back to the previous page
        }
    </script>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="student-contactUs.php">Contact Us</a> | <a href="student-about.php">About Us</a></p>
    </footer>
</body>
</html>
