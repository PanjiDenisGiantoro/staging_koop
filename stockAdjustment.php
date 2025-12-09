<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL & ~E_NOTICE); // hides NOTICE level stuff too

print '
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
.col-1 {
  cursor: grab;
}
.drag-handle {
  cursor: grab;
  font-size: 1.2rem;
  padding: 0 4px;
  user-select: none;
}
.drag-handle:active {
  cursor: grabbing;
}
/* Header row */
.row.fw-bold.text-uppercase {
margin-left:8px;
}
</style>
';

$firstDayOfMonth = date("Y-m-01"); // First day of the current month
$today = date("Y-m-d"); // Current date
$mn = isset($_GET['mn']) ? $_GET['mn'] : ''; // Ensure $mn is set
$createdBy = get_session("Cookie_userName");

$sFileRef = "?vw=ACCconsolidate&mn=$mn";

//adjNo
$getNo = "SELECT MAX(CAST(right(adjNo,6)
AS SIGNED INTEGER )) AS nombor 
   FROM adjustment";

$rsNo = $conn->Execute($getNo);
if ($rsNo) {
$nombor = intval($rsNo->fields('nombor')) + 1;
$nombor = sprintf("%06s", $nombor);
$adjNo 	= 'ADJ' . $nombor;
} else {
$adjNo 	= 'ADJ000001';
}

//kod stok
$stockList = Array();
$stockVal  = Array();
$GetStock = ctGeneralACC("","AN");
$stockOptions = '<option value="">-- Pilih Kod Stok --</option>';

if ($GetStock->RowCount() <> 0) {
  while (!$GetStock->EOF) {
    $name = $GetStock->fields['name'];
    $code = $GetStock->fields['code'];
    array_push($stockList, $name);
    array_push($stockVal, $code);
    $stockOptions .= '<option value="' . $code . '">' . $code . ' - ' . $name . '</option>';
    $GetStock->MoveNext();
  }
}
print '<script>
var stockOptions = ' . json_encode($stockOptions) . ';
</script>';

print '
<!-- Button to open modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dateModal">
+ Baru
</button>
</div>

<!-- Selection Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="dateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 100%; width: 70vw;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dateModalLabel">Pelarasan Stok</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
        <form id="adjustmentForm">
        <input name="createdBy" type="hidden" value="'.$createdBy.'">
          <div class="form-group">
            <label for="adjNo">No. Pelarasan</label>
            <div class="col-sm-2">
              <input type="text" class="form-control" id="adjNo" name="adjNo" value="' . $adjNo . '">
            </div>
          </div>
          <div class="form-group">
            <label for="tarikh_adj">Tanggal Pelarasan</label>
            <div class="col-sm-2">
              <input type="date" class="form-control" id="tarikh_adj" name="tarikh_adj" value="' . $today . '">
            </div>
          </div>
          <div class="form-group">
            <label for="tajuk">Perkara</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" id="tajuk" name="tajuk" value="Pelarasan Stok">
            </div>
          </div>
<hr>
<h5>Senarai Item Pelarasan &nbsp;&nbsp;<button type="button" class="btn btn-success btn-sm" id="addRow">+ Tambah Baris</button></h5>

<div class="row fw-bold text-uppercase border-bottom pb-1 mb-2 text-nowrap" style="width: 100%;">
  <div hidden style="flex: 0.5;"></div>
  <div style="flex: 1; text-align:left">Kod Stok</div>
  <div style="flex: 1; text-align:left">Catatan</div>
  <div style="flex: 0.5; text-align:left">Kuantiti</div>
  <div style="flex: 0.5; text-align:left">Kos Unit</div>
  <div style="flex: 0.5; text-align:left">Jumlah</div>
  <div style="flex: 0.05;"></div>
</div>
<div id="dynamicRows" class="mb-3"></div>

        </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="simpan">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!--toast markup-->
<div id="customToast" style="
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2);
    z-index: 9999;
    padding: 1.5rem;
    text-align: center;
    max-width: 90%;
    width: 300px;
