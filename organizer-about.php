<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Event Management System</title>
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

        /* About Us Container */
        .about-container {
            width: 100%;
            max-width: 1500px;
            background-color:#E0E0AA;
            padding: 60px;
            margin: 100px auto;
            border-radius: 15px;
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

            .about-container {
                padding: 20px;
            }
        }

    </style>
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center;">
            <img src="image/logo.png" alt="Logo"> <!-- Replace with your logo path -->
            <h1> Event Management System</h1>
        </div>
        <div>
            <a href="organizer-dashboard.php">Dashboard</a>
            <a href="organizer-about.php">About Us</a>
            <a href="organizer-contactUs.php">Contact Us</a>
            <a href="organizer-profile.php">Profile</a>
            <a href="login.php">Sign Out</a>
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
        <p>&copy; <?php echo date("Y"); ?> Event Management System | <a href="organizer-contactUs.php">Contact Us</a> | <a href="organizer-about.php">About Us</a></p>
    </footer>
</body>
</html>
