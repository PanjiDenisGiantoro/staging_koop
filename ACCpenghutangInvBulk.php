<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCpenghutangInvBulk.php
 *          Date 		: 	14/5/2024 - List of invoices of a specific debtor company with their due amounts
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 30;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($filter))	$filter = "ALL";

$sFileName = "ACCpenghutangInvBulk.php"; //file name
//--- Prepare department list
$deptList 	= array();
$deptVal  	= array();
$sSQL 		= "	SELECT a.departmentID, b.code as deptCode, b.name as deptName
			FROM userdetails a,	general	b
			WHERE a.departmentID = b.ID
			AND	  a.status = 1
			GROUP BY a.departmentID";
$rs			= &$conn->Execute($sSQL);
if ($rs->RowCount()	<> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields('deptName'));
		array_push($deptVal, $rs->fields('departmentID'));
		$rs->MoveNext();
	}
}

// excluded invoices that have 0 balance & no initial amount & negative balance
$sSQL 	 = "";
$sWhere  = "  A.invNo IS NOT NULL ";
$sWhere .= " AND A.outstandingbalance > 0 ";
$sWhere .= " AND (B.balance > 0 OR B.invNo IS NULL) ";
$sWhere .= " AND (A.companyID = $compID";
$sWhere .= " OR B.companyID = $compID)";

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.invNo like '%" . $q . "%'";
	}
}

if ($compID) $sWhere .= " AND A.companyID in (" . $compID . ") ";

$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "SELECT A.invNo AS invoice_invNo, A.companyID, A.*, B.invNo AS payment_invNo, B.balance
	FROM cb_invoice A 
	LEFT JOIN (
		SELECT invNo, MAX(ID) AS max_id 
		FROM cb_payments 
		GROUP BY invNo 
	) AS max_payments 
	ON A.invNo = max_payments.invNo
	LEFT JOIN cb_payments B 
	ON max_payments.max_id = B.ID";

$sSQL 		= $sSQL . $sWhere . ' ORDER BY A.tarikh_inv DESC';
$GetInvoice = &$conn->Execute($sSQL);

$GetInvoice->Move($StartRec - 1);

$TotalRec 	= $GetInvoice->RowCount();
$TotalPage 	= ($TotalRec / $pg);

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
</head>
<script language="JavaScript">
function selPinjaman(invNo,amt,kodGL)
{
window.opener.document.MyForm.invNo2.value = invNo;
window.opener.document.MyForm.invAmaun2.value = amt;
window.opener.document.MyForm.b_kodGL.value = kodGL;
window.close();
}

</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<input type="hidden" name="compID" value="' . $compID . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" style="font-size: 9pt;">
				<h5 class="card-title">Senarai Invois Syarikat Penghutang <b>' . dlookup("generalacc", "name", "ID=" . $compID) . '</b></h5>
	<tr	valign="top" class="Header">
		<td	align="left" >
			Carian Melalui
			<select	name="by" class="form-select-sm">';
if ($by	== 1)	print '<option value="1" selected>Nombor Invois</option>';
else print '<option	value="1">Nombor Invois</option>';
print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="20" class="form-control-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

print '	</select>
		</td>
	</tr>
	<tr	valign="top" class="textFont">
		<td>
			<table width="100%" style="font-size: 9pt;">
				<tr>
					<td	 class="textFont" align ="left">&nbsp;</td>
					<td	align="right" class="textFont">

					Paparan	<SELECT	name="pg" class="form-select-xs" onchange="doListAll();">';
if ($pg	== 5)	print '<option value="5" selected>5</option>';
else print '<option	value="5">5</option>';
if ($pg	== 10)	print '<option value="10" selected>10</option>';
else print '<option	value="10">10</option>';
if ($pg	== 20)	print '<option value="20" selected>20</option>';
else print '<option	value="20">20</option>';
if ($pg	== 30)	print '<option value="30" selected>30</option>';
else print '<option	value="30">30</option>';
if ($pg	== 40)	print '<option value="40" selected>40</option>';
else print '<option	value="40">40</option>';
if ($pg	== 50)	print '<option value="50" selected>50</option>';
else print '<option	value="50">50</option>';
if ($pg	== 100)	print '<option value="100" selected>100</option>';
else print '<option	value="100">100</option>';
if ($pg	== 200)	print '<option value="200" selected>200</option>';
else print '<option	value="200">200</option>';
if ($pg	== 300)	print '<option value="300" selected>300</option>';
else print '<option	value="300">300</option>';
if ($pg	== 400)	print '<option value="400" selected>400</option>';
else print '<option	value="400">400</option>';
if ($pg	== 500)	print '<option value="500" selected>500</option>';
else print '<option	value="500">500</option>';
if ($pg	== 1000) print '<option	value="1000" selected>1000</option>';
else print '<option	value="1000">1000</option>';

