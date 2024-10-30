<?php
// Define the path to your JSON file
$jsonFilePath = './events.json';
$userFilePath = './users.json';
$eventId = isset($_GET['eventId']) ? htmlspecialchars($_GET['eventId']) : null;
// Function to read events from the JSON file
function readEvents($jsonFilePath) {
    if (file_exists($jsonFilePath)) {
        $jsonData = file_get_contents($jsonFilePath);
        return json_decode($jsonData, true);
    }
    return [];
}

// Function to write events to the JSON file
function writeEvents($jsonFilePath, $events) {
    $jsonData = json_encode($events, JSON_PRETTY_PRINT);
    file_put_contents($jsonFilePath, $jsonData);
}

function getUserID($userFilePath, $username) {
    if (!file_exists($userFilePath)) {
        return null; // Return null if the file does not exist
    }
    $users = json_decode(file_get_contents($userFilePath), true);
    if (is_array($users)) {
        foreach ($users as $user) {
            if (isset($user['username']) && $user['username'] === $username) {
                return $user['userID'];; // Ensure this line correctly returns the userID
            }
        }
    }
    return null; // Return null if user not found
}
$username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';


$userID = getUserID($userFilePath, $username);

// Initialize variables
$message = '';
$error = '';
$eventData = [];

// Read events from the JSON file
$events = readEvents($jsonFilePath);
$eventExists = false;
foreach ($events as $event) {
    if ($event['id'] === $eventId) {
        $eventExists = true;
        break;
    }
}

// Check if an event ID is passed to the script
if (isset($_GET['id'])) {
    $eventId = htmlspecialchars($_GET['id']); // Sanitize event ID input
    // Find the event in the events array
    foreach ($events as $event) {
        if ($event['id'] === $eventId) {
            $eventData = $event;
            break;
        }
    }
    if (empty($eventData)) {
        $error = "Event not found.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $eventId = htmlspecialchars($_POST['eventId']);
    $eventName = htmlspecialchars(trim($_POST['eventName']));
    $organizingCommittee = htmlspecialchars(trim($_POST['committeeName'])); // Fix name attribute
    $startDate = htmlspecialchars(trim($_POST['startDate']));
    $endDate = htmlspecialchars(trim($_POST['endDate']));
    $startTime = htmlspecialchars(trim($_POST['startTime']));
    $endTime = htmlspecialchars(trim($_POST['endTime']));
    $venue = htmlspecialchars(trim($_POST['venue']));
    $flyer = '';

    if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['flyer']['tmp_name'];
        $fileName = $_FILES['flyer']['name'];
        $fileSize = $_FILES['flyer']['size'];
        $fileType = $_FILES['flyer']['type'];
    
        // Define the folder where you want to save the image
        $uploadDirectory = './upload_flyer/';
    
        // Ensure the uploads directory exists
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true); // Create directory if it doesn't exist
        }
    
        // Create a unique filename to avoid conflicts
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = uniqid('flyer_', true) . '.' . $fileExtension;
    
        // Define the destination path for the file
        $destPath = $uploadDirectory . $newFileName;
    
        // Validate file type (you can add more types if needed)
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (in_array($fileType, $allowedTypes) && $fileSize < 5000000) { // Max size 5MB
            // Move the uploaded file to the designated folder
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $flyer = $destPath; // Store the flyer path in the variable
            } else {
                $error = "Failed to move the uploaded file.";
            }
        } else {
            $error = "Invalid file type or size. Please upload a JPEG, PNG, or PDF file under 5MB.";
        }
    } else {
        // If no new file uploaded, keep the old flyer (if updating)
        foreach ($events as $event) {
            if ($event['id'] === $eventId) {
                $flyer = $event['flyer']; // Retain the old flyer
                break;
            }
        }
    }
    

    // Update event data
    if (empty($error)) {
        $updatedEvent = [
            'id' => $eventId,
            'name' => $eventName,
            'committee' => $organizingCommittee,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'venue' => $venue,
            'flyer' => $flyer,
            'userID' => $userID,
        ];

        // Find the index of the event to be updated
        foreach ($events as &$event) {
            if ($event['id'] === $eventId) {
                $event = $updatedEvent; // Update the event data
                break;
            }
        }
        writeEvents($jsonFilePath, $events); // Save updated events to the JSON file
        $message = "Event updated successfully!";
    }
}

// Include your HTML and form structure below
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
        <h2>Update Event</h2>
        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo $message; ?></div>
        <?php elseif (!empty($error)) : ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="eventId" value="<?php echo htmlspecialchars($eventData['id'] ?? ''); ?>">
            <label for="eventName">Event Name</label>
            <input type="text" name="eventName" id="eventName" value="<?php echo htmlspecialchars($eventData['name'] ?? ''); ?>" required>

            <label for="committeeName">Organizing Committee</label>
            <select id="committeeName" name="committeeName">
                <option value="ComSociety">ComSociety</option>
                <option value="IEEE-StudentBranch">IEEE-StudentBranch</option>
                <option value="IEEE-WIE">IEEE-WIE</option>
            </select>

            <label for="startDate">Start Date</label>
            <input type="date" name="startDate" id="startDate" value="<?php echo htmlspecialchars($eventData['startDate'] ?? ''); ?>" required>

            <label for="endDate">End Date</label>
            <input type="date" name="endDate" id="endDate" value="<?php echo htmlspecialchars($eventData['endDate'] ?? ''); ?>" required>

            <label for="startTime">Start Time</label>
            <input type="time" name="startTime" id="startTime" value="<?php echo htmlspecialchars($eventData['startTime'] ?? ''); ?>" required>

            <label for="endTime">End Time</label>
            <input type="time" name="endTime" id="endTime" value="<?php echo htmlspecialchars($eventData['endTime'] ?? ''); ?>" required>

            <label for="venue">Venue</label>
            <select id="venue" name="venue" required>
                <option value="">Select Venue</option>
                <option value="DCS Auditorium">DCS Auditorium</option>
                <option value="CSL 1 and 2">CSL 1 and 2</option>
                <option value="CSL 3 and 4">CSL 3 and 4</option>
            </select>

            <label for="flyer">Upload Flyer (Optional)</label>
            <input type="file" name="flyer" id="flyer">
            
            <label for="username">User Name:</label>
            <input type="text" name="username" placeholder="Enter your username" required>

            <button type="submit">Update Event</button>
            <button type="button" id="backButton" onclick="window.location.href='Organizer-dashboard.php'">Back</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Event Management System | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    </footer>
</body>
</html>
