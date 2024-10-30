<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Event Management System</title>
    <style>
        /* General Reset */
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

        nav h1 {
            color: white;
            font-size: 24px; /* Adjusted size for better visibility */
            margin: 0; /* Remove margin */
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

        /* About Us Container */
        .about-container {
            width: 100%;
            max-width: 1000px;
            background-color: #E0E0AA;
            padding: 40px;
            margin: 100px auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: block;
            line-height:1.8;
        }

        /* Heading Styles */
        h2 {
            text-align: center;
            color: #07257F; /* Header color */
            margin-bottom: 20px;
            font-size: 28px; /* Larger font size for emphasis */
        }

        h3 {
            color: #07257F;
            margin: 20px 0 10px; /* Adjust spacing */
        }

        p {
            color: #333; /* Darker text for better readability */
            margin: 10px 0; /* Add space between paragraphs */
            font-size: 16px; /* Font size for body text */
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

    <div class="about-container">
        <h2>About Our Event Management System</h2>
        <p>Welcome to our Event Management System (EMS), designed to streamline the process of organizing and managing events efficiently. Our platform offers a range of features for organizers and participants, ensuring a smooth experience from event creation to registration and attendance.</p>
        
        <hr>

        <h3>Our Vision</h3>
        <p>To be a leading platform in event management, revolutionizing how events are organized and experienced worldwide.</p>

        <hr>

        <h3>Our Mission</h3>
        <p>Our mission is to provide an intuitive and user-friendly system that enhances collaboration among organizers, fosters community engagement, and facilitates seamless event participation for everyone involved.</p>
    </div>
    <footer>
        <p>&copy; 2024 Event Management System | <a href="contactUS.php">Contact Us</a> | <a href="about.php">About Us</a></p>
    </footer>
</body>
</html>
