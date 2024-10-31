<?php
session_start();
require_once 'db_connection.php'; // Include database connection
if (!isset($_SESSION['userID'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$loggedInUserID = $_SESSION['userID']; // Store logged-in user's ID

// Check if the event ID is provided in the URL
if (!isset($_GET['id'])) {
    die("Event ID not provided.");
}

$eventID = $_GET['id'];

// Load the event details from the database
function loadEvent($db, $eventID) {
    $query = "SELECT * FROM event WHERE eventID = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $eventID); // Bind the eventID parameter
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Establish database connection
$db = new mysqli($host, $user, $pass, $db);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the current event details
$event = loadEvent($db, $eventID);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $committee = $_POST['committeeName'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $venue = $_POST['venue'];
    $flyer = '';
    
 // Optional: Handle file upload for the flyer
$flyer = file_get_contents($_FILES['flyer']['tmp_name']); // Get the binary data of the uploaded file

// Update query
$updateQuery = "UPDATE event SET name=?, committee=?, startDate=?, endDate=?, startTime=?, endTime=?, venue=?";

if (!empty($flyer)) {
    $updateQuery .= ", flyer=?";
}

$updateQuery .= " WHERE eventID=?";

$stmt = $db->prepare($updateQuery);

// Bind parameters
if (!empty($flyer)) {
    $stmt->bind_param("ssssssssi", $name, $committee, $startDate, $endDate, $startTime, $endTime, $venue, $flyer, $eventID);
} else {
    $stmt->bind_param("sssssssi", $name, $committee, $startDate, $endDate, $startTime, $endTime, $venue, $eventID);
}

// Execute the update
if ($stmt->execute()) {
    header("Location: organizer-dashboard.php"); // Redirect back to the dashboard
    exit();
} else {
    echo "Error updating event: " . $stmt->error;
}

}

// Close the database connection
$db->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styling */
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
            line-height: 0.3;
        }

        /* Navigation Bar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #07257F;
            padding: 1px 30px;
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

        /* Event Form Container */
        .event-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 65px; /* Adjust to leave space for navbar */
        }

        h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        /* Form Styling */
        label {
            display: block;
            font-weight: 600;
            margin: 10px 0 5px;
            text-align: left;
        }

        input[type="text"], input[type="date"], input[type="time"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        /* File Input Specific Styling */
        input[type="file"] {
            padding: 6px; /* Adjust padding for file input */
            border: 1px dashed #007bff;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        input[type="file"]:hover {
            border-color: #0056b3; /* Change border color on hover */
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Message Styling */
        .message {
            color: green;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            font-weight: 600;
        }

        /* Back Button Styling */
        #backButton {
            background-color: #6c757d;
        }

        #backButton:hover {
            background-color: #5a6268;
        }

        /* Footer Styling */
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
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav>
    <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo"> <!-- Replace with your logo path -->
            <a style="padding: 20px 20px;" href="organizer-dashboard.php">Dashboard</a>
        </div>
        <div>
            <a href="admin-dashboard.php">Admin Dashboard</a>
            <a href="events.php">View Events</a>
            <a href="sign-out.php">Sign Out</a>
        </div>
    </nav>

    <div class="event-container">
    <h2>Update Event Details</h2>
    <form action="" method="post" enctype="multipart/form-data">
        
        <label for="name">Event Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
        
        <label for="organizingCommittee">Organizing Committee:</label>
            <select id="committeeName" name="committeeName">
                <option value="ComSociety">ComSociety</option>
                <option value="IEEE-StudentBranch">IEEE-StudentBranch</option>
                <option value="IEEE-WIE">IEEE-WIE</option>
            </select>
        
        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" value="<?php echo htmlspecialchars($event['startDate']); ?>" required>
        
        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" value="<?php echo htmlspecialchars($event['endDate']); ?>" required>
        
        <label for="startTime">Start Time:</label>
        <input type="time" name="startTime" value="<?php echo htmlspecialchars($event['startTime']); ?>" required>
        
        <label for="endTime">End Time:</label>
        <input type="time" name="endTime" value="<?php echo htmlspecialchars($event['endTime']); ?>" required>
        
        <label for="venue">Venue:</label>
            <select id="venue" name="venue" required>
                <option value="">Select Venue</option>
                <option value="DCS Auditorium">DCS Auditorium</option>
                <option value="CSL 1 and 2">CSL 1 and 2</option>
                <option value="CSL 3 and 4">CSL 3 and 4</option>
            </select>
        
        <label for="flyer">Event Flyer (Optional):</label>
        <input type="file" name="flyer" accept="image/*,application/pdf">
        
        <button type="submit">Update Event</button>
            <button type="button" id="backButton" onclick="window.location.href='Organizer-dashboard.php'">Back</button>
        </form>
    </div>

    <footer>
        <p>&copy;  <?php echo date("Y"); ?> Event Management System | <a href="organizer-contactUs.php">Contact Us</a> | <a href="organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>
