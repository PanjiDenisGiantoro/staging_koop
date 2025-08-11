<?
include("koperasiQry.php");	
// echo "<script>alert('".json_encode($_POST)."');</script>";

$display = 0;

$getNo = "SELECT MAX(CAST(right(ciNo,6)
            AS SIGNED INTEGER )) AS nombor 
            FROM consolidate
            WHERE ciNo LIKE 'CI%'";

$rsNo = $conn->Execute($getNo);
if ($rsNo) {
    $nombor = intval($rsNo->fields('nombor')) + 1;
    $nombor = sprintf("%06s", $nombor);
    $ciNo 	= 'CI' . $nombor;
} else {
    $ciNo 	= 'CI000001';
}

$strHeaderTitle = '&nbsp;</b><a class="maroon" href="?vw=ACCconsolidateList&mn=' . $mn . '">SENARAI</a><b>' . '&nbsp;>&nbsp;PERMOHONAN BARU CONSOLIDATED INVOICE</b>&nbsp;[TARIKH DIPILIH: '.$dtFrom.' - '.$dtTo.']';
print' <div class="maroon" align="left">' . $strHeaderTitle . '</div>
<div style="width: 100%; text-align:left">
<div>&nbsp;</div>';


print'
<div class="table-responsive">
<form name="MyForm" action="?vw=ACCconsolidate&mn=' . $mn . '" method="post">
<input type="hidden" name="dtFrom" value="' . $dtFrom . '">
<input type="hidden" name="dtTo" value="' . $dtTo . '">

<tr>
	<td width="48%">
		<table border="0" cellspacing="1" cellpadding="2">
			<tr>
				<td>No. CI</td>
				<td valign="top"></td><td><input class="form-controlx"  id="ciNo" name="ciNo" value="' . $ciNo . '" type="text" size="20" maxlength="50"></td>
		</table>
	</td>	
</tr>';

print' <div style="width: 100%; text-align:left">
<div>&nbsp;</div>';


$sSQL ="
(
    SELECT DISTINCT A.invNo, A.tarikh_inv, A.companyID, A.description, A.outstandingbalance, C.tarikh_doc, NULL AS noteNo
    FROM cb_invoice A
    LEFT JOIN transactionacc C ON A.invNo = C.docNo
    WHERE C.tarikh_doc >= '" . $dtFrom . "' 
    AND C.tarikh_doc <= '" . $dtTo . "'
)
UNION ALL
(
    SELECT DISTINCT NULL AS noteNo, B.tarikh_note, B.companyID, B.catatan, B.pymtAmt, C.tarikh_doc, B.noteNo
    FROM note B
    LEFT JOIN transactionacc C ON B.noteNo = C.docNo
    WHERE B.noteNo LIKE 'CN%'
    AND C.tarikh_doc >= '" . $dtFrom . "' 
    AND C.tarikh_doc <= '" . $dtTo . "'
)
ORDER BY tarikh_doc DESC";
$GetBaucers = &$conn->Execute($sSQL);

