<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $subject = htmlspecialchars(strip_tags(trim($_POST['subject'])));
    $message = htmlspecialchars(strip_tags(trim($_POST['message'])));

    // Here, you can process the data, like sending an email or storing it in a database
    // For example, sending an email
    $to = "2021csc054@univ.jfn.ac.lk"; // Change to your email
    $headers = "From: $name <$email>" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $message, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Failed to send message. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(to right, #A1C4FD, #C2E9FB);
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column; /* Arrange elements vertically */
        min-height: 100vh; /* Full height for body */
    }

    nav {
        display: flex;
        justify-content: space-between;
        color: white;
        align-items: center;
        background-color: #07257F; /* Navbar background color */
        padding: 10px 20px;
        width: 100%; /* Make sure the nav takes full width */
        position: fixed; /* Fixed position */
        top: 0; /* Stick to the top */
        left: 0; /* Align with the left edge */
        z-index: 1000; /* Stay on top of other content */
    }

    nav img {
        height: 60px; /* Increase logo height */
        width: auto; /* Maintain aspect ratio */
        margin-right: 20px; /* Space between logo and links */
        border-radius: 10px;
    }

    nav a {
        color: white;
        padding: 10px 20px; /* Reduce padding for better alignment */
        text-decoration: none;
        margin: 0 10px;
        border-radius: 14px;
        font-size: 18px; /* Slightly smaller font size for better spacing */
        transition: background-color 0.3s ease;
    }

    nav a:hover {
        background-color: #0056b3;
    }

    .container {
        max-width: 600px;
        margin: 150px auto 30px; /* Center with auto margins and space below */
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #07257F; /* Header color */
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        color: #07257F;
    }

    input[type="text"], input[type="email"], textarea {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical; /* Allows vertical resizing of textarea */
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, input[type="email"]:focus, textarea:focus {
        border-color: #0A3AAE; /* Focus color */
        outline: none; /* Remove outline */
    }

    button {
        background-color: #0A3AAE; /* Button color */
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        margin-right:20px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0946C0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Hover color */
    }

    footer {
            background-color: #07257F; /* Match with the navbar color */
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            margin-top: auto; /* Ensures footer stays at the bottom */
            width: 100%;
            position: relative;
        }

        footer a {
            color: white;
            text-decoration: none;
            padding: 0 10px;
        }

       
        footer a:hover {
            color: #ffcc00; /* Highlight on hover */
            text-decoration: underline; /* Underline on hover */
        }
</style>
>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo">
            <h1>Event Management System</h1>
        </div>
        <div>
        <a href="login.php" style="background: white; border: none; color: black; cursor: pointer; 
            padding: 14px 20px;margin-right:30px; text-decoration: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 24px; font-size: 20px; transition: background-color 0.3s ease;"
            onmouseover="this.style.backgroundColor='#7D7F86'; this.style.color = 'white';" 
            onmouseout="this.style.backgroundColor='white'; this.style.color = 'black';">
            Sign In
        </a>
        </div>
    </nav>
    <div class="container">
        <h2>Contact Us</h2>
        <form action="send_message.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>
