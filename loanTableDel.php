<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	loanTableDel.php
 *          Date 		: 	06/06/2016
 *********************************************************************************/

if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "0";
if (!isset($dept))		$dept = "";
if (!isset($yr)) $yr	= date("Y");
if (!isset($yy)) $yy	= date("Y");
if (!isset($mm)) $mm	= date("m");

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (get_session("Cookie_groupID") <> 1 and get_session("Cookie_groupID") <> 2 or get_session("Cookie_koperasiID") <> $koperasiID) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = 'loanTableDel.php';
$sFileRef  = 'loanEdit.php';
$title     = "Pengurusan Pembiayaan Diluluskan (Bagi Potongan Gaji Bulanan)";

//----print penyata tahunan Pembiayaan
if ($action <> "") {
	print '	<script>';
	if ($action == "Penyata") {
		print ' rptURL = "loanYearly.php?yr=' . $yr . '&loanID=' . $pk[0] . '";';
		print ' window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");';
	}
	print ' </script>';
}

if ($action	== "finish") {

	for ($i = 0; $i < count($pk); $i++) {

		$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
			FROM loans where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sqlLoan);
		if ($Get->RowCount() > 0) {
			$loanAmt = $Get->fields(loanAmt);
			$totUntung = $Get->fields(totUntung);
			$loanType = $Get->fields(loanType);
			//$loanNo = $Get->fields(loanNo);
		}

		$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

		$sql = "select rnoBond FROM loandocs where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $bond = $Get->fields(rnoBond);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID = '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID <> '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);

		//if($bakiPkk <=0 && $bakiUnt >= $totUntung){
		//r01 $str = implode("," ,$pk	);
		$updatedBy	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL =	'';
		$sWhere	= '';
		$sWhere	= '	loanID	in (' . $str	. ')';
		$sWhere	= '	loanID	= ' . $pk[$i];
		$sSQL	= '	UPDATE loans ';
		$sSQL	.= ' SET ' .
			' status	=' . tosql(9, "Text") .
			' ,selesaiBy	=' . tosql($updatedBy, "Text") .
			' ,selesaiDate='	. tosql($updatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		//print '<br>'.$sSQL;
		$rs	= &$conn->Execute($sSQL);

		// status update potongan gaji
		$sqlLoan2 = "SELECT * FROM loans where loanID = '" . $pk[$i] . "'";
		$rs2	= &$conn->Execute($sqlLoan2);
		$loanNo = $rs2->fields(loanNo);

		$sqlLoan3 = "SELECT * FROM potbulan where loanID = '" . $loanNo . "'";
		$rs3	= &$conn->Execute($sqlLoan3);
		//$lDpot = $rs3->fields(ID);
		if ($rs3->RowCount() > 0) {
			$sSQL =	'';
			$sWhere	= '';
			$sWhere	= '	ID	= ' . $rs3->fields(ID);
			$sSQL	= '	UPDATE potbulan ';
			$sSQL	.= ' SET ' .
				' status	=' . tosql(9, "Text") .
				' ,isSettleStat	=' . tosql(1, "Text") .
				' ,selesaiBy	=' . tosql($updatedBy, "Text") .
				' ,selesaiDate='	. tosql($updatedDate, "Text");
			$sSQL .= ' WHERE ' . $sWhere;
			//print '<br>'.$sSQL;
			$rs	= &$conn->Execute($sSQL);
		}
		//}else{ 
	} //for close
	print '<script>alert("PEMBIAYAAN TELAH SELESAI");</script>';
	//} test sekjap

}
//--- End -------------------------------------------------------
//--- Prepare department list