print '				</select> setiap	mukasurat.
					</td>
				</tr>';
print '	</table>
		</td>
	</tr>';

if ($GetInvoice->RowCount() == 0) {
	print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan invois untuk syarikat  -</b>
				</td></tr>';
} else {
	if ($GetInvoice->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;
		print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
					<tr class="table-primary">
						<td	nowrap>&nbsp;</td>
						<td	nowrap><b>Nombor Invois</b></td>
						<td	nowrap align="right"><b>Jumlah Invois (RM)</b></td>
						<td	nowrap align="right"><b>Bayaran (RM)</b></td>
						<td	nowrap align="right"><b>Tunggakan (RM)</b></td>
						<td	nowrap align="center"><b>Tarikh Invois</b></td>
					</tr>';
		$amtLoan = 0;
		while (!$GetInvoice->EOF && $cnt <= $pg) {
			$totalInv 	= $GetInvoice->fields('outstandingbalance');
			$amtLoan 	= $amtLoan + tosql($GetInvoice->fields('loanAmt'), "Number");
			$id			= $GetInvoice->fields('companyID');
			$code		= dlookup("generalacc", "code", "ID=" . tosql($GetInvoice->fields('companyID'), "Text"));
			$nama		= str_replace("'", "", dlookup("generalacc", "name", "ID=" . tosql($GetInvoice->fields('companyID'), "Text")));
			$b_Baddress	= dlookup("generalacc", "b_Baddress", "ID=" . tosql($GetInvoice->fields('companyID'), "Text"));
			$kodGL 		= dlookup("generalacc", "b_kodGL", "ID=" . $compID);
			$invNo 		= $GetInvoice->fields('invNo');

			$sSQL1 	= "SELECT * FROM cb_payments 
					WHERE invNo = " . tosql($invNo, "Text") . " 
					AND ID = (SELECT MAX(ID) FROM cb_payments WHERE invNo = " . tosql($invNo, "Text") . ")";
			$rsB 	= &$conn->Execute($sSQL1);

			if (!$rsB->fields('balance')) {
				$balance = $totalInv;
			} else {
				$balance = $rsB->fields('balance');
			}

			$sSQL2 	= "SELECT SUM(outstandingbalance - balance) AS total_diff
					FROM cb_payments WHERE invNo = " . tosql($invNo, "Text") . "";
			$rsC 	= &$conn->Execute($sSQL2);

			$dahbayar = $rsC->fields('total_diff');

			print '	<tr>
		<td	class="Data" align="right">' . $bil . '</td>

        <td class="Data" nowrap>
        <a href="javascript:selPinjaman(\'' . $invNo . '\',\'' . $balance . '\',\'' . $kodGL . '\');">
                ' . $GetInvoice->fields('invNo') . '
            </a>
        </td>

		<td	class="Data" align="right">' . number_format($totalInv, 2) . '</td>
		<td	class="Data" align="right">' . number_format($dahbayar, 2) . '</td>
		<td	class="Data" align="right">' . number_format($balance, 2) . '</td>


		<td	class="Data" align="center">' . toDate("d/m/yy", $GetInvoice->fields('tarikh_inv')) . '</td>
		</tr>';
			$cnt++;
			$bil++;
			$GetInvoice->MoveNext();
		}
		print ' </table>
			</td>
		</tr>		
		<tr>
			<td>';
		if ($TotalRec > $pg) {
			print '
					<table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
			if ($TotalRec % $pg == 0) {
				$numPage = $TotalPage;
			} else {
				$numPage = $TotalPage + 1;
			}
			print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}
		print '
			</td>
		</tr>';

		print '
				</td>
			</tr>
				</table>
				
						</td>
					</tr>';
	} else {
		print '
					<tr><td	class="Label" align="center" height=50 valign=middle>
						<b>- Tiada rekod mengenai syarikat  -</b>
					</td></tr>';
	} // end of ($GetInvoice->RowCount() <> 0)
} // end of ($q == "" AND $dept == "")
print '		</table>
		</td>
	</tr>
</table>
</form>
<p align="center" class="footer">' . $retooFetis . '</p>
</body>
</html>';

print '
<script language="JavaScript">

    function doListAll() {
        var c = document.forms[\'MyForm\'].pg;
        document.location = "' . $sFileName . '?compID=' . $compID . '&StartRec=1&pg=" + c.options[c.selectedIndex].value + "&filter=' . $filter . '";
    }

</script>';