if ($GetBaucers->RowCount() <> 0) {
    $cnt = 1;

    print '
    <tr valign="top">
        <td valign="top">
            <table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
                <tr class="table-primary">
                    <td nowrap>&nbsp;</td>
                    <td nowrap><b>No. Dokumen</b></td>
                    <td nowrap align="center"><b>Tarikh</b></td>
                    <td nowrap><b>Nama Syarikat | TIN LHDN</b></td>
                    <td nowrap align="left"><b>Catatan</b></td>
                    <td nowrap align="right"><b>Jumlah (RM)</b></td>
                    <td nowrap align="center"><b>Lhdn Status</b></td>
                </tr>';

    while (!$GetBaucers->EOF) {
        $namacomp = dlookup("generalacc", "name", "ID=" . tosql($GetBaucers->fields('companyID'), "Text"));
        $description = $GetBaucers->fields('description') ? $GetBaucers->fields('description') : $GetBaucers->fields('catatan');
        $tarikh = $GetBaucers->fields('tarikh_inv') ? toDate("d/m/y", $GetBaucers->fields('tarikh_inv')) : toDate("d/m/y", $GetBaucers->fields('tarikh_note'));
        $amaun = $GetBaucers->fields('outstandingbalance') ? $GetBaucers->fields('outstandingbalance') : $GetBaucers->fields('pymtAmt');
        $docNo = $GetBaucers->fields('invNo') ? $GetBaucers->fields('invNo') : $GetBaucers->fields('noteNo');

        $result = dlookup("generalacc", "b_tinLhdn", "ID=" . tosql($GetBaucers->fields("companyID"), "Text"));
        $tinLhdn = $result ? '<span style="color: green; font-size: 16px;" title="' . htmlspecialchars($result) . '">&#10004;</span>' : '<span style="color: red; font-size: 16px;">&#10008;</span>';

        print '
        <tr>
            <td class="Data" style="text-align: center; vertical-align: middle;">' . $cnt . '</td>
            <td class="Data" style="text-align: left; vertical-align: middle;" nowrap>' . $docNo . '</td>
            <td class="Data" style="text-align: center; vertical-align: middle;">' . $tarikh . '</td>
		    <td class="Data" style="text-align: left; vertical-align: middle;">' . $namacomp . '&nbsp;' . $tinLhdn . '</td>
            <td class="Data" style="text-align: left; vertical-align: middle;">' . $description . '</td>
            <td class="Data" style="text-align: right; vertical-align: middle;">' . number_format($amaun, 2) . '</td>
            <td class="Data" style="text-align: center; vertical-align: middle;">-</td>
        </tr>';

        $cnt++;
        $GetBaucers->MoveNext();
    }
    $GetBaucers->Close();
}

print '</table>
    </td>
    </tr>';

print'
<button type="submit" name="action" class="btn btn-primary btn-sm w-md waves-effect waves-light" value="Simpan" onclick="CheckField(\'Simpan\')">SIMPAN</button>
';



print'
</form>
</div>';

//pilihan simpan
if ($action == "Simpan" || $simpan) {
	$updatedBy 	    = get_session("Cookie_userName");
	$updatedDate    = date("Y-m-d H:i:s");

    $sSQL = "";
	$sSQL	= "INSERT INTO consolidate (" .
		"ciNo, " .
        "dtFrom, " .
		"dtTo, " .
		"createdDate, " .
		"createdBy, " .
		"updatedDate, " .
		"updatedBy) " .

		" VALUES (" .
		"'" . $ciNo . "', " .
        "'" . $dtFrom . "', " .
        "'" . $dtTo . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "', " .
		"'" . $updatedDate . "', " .
		"'" . $updatedBy . "')";

        if ($display) print $sSQL . '<br />';
        else
            $rs = &$conn->Execute($sSQL);

        if (!$display) {
            print '<script>
        window.location = "?vw=ACCconsolidateList&mn=' . $mn . '";
        </script>';
        }
}

print '
<script language="JavaScript">

	function CheckField(act) {
	    e = document.MyForm;
		count = 0;	
		for(c=0; c<e.elements.length; c++) {
		  if(e.elements[c].name=="ciNo" && e.elements[c].value==\'\') {
			alert(\'Sila pilih semula tarikh!\');
            count++;
		  }
		  
        }
		if(count==0) {

		// Disable the submit button to prevent duplicate entries by user if click button multiple times
          var submitButton = document.querySelector("input[name=\"action\"]"); 
        if (submitButton) submitButton.disabled = true;

        // Submit the form
        console.log("Submitting form...");
        console.log("Action value:", e.action.value);

        e.submit();

        // Re-enable the button after 5 seconds (in case of error)
        setTimeout(function() {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.value = act;
            }
        }, 5000);
        }
	}

    </script>';

?>