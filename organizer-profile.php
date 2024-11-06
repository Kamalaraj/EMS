<?php
require_once 'db_connection.php';
session_start();
if (isset($_POST['signOut'])) {
    session_unset();
    session_destroy();
    header('Location:login.php');
    exit();
}

// Check if the user is an organizer; otherwise, redirect to login page
if (!isset($_SESSION['usertype'])) {
    echo "<script>alert('You must be an organizer to access this page.'); window.location.href = 'login.php';</script>";
    exit();
}

function loadUserData($pdo) {
    $currentUserID = $_SESSION['userID'] ?? null;
    if ($currentUserID) {
        $stmt = $pdo->prepare("SELECT * FROM organizer WHERE userID = :userID");
        $stmt->execute(['userID' => $currentUserID]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['currentUser'] = $user; // Store user data in the session
            return $user;
        }
    }
    return null;
}

// Fetch current user's details from database
$currentUser = loadUserData($pdo);

// Handle profile image upload
if (isset($_POST['uploadImage'])) {
    // Check if a file was uploaded
    if (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] === UPLOAD_ERR_OK) {
        // Read the image file
        $imageData = file_get_contents($_FILES["profileImage"]["tmp_name"]);
        
        // Prepare to update the database with the image data
        $userID = $_SESSION['currentUser']['userID']; // Assuming the user ID is stored in session
        $stmt = $pdo->prepare("UPDATE organizer SET profileImage = ? WHERE userID = ?");
        
        // Execute the update with the binary image data
        if ($stmt->execute([$imageData, $userID])) {
            // Update the session with the image data (if needed)
            $_SESSION['currentUser']['profileImage'] = $imageData;
            echo "<script>alert('Profile image uploaded and saved in database successfully!');</script>";
        } else {
            echo "<script>alert('Sorry, there was an error updating the database.');</script>";
        }
    } else {
        echo "<script>alert('No file uploaded or there was an upload error.');</script>";
    }
}




// Fetch current user's details from session
$currentUser = $_SESSION['currentUser'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Profile</title>
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
        /* Container Styling */
        .auth-container {
            width: 90%;
            max-width: 800px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            line-height:2;
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

        /* Header Styling */
        h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #007bff;
        }

        /* Profile Information Styling */
        .profile-info {
            text-align: left;
            margin-top: 20px;
        }

        .profile-info p {
            font-size: 1.1em;
            margin: 10px 0;
        }

        .profile-info img {
            margin-bottom: 10px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Upload Button Styling */
        .file-upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .file-upload-container input[type="file"] {
            margin-bottom: 10px;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #007bff;
            border-radius: 5px;
        }

        /* Button Styling */
        .auth-container button,
        .auth-container a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            font-size: 1em;
            color: #ffffff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .auth-container button:hover,
        .auth-container a:hover {
            background-color: #0056b3;
        }

        /* Sign Out Button Styling */
        .auth-container button[name="signOut"] {
            background-color: #dc3545;
        }

        .auth-container button[name="signOut"]:hover {
            background-color: #b02a37;
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

            .auth-container {
                padding: 20px;
            }
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

    <div class="auth-container">
        <h2>Organizer Profile</h2>

        <div class="profile-info">
        <?php if (!empty($currentUser['profileImage'])): ?>
        <div class="profile-image-container" style="position: relative; display: inline-block;">
        <img src="data:image/jpeg;base64,<?php echo base64_encode($currentUser['profileImage']); ?>" alt="Profile Image" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
        <span class="edit-icon" onclick="toggleUploadForm()" style="position: absolute; bottom: 10px; right: 10px; cursor: pointer; background-color: #007bff; color: white; padding: 5px; border-radius: 50%;">
            &#9998; <!-- This is a pencil/edit icon -->
        </span>
        </div>
    <?php else: ?>
    <div class="profile-image-container" style="position: relative; display: inline-block;">
        <img src="default-profile.png" alt="Default Profile Image" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
        <span class="edit-icon" onclick="toggleUploadForm()" style="position: absolute; bottom: 10px; right: 10px; cursor: pointer; background-color: #007bff; color: white; padding: 5px; border-radius: 50%;">
            &#9998; <!-- This is a pencil/edit icon -->
        </span>
    </div>
<?php endif; ?>

<!-- Form to upload profile image -->
<div id="fileUploadContainer" class="file-upload-container" style="display: none; margin-top: 10px;">
    <form action="organizer-profile.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="profileImage" accept="image/*" required>
        <button type="submit" name="uploadImage">Upload Image</button>
    </form>
</div>

<script>
    // Function to toggle the visibility of the file upload form
    function toggleUploadForm() {
        const uploadForm = document.getElementById('fileUploadContainer');
        if (uploadForm.style.display === "none") {
            uploadForm.style.display = "block";
        } else {
            uploadForm.style.display = "none";
        }
    }
</script>
            <p><strong>Organizing Committee Name:</strong> <?php echo htmlspecialchars($currentUser['committeeName']); ?></p>
            <p><strong>Chair Person Name:</strong> <?php echo htmlspecialchars($currentUser['chairPersonName']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($currentUser['email']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($currentUser['username']); ?></p>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="Organizer-contactUs.php">Contact Us</a> | <a href="Organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>

