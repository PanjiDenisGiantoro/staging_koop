<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	ACCbankrecon.php
 *          Date 		: 	04/8/2006
 *********************************************************************************/
if (!isset($mm))	$mm = "ALL"; //date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);
date_default_timezone_set("Asia/Jakarta");

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($code))		$code = "ALL";
if (!isset($filter))	$filter = "0";
if (!isset($dept))		$dept = "";

include("header.php");
include("koperasiQry.php");

$IDName = get_session("Cookie_userName");
$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>'; //dari mana file ni
}


$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.deductID,b.name as deptName 
			FROM transactionacc a, generalacc b
			WHERE a.deductID = b.ID
			AND b.a_class IN (132) GROUP BY a.deductID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(deductID));
		$rs->MoveNext();
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($action == "delete") {

	$tarikh_bankrec = date("Y-m-d H:i:s");

	$sWhere = "";
	$sWhere = "docNo=" . tosql($pk[0], "Text");
	$docNo = dlookup("transactionacc", "docNo", $sWhere);
	$sSQL	= "UPDATE transactionacc SET " .
		"stat_check='1'," .
		"tarikh_bankrec='" . $tarikh_bankrec . "' WHERE " . $sWhere;

	$rs = &$conn->Execute($sSQL);

	$strActivity = $_POST['Submit'] . 'Pengesahan Bank Rekonsilasi - ' . $docNo;
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

	print '<script>alert("Penyata kewangan telah disahkan.");</script>';
}

if ($action == "finish") {
	$sWhere = "";
	for ($i = 0; $i < count($pk); $i++) {
		$sWhere = "docNo=" . tosql($pk[0], "Text");
		$docNo = dlookup("transactionacc", "docNo", $sWhere);
		$sSQL = "UPDATE transactionacc SET stat_check = 0 WHERE " . $sWhere;

		$rs = &$conn->Execute($sSQL);

		$strActivity = $_POST['Submit'] . 'Penarikan Pengesahan Bank Rekonsilasi - ' . $docNo;
		activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 3);

		print '<script>alert("Penarikan penyata kewangan telah ditarik semula.");</script>';
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$sFileName 	= "?vw=ACCbankrecon&mn=$mn"; //file name

$sFileRef  	= "?vw=ACCbaucerpembayaran&mn=$mn"; // file ni pergi mane
$sFileRef1  = "?vw=ACCresitpembayaran&mn=$mn"; // file ni pergi mane
$sFileRef2  = "?vw=ACCSingleEntry&mn=$mn"; // file ni pergi mane
$sFileRef3  = "?vw=ACCDebtorPayment&mn=$mn"; // file ni pergi mane
$sFileRef4  = "?vw=ACCbillpembayaran&mn=$mn"; // file ni pergi mane
$sFileRef5  = "?vw=baucer&mn=$mn"; // file ni pergi mane
$sFileRef6  = "?vw=resit&mn=$mn"; // file ni pergi mane
$sFileRef7  = "?vw=ACCinvoicedebtor&mn=$mn"; // file ni pergi mane
$sFileRef8  = "?vw=jurnal&mn=$mn"; // file ni pergi mane

$title     	=  "Bank Rekonsilasi"; //Title 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($dept <> "") {
	$getQ .= " AND A.deductID = '" . $dept . "'";
} else {
	$getQ .= " AND B.a_class IN (132)";
}

$sSQL = "SELECT DISTINCT A.*,B.ID,A.ID as transID FROM transactionacc A, generalacc B WHERE A.deductID=B.ID AND A.docID NOT IN (0,15) AND YEAR(A.tarikh_doc) = " . $yy;

if ($mm <> "ALL") $sSQL .= " AND MONTH(A.tarikh_doc) =" . $mm;
$sSQL .= $getQ . " ORDER BY A.tarikh_doc DESC";
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$GetVouchers = &$conn->Execute($sSQL);
$GetVouchers->Move($StartRec - 1);

$TotalRec = $GetVouchers->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '<div class="table-responsive">
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="code" value="' . $code . '">
<input type="hidden" name="filter" value="' . $filter . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<div clas="row">
    Bulan   
			<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="ALL"';
if ($mm == "ALL") print 'selected';
print '>- Semua -';
for ($j = 1; $j < 13; $j++) {
	print '	<option value="' . $j . '"';
	if ($mm == $j) print 'selected';
	print '>' . $j;
}
print '		</select>
			Tahun 
			<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
for ($j = 2020; $j <= 2030; $j++) {
	print '	<option value="' . $j . '"';
	if ($yy == $j) print 'selected';
	print '>' . $j;
}
print '		</select>

		<input type="submit" name="action1" value="Capai" class="btn btn-sm btn-secondary">
		
</div><br/>
<div clas="row">
    			Bank
			<select name="dept" class="form-select-sm" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '		</select>&nbsp;&nbsp; 
			 <input type="submit" class="btn btn-sm btn-secondary" value="Cari">';

if (($IDName == 'admin') or ($IDName == 'superadmin')) {
	print '&nbsp; <input type="button" class="btn btn-sm btn-primary" value="Pengesahan" onClick="ITRActionButtonClick(\'delete\');"> ';
}
echo ' 
</div>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
';

