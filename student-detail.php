<?php
session_start(); // Start the session

// Sanitize the event ID
$eventId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (!$eventId) {
    die("Event ID is required to view student details.");
} 

// Function to get registrations from the JSON file
function loadRegistrations($eventId) {
    $file = "registrations/registration_{$eventId}.json";
    
    // Create file if it doesn't exist
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $jsonContent = file_get_contents($file);
    
    // Check if reading the file was successful
    if ($jsonContent === false) {
        return []; // Return an empty array if there is an error
    }

    return json_decode($jsonContent, true);
}

// Retrieve registrations from the JSON file
$registrations = loadRegistrations($eventId);

// Filter registrations by event ID
$eventRegistrations = array_filter($registrations, function($reg) use ($eventId) {
    return isset($reg['eventId']) && $reg['eventId'] === $eventId;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Students</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #A1C4FD, #C2E9FB);
            display: flex;
            justify-content: center;
            align-items: center;
            line-height: 1.5;
            margin-top: 90px;
            width: 100%;
            padding: 10px;
        }
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
            background: white;
            padding: 10px 20px;
            text-decoration: none;
            margin: 0 10px;
            border-radius: 24px;
            font-size: 20px;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #7D7F86;
        }
        /* Container Styles */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
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
        footer {
            background-color: #07257F;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
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
            <a style="padding: 10px 20px;" href="organizer-dashboard.php">Dashboard</a>
        </div>
        <div>
            <a href="organizer-about.php">About Us</a>
            <a href="organizer-contactUS.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php" style="background: white; border: none; color: black; cursor: pointer; padding: 10px 20px; margin-right: 30px; text-decoration: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); border-radius: 24px; font-size: 20px; transition: background-color 0.3s ease;"
               onmouseover="this.style.backgroundColor='#7D7F86'; this.style.color = 'white';"
               onmouseout="this.style.backgroundColor='white'; this.style.color='black';">Sign out</a>
        </div>
    </nav>
    <div class="container">
        <h1>Registered Students for Event ID: <?php echo htmlspecialchars($eventId); ?></h1>
        <table>
            <thead>
                <tr>
                    <th>Registration_ID</th>
                    <th>Event_Creator_ID</th>
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
                        <tr data-registration-number="<?php echo htmlspecialchars($reg['registrationNumber']); ?>">
                            <td><?php echo htmlspecialchars($reg['registrationID']); ?></td>
                            <td><?php echo htmlspecialchars($reg['eventCreatorUserID']); ?></td>
                            <td><?php echo htmlspecialchars($reg['firstName']); ?></td>
                            <td><?php echo htmlspecialchars($reg['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($reg['registrationNumber']); ?></td>
                            <td><?php echo htmlspecialchars($reg['levelOfStudy']); ?></td>
                            <td><?php echo htmlspecialchars($reg['registrationDate']); ?></td>
                            <td>
                                <button class="confirm-button" onclick="confirmRegistration('<?php echo htmlspecialchars($reg['registrationNumber']); ?>')">Confirm</button>
                                <button class="reject-button" onclick="rejectRegistration('<?php echo htmlspecialchars($reg['registrationNumber']); ?>')">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No students registered for this event.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
        <p>&copy; 2024 Event Management System | <a href="Organizer-contactUs.php">Contact Us</a> | <a href="Organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>