if ($action	== "Lapuk") {

	for ($i = 0; $i < count($pk); $i++) {

		$sqlLoan = "SELECT * , (loanAmt * kadar_u /100 * loanPeriod/12) AS totUntung
			FROM loans where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sqlLoan);
		if ($Get->RowCount() > 0) {
			$loanAmt = $Get->fields(loanAmt);
			$totUntung = $Get->fields(totUntung);
			$loanType = $Get->fields(loanType);
			//$loanNo = $Get->fields(loanNo);
		}

		$sql = "select c_Deduct FROM general where ID = '" . $loanType . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $c_Deduct = $Get->fields(c_Deduct);

		$sql = "select rnoBond FROM loandocs where loanID = '" . $pk[$i] . "'";
		$Get =  &$conn->Execute($sql);
		if ($Get->RowCount() > 0) $bond = $Get->fields(rnoBond);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID = '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiPkk = $rsOpen->fields(yuranDb) - $rsOpen->fields(yuranKt);

		$getOpen = "SELECT 
		SUM(CASE WHEN addminus = '0' THEN pymtAmt ELSE 0 END) AS yuranDb, 
		SUM(CASE WHEN addminus = '1' THEN pymtAmt ELSE 0 END) AS yuranKt
		FROM transaction
		WHERE
		pymtRefer = '" . $bond . "'
		AND deductID <> '" . $c_Deduct . "' 
		AND month(createdDate) <= " . $mm . "
		AND year(createdDate) <= " . $yr . "
		GROUP BY pymtRefer";
		$rsOpen = $conn->Execute($getOpen);
		if ($rsOpen->RowCount() == 1) $bakiUnt = $rsOpen->fields(yuranKt) - $rsOpen->fields(yuranDb);

		//if($bakiPkk <=0 && $bakiUnt >= $totUntung){
		//r01 $str = implode("," ,$pk	);
		$updatedBy	= get_session("Cookie_userName");
		$updatedDate = date("Y-m-d H:i:s");
		$sSQL =	'';
		$sWhere	= '';
		$sWhere	= '	loanID	in (' . $str	. ')';
		$sWhere	= '	loanID	= ' . $pk[$i];
		$sSQL	= '	UPDATE loans ';
		$sSQL	.= ' SET ' .
			' status	=' . tosql(7, "Text") .
			' ,selesaiBy	=' . tosql($updatedBy, "Text") .
			' ,selesaiDate='	. tosql($updatedDate, "Text");
		$sSQL .= ' WHERE ' . $sWhere;
		//print '<br>'.$sSQL;
		$rs	= &$conn->Execute($sSQL);

		// status update potongan gaji
		$sqlLoan2 = "SELECT * FROM loans where loanID = '" . $pk[$i] . "'";
		$rs2	= &$conn->Execute($sqlLoan2);
		$loanNo = $rs2->fields(loanNo);

		$sqlLoan3 = "SELECT * FROM potbulan where loanID = '" . $loanNo . "'";
		$rs3	= &$conn->Execute($sqlLoan3);
		//$lDpot = $rs3->fields(ID);
		if ($rs3->RowCount() > 0) {
			$sSQL =	'';
			$sWhere	= '';
			$sWhere	= '	ID	= ' . $rs3->fields(ID);
			$sSQL	= '	UPDATE potbulan ';
			$sSQL	.= ' SET ' .
				' status	=' . tosql(7, "Text") .
				' ,selesaiBy	=' . tosql($updatedBy, "Text") .
				' ,selesaiDate='	. tosql($updatedDate, "Text");
			$sSQL .= ' WHERE ' . $sWhere;
			//print '<br>'.$sSQL;
			$rs	= &$conn->Execute($sSQL);
		}
		//}else{ 
	} //for close
	print '<script>alert("PEMBIAYAAN HUTANG LAPUK TELAH DIAKTIFKAN");</script>';
	//} test sekjap

}


$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT a.departmentID, b.code as deptCode, b.name as deptName 
			FROM userdetails a, general b
			WHERE a.departmentID = b.ID
			AND   a.status IN (1,4) 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}