">
  <div id="toastMessage" style="margin-bottom: 1rem; font-size: 1rem;">Saved successfully!</div>
  <button type="button" id="toastOkBtn" class="btn btn-primary btn-sm">OK</button>
</div>

<script>
let bilCounter = 1;

function createRow(bil) {
  return `
    <div class="row g-2 align-items-end mb-2 row-item" data-bil="${bil}"  style="width: 100%;">
      <div hidden style="flex: 0.5;">
          <input type="text" class="form-control" readonly value="${bil}">
      </div>
      <div style="flex: 1;">
        <div class="d-flex align-items-center gap-1">
          <span class="btn btn-light btn-sm drag-handle" title="Seret untuk susun">&#8942;&#8942;</span>
          <select class="form-control flex-grow-1" name="stokID[]">${stockOptions}</select>
        </div>
      </div>
      <div style="flex: 1;">
      <textarea rows="1" class="form-control" name="catatan[]"></textarea>
      </div>
      <div style="flex: 0.5;"">
        <input type="text" class="form-control" name="kuantiti[]" oninput="calculate()">
      </div>
      <div style="flex: 0.5;">
        <input type="text" class="form-control" name="kosUnit[]" oninput="calculate()">
      </div>
      <div style="flex: 0.5;">
        <input type="text" class="form-control" name="jumlah[]" readonly>
      </div>
      <div style="flex: 0.05;">
          <button type="button" class="btn btn-danger btn-sm deleteRow">&times;</button>
      </div>
    </div>
  `;
}

document.getElementById("addRow").addEventListener("click", function () {
  const container = document.getElementById("dynamicRows");
  container.insertAdjacentHTML("beforeend", createRow(bilCounter));

  // Initialize select2 on the newly added dropdown only
  $(\'select[name="stokID[]"]\').last().select2({
    placeholder: "- Pilih -"
  });

  // Re-initialize the Sortable instance after adding the row
  initializeSortable();

  bilCounter++;
});

document.getElementById("dynamicRows").addEventListener("click", function (e) {
  if (e.target.classList.contains("deleteRow")) {
    const row = e.target.closest(".row-item");
    row.remove();

    // Re-initialize the Sortable instance after deleting the row
    initializeSortable();

    // Reorder bil
    const rows = document.querySelectorAll(".row-item");
    bilCounter = 1;
    rows.forEach(r => {
      r.setAttribute("data-bil", bilCounter);
      r.querySelector("input[readonly]").value = bilCounter;
      bilCounter++;
    });
  }
});

