<?php
session_start(); // Start the session

// Include database connection file
require_once 'db_connection.php';

// Sanitize the event ID
$eventId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (!$eventId) {
    die("Event ID is required to view student details.");
}

try {
    // Prepare the SQL query to fetch registration details for the given event ID
    $stmt = $pdo->prepare("SELECT registrationID, organizerID,studentID, firstName, lastName, studentRegNo, levelOfStudy, registrationDate,approval 
                           FROM registration 
                           WHERE eventId = :eventId");
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_STR);
    $stmt->execute();
    $eventRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching registrations: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Students</title>
    <style>
       /* General Reset */
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
        }

        /* Sticky Navbar */
        nav {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #07257F;
            color: white;
            padding: 5px 10px;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        nav img {
            height: 60px;
            width: auto;
            margin-right: 20px;
            border-radius: 10px;
        }

        nav h1 {
            font-size: 1.5em;
            margin: 0;
            color: white;
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
            color:blue;
        }
        /* Container Styles */
        .container {
            width: 75%;
            margin: 20px auto;
            padding: 50px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        /* Heading Styles */
        h1 {
            text-align: center;
            color: #333;
        }
        /* Table Styles */
        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            max-width: 100%; /* Enables horizontal scrolling */
        }
        table {
            width: 100%;
            max-width: 100%; /* Prevents table from exceeding container */
            border-collapse: collapse;
            margin: 20px auto;
            table-layout: auto;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        /* Button Styles */
        #backButton {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        #backButton:hover {
            background-color: #0056b3;
        }
        .confirm-button, .reject-button {
            padding: 5px 10px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }
        .confirm-button {
            background-color: #28a745;
        }
        .confirm-button:hover {
            background-color: #218838;
        }
        .reject-button {
            background-color: #dc3545;
        }
        .reject-button:hover {
            background-color: #c82333;
        }
        /* Sticky Footer */
        footer {
            background-color: #07257F;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            margin-top: auto;
            position: sticky; /* Change to sticky */
            bottom: 0; /* Stick to the bottom */
            left: 0;
            width: 100%;
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

        /* Responsive Styles */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                padding: 10px;
            }

            nav img {
                height: 40px; /* Adjust logo height for mobile */
            }
            .container{
                padding:20px;
            }
            nav a {
                font-size: 18px; /* Adjust font size for links */
            }
            table {
                font-size: 14px; /* Smaller font size on mobile */
            }

            th, td {
                padding: 8px; /* Reduced padding for better fit */
            }

            /* Adjust column width on smaller screens */
            th:nth-child(1), td:nth-child(1) { width: 10%; } /* Registration_ID */
            th:nth-child(2), td:nth-child(2) { width: 15%; } /* Event_Creator_ID */
            th:nth-child(3), td:nth-child(3) { width: 10%; } /* Student_ID */
            th:nth-child(4), td:nth-child(4) { width: 20%; } /* First Name */
            th:nth-child(5), td:nth-child(5) { width: 20%; } /* Last Name */
            th:nth-child(6), td:nth-child(6) { width: 15%; } /* Registration Number */
            th:nth-child(7), td:nth-child(7) { width: 10%; } /* Level of Study */
            th:nth-child(8), td:nth-child(8) { width: 15%; } /* Registration Date */
            th:nth-child(9), td:nth-child(9) { width: 15%; } /* Approval */
        }
    </style>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo"> <!-- Replace with your logo path -->
            <h1>Event Management System</h1>
        </div>
        <div>
            <a href="organizer-dashboard.php">Dashboard</a>
            <a href="organizer-about.php">About Us</a>
            <a href="organizer-contactUS.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php">Sign Out</a>
        </div>
    </nav>
    <div class="container">
        <h1>Registered Students for Event ID: <?php echo htmlspecialchars($eventId); ?></h1>
        <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Registration_ID</th>
                    <th>Event_Creator_ID</th>
                    <th>Student_ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Registration Number</th>
                    <th>Level of Study</th>
                    <th>Registration Date </th>
                    <th>Approval</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php if (!empty($eventRegistrations)): ?>
                    <?php foreach ($eventRegistrations as $reg): ?>
                        <tr data-registration-number="<?php echo htmlspecialchars($reg['studentRegNo']); ?>">
                            <td><?php echo htmlspecialchars($reg['registrationID']); ?></td>
                            <td><?php echo htmlspecialchars($reg['organizerID']); ?></td>
                            <td><?php echo htmlspecialchars($reg['studentID']); ?></td>
                            <td><?php echo htmlspecialchars($reg['firstName']); ?></td>
                            <td><?php echo htmlspecialchars($reg['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($reg['studentRegNo']); ?></td>
                            <td><?php echo htmlspecialchars($reg['levelOfStudy']); ?></td>
                            <td><?php echo htmlspecialchars($reg['registrationDate']); ?></td>
                            <td>
                    <span><?php echo htmlspecialchars($reg['approval']); ?></span>
                    <?php if ($reg['approval'] === 'Pending'): // Check if the approval status is still pending ?>
                        <button class="confirm-button" onclick="confirmRegistration('<?php echo htmlspecialchars($reg['studentRegNo']); ?>')">Confirm</button>
                        <button class="reject-button" onclick="rejectRegistration('<?php echo htmlspecialchars($reg['studentRegNo']); ?>')">Reject</button>
                    <?php endif; ?>
                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No students registered for this event.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <button id="backButton" onclick="goBack()">Back to Events</button>
    </div>
    <script>
        function goBack() {
            window.history.back();
        }

        const eventId = "<?php echo htmlspecialchars($eventId); ?>";

        function confirmRegistration(registrationNumber) {
            updateRegistration('confirm', registrationNumber);
        }

        function rejectRegistration(registrationNumber) {
            if (confirm("Are you sure you want to reject this registration?")) {
                updateRegistration('reject', registrationNumber);
            }
        }

        function updateRegistration(action, registrationNumber) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "process-registration.php?eventId=" + encodeURIComponent(eventId), true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message);

                    // Update the status in the table without refreshing the page
                    const row = document.querySelector(`tr[data-registration-number="${registrationNumber}"]`);
                    if (row) {
                        row.querySelector("td:last-child").innerText = action === 'confirm' ? 'Confirmed' : 'Rejected';
                    }
                }
            };
            xhr.send(`action=${action}&registrationNumber=${registrationNumber}`);
        }
    </script>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="Organizer-contactUs.php">Contact Us</a> | <a href="Organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>
