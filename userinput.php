<?php
session_start(); // Start the session
include 'ayt.php'; // Ensure database connection is included

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['userid']; 

// Fetch user status from the database
$query = "SELECT status FROM users WHERE userid = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Check if the user is active
if ($user['status'] !== 'active') {
    echo "<script>alert('Your account is not active. Please wait for admin approval.'); window.location.href='homepage.php';</script>";
    exit();
}

if (isset($_POST['add'])) {
    // Get the form data
    $date = $_POST['date'];
    $cdate = $_POST['cdate'];
    $agentname = ($_POST['agentname'] === 'Other') ? $_POST['customAgentName'] : $_POST['agentname'];
    $totalAmount = $_POST['totalAmount'];
    $paidAmount = $_POST['paidAmount'];
    $sugama = $_POST['sugama'];
    $ganesh = $_POST['ganesh'];
    $others = $_POST['others'];
    $ac = $_POST['ac'];
    $laguage = $_POST['laguage'];
    $remark = $_POST['remark'];

    $tableName = "transaction"; // Construct the table name dynamically for monthly data

    // Check if the expense already exists for that month and year, if so, update it, otherwise insert it
    $checkExpenseQuery = "SELECT * FROM $tableName WHERE agentname = ? AND userid = ?";
    $stmt = mysqli_prepare($conn, $checkExpenseQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $agentname, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Expense already exists, update the value for that month
        $updateQuery = "UPDATE $tableName SET date = ?, cdate= ? , totalAmount = ?, paidAmount = ?, sugama = ?, ganesh = ?, others = ?, laguage = ?, ac = ?, remark = ? WHERE agentname = ? AND userid = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', $date,$cdate, $totalAmount, $paidAmount, $sugama, $ganesh, $others, $laguage, $ac, $remark, $agentname, $user_id);
        mysqli_stmt_execute($stmt);
    } else {
        // Insert new expense
        $insertQuery = "INSERT INTO $tableName (date, userid, cdate, agentname, totalAmount, paidAmount, sugama, ganesh, others, laguage, ac, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', $date, $user_id, $cdate, $agentname, $totalAmount, $paidAmount, $sugama, $ganesh, $others, $laguage, $ac, $remark);
        mysqli_stmt_execute($stmt);
    }

    // Redirect after insertion to avoid resubmission on page refresh
    header("Location: userinput.php"); // Adjust the redirect page accordingly
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User input</title>
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
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px; /* Adds padding for smaller screens */
}

h1 {
    text-shadow: none;
    border-bottom: 2px solid cyan;
    margin: 20px auto;
    padding: 20px;
    font-size: 2.5rem;
    width: 90%; /* Adjusted width for responsiveness */
    text-align: center;
}

button {
    padding: 10px 15px;
    background: none;
    border: 2px solid cyan;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.5s ease;
}

button:hover {
    color: yellow;
    box-shadow: 0px 0px 20px cyan;
}

.input-ex {
    display: flex;
    flex-wrap: wrap; /* Allows wrapping for smaller screens */
    gap: 15px; /* Adds spacing between fields */
    width: 100%; /* Makes container full-width */
    max-width: 900px; /* Restricts maximum width */
    margin: auto;
    justify-content: space-between;
}

.input-ex label {
    font-size: 1rem;
    font-weight: bold;
}

.input-ex input,
.input-ex select {
    width: 100%; /* Full width for small screens */
    max-width: 400px; /* Restricts maximum width */
    padding: 10px;
    background: transparent;
    border: none;
    border-bottom: 2px solid cyan;
    outline: none;
    color: white;
    font-size: 1rem;
}

select {
    background: black;
    color: cyan;
    font-weight: bold;
    border: 2px solid cyan; box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
}

option {
    background: black;
    color: cyan;
}

/* Buttons container */
.btn {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    justify-content: center;
}

.out {
    width: 50px;
    padding: 8px;
    background: black;
    border: 2px solid cyan;
    border-radius: 30px;
    position: absolute;
    top: 10px;
    left: 10px;
}