$idloan  = array();
// to exclude vehicle $sSQL = "SELECT a.ID FROM `general` a, general b WHERE a.c_Deduct = b.ID AND b.code NOT LIKE '51%'";
$sSQL = "SELECT a.ID FROM `general` a, general b WHERE a.c_Deduct = b.ID AND b.code NOT LIKE '51%'";
$rs = &$conn->Execute($sSQL);
if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		array_push($idloan, $rs->fields(ID));
		$rs->MoveNext();
	}
	$idloan = implode(",", $idloan);
}

//$GetLoan = ctLoanStatusDept($q,$by,"3",$dept,$idloan);
// used by : loan.php, loanTable.php, loanList.php
//function ctLoanStatusDept($q,$by,$status,$dept,$id = 0) {
//	global $conn;
$status = 3;
$statusDel = 1;

$sSQL = "";
$sWhere = " A.statusDel IN('" . tosql($statusDel, "Number") . "') AND A.status = " . tosql($status, "Number");
if ($dept <> "") {
	$sWhere .= " AND B.departmentID = " . tosql($dept, "Number");
	$sWhere .= " AND A.userID = B.userID ";
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND B.memberID like '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND A.userID = C.userID ";
		$sWhere .= " AND C.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND A.userID = B.userID ";
		$sWhere .= " AND A.loanNo = '" . $q . "'";
	}
}

//	if ($id) {
//			$sWhere .= " AND A.loanType in (".$id.") ";						
//	}

$sWhere = " WHERE (" . $sWhere . ")";
if ($q <> "") {
	if ($by == 1 or $by == 3) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else if ($by == 2) {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B, users C";
	}
} else {
	if ($dept <> "") {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A, userdetails B";
	} else {
		$sSQL = "SELECT	DISTINCT A.* FROM 	loans A ";
	}
}
$sSQL = $sSQL . $sWhere . ' ORDER BY A.applyDate DESC';
$GetLoan = &$conn->Execute($sSQL);
$GetLoan->Move($StartRec - 1);

$TotalRec = $GetLoan->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="filter" value="' . $filter . '">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>
    <tr valign="top" class="Header">
	   	<td align="left" >
			Carian melalui 
			<select name="by" class="Data">';
if ($by	== 1)	print '<option value="1" selected>Nombor Anggota</option>';
else print '<option	value="1">Nombor Anggota</option>';
if ($by	== 2)	print '<option value="2" selected>Nama Anggota</option>';
else print '<option	value="2">Nama Anggota</option>';
if ($by	== 3)	print '<option value="3" selected>No rujukan</option>';
else print '<option	value="3">No rujukan</option>';

print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="Data">
 			<input type="submit" class="but" value="Cari">	
			Jabatan
			<select name="dept" class="Data" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
for ($i = 0; $i < count($deptList); $i++) {
	print '	<option value="' . $deptVal[$i] . '" ';
	if ($dept == $deptVal[$i]) print ' selected';
	print '>' . $deptList[$i];
}
print '		</select>&nbsp;&nbsp;           
		</td>
	</tr>';
