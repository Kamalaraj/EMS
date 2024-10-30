<?php
session_start(); // Start the session for user management
// Define the path to your JSON file

$error = '';
$success = '';
$eventId = '';

$eventId = isset($_GET['eventId']) ? htmlspecialchars($_GET['eventId']) : null;




// Check if the file exists before attempting to read

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

// Function to save registrations to a JSON file for a specific event
function saveRegistrations($eventId, $registrations) {
    // Check if the registrations directory exists, if not create it
    if (!file_exists('registrations')) {
        mkdir('registrations', 0777, true); // Create directory if it doesn't exist
    }

    // Use double quotes for variable interpolation
    $file = "registrations/registration_{$eventId}.json";
    
    // Attempt to write to the file and handle possible errors
    if (file_put_contents($file, json_encode($registrations, JSON_PRETTY_PRINT)) === false) {
        echo "Failed to write to file: $file";
    } else {
        //echo "Registrations saved successfully to: $file";
    }
}

// Function to get registrations from a JSON file for a specific event
function getRegistrations($eventId) {
    $file = "registrations/registration_{$eventId}.json"; // Use double quotes for variable interpolation
    if (!file_exists($file)) {
        return []; // Return an empty array if the file doesn't exist
    }
    return json_decode(file_get_contents($file), true);
}

function getEventCreatorUserID($eventId) {
    $file = 'events.json';
    
    // Ensure events.json exists and read its contents
    if (!file_exists($file)) {
        return 'Unknown'; // Default value if file doesn't exist
    }

    $events = json_decode(file_get_contents($file), true);

    // Search for the event by ID and return EventCreatorUserID if found
    foreach ($events as $event) {
        if (isset($event['id']) && $event['id'] === $eventId) {
            return $event['userID'] ?? 'Unknown'; // Fallback to 'Unknown' if key isn't found
        }
    }
    return 'Unknown'; // Return default if event ID isn't found
}

$eventCreatorUserID = getEventCreatorUserID($eventId);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $registrationNumber = trim($_POST['registrationNumber']);
    $levelOfStudy = trim($_POST['levelOfStudy']);
    $eventId = isset($_POST['eventId']) ? $_POST['eventId'] : '';
    $eventId = isset($_POST['eventCreatorUserID']) ? $_POST['eventCreatorUserID'] : '';
    $registrationDate = trim($_POST['registrationDate']);
    $registrationID = trim($_POST['registrationID']);


    

    // Validate form data
    if (empty($firstName) || empty($lastName) || empty($registrationNumber) || empty($levelOfStudy)) {
        $error = "Please fill in all fields.";
    } else {
        // Fetch existing registrations from the JSON file
        $registrations = getRegistrations($eventId);

        // Check for duplicate registration number within the same event
        foreach ($registrations as $reg) {
            if ($reg['eventId'] === $eventId && $reg['registrationNumber'] === $registrationNumber) {
                $error = "Registration number already exists for this event. Please use a different one.";
                break;
            }
        }

        // If no errors, add new registration data
        if (empty($error)) {
            $registrationID = 'R' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 9));
            $registrationData = [
                'registrationID' => $registrationID,
                'eventId' => $eventId,
                'eventCreatorUserID' => $eventCreatorUserID,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'registrationNumber' => $registrationNumber,
                'levelOfStudy' => $levelOfStudy,
                'registrationDate' => $registrationDate,
                'type' => 'student',
                'approval' => 'Pending'
            ];
            $registrations[] = $registrationData; // Add new registration

            // Save registrations to JSON file for the specific event
            saveRegistrations($eventId, $registrations);

            $success = "Registration successful!";
            // Optionally, redirect to the dashboard
            // header("Location: student-dashboard.php");
            // exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
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
            max-width: 600px;
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
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="text"], select, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        #backButton {
            background-color: #007bff;
            color: white;
            font-size: 14px;
            cursor: pointer;
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
    <h1> Student Registration for Event ID: <?php echo htmlspecialchars($eventId); ?></h1>
        <!-- Display error or success message -->
        <div class="message">
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif ($success): ?>
                <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
        </div>

        <!-- Registration Form -->
        <form id="studentRegistrationForm" method="POST" action="">
            <input type="hidden" name="eventId" value="<?php echo htmlspecialchars($eventId); ?>">
            <input type="hidden" name="registrationID" value="<?php echo htmlspecialchars($registrationID); ?>">

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required>

            <label for="registrationNumber">Registration Number:</label>
            <input type="text" id="registrationNumber" name="registrationNumber" required>

            <label for="levelOfStudy">Level of Study:</label>
            <select name="levelOfStudy" id="levelOfStudy" required>
                <option value="">Select here</option>
                <option value="Level 1">Level 1</option>
                <option value="Level 2">Level 2</option>
                <option value="Level 3">Level 3</option>
                <option value="Level 4">Level 4</option>
            </select>
            
            <label for="registrationDate">Registration Date:</label>
            <input type="date" id="registrationDate" name="registrationDate" required>
            <button type="submit">Register</button>
        </form>

        <button id="backButton" onclick="goBack()">Back to Events</button>
    </div>
    <script>
        function goBack() {
            window.history.back(); // Go back to the previous page
        }
    </script>
    <footer>
        <p>&copy; 2024 Event Management System | <a href="student-contactUs.php">Contact Us</a> | <a href="student-about.php">About Us</a></p>
    </footer>
</body>
</html>