// Initialize the sortable instance
let sortable;
function initializeSortable() {
  if (sortable) {
    sortable.destroy(); // Destroy the previous instance
  }
  sortable = new Sortable(document.getElementById(\'dynamicRows\'), {
    animation: 150,
    handle: \'.drag-handle\',
    onEnd: function () {
      const rows = document.querySelectorAll(\'.row-item\');
      let counter = 1;
      rows.forEach(row => {
        row.setAttribute(\'data-bil\', counter);
        row.querySelector(\'input[readonly]\').value = counter;
        counter++;
      });
      bilCounter = counter;
    }
  });
}

// Initialize on page load
initializeSortable();

function calculate() {
  const rows = document.querySelectorAll(\'.row-item\');
  rows.forEach(row => {
    const qtyInput = row.querySelector(\'input[name="kuantiti[]"]\');
    const kosInput = row.querySelector(\'input[name="kosUnit[]"]\');
    const jumlahInput = row.querySelector(\'input[name="jumlah[]"]\');

    const qty = parseFloat(qtyInput.value) || 0;
    const kos = parseFloat(kosInput.value) || 0;
    const jumlah = qty * kos;

    jumlahInput.value = jumlah.toFixed(2);
  });
}

// Ensure jQuery is loaded
if (typeof jQuery === \'undefined\') {
    console.error("jQuery is not loaded! Form submission will not work.");
}

// AJAX submission code
$("#simpan").on("click", function (e) {
    e.preventDefault();
    
    // Show loading indicator
    $(this).prop(\'disabled\', true).html(\'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...\');
    
    const formData = $("#adjustmentForm").serialize();
    console.log("Form Data Sent:", formData);
    
    // Basic validation
    let valid = true;
    let errorMsg = "";
    
    // Check if at least one stock item is selected
    if ($(\'select[name="stokID[]"]\').length === 0) {
        valid = false;
        errorMsg = "Please add at least one item";
    } else {
        // Check if first stock code is selected
        if (!$(\'select[name="stokID[]"]\').first().val()) {
            valid = false;
            errorMsg = "Please select a stock code";
        }
    }
    
    if (!valid) {
        alert(errorMsg);
        $(this).prop(\'disabled\', false).html(\'Simpan\');
        return;
    }
    
    // Use plain XMLHttpRequest for maximum compatibility
    const xhr = new XMLHttpRequest();
    xhr.open(\'POST\', \'saveStockAdjustment.php\', true);
    xhr.setRequestHeader(\'Content-Type\', \'application/x-www-form-urlencoded\');
    
    xhr.onload = function() {
        // Re-enable the button
        $("#simpan").prop(\'disabled\', false).html(\'Simpan\');
        
        console.log("XHR Status:", xhr.status);
        console.log("XHR Response:", xhr.responseText);
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                console.log("Parsed response:", response);
                
                if (response && response.success) {
                   showCustomToast("Saved! ADJ No: " + response.adjNo, function () {
                    // Reset the form
                    $("#adjustmentForm")[0].reset();
                    $("#dynamicRows").empty();
                    bilCounter = 1;
                    $("#dateModal").modal(\'hide\');
                    location.reload();
                });
                    
                    // Reset the form
                    $("#adjustmentForm")[0].reset();
                    
                    // Clear all dynamic rows
                    $("#dynamicRows").empty();
                    
                    // Reset the counter
                    bilCounter = 1;
                    
                    // Close the modal
                    $("#dateModal").modal(\'hide\');
                    
                    // Reload the page to show updated list (optional)
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    showCustomToast("Failed to save. " + (response.error || "Unknown error"));
                }
            } catch (e) {
                console.error("Error parsing JSON:", e);
                console.log("Raw response:", xhr.responseText);
                
                if (xhr.responseText.includes("success") && xhr.responseText.includes("true")) {
                    // Response contains success but couldnt be parsed properly
                    showCustomToast("Saved successfully!", function () {
                        $("#adjustmentForm")[0].reset();
                        $("#dynamicRows").empty();
                        bilCounter = 1;
                        $("#dateModal").modal(\'hide\');
                        location.reload();
                    });

                    // Reset the form
                    $("#adjustmentForm")[0].reset();
                    
                    // Clear all dynamic rows
                    $("#dynamicRows").empty();
                    
                    // Reset the counter  
                    bilCounter = 1;
                    
                    // Close the modal
                    $("#dateModal").modal(\'hide\');
                    
                    // Reload the page to show updated list (optional)
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert("Error processing response: " + xhr.responseText.substring(0, 100));
                }
            }
        } else {
            alert("Server returned status " + xhr.status + ": " + xhr.statusText);
        }
    };
    
    xhr.onerror = function() {
        console.error("XHR Error:", xhr.statusText);
        $("#simpan").prop(\'disabled\', false).html(\'Simpan\');
        alert("Connection error. Please check your network connection and try again.");
    };
    
    xhr.timeout = 30000; // 30 seconds
    xhr.ontimeout = function() {
        $("#simpan").prop(\'disabled\', false).html(\'Simpan\');
        alert("Request timed out. Please try again.");
    };
    
    try {
        xhr.send(formData);
    } catch (e) {
        console.error("Send error:", e);
        $("#simpan").prop(\'disabled\', false).html(\'Simpan\');
        alert("Error sending request: " + e.message);
    }
});

function showCustomToast(message, callback) {
    $("#toastMessage").text(message);
    $("#customToast").fadeIn();

    $("#toastOkBtn").off("click").on("click", function () {
        $("#customToast").fadeOut();
        if (typeof callback === "function") callback();
    });
}

</script>

';


?>