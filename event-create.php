<?php

require_once 'db_connection.php'; // Include your database connection
// Initialize message variable
$message = '';
$error = '';


function generateUniqueEventID($pdo) {
    while (true) {
        $eventID = 'E' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 9));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM event WHERE eventID = ?");
        $stmt->execute([$eventID]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            return $eventID; // Return if it's unique
        }
    }
}



$username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
$userID = ''; // Initialize userID variable

if (!empty($username)) {
    $stmt = $pdo->prepare("SELECT userID FROM user WHERE username = ?");
    $stmt->bindParam(1, $username);
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) {
        $userID = $result['userID'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $eventId = generateUniqueEventID($pdo);
    // Validate and sanitize input data
    $eventName = htmlspecialchars(trim($_POST['eventName']));
    $organizingCommittee = htmlspecialchars(trim($_POST['committeeName']));
    $startDate = htmlspecialchars(trim($_POST['startDate']));
    $endDate = htmlspecialchars(trim($_POST['endDate']));
    $startTime = htmlspecialchars(trim($_POST['startTime']));
    $endTime = htmlspecialchars(trim($_POST['endTime']));
    $venue = htmlspecialchars(trim($_POST['venue']));
    $flyer = '';

    // Handle file upload
    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['flyer']['tmp_name'];
        $fileName = $_FILES['flyer']['name'];
        $fileSize = $_FILES['flyer']['size'];
        $fileType = $_FILES['flyer']['type'];

        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (in_array($fileType, $allowedTypes) && $fileSize < 5000000) { // Max size 5MB
            // Read the file content
            $flyer = file_get_contents($fileTmpPath);
        } else {
            $error = "Invalid file type or size. Please upload a JPEG, PNG, or PDF file under 5MB.";
        }
      }
    

    // Insert event details into the database
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO event (eventID, name, committee, startDate, endDate, startTime, endTime, venue, flyer, userID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$eventId, $eventName, $organizingCommittee, $startDate, $endDate, $startTime, $endTime, $venue, $flyer, $userID]);

            $message = "Event created successfully!";
        } catch (PDOException $e) {
            $error = "Error inserting event: " . $e->getMessage();
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="style.css">
    <style>
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


        /* Event Form Container */
        .event-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 30px auto;
            font-size:20px; /* Adjust to leave space for navbar */
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

            nav a {
                font-size: 18px; /* Adjust font size for links */
            }

            .event-container {
                padding: 20px;
            }
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-weight: 600;
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
            <a href="organizer-contactUs.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php">Sign Out</a>
        </div>
    </nav>

    <div class="event-container">
        <h2>Create Event</h2>

        <!-- Display messages -->
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="eventId" value="<?php echo $eventId; ?>">

            <label for="eventName">Event Name:</label>
            <input type="text" id="eventName" name="eventName" required>

            <label for="organizingCommittee">Organizing Committee:</label>
            <select id="committeeName" name="committeeName">
                <option value="ComSociety">ComSociety</option>
                <option value="IEEE-StudentBranch">IEEE-StudentBranch</option>
                <option value="IEEE-WIE">IEEE-WIE</option>
            </select>

            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" required>

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" required>

            <label for="startTime">Start Time:</label>
            <input type="time" id="startTime" name="startTime" required>

            <label for="endTime">End Time:</label>
            <input type="time" id="endTime" name="endTime" required>

            <label for="venue">Venue:</label>
            <select id="venue" name="venue" required>
                <option value="">Select Venue</option>
                <option value="DCS Auditorium">DCS Auditorium</option>
                <option value="CSL 1 and 2">CSL 1 and 2</option>
                <option value="CSL 3 and 4">CSL 3 and 4</option>
            </select>

            <label for="flyer">Upload Flyer or Poster:</label>
            <input type="file" id="flyer" name="flyer" accept=".jpg, .jpeg, .png, .pdf">
            
            <label for="username">User Name:</label>
            <input type="text" name="username" placeholder="Enter your username" required>
            <button type="submit">Create Event</button>
        </form>
        <button id="backButton" onclick="window.location.href='organizer-dashboard.php'">Back to Events</button>
    </div>


    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="Organizer-contactUs.php">Contact Us</a> | <a href="Organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>