<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	dividenList.php
 *          Date 		: 	15/6/2006
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 100;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";
if (!isset($mth)) 	$mth = date("n");
if (!isset($yr)) 	$yr	= date("Y");
if (!isset($mm))	$mm = date("n");
if (!isset($yy))	$yy = date("Y");

include("header.php");
include("koperasiQry.php");

date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=dividenList&mn=$mn";
$sFileRef  = "?vw=Edit_memberStmt&mn=$mn";
$title     = "Daftar Dividen";

$updatedDate = date("Y-m-d H:i:s");
$updatedBy 	= get_session("Cookie_userName");
//...............................................................
if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status = 1 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

$sSQL = "";
$sWhere = " a.userID = b.userID AND a.userID = c.userID AND c.AmtDiv > 0 and c.yearDiv = '" . $yy . "' and b.status IN ('1','4')";
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID like '%" . $q . "%'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC like '%" . $q . "%'";
	}
}

if ($ID) {
	$sWhere .= " AND b.userID = " . tosql($ID, "Text");
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b, dividen c ";
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) ASC";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<div class="table-responsive">
<form name="MyForm" action="?vw=dividenList&mn=' . $mn . '" method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">';

if ($generate) {
	$rpath = realpath("dividenList.php");
	$dpath = dirname($rpath);
	$fname = trim($fname);
	$fname = 'DIV' . $yy . '.csv';
	$filename = $dpath . '/DIVIDEN/' . $fname;
	$file = fopen($filename, 'w', 1);

	$bil = 1;
	while (!$GetMember->EOF) {

		$rsOpn = getListDividen($GetMember->fields(userID), $yy);
		$totalFees = $rsOpn->fields(AmtDiv);
		$totalAllFee = getFees($GetMember->fields(userID));
		$namatrans = 'Dividen ' . $yy . '';

		$namaang = substr($GetMember->fields(name), 0, 40);

		$IC    = $GetMember->fields(newIC);
		$space = "'";
		$kadIC = $space . $IC;

		$akaunbank  = $GetMember->fields(accTabungan);
		$space      = "'";
		$akaunbank2 = $space . $akaunbank;

		$IDBank   = $GetMember->fields(bankID);
		$namabank = dlookup("general", "name", "ID=" . $IDBank);
		$codebank = dlookup("general", "code", "ID=" . $IDBank);
		$space2   = "-";

		$CodeNameBank = $codebank . $space2 . $namabank;

		// dlookup("general", "name", "ID=" . tosql($rs->fields(deductID), "Number"))

		fwrite($file, $namaang);
		fwrite($file, ",");
		fwrite($file, $kadIC);
		fwrite($file, ",");
		fwrite($file, $CodeNameBank);
		fwrite($file, ",");
		fwrite($file, $akaunbank2);
		fwrite($file, ",");
		fwrite($file, $totalFees);
		fwrite($file, ",");
		fwrite($file, $GetMember->fields(userID));
		fwrite($file, ",");
		fwrite($file, $namatrans);
		fwrite($file, "\r\n");

		$GetMember->MoveNext();
		$bil++;
	}

	$strActivity = $_POST['Submit'] . 'Generate Senarai Dividen';
	activityLog($sSQL, $strActivity, get_session('Cookie_userID'), get_session('Cookie_userName'), 1);

	fclose($file);

	$link =  '<a href="/stagingclean/DIVIDEN/' . $fname . '">' . $fname . '</a>';
} else {
	//print 'sila masukkan nama fail.';
}
//--------
if ($link) {
	print '<tr><td>&nbsp;<b>(RIGHT CLICK - SAVE LINK AS TO DOWNLOAD):</b>&nbsp;' . $link . ' </td></tr>';
}

if (get_session("Cookie_groupID") > 0) {
	print '    <tr valign="top" class="textFont">
	   	<td align="left">
			<table>
				<tr>
				<td class="textFont">&nbsp;

						Pilih Tahun  
						<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
	for ($j = 2020; $j <= 2030; $j++) {
		print '	<option value="' . $j . '"';
		if ($yy == $j) print 'selected';
		print '>' . $j;
	}
	print '</select>&nbsp;&nbsp;<input type="button" name="generate" value="Generate CSV File" class="btn btn-sm btn-secondary" onclick= "Javascript:(window.location.href=\'?vw=dividenList&action=view&mn=' . $mn . '&yy=' . $yy . '&generate=y\')">	</td>
</tr></table></td></tr>';

	if ($GetMember->RowCount() <> 0) {
		$bil = $StartRec;
		$cnt = 1;

		print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">&nbsp;</td>
						<td align="right" class="textFont">
						
						</td>
					</tr>
				</table>
			</td>
		</tr>';
		//}

		print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					
					<tr class="table-primary">
						<td align="center" nowrap>Bil</td>
						<td align="left"   nowrap>Nomor - Nama Anggota</td>
						<td align="center" nowrap>Kartu Identitas</td>
						<td align="center" nowrap>Akaun Bank</td>
						<td nowrap align="right">Dividen Tahun ' . $yy . ' (RM)</td>
					</tr>';
		$totalFees = 0;
		$totalShares = 0;

		while (!$GetMember->EOF  && $cnt <= $pg) {

			$rsOpn = getListDividen($GetMember->fields(userID), $yy);
			$totalFees = number_format($rsOpn->fields(AmtDiv), 2);
			$totalAllFee = getFees($GetMember->fields(userID));
			$totalAllFee = number_format($totalAllFee, 2);

			print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data">' . $GetMember->fields('memberID') . ' - ' . $GetMember->fields(name) . '</td>
						<td class="Data" align="center">' . $GetMember->fields('newIC') . '</td>		
						<td class="Data" align="center">' . $GetMember->fields('accTabungan') . '</td>						
						<td class="Data" align="right">' . $totalFees . '</td>
					</tr>';
			$cnt++;
			$bil++;

			$GetMember->MoveNext();
		}
		$GetMember->Close();

		//.......... check ...............
		if ($apply) {
			$sSQL10 = "SELECT *
			FROM dividen
			WHERE startYear = " . $yymm . "";
			$rsChecking = &$conn->Execute($sSQL10);
			if ($rsChecking->RowCount() > 0) {
				print '<script>alert("Permohonan Dividen telah dikemaskini di dalam sistem !");</script>';
			}
		}

		//..........end check ............
		print '</table>
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
			print '<tr><td class="textFont" valign="top" align="left">Data Dari : <br>';
			for ($i = 1; $i <= $numPage; $i++) {
				print '<A href="' . $sFileName . '&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '&q=' . $q . '&by=' . $by . '&dept=' . $dept . '">';
				print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a> &nbsp; &nbsp;';
			}
			print '</td>
						</tr>
					</table>';
		}
		print '
			</td>
		</tr>';
	} else {
		if ($q == "") {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
		} else {
			print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
		}
	} // end of ($GetMember->RowCount() <> 0)
	// end of ($q == "" AND $dept == "")
} else {

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberPenyataYearly\')">Penyata Tahunan Anggota</a>
		</td>
	</tr>';
}
print ' 
</table>
</form></div>';

include("footer.php");
