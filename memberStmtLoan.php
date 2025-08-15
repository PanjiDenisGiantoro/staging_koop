<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberStmt.php
 *          Date 		: 	15/6/2006
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";
if (!isset($mth)) 		$mth = date("n");
if (!isset($yr)) 		$yr = date("Y");
if (!isset($mm))		$mm = date("m");
if (!isset($yy))		$yy = date("Y");

include("header.php");
include("koperasiQry.php");
include("koperasiList.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 0 and get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = '?vw=memberStmtLoan&mn=3';
$sFileRef  = '?vw=memberStmtLoan&mn=3';
$title     = "Senarai Penyata Pembiayaan";

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
$sWhere = " a.userID = b.userID AND b.status <> 0";
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
$sSQL = "SELECT	DISTINCT a.*, b.* FROM users a, userdetails b";
$sSQL = $sSQL . $sWhere . " order by CAST( b.memberID AS SIGNED INTEGER ) desc";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">
<div class="table-responsive">    
<table class="" border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<div><h5 class="card-title"><i class="fas fa-chart-line"></i>&nbsp;' . strtoupper($title) . '';

if (get_session("Cookie_groupID") > 0) {
	print '<tr valign="top" class="Header"><td align="left" >
Carian melalui <select name="by" class="Data">';
	if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
	else print '<option value="1">Nombor Anggota</option>';
	if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
	else print '<option value="2">Nama Anggota</option>';
	if ($by == 3)	print '<option value="3" selected>No KP Baru</option>';
	else print '<option value="3">No KP Baru</option>';
	print '</select>
		<input type="text" name="q" value="" maxlength="50" size="30" class="Data">
 		<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;		
		Jabatan
		<select name="dept" class="Data" onchange="document.MyForm.submit();">
		<option value="">- Semua -';
	for ($i = 0; $i < count($deptList); $i++) {
		print '	<option value="' . $deptVal[$i] . '" ';
		if ($dept == $deptVal[$i]) print ' selected';
		print '>' . $deptList[$i];
	}
	print '</select></td></tr>';
	print '<tr valign="top" class="textFont"><td align="left">
    <table class="table">
	<tr>
	<td class="textFont">Pilihan Bulan/Tahun</td>
	<td class="textFont">:&nbsp;
	Bulan   : 
			<select name="mm" class="data" onchange="document.MyForm.submit();">
			<option value="ALL"';
	if ($mm == "ALL") print 'selected';
	for ($j = 1; $j < 13; $j++) {
		print '	<option value="' . $j . '"';
		if ($mm == $j) print 'selected';
		print '>' . $j;
	}
	print '</select>
				Tahun  : 
				<select name="yy" class="data" onchange="document.MyForm.submit();">';
	for ($j = 1989; $j <= 2079; $j++) {
		print '	<option value="' . $j . '"';
		if ($yy == $j) print 'selected';
		print '>' . $j;
	}
	print '</select></td>
				</tr>';

	if ($q == "" and $dept == "ALL") {
		print '		
	<tr><td	class="Label" align="center" height=50 valign=middle>
		<hr size="1"><b>- Sila masukkan No / Nama Anggota ATAU pilih Jabatan  -</b><hr size="1">
	</td></tr>';
	} else {
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
		</tr>';

			print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap colspan="5" height="20">&nbsp;</td>
						<td nowrap colspan="3" align="center">&nbsp;Simpanan</td>
					</tr>
					<tr class="header">
						<td nowrap rowspan="1" height="20">&nbsp;</td>
						<td nowrap>&nbsp;No/Nama Anggota</td>
						<td nowrap>&nbsp;No KP Baru</td>
						<td nowrap colspan="2">&nbsp;Jabatan</td>
						<td nowrap align="center">&nbsp;Yuran</td>
						<td nowrap align="center">&nbsp;Syer</td>
					</tr>';
			$totalFee = 0;
			$totalShare = 0;
			while (!$GetMember->EOF && $cnt <= $pg) {
				$totalFees = number_format(getFees($GetMember->fields(userID), $yr), 2);
				$totalSharesTK = number_format(getSharesterkini($GetMember->fields(userID), $yr), 2);
				print ' <tr>
			<td class="Data" align="right">' . $bil . '&nbsp;</td>
			<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(userID)) . ' " ' . $objchk . '>
			<a href="' . $sFileRef . '?ID=' . tohtml($GetMember->fields(userID)) . '">' . $GetMember->fields('memberID') . ' - ' . $GetMember->fields(name) . '</a></td>
			<td class="Data">&nbsp;' . $GetMember->fields('newIC') . '</td>						
			<td class="Data" colspan="2">&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetMember->fields('departmentID'), "Number")) . '</td>						
			<td class="Data" align="right">' . $totalFees . '&nbsp;</td>
			<td class="Data" align="right">' . $totalSharesTK . '&nbsp;</td>
			</tr>';
				$cnt++;
				$bil++;
				$GetMember->MoveNext();
			}
			$GetMember->Close();

			print '</table></td></tr><tr><td>';
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
		} else {
			if ($q == "") {
				print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tiada Rekod Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
			} else {
				print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Carian rekod "' . $q . '" tidak jumpa  -</b><hr size=1"></td></tr>';
			}
		} // end of ($GetMember->RowCount() <> 0)
	} // end of ($q == "" AND $dept == "")

}
print '
<tr><td>&nbsp;</td></tr>
<tr><td>
	<h6 class="card-subtitle" valign="top">
		&nbsp;Penyata Tahunan
	</h6></td></tr>';