if ($GetLoan->RowCount() <> 0) {
	$bil = $StartRec;
	$cnt = 1;
	print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr> <td  class="textFont"><input type="checkbox" onClick="ITRViewSelectAll()" class="form-check-input"> Select All</td>
						<td  class="textFont">Lejar pembiayaan :
			<input type="button" class="but" value="Lejer" onClick="ITRActionButtonClick(\'loanYearly\');">                       
			Pengesahan Pembiayaan selesai :
			<input type="button" class="but" value="Selesai" onClick="ITRActionButtonFinish(\'finish\');">
			</td>
						<td align="right" class="textFont">
							Paparan <SELECT name="pg" class="Data" onchange="doListAll();">';
	if ($pg == 5)	print '<option value="5" selected>5</option>';
	else print '<option value="5">5</option>';
	if ($pg == 10)	print '<option value="10" selected>10</option>';
	else print '<option value="10">10</option>';
	if ($pg == 20)	print '<option value="20" selected>20</option>';
	else print '<option value="20">20</option>';
	if ($pg == 30)	print '<option value="30" selected>30</option>';
	else print '<option value="30">30</option>';
	if ($pg == 40)	print '<option value="40" selected>40</option>';
	else print '<option value="40">40</option>';
	if ($pg == 50)	print '<option value="50" selected>50</option>';
	else print '<option value="50">50</option>';
	if ($pg == 100)	print '<option value="100" selected>100</option>';
	else print '<option value="100">100</option>';
	if ($pg == 200)	print '<option value="200" selected>200</option>';
	else print '<option value="200">200</option>';
	if ($pg == 300)	print '<option value="300" selected>300</option>';
	else print '<option value="300">300</option>';
	if ($pg == 400)	print '<option value="400" selected>400</option>';
	else print '<option value="400">400</option>';
	if ($pg == 500)	print '<option value="500" selected>500</option>';
	else print '<option value="500">500</option>';
	if ($pg == 1000) print '<option value="1000" selected>1000</option>';
	else print '<option value="1000">1000</option>';
	print '				</select>setiap mukasurat.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	    <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap>&nbsp;</td>
						<td nowrap>&nbsp;No Rujukan/Pembiayaan</td>
						<td nowrap>&nbsp;No/Nama Anggota</td>
						<td nowrap>&nbsp;Surat Tawaran</td>
						<td nowrap align="center">&nbsp;Tarikh Baucer</td>
						<!--td nowrap align="center">&nbsp;Jadual Bayar Balik</td-->
						<td nowrap>&nbsp;Nombor bond</td>
					</tr>';
	$amtLoan = 0;
	while (!$GetLoan->EOF && $cnt <= $pg) {
		//$conn->debug=1;
		//$c_deduct = dlookup("general", "c_Deduct", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		//if(substr(dlookup("general", "code", "ID=" . tosql($c_deduct, "Text")),0,2)=='51') continue;
		$jabatan = dlookup("userdetails", "departmentID", "userID=" . tosql($GetLoan->fields(userID), "Text"));
		//			$amt = dlookup("general", "c_Maksimum", "ID=" . tosql($GetLoan->fields(loanType), "Number"));
		$amt = $GetLoan->fields('loanAmt');
		$amtLoan += $amt;
		$status = $GetLoan->fields(status);
		$colorStatus = "Data";
		if ($status == 1) $colorStatus = "greenText";
		if ($status == 2) $colorStatus = "redText";
		//sprintf("%010d", $GetLoan->fields(loanID))
		$startPymtDate = dlookup("loandocs", "rcreatedDate", "loanID=" . $GetLoan->fields(loanID));
		if ($startPymtDate) $startPymtDate = toDate("d/m/Y", $startPymtDate);
		else $startPymtDate = "Proses Baucer";
		//--------------
		$loanType				= $GetLoan->fields('loanType');
		$codegroup				= dlookup("general", "parentID", "ID=" . $loanType);

		$colorPen = "Data";
		if ($GetLoan->fields(isApproved) == 1) {
			$colorPen = "greenText";
			$pengesahan = "Pengesahan Dibuat";
		} else {
			$colorPen = "redText";
			$pengesahan = "Tiada Pengesahan";
		}
		$bond = dlookup("loandocs", "rnobond", "loanID=" . tosql($GetLoan->fields(loanID), "Text"));
		if ($bond == '') $bond = 'AJK';

		if ($codegroup <> 1638) {
			$table  = "loanJadual.php?id=" . $GetLoan->fields(loanID);
		} else {
			$table = "loanJadual78.php?type=vehicle&page=view&id=" . $GetLoan->fields(loanID);
		}

		print '         <tr>
						<td class="Data" align="right" height="25">' . $bil . '&nbsp;</td>
						<td class="Data">
						&nbsp;<input type="checkbox" class="form-check-input" name=pk[] value=' . $GetLoan->fields(loanID) . '>'
			. '<a href="' . $table . '">'
			. $GetLoan->fields(loanNo) . ' - '
			. dlookup("general", "name", "ID=" . tosql($GetLoan->fields(loanType), "Number"))
			. '</a>'
			. '</td>
						<td class="Data">&nbsp;'
			. dlookup("userdetails", "memberID", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '-'
			. dlookup("users", "name", "userID=" . tosql($GetLoan->fields(userID), "Text")) . '</td>
						<td class="Data"><font class="' . $colorPen . '">&nbsp;' . $pengesahan . '&nbsp;</font>' . toDate("d/m/Y", $GetLoan->fields(approvedDate)) . '';
		if ($GetLoan->fields(isApproved) == 1) {
			print '<input type=button value="Cetak" class="but" onClick=window.open("tawaranSah2.php?ID=' . $GetLoan->fields(loanID) . '","pop","top=50,left=50,width=700,height=450,scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");></td>';
		}
		print '	<td class="Data" align="center">&nbsp;' . $startPymtDate . '</td>
						<!--td class="Data" align="center">&nbsp;';

		if ($startPymtDate <> "Proses Baucer") {
			if ($codegroup <> 1638) {
				print '<input type=button value="Lihat Jadual" class="but" onClick=window.open("loanJadual.php?id=' . $GetLoan->fields(loanID) . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no");>';
			} else {
				print '<input type=button value="Cetak" class="but" onClick=window.open("loanJadual78.php?type=vehicle&page=view&id=' . $GetLoan->fields(loanID) . '","pop","scrollbars=yes,resizable=yes,toolbars=no,location=no,menubar=no,");>';
			}
		}
		print '		</td-->
						<td class="Data">&nbsp;' . $bond . '</td>
					</tr>';
		$cnt++;
		$bil++;
		$GetLoan->MoveNext();
	}
	$GetLoan->Close();

	print '	</table>
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
		print '<tr><td class="textFont" valign="top" align="left">Rekod Dari : ' . $TotalRec . '<br>';
		for ($i = 1; $i <= $numPage; $i++) {
			if (is_int($i / 10)) print '<br />';
			print '<A href="' . $sFileName . '?&StartRec=' . (($i * $pg) + 1 - $pg) . '&pg=' . $pg . '">';
			print '<b><u>' . (($i * $pg) - $pg + 1) . '-' . ($i * $pg) . '</u></b></a>&nbsp;&nbsp;';
		}
		print '</td>
						</tr>
					</table>';
	}
	print '
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Rekod : <b>' . $TotalRec . '</b></td>
		</tr-->';
} else {
	if ($q == "") {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
	} else {
		print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
	}
}
print ' 
</table>
</form>';

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


	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value;
	}
	
	function ITRActionButtonClick(rpt) {
	e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			if(count != 1) {
				alert(\'Sila pilih satu pembiayaan !\');
			} else {
				if (rpt == "loanYearly" )  {
					url = "loanYearly.php?pk="+ pk +"&yr=' . $yy . '";
				}
				
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			}
		}
	}

	function ITRActionButtonClick_o(v) {
		e = document.MyForm;
		if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
		} else {
			count=0;
			for(c=0; c<e.elements.length; c++) {
				if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
					count++;
					pk = e.elements[c].value;
				}
			}
	        
			
			if(count != 1) {
				alert(\'Sila pilih satu pembiayaan sahaja \');
			} else {
	            e.action.value = v;
	            e.submit();
			}
		}
	}

	
	function ITRActionButtonFinish(v) {
	      e = document.MyForm;
	      if(e==null) {
			alert(\'Sila pastikan nama form diwujudkan.!\');
	      } else {
	        count=0;
	        for(c=0; c<e.elements.length; c++) {
	          if(e.elements[c].name=="pk[]" && e.elements[c].checked) {
	            count++;
	          }
	        }
	        
	        if(count==0) {
	          alert(\'Sila pilih rekod yang hendak Dilakukan.\');
	        } else {
	          if(confirm(count + \' rekod hendak Dilakukan?\')) {
	            e.action.value = v;
	            e.submit();
	          }
	        }
	      }
	    }	   
	
</script>';

include("footer.php");
