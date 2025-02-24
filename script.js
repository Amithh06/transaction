document.getElementById("transaction-form").addEventListener("submit", function(event) {
  event.preventDefault();

  const serialNumber = document.getElementById("serial-number").value;
  const date = document.getElementById("date").value;
  const agentName = document.getElementById("agent-name").value;
  const totalAmount = parseFloat(document.getElementById("total-amount").value);
  const paidAmount = parseFloat(document.getElementById("paid-amount").value);
  const balanceAmount = totalAmount - paidAmount;

  // Convert the date to day-month-year format (e.g., 05-02-2025)
  const formattedDate = new Date(date).toLocaleDateString("en-GB");

  const table = document.getElementById("transaction-table").getElementsByTagName("tbody")[0];

  const newRow = table.insertRow();

  newRow.innerHTML = `
      <td>${serialNumber}</td>
      <td>${formattedDate}</td>
      <td>${agentName}</td>
      <td>${totalAmount.toFixed(2)}</td>
      <td>${paidAmount.toFixed(2)}</td>
      <td>${balanceAmount.toFixed(2)}</td>
      <td>
          <button onclick="editRow(this)">Edit</button>
          <button onclick="deleteRow(this)">Delete</button>
      </td>
  `;

  document.getElementById("transaction-form").reset();
  updateTotals();
});

function editRow(button) {
  const row = button.parentElement.parentElement;
  const cells = row.getElementsByTagName("td");

  document.getElementById("serial-number").value = cells[0].textContent;
  document.getElementById("date").value = cells[1].textContent;
  document.getElementById("agent-name").value = cells[2].textContent;
  document.getElementById("total-amount").value = parseFloat(cells[3].textContent);
  document.getElementById("paid-amount").value = parseFloat(cells[4].textContent);

  deleteRow(button);
}

function deleteRow(button) {
  const row = button.parentElement.parentElement;
  row.remove();
  updateTotals();
}

function updateTotals() {
  const table = document.getElementById("transaction-table").getElementsByTagName("tbody")[0];
  const rows = table.rows;

  const dateTotals = {};

  // Initialize totals for each date
  for (let row of rows) {
      const date = row.cells[1].textContent;
      const totalAmount = parseFloat(row.cells[3].textContent);
      const paidAmount = parseFloat(row.cells[4].textContent);
      const balanceAmount = parseFloat(row.cells[5].textContent);

      if (!dateTotals[date]) {
          dateTotals[date] = {
              totalAmount: 0,
              paidAmount: 0,
              balanceAmount: 0,
              rows: []
          };
      }

      dateTotals[date].totalAmount += totalAmount;
      dateTotals[date].paidAmount += paidAmount;
      dateTotals[date].balanceAmount += balanceAmount;
      dateTotals[date].rows.push(row);
  }

  // Add totals for each date and each column
  let lastRow = document.getElementById("transaction-table").getElementsByTagName("tfoot")[0];

  if (!lastRow) {
      lastRow = document.createElement("tfoot");
      document.getElementById("transaction-table").appendChild(lastRow);
  }

  lastRow.innerHTML = ""; // Clear previous totals

  // Create total rows for each date
  for (let date in dateTotals) {
      const totals = dateTotals[date];
      const totalRow = document.createElement("tr");

      totalRow.innerHTML = `
          <td colspan="3" style="border: 1px solid black; padding: 5px;">Total for ${date}</td>
          <td style="border: 1px solid black; padding: 5px;">${totals.totalAmount.toFixed(2)}</td>
          <td style="border: 1px solid black; padding: 5px;">${totals.paidAmount.toFixed(2)}</td>
          <td style="border: 1px solid black; padding: 5px;">${totals.balanceAmount.toFixed(2)}</td>
          <td style="border: 1px solid black;"></td> <!-- No action column in totals row -->
      `;
      lastRow.appendChild(totalRow);
  }
}

document.getElementById("excel-btn").addEventListener("click", function() {
  const table = document.getElementById("transaction-table");
  let tableHTML = table.outerHTML;

  // Remove the "Actions" column (Edit/Delete buttons) from the HTML before export
  tableHTML = tableHTML.replace(/<td>.*?<\/td>/g, function(match) {
      return match.replace(/<button.*?>.*?<\/button>/g, '');  // Remove the Edit/Delete buttons from rows
  });

  // Add inline styling to add borders to each cell in the table
  tableHTML = tableHTML.replace(/<table>/, '<table border="1" style="border-collapse: collapse;">');
  tableHTML = tableHTML.replace(/<td>/g, '<td style="border: 1px solid black; padding: 5px;">');
  tableHTML = tableHTML.replace(/<th>/g, '<th style="border: 1px solid black; padding: 5px;">');

  const uri = "data:application/vnd.ms-excel;base64,";
  const base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))); };
  const format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }); };

  const html = format(tableHTML, {});
  const link = document.createElement("a");
  link.href = uri + base64(html);
  link.download = "transactions.xls";
  link.click();
});
