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

            .container {
                padding: 20px;
            }
        }
</style>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo">
            <h1>Event Management System</h1>
        </div>
        <div>
        <a href="login.php">Sign In</a>
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