if ($GetVouchers->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td class="textFont">

						<input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All ||
							
						Pengesahan';
	if (($IDName == 'superadmin') or ($IDName == 'admin')) {
		print '	<input type="button" class="btn btn-sm btn-danger" value="Penarikan Pengesahan" onClick="ITRActionButtonClick(\'finish\');">	';
	}

	print ' </td></tr>';

	print '</td>
					
						<td align="right" class="textFont">';
	echo papar_ms($pg);
	print '</td>
					</tr>
				</table>
			</td>
		</tr>';
	print '
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap>&nbsp;</td>
						<td nowrap>Nomor Rujukan&nbsp;&nbsp;</td>
						<td nowrap align ="center">Batch</td>
						<td nowrap>Bank</td>
						<td nowrap align ="right">Debit (RP)</td>
						<td nowrap align ="right">Kredit (RP)</td>
						<td nowrap align ="center">Tanggal</td>
						<td nowrap>Pengesahan</td>
											
					</tr>';

	$DRTotal = 0;
	$CRTotal = 0;
	while (!$GetVouchers->EOF && $cnt <= $pg) {
		$jumlah = 0;
		$tarikh_baucer = toDate("d/m/y", $GetVouchers->fields(tarikh_doc));

		$bankname = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(deductID), "Text"));
		$batchName = dlookup("generalacc", "name", "ID=" . tosql($GetVouchers->fields(batchNo), "Text"));

		$colorPen = "Data";
		if ($GetVouchers->fields(stat_check) == 1) {
			$colorPen = "greenText";
			$pengesahan = "Pengesahan Telah Dibuat";
		} else {
			$colorPen = "redText";
			$pengesahan = "Pengesahan Belum Dilakukan";
		}
		$sSQL2 = "SELECT g_lockstat FROM generalacc WHERE ID = " . $GetVouchers->fields(batchNo) . " ORDER BY ID";
		$rsDetail = &$conn->Execute($sSQL2);

		print ' <tr>
			<td class="Data" align="right">' . $bil . '</td>';
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if ($GetVouchers->fields(docID) == 3) { //BAUCER

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef . '&action=view&no_baucer=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}

			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 4) { //RESIT

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef1 . '&action=view&no_resit=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 2) { //SINGLE ENTRY

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef2 . '&action=view&SENO=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 6) { //BAYAR INVOICE

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef3 . '&action=view&RVNo=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 7) { //BAYAR BIL

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef4 . '&action=view&no_bill=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 12) { //BAUCER ANGGOTA

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef5 . '&action=view&no_baucer=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 10) { //RESIT ANGGOTA

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef6 . '&action=view&no_resit=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 5) { //INVOICE ANGGOTA

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {

				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef7 . '&action=view&invNo=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if ($GetVouchers->fields(docID) == 11) { //JURNAL ANGGOTA

			if ($rsDetail->fields(g_lockstat) == 1) {
				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				
				' . $GetVouchers->fields(docNo) . '
			</td>';
			} else {

				print 	'<td class="Data">
				<input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetVouchers->fields(docNo)) . '">
				<a href="' . $sFileRef8 . '&action=view&no_jurnal=' . tohtml($GetVouchers->fields(docNo)) . '&yy=' . $yy . '&mm=' . $mm . '">
				' . $GetVouchers->fields(docNo) . '
			</td>';
			}
			print '	<td class="Data" align="center">' . $batchName . '</td>';
			print '	<td class="Data">' . $bankname . '</td>';

			if ($GetVouchers->fields(addminus) == 0) {
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
				print '	<td class="Data" align ="right">0.00</td>';
			}
			if ($GetVouchers->fields(addminus) == 1) {
				print '	<td class="Data" align ="right">0.00</td>';
				print '	<td class="Data" align ="right">' . $GetVouchers->fields(pymtAmt) . '</td>';
			}
			print '	<td class="Data" align="center">' . $tarikh_baucer . '</td>';
		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////


		print '<td class="Data" align="left"><font class="' . $colorPen . '">' . $pengesahan . '</font>';

		print '	</tr>';
		$cnt++;
		$bil++;
		$GetVouchers->MoveNext();
	}
	$GetVouchers->Close();

	print '	</table>
</td></tr><tr><td>';

	if ($TotalRec > $pg) {
		print ' <table border="0" cellspacing="5" cellpadding="0"  class="textFont" width="100%">';
		if ($TotalRec % $pg == 0) {
			$numPage = $TotalPage;
		} else {
			$numPage = $TotalPage + 1;
		}
		print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
		for ($i = 1; $i <= $numPage; $i++) {
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td></tr></table>';
	}
	print '</td></tr><tr>
			<td class="textFont">Jumlah Rujukan : <b>' . $GetVouchers->RowCount() . '</b></td>
		</tr>';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . ' Bagi Bulan/Tahun - ' . $mm . '/' . $yy . ' -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form></div>';

include("footer.php");

print '
<script language="JavaScript">
	var allChecked=false;
	function ITRViewSelectAll() {
	    e = document.MyForm.elements;
	    allChecked = !allChecked;
	    for(c=0; c< e.length; c++) {
	      if(e[c].type=="checkbox" && e[c].name!="all") {
	        e[c].checked = allChecked;
	      }
	    }
	}

	function ITRActionButtonClick(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak disahkan.\');
	        } else {
	          if(confirm(count + \' rekod hendak disahkan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }	   

	function ITRActionButtonStatus() {
		e = document.MyForm;
		if(e==null) {
			alert(\'Silakan pastikan nama form dibuat/tersedia.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Silakan pilih satu data saja untuk memperbarui status\');
			} else {
				window.open(\'transStatus.php?pk=\' + pk,\'status\',\'top=50,left=50,width=500,height=250,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no\');					
			}
		}
	}
		
	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '&yy=' . $yy . '&mm=' . $mm . '&code=' . $code . '&filter=' . $filter . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}

</script>';