.out a {
    text-decoration: none;
    color: white;
    font-size: 1rem;
    transition: 0.4s ease;
}

.out a:hover {
    color: cyan;
    text-shadow: 0px 0px 15px orange;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    h1 {
        font-size: 2rem; /* Smaller title font size */
    }

    .input-ex {
        flex-direction: column; /* Stacks fields vertically */
        align-items: center;
    }

    .input-ex input,
    .input-ex select {
        width: 90%; /* Ensures inputs are responsive */
    }

    button {
        font-size: 0.9rem;
        padding: 8px 12px;
    }

    .out a {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.5rem;
    }

    button {
        font-size: 0.8rem;
        padding: 6px 10px;
    }

    .out a {
        font-size: 0.8rem;
    }
}

    </style>

<script>
      
        function clearForm() {
            document.getElementById("expenseForm").reset();
        }

    function handleAgentChange() {
        const agentSelect = document.getElementById('agentname');
        const customAgentInput = document.getElementById('customAgentName');

        if (agentSelect.value === 'Other') {
            customAgentInput.style.display = 'block';
            customAgentInput.setAttribute('required', true); // Make custom input required
        } else {
            customAgentInput.style.display = 'none';
            customAgentInput.removeAttribute('required'); // Remove required attribute
        }
    }

    </script>
</head>
<body>
    
<div class="out">
        <a href="homepage.php"><-</a>
</div>

<h1>Add Transaction</h1>

<form id="expenseForm" action="userinput.php" method="POST">


    <div class="input-ex">
        <div class="ft">

        <label id="dl">Date : </label>
        <input id="d" type="date" name="date" required ><br><br><br>

        <label id="cdl">Collection Date : </label>
        <input id="cd" type="date" name="cdate" required ><br><br><br>

        <label>Agent Name: </label>
<select id="agentname" name="agentname" onchange="handleAgentChange()">
    <option value="Sangamesh Belur">Sangamesh Belur</option>
    <option value="Harish Gudur">Harish Gudur</option>
    <option value="Fortune Travels
">Fortune Travels
</option>
    <option value="V.G.S  ಬಾದಾಮಿ
">V.G.S  ಬಾದಾಮಿ
</option>
    <option value="ರಾಜಗುರು ಬಾದಾಮಿ
">ರಾಜಗುರು ಬಾದಾಮಿ
</option>
    <option value="ತೇರದಾಳ
">ತೇರದಾಳ
</option>
    <option value="ಕುಷ್ಟಗಿ ಭಾಗೀರಥಿ 
">ಕುಷ್ಟಗಿ ಭಾಗೀರಥಿ 
</option>
    <option value="Hoovina hadagali
">Hoovina hadagali
</option>
<option value="ಸತ್ಕಾರ ಮುಂಡರಗಿ

"> ಸತ್ಕಾರ ಮುಂಡರಗಿ
</option>
<option value="ಪಟ್ಟಣಶೆಟ್ಟಿ ಇಲಕಲ್ಲ

">ಪಟ್ಟಣಶೆಟ್ಟಿ ಇಲಕಲ್ಲ

</option>
<option value="ಪಟ್ಟಣಶೆಟ್ಟಿ ಇಲಕಲ್ಲ

">ಪಟ್ಟಣಶೆಟ್ಟಿ ಇಲಕಲ್ಲ

</option>
<option value="ಗಣೇಶ ಟ್ರಾವಲ್ಸ್ ಇಲಕಲ್ಲ

">ಗಣೇಶ ಟ್ರಾವಲ್ಸ್ ಇಲಕಲ್ಲ

</option>
<option value="ಆರ್ಯನ್ ಹುನಗುಂದ

">ಆರ್ಯನ್ ಹುನಗುಂದ

</option>
<option value="R Kamatgi
">R Kamatgi
</option>
<option value="ಗೋಪಾಲ ಕಾಟವಾ

