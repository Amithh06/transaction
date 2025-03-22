
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background:url("i13.jpeg");
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-box {
            width: 350px;
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 255, 255, 0.2);
            text-align: center;
        }
        
        .input-box {
            margin: 20px 0;
            position: relative;
        }
        
        .input-box input, .input-box select {
            width: 100%;
            padding: 10px;
            background: transparent;
            border: none;
            border-bottom: 2px solid cyan;
            outline: none;
            color: white;
            font-size: 18px;
        }
        
        .input-box label {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            transition: 0.3s;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: 0;
            font-size: 14px;
            color: orange;
            text-shadow: 0px 0px 10px orange, 0px 0px 20px rgba(0, 255, 255, 0.7);
        }
        
        button {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            font-weight: 600;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.7s;
            position: relative;
            overflow: hidden;
        }
        
        button:hover {
            color: yellow;
            box-shadow: 0px 0px 20px cyan;
        }
        
        .register-link {
            margin-top: 15px;
            font-size: 14px;
        }
        
        .register-link a {
            color: yellow;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        option{
            background: #0a0a0a;
            color: cyan;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>LOG IN</h2>
            <form action="login.php" method="POST">
                <div class="input-box">
                    <select name="role" required>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="input-box">
                    <input type="text" name="userid" required>
                    <label>UserID</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <button type="submit">Login</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="signup.php">Create one</a>
            </div>
        </div>
    </div>
</body>
</html>


<?php
session_start();
include 'ayt.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $role = $_POST['role'];
    $username = trim($_POST['userid']);
    $password = trim($_POST['password']);

    // Hardcoded Admin Credentials
    $admin_username = "Admin";
    $admin_password = "Admin123"; // Ideally, this should also be hashed in a real-world application

    if ($role == "Admin") {
        // Admin login check
        if ($username === $admin_username && $password === $admin_password) {
            // Start session for Admin and redirect
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: admin_dashboard.php"); // Redirect to Admin Dashboard
            exit();
        } else {
            echo "<script>alert('Invalid Admin Credentials');</script>";
        }
    } else { 
        // User login - authenticate user from the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE userid = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Check if user status is 'active'
            if ($user['status'] !== 'active') {
                echo "<script>alert('Your account is not active yet. Please wait for admin approval.');</script>";
                exit();
            }

            // Verify password (use password_verify for hashed password storage in the database)
            if (password_verify($password, $user['password'])) {
                // Start session for User and redirect to homepage
                $_SESSION['user_logged_in'] = true;
                $_SESSION['userid'] = $user['userid'];
                $_SESSION['username'] = $user['full_name'];

                header("Location: homepage.php"); // Redirect user after login
                exit();
            } else {
                echo "<script>alert('Incorrect Password');</script>";
            }
        } else {
            echo "<script>alert('User not found');</script>";
        }
        $stmt->close();
    }
}
?>



