<?php
require_once 'db_connection.php'; // Include your database connection
// Initialize message variable
$message = '';
$event = null; // Initialize the event variable

// Check if 'id' is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $eventID = htmlspecialchars(trim($_GET['id']));

    // Fetch the event details from the database
    $stmt = $pdo->prepare("SELECT * FROM event WHERE eventID = ?");
    $stmt->execute([$eventID]);
    $event = $stmt->fetch();

    if (!$event) {
        $error = "Event not found.";
    }
} else {
    $error = "No event ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $eventName = htmlspecialchars(trim($_POST['eventName']));
    $organizingCommittee = htmlspecialchars(trim($_POST['committeeName']));
    $startDate = htmlspecialchars(trim($_POST['startDate']));
    $endDate = htmlspecialchars(trim($_POST['endDate']));
    $startTime = htmlspecialchars(trim($_POST['startTime']));
    $endTime = htmlspecialchars(trim($_POST['endTime']));
    $venue = htmlspecialchars(trim($_POST['venue']));
    $flyer = $event['flyer']; // Default to existing flyer

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

    // Update event details in the database
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE event SET name = ?, committee = ?, startDate = ?, endDate = ?, startTime = ?, endTime = ?, venue = ?, flyer = ? WHERE eventID = ?");
            $stmt->execute([$eventName, $organizingCommittee, $startDate, $endDate, $startTime, $endTime, $venue, $flyer, $eventID]);

            $message = "Event updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating event: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
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

        /* Navigation Bar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #07257F;
            padding: 5px 30px;
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
            margin-top: 75px; /* Adjust to leave space for navbar */
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
            color: #ffcc00;
            text-decoration: underline;
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
            <img src="image/logo.png" alt="Logo">
            <a href="organizer-dashboard.php">Dashboard</a>
        </div>
        <div>
            <a href="organizer-about.php">About Us</a>
            <a href="organizer-contactUs.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php">Sign out</a>
        </div>
    </nav>

    <div class="event-container">
        <h2>Update Event</h2>

        <!-- Display messages -->
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($event): ?>
            <form method="POST" enctype="multipart/form-data" action="event-update.php?id=<?php echo $eventID; ?>">
                <label for="eventName">Event Name:</label>
                <input type="text" id="eventName" name="eventName" value="<?php echo htmlspecialchars($event['name']); ?>" required>

                <label for="organizingCommittee">Organizing Committee:</label>
                <select id="committeeName" name="committeeName">
                    <option value="ComSociety" <?php echo $event['committee'] === 'ComSociety' ? 'selected' : ''; ?>>ComSociety</option>
                    <option value="IEEE-StudentBranch" <?php echo $event['committee'] === 'IEEE-StudentBranch' ? 'selected' : ''; ?>>IEEE-StudentBranch</option>
                    <option value="IEEE-WIE" <?php echo $event['committee'] === 'IEEE-WIE' ? 'selected' : ''; ?>>IEEE-WIE</option>
                </select>

                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate" value="<?php echo htmlspecialchars($event['startDate']); ?>" required>

                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate" value="<?php echo htmlspecialchars($event['endDate']); ?>" required>

                <label for="startTime">Start Time:</label>
                <input type="time" id="startTime" name="startTime" value="<?php echo htmlspecialchars($event['startTime']); ?>" required>

                <label for="endTime">End Time:</label>
                <input type="time" id="endTime" name="endTime" value="<?php echo htmlspecialchars($event['endTime']); ?>" required>

                <label for="venue">Venue:</label>
                <select id="venue" name="venue" required>
                    <option value="DCS Auditorium" <?php echo $event['venue'] === 'DCS Auditorium' ? 'selected' : ''; ?>>DCS Auditorium</option>
                    <option value="CSL 1 and 2" <?php echo $event['venue'] === 'CSL 1 and 2' ? 'selected' : ''; ?>>CSL 1 and 2</option>
                    <option value="CSL 3 and 4" <?php echo $event['venue'] === 'CSL 3 and 4' ? 'selected' : ''; ?>>CSL 3 and 4</option>
                </select>

                <label for="flyer">Upload Flyer or Poster:</label>
                <input type="file" id="flyer" name="flyer" accept=".jpg, .jpeg, .png, .pdf">
                
                <button type="submit">Update Event</button>
            </form>
        <?php endif; ?>
        <button onclick="window.location.href='organizer-dashboard.php'">Back to Events</button>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="Organizer-contactUs.php">Contact Us</a> | <a href="Organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>
