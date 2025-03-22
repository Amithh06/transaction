<?php
session_start();
include 'ayt.php'; // Ensure this file connects to the database

// Fetch users with status 'pending' (assuming user status is 'pending' when registered)
$query = "SELECT * FROM users WHERE status = 'pending'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error: " . mysqli_error($conn)); // Show error if query fails
}

if (isset($_POST['accept'])) {
    $id = $_POST['user_id'];
    $_SESSION['user_id'] = $id;

    // Update user status to 'active' when admin accepts the request
    $updateStatus = "UPDATE users SET status = 'active' WHERE userid = ?";
    $stmt = mysqli_prepare($conn, $updateStatus);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);



    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_POST['decline'])) {
    $id = $_POST['user_id'];
    
    // If admin declines, just remove the pending user (no need for user_requests table)
    $deleteUser = "DELETE FROM users WHERE userid = ?";
    $stmt = mysqli_prepare($conn, $deleteUser);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: url("i15.webp");
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex-direction: column;
        }

        h1 {
            text-shadow: none;
            border-bottom: 2px solid cyan;
            width: 1300px;
            margin: 20px auto;
            padding: 20px;
            margin-top: 40px;
            position: absolute;
            top: 40px;
            font-size: 40px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        table {
            width: 90%;
            max-width: 1000px;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0px 0px 20px rgba(0, 255, 255, 0.5);
            overflow: hidden;
            border: 2px solid cyan;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 2px solid rgba(0, 255, 255, 0.5);
            transition: 0.4s;
        }

        th {
            background: rgba(0, 0, 0, 0.8);
            color: yellow;
        }

        button {
            padding: 8px 15px;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.5s;
        }

        button:hover {
            background: none;
            color: yellow;
            box-shadow: 0px 0px 20px cyan;
        }

        .logout {
            margin-top: 30px;
        }

        .logout a {
            text-decoration: none;
            color: yellow;
            font-size: 18px;
            transition: 0.4s;
        }

        .logout a:hover {
            text-decoration: underline;
            text-shadow: 0px 0px 15px orange;
        }
    </style>
</head>
<body>
    <h1>Admin Interface</h1>
    <h2>Pending User Requests</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['userid']); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $row['userid']; ?>">
                        <button type="submit" name="accept">Accept</button>
                        <button type="submit" name="decline">Decline</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <div class="logout">
        <a href="login.php">Logout</a>
    </div>
</body>
</html>
