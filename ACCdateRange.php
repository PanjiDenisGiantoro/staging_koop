<?php
$firstDayOfMonth = date("Y-m-01"); // First day of the current month
$today = date("Y-m-d"); // Current date
$mn = isset($_GET['mn']) ? $_GET['mn'] : ''; // Ensure $mn is set

$sFileRef = "?vw=ACCconsolidate&mn=$mn";

print '
<!-- Button to open modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dateModal">
+ Baru
</button>
</div>

<!-- Date Selection Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="dateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dateModalLabel">Select Date Range</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="dateForm">
          <div class="form-group">
            <label for="dtFrom">Date From</label>
            <input type="date" class="form-control" id="dtFrom" name="dtFrom" value="' . $firstDayOfMonth . '">
          </div>
          <div class="form-group">
            <label for="dtTo">Date To</label>
            <input type="date" class="form-control" id="dtTo" name="dtTo" value="' . $today . '">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="ok">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById("ok").addEventListener("click", function() {
    var dtFrom = document.getElementById("dtFrom").value;
    var dtTo = document.getElementById("dtTo").value;

    if (dtFrom > dtTo) {
        alert("Tanggal Pada tidak boleh melebihi dari Tanggal Hingga");
    } else {
        window.location.href = "' . $sFileRef . '&dtFrom=" + dtFrom + "&dtTo=" + dtTo;
    }
});
</script>
';
?>