print '
	<tr>
		<td class="Label" valign="top">
		<i class="mdi mdi-arrow-right"></i>&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'loanUserYearly\')">Pembiayaan</a>
		</td>
	</tr>
	';


print '
	<tr>
		<td>&nbsp;
		</td>
	</tr>
	';

print ' 
</table>
</div>
</form>';

include("footer.php");

print '
<script language="JavaScript">
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
				alert(\'Sila pilih satu anggota sahaja \');
			} else {
				if (rpt == "memberMonthly" )  {
					url = "memberMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "memberYearly" )  {
					url = "memberYearly.php?yr=' . $yy . '&id=" + pk;
				} else if (rpt == "memberLoan" )  {
					url = "memberLoan.php?pk=" + pk;
				} else if (rpt == "loanUserYearly" )  {
					url = "loanUserYearly.php?pk="+ pk +"&yr=' . $yy . '";
				} else if (rpt == "shareMonthly" )  {
					url = "shareMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "shareYearly" )  {
					url = "shareYearly.php?yr=' . $yy . '&id=" + pk;
				} else if (rpt == "feesMonthly" )  {
					url = "feesMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "feesYearly" )  {
					url = "feesYearly.php?yr=' . $yy . '&id=" + pk;
				}else if (rpt == "memberPenyataYearly" )  {
					url = "memberPenyataYearly.php?pk="+ pk +"&yr=' . $yy . '&id=" + pk;
				} else if (rpt == "feesYearlyAll" )  {
					url = "feesYearlyAll.php?yr=' . $yy . '&id=" + pk;
				}
								
				window.location.href = url;
			}
		}
	}

	function selectPenyata(rpt) {
		if (rpt == "feesMonthly" || rpt == "shareMonthly" || rpt == "memberMonthly") {
			url = "selMthYear.php?rpt="+rpt+"&id=' . $ID . '";
		} else if (rpt == "rptG2Dept") {
			url = "?vw=selYearN&mn=3&rpt="+rpt+"&id=' . $ID . '";
		} else {
			url = "?vw=selYearN&mn=3&rpt="+rpt+"&id=' . $ID . '";
		}
					
		window.location.href = url;	
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value + "&dept=' . $dept . '";
	}

	function selectPop(rpt) {
		if (rpt == "greImportBul") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYearN.php?rpt="+rpt+"&id=ALL";
		}			
		window.location.href = url;	
	}	  

	function ITRActionButtonClick_old(rpt) {
		if (rpt == "BulananU") {
			url = "memberMonthly.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "TahunanU") {
			url = "memberYearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "SenaraiP") {
			url = "memberLoan.php?pk=' . $pk[0] . '";
		} else if (rpt == "TahunanP") {
			url = "loanUserYearly.php?pk=' . $pk[0] . '&yr=' . $yrS . '";
		} else if (rpt == "BulananS") {
			url = "shareMonthly.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "TahunanS") {
			url = "shareYearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "TahunanY") {
			url = "feesYearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "PenyataTahunan") {
			url = "memberPenyataYearly.php?pk=' . $pk[0] . '&yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "Import") {
			url = "greImportPot.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "Eksport") {
			url = "greEksportPot.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		}		
		window.location.href = rptURL;
	}
</script>';
