$(document).ready(function() {
  // Handle form submission
  $('#inputForm').submit(function(e) {
      e.preventDefault();

      // Get input values
      const date = $('#date').val();
      const agentName = $('#agentName').val();
      const totalAmount = parseFloat($('#totalAmount').val());
      const paidAmount = parseFloat($('#paidAmount').val());
      const balance = totalAmount - paidAmount;
      const utrId = $('#utrId').val();

      // Add data to the first table
      const row = `<tr class="collection-row">
          <td class="agentName">${agentName}</td>
          <td class="totalAmount">${totalAmount}</td>
          <td class="paidAmount">${paidAmount}</td>
          <td class="balance">${balance}</td>
          <td class="utrId">${utrId}</td>
          <td><button class="editBtn">Edit</button></td>
          <td><button class="clearBtn">Clear</button></td>
      </tr>`;
      $('#collectionTable tbody').append(row);

      // Add data to second table (automatically fill Agent Name and Balance)
      const secondTableRow = `<tr class="agent-row">
          <td class="agentName">${agentName}</td>
          <td class="balance">${balance}</td>
          <td><input type="number" class="sugama" value="0"></td>
          <td><input type="number" class="ganesh" value="0"></td>
          <td><input type="number" class="others" value="0"></td>
          <td><input type="number" class="luggage" value="0"></td>
          <td><input type="number" class="total" value="0" readonly></td>
          <td><input type="number" class="acPay" value="0"></td>
          <td><input type="text" class="remark"></td>
          <td><button class="saveBtn">Save</button></td>
      </tr>`;
      $('#agentDataTable tbody').append(secondTableRow);

      // Clear form fields
      $('#inputForm')[0].reset();
  });

  // Handle row clearing in the collection table
  $(document).on('click', '.clearBtn', function() {
      $(this).closest('tr').remove();
  });

  // Handle row editing in the collection table
  $(document).on('click', '.editBtn', function() {
      const row = $(this).closest('tr');
      const agentName = row.find('td:eq(0)').text();
      const totalAmount = row.find('td:eq(1)').text();
      const paidAmount = row.find('td:eq(2)').text();
      const balance = row.find('td:eq(3)').text();
      const utrId = row.find('td:eq(4)').text();

      $('#agentName').val(agentName);
      $('#totalAmount').val(totalAmount);
      $('#paidAmount').val(paidAmount);
      $('#utrId').val(utrId);

      // Remove row after editing
      row.remove();
  });

  // Handle calculation for total in second table when input changes
  $(document).on('input', '.sugama, .ganesh, .others, .luggage, .acPay', function() {
      const row = $(this).closest('tr');
      const sugama = parseFloat(row.find('.sugama').val()) || 0;
      const ganesh = parseFloat(row.find('.ganesh').val()) || 0;
      const others = parseFloat(row.find('.others').val()) || 0;
      const luggage = parseFloat(row.find('.luggage').val()) || 0;
      const acPay = parseFloat(row.find('.acPay').val()) || 0;

      const total = sugama + ganesh + others + luggage + acPay;
      row.find('.total').val(total);
  });

  // Handle save button functionality for the second table
  $(document).on('click', '.saveBtn', function() {
      const row = $(this).closest('tr');
      const sugama = row.find('.sugama').val();
      const ganesh = row.find('.ganesh').val();
      const others = row.find('.others').val();
      const luggage = row.find('.luggage').val();
      const acPay = row.find('.acPay').val();
      const remark = row.find('.remark').val();

      // Logic to save data (This could be to an array, localStorage, or server)
      alert(`Data Saved for ${row.find('.agentName').text()}: Sugama - ${sugama}, Ganesh - ${ganesh}, Others - ${others}, Luggage - ${luggage}, A/C Pay - ${acPay}, Remark - ${remark}`);
  });

  // Excel Export functionality
  $('#downloadExcelBtn').click(function() {
      // Prepare data for Table 1 (Collection Table)
      const collectionTableData = [];
      $('#collectionTable tbody tr').each(function() {
          const row = $(this);
          const rowData = [
              row.find('.agentName').text(),
              row.find('.totalAmount').text(),
              row.find('.paidAmount').text(),
              row.find('.balance').text(),
              row.find('.utrId').text()
          ];
          collectionTableData.push(rowData);
      });

      // Prepare data for Table 2 (Agent Data Table)
      const agentDataTableData = [];
      $('#agentDataTable tbody tr').each(function() {
          const row = $(this);
          const rowData = [
              row.find('.agentName').text(),
              row.find('.balance').text(),
              row.find('.sugama').val(),
              row.find('.ganesh').val(),
              row.find('.others').val(),
              row.find('.luggage').val(),
              row.find('.total').val(),
              row.find('.acPay').val(),
              row.find('.remark').val()
          ];
          agentDataTableData.push(rowData);
      });

      // Create a new workbook with two sheets
      const wb = XLSX.utils.book_new();

      // Add Collection Table (Sheet1)
      const ws1 = XLSX.utils.aoa_to_sheet([["Agent Name", "Total Amount", "Paid Amount", "Balance", "UTR ID"], ...collectionTableData]);

      // Apply styles to the header and borders for Table 1
      const headerStyle = {
          font: { bold: true, color: { rgb: "FFFFFF" }, sz: 12 },  // White text with bold and size 12
          fill: { fgColor: { rgb: "4F81BD" } },  // Blue background for header
          border: {
              top: { style: "thin", color: { rgb: "000000" } },
              left: { style: "thin", color: { rgb: "000000" } },
              bottom: { style: "thin", color: { rgb: "000000" } },
              right: { style: "thin", color: { rgb: "000000" } }
          }
      };

      // Apply the header style to each header cell
      for (let col = 0; col < 5; col++) {
          ws1[XLSX.utils.encode_cell({r: 0, c: col})].s = headerStyle;
      }

      // Apply borders and alternating row colors
      for (let r = 1; r < collectionTableData.length + 1; r++) {
          for (let c = 0; c < 5; c++) {
              const cellRef = XLSX.utils.encode_cell({r, c});
              if (!ws1[cellRef]) continue;
              ws1[cellRef].s = {
                  border: {
                      top: { style: "thin", color: { rgb: "000000" } },
                      left: { style: "thin", color: { rgb: "000000" } },
                      bottom: { style: "thin", color: { rgb: "000000" } },
                      right: { style: "thin", color: { rgb: "000000" } }
                  }
              };
              if (r % 2 === 0) {
                  ws1[cellRef].s.fill = { fgColor: { rgb: "D9E1F2" } };  // Light blue background for even rows
              }
          }
      }

      XLSX.utils.book_append_sheet(wb, ws1, "Collection Table");

      // Add Agent Data Table (Sheet2)
      const ws2 = XLSX.utils.aoa_to_sheet([["Agent Name", "Balance", "Sugama", "Ganesh", "Others", "Luggage", "Total", "A/C Pay", "Remark"], ...agentDataTableData]);

      // Apply styles to the header and borders for Table 2
      for (let col = 0; col < 9; col++) {
          ws2[XLSX.utils.encode_cell({r: 0, c: col})].s = headerStyle;
      }

      // Apply borders and alternating row colors for Table 2
      for (let r = 1; r < agentDataTableData.length + 1; r++) {
          for (let c = 0; c < 9; c++) {
              const cellRef = XLSX.utils.encode_cell({r, c});
              if (!ws2[cellRef]) continue;
              ws2[cellRef].s = {
                  border: {
                      top: { style: "thin", color: { rgb: "000000" } },
                      left: { style: "thin", color: { rgb: "000000" } },
                      bottom: { style: "thin", color: { rgb: "000000" } },
                      right: { style: "thin", color: { rgb: "000000" } }
                  }
              };
              if (r % 2 === 0) {
                  ws2[cellRef].s.fill = { fgColor: { rgb: "D9E1F2" } };  // Light blue background for even rows
              }
          }
      }

      XLSX.utils.book_append_sheet(wb, ws2, "Agent Data");

      // Generate Excel file and prompt download
      XLSX.writeFile(wb, "Agent_Report.xlsx");
  });
});
