<?php
// Include the database connection file
include 'ayt.php';


// Fetch all transactions grouped by date
$query = "SELECT * FROM transaction ORDER BY date = CURDATE() DESC, date ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

// Prepare data for display
$transactionsByDate = [];
while ($row = mysqli_fetch_assoc($result)) {
    $transactionsByDate[$row['date']][] = $row;
}

// Helper function to format the date
function formatDate($date) {
    $dateTime = new DateTime($date);
    return $dateTime->format('d-m-Y');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Table</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
            padding: 20px;
        }

        h1 {
            border-bottom: 2px solid cyan;
            margin: 20px 0;
            font-size: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 2px solid cyan;
            box-shadow: 0px 0px 20px rgba(0, 255, 255, 0.5);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid cyan;
        }

        th {
            background: rgba(0, 0, 0, 0.8);
            color: yellow;
        }

        td {
            background: rgba(0, 0, 0, 0.5);
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

        .out {
            margin-bottom: 20px;
        }

        .out a {
            text-decoration: none;
            color: white;
            padding: 10px;
            background: black;
            border: 2px solid cyan;
            border-radius: 10px;
            transition: 0.3s;
        }

        .out a:hover {
            color: cyan;
            text-shadow: 0px 0px 10px cyan;
        }
    </style>
</head>
<body>

<div class="out">
    <a href="homepage.php">&larr; Back</a>
</div>

<h1>Transactions Table</h1>

<?php foreach ($transactionsByDate as $date => $transactions) { ?>
    <table>
        <thead>
            <tr>
                <th colspan="14">Date: <?php echo htmlspecialchars(formatDate($date)); ?></th>
            </tr>
            <tr>
                <th>Collection Date</th>
                <th>Agent Name</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
                <th>Sugama</th>
                <th>Ganesh</th>
                <th>Others</th>
                <th>Laguage</th>
                <th>A/C</th>
                <th>Total</th>
                <th>Remark</th>
                <th>Row Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction) { ?>
                <tr>
                    <td><?php echo htmlspecialchars(formatDate($transaction['Cdate'])); ?></td>
                    <td><?php echo htmlspecialchars($transaction['agentname']); ?></td>
                    <td><?php echo number_format($transaction['totalAmount'], 2); ?></td>
                    <td><?php echo number_format($transaction['paidAmount'], 2); ?></td>
                    <td><?php echo number_format($transaction['balance'], 2); ?></td>
                    <td><?php echo number_format($transaction['sugama'], 2); ?></td>
                    <td><?php echo number_format($transaction['ganesh'], 2); ?></td>
                    <td><?php echo number_format($transaction['others'], 2); ?></td>
                    <td><?php echo number_format($transaction['laguage'], 2); ?></td>
                    <td><?php echo number_format($transaction['ac'], 2); ?></td>
                    <td><?php echo number_format($transaction['total'], 2); ?></td>
                    <td><?php echo htmlspecialchars($transaction['remark']); ?></td>
                    <td class="row-total"></td>
        
                </tr>
            <?php } ?>
            <tr>
                <td colspan="13" style="font-weight: bold; color: yellow;">Grand Total</td>
                <td class="grand-total"></td>
            </tr>
        </tbody>
    </table>
<?php } ?>

<script>
document.querySelectorAll('table').forEach(table => {
    let columnTotals = Array(7).fill(0); // Initialize totals for Paid Amount, Balance, Sugama, Ganesh, Others, Laguage, A/C

    // Loop through each row (excluding the header and grand total row)
    const rows = Array.from(table.querySelectorAll('tbody tr')).slice(0, -1);

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        let rowTotal = 0;

        // Column indices for required columns
        const indices = [3, 4, 5, 6, 7, 8, 9]; // Paid Amount, Balance, Sugama, Ganesh, Others, Laguage, A/C

        indices.forEach((index, i) => {
            const value = parseFloat(cells[index].textContent) || 0;
            rowTotal += value; // Calculate the row total
            columnTotals[i] += value; // Add to corresponding column total
        });

        // Set the row total in the "Column Total" field
        cells[12].textContent = rowTotal.toFixed(2);
    });

    // Add grand total to the last row
    const grandTotalCell = table.querySelector('.grand-total');
    const grandTotal = columnTotals.reduce((sum, val) => sum + val, 0);
    grandTotalCell.textContent = grandTotal.toFixed(2);
});
</script>
<button id="exportExcel" style="margin: 20px; padding: 10px; border: 2px solid cyan; background: black; color: white; cursor: pointer; font-size: 16px; transition: 0.5s;">
    Export to Excel
</button>
<script>
document.getElementById("exportExcel").addEventListener("click", function () {
    // Create a new workbook
    const workbook = XLSX.utils.book_new();

    // Iterate through all tables on the page
    document.querySelectorAll("table").forEach((table, index) => {
        const sheet = []; // Temporary sheet data array

        // Extract table headers
        const headers = [];
        table.querySelectorAll("thead th").forEach(th => headers.push(th.innerText));
        sheet.push(headers);

        // Extract table rows
        table.querySelectorAll("tbody tr").forEach(row => {
            const rowData = [];
            row.querySelectorAll("td").forEach(cell => rowData.push(cell.innerText));
            sheet.push(rowData);
        });

        // Create a worksheet from the data
        const worksheet = XLSX.utils.aoa_to_sheet(sheet);

        // Add the worksheet to the workbook
        XLSX.utils.book_append_sheet(workbook, worksheet, `Table ${index + 1}`);
    });

    // Export the workbook to a downloadable Excel file
    XLSX.writeFile(workbook, "Transactions.xlsx");
});
</script>


</body>
</html>
