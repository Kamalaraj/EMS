<?php
session_start();
if (isset($_POST['signOut'])) {
    session_unset();
    session_destroy();
    header('Location:login.php');
    exit();
}

// Check if the user is an organizer; otherwise, redirect to login page
if (!isset($_SESSION['currentUser']) || $_SESSION['currentUser']['type'] !== 'organizer') {
    echo "<script>alert('You must be an organizer to access this page.'); window.location.href = 'login-signup.php';</script>";
    exit();
}

function loadUserData() {
    $jsonFile = 'users.json'; // Update with the actual path to your JSON file
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $users = json_decode($jsonData, true);
        
        // Assuming you have a mechanism to get the current user's identifier, like their username or ID
        // Here, we'll simulate getting the current user from the session
        $currentUserID = $_SESSION['userID'] ?? null; // Update as necessary to match your logic
        
        foreach ($users as $user) {
            if ($user['userID'] == $currentUserID) {
                $_SESSION['currentUser'] = $user; // Store the user data in the session
                return $user;
            }
        }
    }
    return null; // Return null if user not found
}

// Handle profile image upload
if (isset($_POST['uploadImage'])) {
    $targetDir = "./upload_organizer/"; // Folder to store uploaded images (ensure the trailing slash)
    $targetFile = $targetDir . basename($_FILES["profileImage"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the uploaded file is an image
    $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
    if ($check !== false) {
        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            // Update the session with the image path
            $_SESSION['currentUser']['profile_image'] = $targetFile;
            $currentUser['profile_image'] = $targetFile;

            // Success message
            echo "<script>alert('Profile image uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Sorry, there was an error uploading your image.');</script>";
        }
    } else {
        echo "<script>alert('File is not an image.');</script>";
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #A1C4FD, #C2E9FB);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
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

        /* Container Styling */
        .auth-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            padding: 50px;
            margin-top: 120px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
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

        /* Footer */
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
            <a href="organizer-contactUs.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php" style="background: white; border: none; color: black; cursor: pointer; padding: 10px 20px; margin-right: 30px; text-decoration: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); border-radius: 24px; font-size: 20px; transition: background-color 0.3s ease;"
               onmouseover="this.style.backgroundColor='#7D7F86'; this.style.color = 'white';"
               onmouseout="this.style.backgroundColor='white'; this.style.color='black';">Sign out</a>
        </div>
    </nav>

    <div class="auth-container">
        <h2>Organizer Profile</h2>

        <div class="profile-info">
        <?php if (!empty($currentUser['profile_image'])): ?>
    <div class="profile-image-container" style="position: relative; display: inline-block;">
        <img src="<?php echo htmlspecialchars($currentUser['profile_image']); ?>" alt="Profile Image" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
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
            <p><strong>Organizer ID:</strong> <?php echo isset($currentUser['userID']) ? htmlspecialchars($currentUser['userID']) : 'Not Available'; ?></p>
            <p><strong>Organizing Committee Name:</strong> <?php echo htmlspecialchars($currentUser['committee']); ?></p>
            <p><strong>Chair Person Name:</strong> <?php echo htmlspecialchars($currentUser['chairPerson']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($currentUser['organizer_email']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($currentUser['username']); ?></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Event Management System | <a href="Organizer-contactUs.php">Contact Us</a> | <a href="Organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>

