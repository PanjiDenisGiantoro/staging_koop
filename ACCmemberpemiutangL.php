<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCmemberpemiutangL.php
 *          Date 		: 	06/10/2003
 *		   Amended		:	01/07/2024 - list all invoices of debtors with their amounts and due amounts
 *********************************************************************************/
include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))    $StartRec = 1;
if (!isset($pg))        $pg = 30;
if (!isset($q))            $q = "";
if (!isset($by))        $by = "1";
if (!isset($dept))        $dept = "";
if (!isset($filter))    $filter = "ALL";

$sFileName = "ACCmemberpemiutangL.php"; //file name

//--- Prepare department list
$deptList     = array();
$deptVal      = array();
$sSQL         = " SELECT a.departmentID, b.code as deptCode, b.name as deptName
			FROM userdetails a,	general	b
			WHERE a.departmentID = b.ID
			AND	  a.status = 1
			GROUP BY a.departmentID";
$rs            = &$conn->Execute($sSQL);
if ($rs->RowCount()    <> 0) {
    while (!$rs->EOF) {
        array_push($deptList, $rs->fields(deptName));
        array_push($deptVal, $rs->fields(departmentID));
        $rs->MoveNext();
    }
}

// excluded invoices that have 0 balance & no initial amount & negative balance
$sSQL     = "";
$sWhere = "  A.invNo IS NOT NULL ";
$sWhere .= " AND A.outstandingbalance > 0 ";
$sWhere .= " AND (B.balance > 0 OR B.invNo IS NULL) ";

if ($q <> "") {
    if ($by == 1) {
        $sWhere .= " AND A.invNo like '%" . $q . "%'";
    } else if ($by == 2) {
        $sWhere .= " AND A.companyID = C.ID ";
        $sWhere .= " AND C.name like '%" . $q . "%'";
    } else if ($by == 3) {
        $sWhere .= " AND A.userID = B.userID ";
        $sWhere .= " AND B.newIC like '%" . $q . "%'";
    }
}

if ($id) $sWhere .= " AND A.companyID in (" . $id . ") ";

$sWhere = " WHERE (" . $sWhere . ")";

$sSQL = "SELECT A.invNo AS invoice_invNo, A.*, B.invNo AS payment_invNo, B.balance 
			FROM cb_invoice A 
			LEFT JOIN (
				SELECT invNo, MAX(ID) AS max_id 
				FROM cb_payments 
				GROUP BY invNo 
			) AS max_payments 
			ON A.invNo = max_payments.invNo 
			LEFT JOIN cb_payments B 
			ON max_payments.max_id = B.ID
			JOIN generalacc C
			ON A.companyID = C.ID";

$sSQL         = $sSQL . $sWhere . ' ORDER BY A.tarikh_inv DESC';
$GetLoan     = &$conn->Execute($sSQL);

$GetLoan->Move($StartRec - 1);

$TotalRec     =    $GetLoan->RowCount();
$TotalPage     =  ($TotalRec / $pg);

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
	// Assigning PHP variable to JavaScript
	var source = "' . $source . '";

	function selPinjaman(id,code,invNo,nama,b_Baddress,amt)
	{
		window.opener.document.MyForm.companyID.value = id;
		window.opener.document.MyForm.code.value = code;
		if (source == "selfbill") {
			console.log("Debug: source is selfbill");
			window.opener.document.MyForm.invNoRefer.value = invNo;
		} else {
			console.log("Debug: source is NOT selfbill");
			window.opener.document.MyForm.invNo.value = invNo;
		}
		window.opener.document.MyForm.nama.value = nama;
		window.opener.document.MyForm.b_Baddress.value = b_Baddress;
		window.opener.document.MyForm.amt.value = amt;
		window.close();
}
</script>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" class="bodyBG">';

print '
<form name="MyForm" action=' . $PHP_SELF . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="0" width="95%" align="center" class="lineBG">
	<tr>
		<td class="Data">
			<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" style="font-size: 9pt;">
				<h5 class="card-title">Senarai Invois Penghutang</h5>
	<tr	valign="top" class="Header">
		<td	align="left" >
			Carian Melalui
			<select	name="by" class="form-select-sm">';
if ($by    == 1)    print '<option value="1" selected>Nombor Invois</option>';
else print '<option	value="1">Nombor Invois</option>';
if ($by    == 2)    print '<option value="2" selected>Nama Syarikat</option>';
else print '<option	value="2">Nama Syarikat</option>';
print '	</select>
			<input type="text" name="q"	value="" maxlength="50"	size="20" class="form-control-sm">
			<input type="submit" class="btn btn-sm btn-secondary" value="Cari">
			';

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
if ($pg    == 5)    print '<option value="5" selected>5</option>';
else print '<option	value="5">5</option>';
if ($pg    == 10)    print '<option value="10" selected>10</option>';
else print '<option	value="10">10</option>';
if ($pg    == 20)    print '<option value="20" selected>20</option>';
else print '<option	value="20">20</option>';
if ($pg    == 30)    print '<option value="30" selected>30</option>';
else print '<option	value="30">30</option>';
if ($pg    == 40)    print '<option value="40" selected>40</option>';
else print '<option	value="40">40</option>';
if ($pg    == 50)    print '<option value="50" selected>50</option>';
else print '<option	value="50">50</option>';
if ($pg    == 100)    print '<option value="100" selected>100</option>';
else print '<option	value="100">100</option>';
if ($pg    == 200)    print '<option value="200" selected>200</option>';
else print '<option	value="200">200</option>';
if ($pg    == 300)    print '<option value="300" selected>300</option>';
else print '<option	value="300">300</option>';
if ($pg    == 400)    print '<option value="400" selected>400</option>';
else print '<option	value="400">400</option>';
if ($pg    == 500)    print '<option value="500" selected>500</option>';
else print '<option	value="500">500</option>';
if ($pg    == 1000) print '<option	value="1000" selected>1000</option>';
else print '<option	value="1000">1000</option>';

