<?php

/*********************************************************************************
 *          Project       :  iKOOP.com.my
 *          Filename      :  selLoan.php
 *          Used By       :  loanApply.php
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

$yr = date("Y");
$sSQL = "";
$sWhere = "a.loanID = b.loanID AND a.userID = '" . $pk . "' AND a.status = '3'";
$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT a . * , (
                a.loanAmt * a.kadar_u /100 * a.loanPeriod /12
                ) AS tot_untung, b.rnoBaucer
                FROM loans a, loandocs b";
$sSQL = $sSQL . $sWhere . ' order by a.loanNo ASC';
$GetLoan = &$conn->Execute($sSQL);

print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>' . $emaNetis . '</title>
    <meta name="GENERATOR" content="' . $yVZcSz2OuGE5U . '">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="expires" content="0"> 
    <meta http-equiv="cache-control" content="no-cache">
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />  
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet"/>      
</head>
	<script language="JavaScript">
	function selPinjaman(id, bakiBB, untungS) {
		// Update values only for the selected row
		var bakiBBField = document.getElementById("totalBakiBB");
		var monthlyField = document.getElementById("totalUntungS");

		// Check if the checkbox is checked
		if (document.getElementById("checkbox-" + id).checked) {
			bakiBBField.value = parseFloat(bakiBBField.value) + bakiBB;
			monthlyField.value = parseFloat(monthlyField.value) + untungS;
		} else {
			bakiBBField.value = parseFloat(bakiBBField.value) - bakiBB;
			monthlyField.value = parseFloat(monthlyField.value) - untungS;
		}

		// Recalculate the totals
		updateTotals();
	}

    // Recalculate totals before form submission
    function updateTotals() {
        var totalBakiBB = 0;
        var totalUntungS = 0;

        var checkboxes = document.querySelectorAll(\'input[type="checkbox"]:checked\');
        checkboxes.forEach(function(checkbox) {
            var id = checkbox.id.replace(\'checkbox-\', \'\'); // Extract ID from checkbox
            var bakiBB = parseFloat(document.getElementById(\'bakiBB-\' + id).value);
            var untungS = parseFloat(document.getElementById(\'untungS-\' + id).value);

            totalBakiBB += bakiBB;
            totalUntungS += untungS;
        });

        // Set the totals to the hidden input fields
        document.getElementById("totalBakiBB").value = totalBakiBB;
        document.getElementById("totalUntungS").value = totalUntungS;
    }
</script>
<body leftmargin="5" topmargin="5" class="bodyBG">';

print '
<form name="MyForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" onsubmit="updateTotals();">
    <div class="table-responsive">
        <input type="hidden" name="action">
        <input type="hidden" name="totalBakiBB" id="totalBakiBB" value="0">
        <input type="hidden" name="totalUntungS" id="totalUntungS" value="0">

        <table class="table" border="0" cellspacing="1" cellpadding="3" width="95%" align="center">
            <tr>
                <td class="Label" colspan="2"><b>Klik Pada Kod Untuk Pilihan.</b></td>
            </tr>';

if ($GetLoan->RowCount() <> 0) {
    print '<tr>
        <td class="Data">
            <table cellpadding="2" cellspacing="1" width="100%" class="table table-bordered table-striped table-sm" style="font-size: 10pt;">
                <thead>
                    <tr class="table-primary">
                        <td class="headerteal" align="center"><b>Nombor Rujukan</b></td>
                        <td class="headerteal"><b>Jenis Pembiayaan</b></td>
                        <td class="headerteal" align="right"><b>Baki Pembiayaan (RM)</b></td>
                        <td class="headerteal" align="right"><b>Untung Sebulan (RM)</b></td>
                    </tr>
                </thead>';

    while (!$GetLoan->EOF) {
        $id          = $GetLoan->fields('loanID');
        $loanNo      = $GetLoan->fields('loanNo');
        $loanType    = $GetLoan->fields('loanType');
        $name        = dlookup("general", "name", "ID=" . tosql($loanType, "Number"));
        $bond        = dlookup("loandocs", "rnoBond", "loanID=" . tosql($id, "Number"));
        $lpotBiaya  = dlookup("loandocs", "lpotBiaya", "loanID=" . tosql($id, "Number"));
        $loanAmt      = $GetLoan->fields('loanAmt');

        // Calculate the outstanding balance and payments
        $yuranDb = $conn->Execute("SELECT SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS count 
        FROM transaction 
        WHERE pymtRefer = '" . $bond . "' 
        AND userID = '" . $pk . "' 
        AND deductID NOT IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745,1781,1851,1859,1842) 
        AND YEAR(createdDate) < " . $yr . " 
        GROUP BY userID");

        $yuranDbCount = $yuranDb->fields['count'];
        // Calculate payments made
        $yuranKt = $conn->Execute("SELECT SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS count 
        FROM transaction 
        WHERE pymtRefer = '" . $bond . "' 
        AND userID = '" . $pk . "' 
        AND deductID NOT IN (1642,1645,1649,1646,1651,1652,1653,1654,1656,1659,1663,1674,1676,1643,1678,1669,1668,1672,1680,1771,1767,1745,1781,1851,1859,1842) 
        AND YEAR(createdDate) < " . $yr . " 
        GROUP BY userID");

        $yuranKtCount = $yuranKt->fields['count'];

        if ($yuranDbCount == '') {
            $yuranDbCount = 0;
        }
        if ($yuranKtCount == '') {
            $yuranKtCount = 0;
        }
        $bakiAwal = $yuranDbCount - $yuranKtCount;
        $bakiAkhir = 0;

        $totaldebit = 0;
        $totalkredit = 0;
        $totalkreditID = 0;

        $sSQL = "SELECT *  
        FROM transaction 
        WHERE userID = '$pk' 
        AND pymtRefer = '$bond'
        AND year(createdDate) = $yr ORDER BY createdDate";

        $rs = &$conn->Execute($sSQL);

        $pymtRefer = $rs->fields('pymtRefer');

        if ($rs->RowCount() <> 0) {
            while (!$rs->EOF) {
                $deductid_s = $rs->fields('deductID');

                $debit = '';
                $kredit = '';
                $kreditID = '';
                if ($rs->fields('addminus') == 0) {
                    $debit = $rs->fields('pymtAmt');
                    $totaldebit += $debit;
                } else {
                    if ($deductid_s == 1642  or  $deductid_s == 1645 or $deductid_s == 1649 or $deductid_s == 1651 or $deductid_s == 1652 or $deductid_s == 1653 or $deductid_s == 1654 or $deductid_s == 1656 or $deductid_s == 1657 or $deductid_s == 1658 or $deductid_s == 1659 or $deductid_s == 1663 or $deductid_s == 1674 or $deductid_s == 1676 or $deductid_s == 1643  or $deductid_s == 1678 or $deductid_s == 1669 or $deductid_s == 1668 or $deductid_s == 1672 or $deductid_s == 1680 or $deductid_s == 1771 or $deductid_s == 1767  or $deductid_s == 1745   or $deductid_s == 1781 or $deductid_s == 1851 or $deductid_s == 1859 or $deductid_s == 1842) {
                        $kreditID = $rs->fields('pymtAmt');
                        $totalkreditID += $kreditID;
                    } else {
                        $kredit = $rs->fields('pymtAmt');
                        $totalkredit += $kredit;
                    }
                }

                $rs->MoveNext();
            }

            $sumDebit = $totaldebit + $bakiAwal;

            $bakiHB = $bakiAwal + $totaldebit;
            $bakiBB = $bakiHB - $totalkredit;
        }

        if ($bond == $pymtRefer) {
            print '<tr>
            <td class="Data" align="center">' . $loanNo . '</td>
            <td class="Data">
                <input type="checkbox" class="form-check-input" id="checkbox-' . $id . '" 
                onclick="selPinjaman(' . $id . ', ' . $bakiBB . ', ' . $lpotBiaya . ')"> ' . $name . '
            </td>
            <td class="Data" align="right">' . number_format($bakiBB, 2) . '</td>
            <td class="Data" align="right">' . number_format($lpotBiaya, 2) . '</td>
            <input type="hidden" id="bakiBB-' . $id . '" value="' . $bakiBB . '">
            <input type="hidden" id="untungS-' . $id . '" value="' . $lpotBiaya . '">
        </tr>';
        } else {
            print '<tr>
            <td class="Data" align="center">' . $loanNo . '</td>
            <td class="Data">
                <input type="checkbox" class="form-check-input" id="checkbox-' . $id . '" 
                onclick="selPinjaman(' . $id . ', ' . $loanAmt . ', ' . $lpotBiaya . ')"> ' . $name . '
            </td>
            <td class="Data" align="right">' . number_format($loanAmt, 2) . '</td>
            <td class="Data" align="right">' . number_format($lpotBiaya, 2) . '</td>
            <input type="hidden" id="bakiBB-' . $id . '" value="' . $loanAmt . '">
            <input type="hidden" id="untungS-' . $id . '" value="' . $lpotBiaya . '">
        </tr>';
        }




        $GetLoan->MoveNext();
    }

    print '</table></td></tr>
    <tr><td colspan="4" align="center"><input type="submit" name="submit" value="Pilih" class="btn btn-primary"></td></tr>
    <tr><td class="Data"><br><i>Jumlah Keseluruhan Rekod Pembiayaan : <b>' . $GetLoan->RowCount() . '</b></i></li></td></tr>';
} else {
    print '
    <tr><td class="Label" align="center">
        <hr size="1"><b>- Tiada Rekod Mengenai Jenis Pembiayaan  -</b><hr size="1">
    </td></tr>';
}

print '</table></div>';
print '</form>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the form submission and process the data
    $totalBakiBB = $_POST['totalBakiBB'];
    $totalUntungS = $_POST['totalUntungS'];

    echo '<script type="text/javascript">
	window.opener.document.getElementById("totalBakiBBField").value = ' . $totalBakiBB . ';
	window.opener.document.getElementById("totalUntungSField").value = ' . $totalUntungS . ';
	window.close();
  </script>';
}
?>

</body>

</html>