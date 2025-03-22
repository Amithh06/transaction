<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: black;
            color: white;
            text-align: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            padding-bottom: 30px;
        }

        .profile-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: orange;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: black;
            cursor: pointer;
            transition: 0.6s;
            position: absolute;
            top: 30px;
            right: 40px;
        }

        .profile-logo:hover {
            box-shadow: 0 0 30px yellow;
        }

        .profile-section {
            width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid cyan;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            display: none; /* Initially hidden */
            opacity: 0; 
            transform: translateY(-50px); /* Slide Up Start */
            transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out; 
        }
        .user{
            width: 500.0px;
            margin: 20px 31.0px;
            padding: 50px;
            border: 2px solid cyan;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            display:inline;
            
        }
        .it{display: flex;
            flex-direction: row;

        }
        .user a{
            font-weight:bold;
            font-size:20px;
            text-decoration:none;
            color:white;
        }
        .briefview {
           
            width: 297.8px;
            margin: 21.8px;
             padding: 9px;
            border: 2px solid cyan;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            display:inline;
        }
        .briefview button{
            font-weight:bold;
            font-size:20px;
            background: transparent;
            color:white;
            border:none;
        }

       
        .show-profile {
            display: block; /* Show the section */
            opacity: 1; 
            transform: translateY(0); /* Slide Down */
        }

        .about-section {
            display: block;
            margin-bottom: 50px;
            width: 1300px;
            margin: 20px auto;
            padding: 20px;
            border-top: 2px solid cyan;
        }

        .button {
            padding: 10px 20px;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.6s;
            position: absolute;
            bottom: 20px;
            right: 40px;
        }

        .button:hover {
            color: yellow;
            box-shadow: 0px 0px 20px cyan;
        }

        h1 {
            border-bottom: 2px solid cyan;
            width: 1300px;
            margin: 20px auto;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['userid'])) {
        echo "<script>alert('Please log in first'); window.location.href='login.php';</script>";
        exit();
    }
    
    $username = $_SESSION['username'];
    $firstLetter = strtoupper(substr($username, 0, 1)); 
    ?>

    <h1>Welcome, <?php echo $username; ?>!</h1>

    <!-- Profile Logo with Click Event -->
    <div class="profile-logo" onclick="toggleProfile()">
        <?php echo $firstLetter; ?>
    </div>

    <div class="it">
    <div class="user" id="user_input">
    <a href="userinput.php">Add Expence</a>
    </div>

    <div class="user" id="vtable">
        <a href="vtableandchart.php">View Table and Chart</a>
    </div>
    </div>

    <div class="briefview">
        <button> Quick Look</button>
    </div>
    
    <div class="profile-section" id="profile">
        <h2>Your Profile</h2>
        <p><b>Name:</b> <?php echo $username; ?></p>
        <p>Update your details soon!</p>
    </div>

    <div class="about-section">
        <h2>About Us</h2>
        <p>Welcome to our platform. We are here to help you manage your finances better.</p>
    </div>

    <!-- Logout Button -->
    <button class="button" onclick="window.location.href='login.php'">Logout</button>

    <script>
        function toggleProfile() {
            var profile = document.getElementById("profile");

            // If the section is hidden, show it with animation
            if (profile.classList.contains("show-profile")) {
                profile.classList.remove("show-profile");
                setTimeout(() => {
                    profile.style.display = "none"; // Hide after animation
                }, 600); // Wait for animation to complete
            } else {
                profile.style.display = "block"; // Show before animation
                setTimeout(() => {
                    profile.classList.add("show-profile");
                }, 10); // Add small delay for smooth effect
            }
        }
    </script>
</body>
</html>