print '				</select> setiap	mukasurat.
					</td>
				</tr>';
print '	</table>
		</td>
	</tr>';

if ($GetLoan->RowCount() == 0) {
    print '		<tr><td	class="Label" align="center" height=50 valign=middle>
					<b>- Sila masukkan Nombor / Nama syarikat  -</b>
				</td></tr>';
} else {
    if ($GetLoan->RowCount() <> 0) {
        $bil = $StartRec;
        $cnt = 1;
        print '	<tr>
					<td class="Data" width="100%">
						
				<table border="0" cellpadding="2" cellspacing="1" width="100%" class="table table-sm table-striped" style="font-size: 9pt;">
					<tr class="table-primary">
						<td	nowrap>&nbsp;</td>
						<td	nowrap><b>Nombor Invois</b></td>
						<td	nowrap><b>No/Nama Syarikat</b></td>
						<td	nowrap><b>Alamat Billing</b></td>
						<td	nowrap align="right"><b>Jumlah Invois (RM)</b></td>
		';
        if (!$source) {
            print '
						<td	nowrap align="right"><b>Bayaran (RM)</b></td>
						<td	nowrap align="right"><b>Tunggakan (RM)</b></td>
		';
        }
        print '
						<td	nowrap align="center"><b>Tarikh Invois</b></td>
				</tr>
		';


        $amtLoan = 0;
        while (!$GetLoan->EOF && $cnt <= $pg) {
            $totalInv     = $GetLoan->fields(outstandingbalance);
            $amtLoan     = $amtLoan + tosql($GetLoan->fields(loanAmt), "Number");
            $id            = $GetLoan->fields(companyID);
            $code        = dlookup("generalacc", "code", "ID=" . tosql($GetLoan->fields(companyID), "Text"));
            $nama        = str_replace("'", "", dlookup("generalacc", "name", "ID=" . tosql($GetLoan->fields(companyID), "Text")));
            $b_Baddress    = dlookup("generalacc", "b_Baddress", "ID=" . tosql($GetLoan->fields(companyID), "Text"));
            $kodGL         = $GetLoan->fields(kodGL);
            $invNo         = $GetLoan->fields(invNo);

            $sSQL1     = "SELECT * FROM cb_payments WHERE invNo = " . tosql($invNo, "Text") . " AND ID = (SELECT MAX(ID) FROM cb_payments WHERE invNo = " . tosql($invNo, "Text") . ")";
            $rsB     = &$conn->Execute($sSQL1);

            if (!$rsB->fields(balance)) {
                $balance = $totalInv;
            } else {
                $balance = $rsB->fields(balance);
            }

            $sSQL2     = "SELECT SUM(outstandingbalance - balance) AS total_diff
					FROM cb_payments WHERE invNo = " . tosql($invNo, "Text") . "";
            $rsC     = &$conn->Execute($sSQL2);
            $dahbayar = $rsC->fields(total_diff);

            // $sSQL2 	= "SELECT SUM(pymtAmt) AS dahbayar FROM transactionacc WHERE pymtReferC = " . tosql($invNo, "Text") . " AND addminus IN (1)";
            // $rsC 	= &$conn->Execute($sSQL2);
            // $dahbayar = $rsC->fields(dahbayar);

            // $balance 		= $totalInv - $dahbayar;

            print '	<tr>
		<td	class="Data" align="right">' . $bil . '</td>

		<td	class="Data"><a href="javascript:selPinjaman(\'' . $id . '\',\'' . $code . '\',\'' . $invNo . '\',\'' . $nama . '\',\'' . $b_Baddress . '\',\'' . $balance . '\');">' . $GetLoan->fields(invNo) . '</a></td>

		<td	class="Data">' . dlookup("generalacc",    "code",    "ID=" .    tosql($GetLoan->fields(companyID),    "Text")) . '-' . dlookup("generalacc", "name", "ID="    . tosql($GetLoan->fields(companyID), "Text")) . '</td>

		<td	class="Data">' . dlookup("generalacc",    "b_Baddress", "ID=" . tosql($GetLoan->fields(companyID), "Text")) . '</td>

		<td	class="Data" align="right">' . number_format($totalInv, 2) . '</td>
		';
            if (!$source) {
                print '
		<td	class="Data" align="right">' . number_format($dahbayar, 2) . '</td>
		<td	class="Data" align="right">' . number_format($balance, 2) . '</td>
		';
            }
            print '
		<td	class="Data" align="center">' . toDate("d/m/yy", $GetLoan->fields(tarikh_inv)) . '</td>
		</tr>';
            $cnt++;
            $bil++;
            $GetLoan->MoveNext();
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
    } // end of ($GetLoan->RowCount() <> 0)
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
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value+"&filter=' . $filter . '";
	}

</script>';