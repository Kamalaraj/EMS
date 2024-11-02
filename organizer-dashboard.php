<?php
session_start();
require_once 'db_connection.php'; // Include database connection
if (!isset($_SESSION['userID'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$loggedInUserID = $_SESSION['userID']; // Store logged-in user's ID

// Load events from the database
function loadEvents($db) {
    $events = [];
    $query = "SELECT eventID, name, committee, startDate, endDate, startTime,endTime,venue,flyer,userID FROM event"; // Include eventID
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }

    return $events;
}




// Establish database connection
$db = new mysqli($host, $user, $pass, $db);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}



// Load events for displaying
$events = loadEvents($db);
$currentDate = date('Y-m-d'); // Get today's date in 'YYYY-MM-DD' format

// Separate events into categories
$previousEvents = [];
$currentEvents = [];
$upcomingEvents = [];

foreach ($events as $event) {
    if ($event['endDate'] < $currentDate) {
        $previousEvents[] = $event;
    } elseif ($event['startDate'] <= $currentDate && $event['endDate'] >= $currentDate) {
        $currentEvents[] = $event;
    } else {
        $upcomingEvents[] = $event;
    }
}


// Handle sign-out
if (isset($_POST['signOut'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
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
            margin-top:10px;
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

        /* Container Styling */
        .auth-container {
            width: 90%;
            max-width: 1000px;
            background-color: #EBE3DA;
            padding: 30px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Header Styling */
        h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #07257F;
            text-align: center;
        }

        /* Event List Styling */
        #eventList {
            margin-top: 30px;
        }
        #eventList h2{
            color:#183487;
        }

        /* Event Item Styling */
        .event-item {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-item h3 {
            font-size: 1.8em;
            color:#183487;
        }

        .event-item p {
            margin: 5px 0;
            font-size: 1em;
            color: #333;
        }
        

        .event-item a {
            text-decoration: none;
            padding: 10px 15px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 10px;
            margin-left: 10px;
            transition: background-color 0.3s ease;
            float:left;
            display:inline-block;
        }

        .event-item a:hover {
            background-color: #0056b3;
        }

        /* Button Styling */
        .auth-container button {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            font-size: 1em;
            color: #ffffff;
            background-color: #007bff;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .auth-container button:hover {
            background-color: #0056b3;
        }
        /* Flyer Section Styling */
        .flyer {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 1em;
            color: #333;
        }

        .flyer h4 {
            font-size: 1.2em;
            color: #07257F;
            margin-bottom: 10px;
        }

        .flyer img {
            display: block;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 100%; /* Ensures it doesn't exceed container width */
            height: auto;
        }

        .flyer a {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .flyer a:hover {
            background-color: #0056b3;
        }

        .flyer p {
            margin-top: 10px;
            font-style: italic;
            color: #666;
        }
        .tab-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .tab-buttons button {
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .tab-buttons button.active {
            background-color: #0056b3;
        }

        .tab-content {
            display: none; /* Hide all sections by default */
        }

        .tab-content.active {
            display: block; /* Show the active section */
        }


        /* Footer Styling */
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
     <script>
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove 'active' class from all buttons
            document.querySelectorAll('.tab-buttons button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab and highlight the button
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
        }
    </script>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo"> <!-- Replace with your logo path -->
            <a href="organizer-dashboard.php">Dashboard</a>
        </div>
        <div>
            <a href="organizer-about.php">About Us</a>
            <a href="organizer-contactUs.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php">Sign Out</a>
        </div>
    </nav>

    <div class="auth-container">
        <h2>Welcome, Organizer!</h2>

        <button onclick="location.href='event-create.php'">Create New Event</button>
        
        <div class="tab-buttons">
            <button data-tab="current-events" class="active" onclick="showTab('current-events')">Current Events</button>
            <button data-tab="upcoming-events" onclick="showTab('upcoming-events')">Upcoming Events</button>
            <button data-tab="previous-events" onclick="showTab('previous-events')">Previous Events</button>
        </div>

        <div id="eventList">
        <div id="previous-events" class="tab-content">
            <h2>Previous Events</h2>
            <?php if (empty($previousEvents)): ?>
                <p>No previous events.</p>
            <?php else: ?>
                <?php foreach ($previousEvents as $event): ?>
                    <div class="event-item">
                        <div>
                            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                            <p>Event ID: <?php echo htmlspecialchars($event['eventID']); ?></p>
                            <p>Organizing Committee: <?php echo htmlspecialchars($event['committee']); ?></p>
                            <p>Date: <?php echo htmlspecialchars($event['startDate']); ?> to <?php echo htmlspecialchars($event['endDate']); ?></p>
                            <p>Time: <?php echo htmlspecialchars($event['startTime']); ?> to <?php echo htmlspecialchars($event['endTime']); ?></p>
                            <p>Venue: <?php echo htmlspecialchars($event['venue']); ?></p>
                            <p class="flyer">Flyer/Poster: <br><br>
                                <?php 
                                    if (!empty($event['flyer'])) {
                                        $fileExtension = pathinfo($event['flyer'], PATHINFO_EXTENSION);
                                        $flyerPath = htmlspecialchars($event['flyer']);
                                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                                            echo '<img src="' . $flyerPath . '" alt="Event Flyer" style="max-width: 300px; height: auto;">';
                                        } elseif ($fileExtension === 'pdf') {
                                            echo '<a href="' . $flyerPath . '" target="_blank">View Flyer (PDF)</a>';
                                        }
                                    } else {
                                        echo '<p>No flyer uploaded for this event.</p>';
                                    }
                                ?>
                            </p>
                        </div>
                        <div>
                                <?php if ($event['userID'] === $loggedInUserID): ?>

                                    <a href="student-detail.php?id=<?php echo $event['eventID']; ?>">View Student Details</a>
                                <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div id="current-events" class="tab-content active">
            <h2>Current Events</h2>
            <?php if (empty($currentEvents)): ?>
                <p>No current events.</p>
            <?php else: ?>
                <?php foreach ($currentEvents as $event): ?>
                    <div class="event-item">
                        <div>
                            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                            <p>Event ID: <?php echo htmlspecialchars($event['eventID']); ?></p>
                            <p>Organizing Committee: <?php echo htmlspecialchars($event['committee']); ?></p>
                            <p>Date: <?php echo htmlspecialchars($event['startDate']); ?> to <?php echo htmlspecialchars($event['endDate']); ?></p>
                            <p>Time: <?php echo htmlspecialchars($event['startTime']); ?> to <?php echo htmlspecialchars($event['endTime']); ?></p>
                            <p>Venue: <?php echo htmlspecialchars($event['venue']); ?></p>
                            <p class="flyer">Flyer/Poster: <br><br>
                                <?php 
                                    if (!empty($event['flyer'])) {
                                        $fileExtension = pathinfo($event['flyer'], PATHINFO_EXTENSION);
                                        $flyerPath = htmlspecialchars($event['flyer']);
                                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                                            echo '<img src="' . $flyerPath . '" alt="Event Flyer" style="max-width: 300px; height: auto;">';
                                        } elseif ($fileExtension === 'pdf') {
                                            echo '<a href="' . $flyerPath . '" target="_blank">View Flyer (PDF)</a>';
                                        }
                                    } else {
                                        echo '<p>No flyer uploaded for this event.</p>';
                                    }
                                ?>
                            </p>
                        </div>
                        <div>
                                <?php if ($event['userID'] === $loggedInUserID): ?>
                                    <a href="event-update.php?id=<?php echo $event['eventID']; ?>">Edit Event</a>
                                    <a href="student-detail.php?id=<?php echo $event['eventID']; ?>">View Student Details</a>
                                <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="upcoming-events" class="tab-content">
            <h2>Upcoming Events</h2>
            <?php if (empty($upcomingEvents)): ?>
                <p>No upcoming events.</p>
            <?php else: ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event-item">
                        <div>
                            <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                            <p>Event ID: <?php echo htmlspecialchars($event['eventID']); ?></p>
                            <p>Organizing Committee: <?php echo htmlspecialchars($event['committee']); ?></p>
                            <p>Date: <?php echo htmlspecialchars($event['startDate']); ?> to <?php echo htmlspecialchars($event['endDate']); ?></p>
                            <p>Time: <?php echo htmlspecialchars($event['startTime']); ?> to <?php echo htmlspecialchars($event['endTime']); ?></p>
                            <p>Venue: <?php echo htmlspecialchars($event['venue']); ?></p>
                            <p class="flyer">Flyer/Poster: <br><br>
                                <?php 
                                    if (!empty($event['flyer'])) {
                                        $fileExtension = pathinfo($event['flyer'], PATHINFO_EXTENSION);
                                        $flyerPath = htmlspecialchars($event['flyer']);
                                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                                            echo '<img src="' . $flyerPath . '" alt="Event Flyer" style="max-width: 300px; height: auto;">';
                                        } elseif ($fileExtension === 'pdf') {
                                            echo '<a href="' . $flyerPath . '" target="_blank">View Flyer (PDF)</a>';
                                        }
                                    } else {
                                        echo '<p>No flyer uploaded for this event.</p>';
                                    }
                                ?>
                            </p>
                        </div>
                        <div>
                                <?php if ($event['userID'] === $loggedInUserID): ?>
                                    <a href="event-update.php?id=<?php echo $event['eventID']; ?>">Edit Event</a>
                                    <a href="student-detail.php?id=<?php echo $event['eventID']; ?>">View Student Details</a>
                                <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>


    </div>

    <footer>
        <p>&copy;  <?php echo date("Y"); ?> Event Management System | <a href="organizer-contactUs.php">Contact Us</a> | <a href="organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>