">ಗೋಪಾಲ ಕಾಟವಾ
</option>
<option value="Kerur
">Kerur
</option>
<option value="Kulgeri cross
">Kulgeri cross
</option>
<option value=" ಭವಾನಿ ಬಾಗಲಕೋಟ
"> ಭವಾನಿ ಬಾಗಲಕೋಟ
</option>
<option value="vidyagiri agent
">vidyagiri agent
</option>
<option value="ಆರಾಧನ ಬಾಗಲಕೋಟ
">ಆರಾಧನ ಬಾಗಲಕೋಟ
</option>
<option value="Mantri Bagalkot

"> Mantri Bagalkot
</option>
<option value="Gaddankeri

"> Gaddankeri
</option>
<option value="ಜೆ ಪಿ ಬೀಳಗಿ

">ಜೆ ಪಿ ಬೀಳಗಿ
</option>
<option value="ಶೀರಾಳಶೆಟ್ಟಿ ಗಲಗಲಿ
">ಶೀರಾಳಶೆಟ್ಟಿ ಗಲಗಲಿ
</option>
<option value="ಸುಗಮಾ ಜಮಖಂಡಿ
">ಸುಗಮಾ ಜಮಖಂಡಿ
</option>
<option value="ಚಿನಿವಾಲ ಲೋಕಾಪುರ
">ಚಿನಿವಾಲ ಲೋಕಾಪುರ
</option>
<option value="ಸಹನಾ ಮುಧೋಳ

">ಸಹನಾ ಮುಧೋಳ
</option>
<option value="M  ಮಹಾಲಿಂಗಪುರ

">M  ಮಹಾಲಿಂಗಪುರ
</option>
<option value="bhimambika ron
">bhimambika ron
</option>
<option value="J P RON
">J P RON
</option>
<option value="ಲಕ್ಷ್ಮೇಶ್ವರ

"> ಲಕ್ಷ್ಮೇಶ್ವರ
</option>
<option value="ಅರುಣ ಮಾಗಡಿ

">ಅರುಣ ಮಾಗಡಿ
</option>
<option value="ಲಕ್ಷ್ಮೀ ಗದಗ

">ಲಕ್ಷ್ಮೀ ಗದಗ
</option>
<option value="ಸುಗಮ ನರೇಗಲ್ಲ 

">ಸುಗಮ ನರೇಗಲ್ಲ 
</option>
<option value="ಹಿರೇಹಾಳ

">ಹಿರೇಹಾಳ

</option>
<option value="Sangamesh Belur

">Sangamesh Belur
</option>
<option value="ಮಹಾಂತೇಶ ಬೇಲೂರ

">ಮಹಾಂತೇಶ ಬೇಲೂರ
</option>


    <option value="Other">Other</option>
</select>
<br><br>
<input id="customAgentName" type="text" name="customAgentName" placeholder="Enter name if 'Other'" style="display: none;"><br><br>


        <label for="totalAmount">Total Amount:</label>
        <input type="number" id="totalAmount" name="totalAmount" required><br><br>

         <label for="paidAmount">Paid Amount:</label>
        <input type="number" id="paidAmount" name="paidAmount" required><br><br>
        </div>
        <div class="se">
        <label for="sugama">Sugama:</label>
        <input type="number" id="sugama" name="sugama" ><br><br>

         <label for="ganesh">Ganesh:</label>
        <input type="number" id="ganesh" name="ganesh" ><br><br>

        <label for="others">Others:</label>
        <input type="number" id="others" name="others" ><br><br>

         <label for="laguage">Laguage:</label>
        <input type="number" id="laguage" name="laguage" ><br><br>

        <label for="ac">A/C Pay:</label>
        <input type="number" id="ac" name="ac" ><br><br>

        <label>Remark</label>
        <input id="remark" type="text" name="remark">
        </div>

    </div>

    <div class="btn">
        <button id="a" name="add" type="submit">Add</button>
        <button id="c" type="button" onclick="clearForm()">Clear</button>
    </div>
</form>
    
</body>
</html>

