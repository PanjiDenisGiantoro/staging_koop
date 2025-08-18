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
if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");
if (!isset($mm))	$mm = date("m");
if (!isset($yy))	$yy = date("Y");

include("header.php");
include("koperasiQry.php");
include("koperasiList.php");
date_default_timezone_set("Asia/Jakarta");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = "?vw=memberStmtHL&mn=$mn";
$sFileRef  = "?vw=memberStmtHL&mn=$mn";
$title     = "Senarai Penyata Hutang Lapuk";

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
		//array_push ($deptList, $rs->fields(deptCode).'-'.$rs->fields(deptName));
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(departmentID));
		$rs->MoveNext();
	}
}

//$GetMember = ctMemberStatusDept($q,$by,"1",$dept);
$sSQL = "";
$sWhere = " a.userID = b.userID AND b.statusHL = 1";;
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID = '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	} else if ($by == 3) {
		$sWhere .= " AND b.newIC = '" . $q . "'";
	} else if ($by == 4) {
		$sWhere .= " AND b.peringkat = '" . $q . "'";
	}
}

if ($ID) {
	$sWhere .= " AND b.userID = " . tosql($ID, "Text");
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*, b.*
			 FROM 	users a, userdetails b";
//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
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
<h5 class="card-title">' . strtoupper($title) . ' &nbsp;</h5>';
if (get_session("Cookie_groupID") > 0) {
	$opt = array(1, 2, 3, 4);
	carianheader($by, $opt, $dept, $deptList, $deptVal);
}
echo '<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center" class="table">
';

if (get_session("Cookie_groupID") > 0) {

	print '    <tr valign="top" class="table-light">
	   	<td align="left">
			<table cellpadding="3">
				<tr>
					<td class="textFont">Pilihan Bulan/Tahun</td>
					<td class="textFont">&nbsp;
						Bulan  
						<select name="mm" class="form-select-sm" onchange="document.MyForm.submit();">
						<option value="ALL"';
	if ($mm == "ALL") print 'selected';
	for ($j = 1; $j < 13; $j++) {
		print '	<option value="' . $j . '"';
		if ($mm == $j) print 'selected';
		print '>' . $j;
	}
	print '</select>
						Tahun 
						<select name="yy" class="form-select-sm" onchange="document.MyForm.submit();">';
	for ($j = 1989; $j <= 2079; $j++) {
		print '	<option value="' . $j . '"';
		if ($yy == $j) print 'selected';
		print '>' . $j;
	}
	print '</select>				
					</td>
				</tr> 

				<tr>
					<td class="textFont">Penyata Pembiayaan</td>
					<td class="textFont">&nbsp; 
			        <!--input type="button" class="btn btn-sm btn-secondary" value="Jadual" onClick="ITRActionButtonClick(\'memberLoan\');" style="width:100px;"--> 
					<input type="button" class="btn btn-sm btn-secondary" value="Tahunan" onClick="ITRActionButtonClick(\'loanUserYearlyHLthn\');" style="width:100px;">  					<input type="button" class="btn btn-sm btn-secondary" value="Keseluruhan" onClick="ITRActionButtonClick(\'loanUserYearlyHLthnNew\');" style="width:100px;">                      
					</td>
				
				</tr>
				<tr>
					<td class="textFont">Penyata Notis Tuntutan (T4)</td>
					<td class="textFont">&nbsp; 
			        <!--input type="button" class="btn btn-sm btn-secondary" value="Jadual" onClick="ITRActionButtonClick(\'memberLoan\');" style="width:100px;"--> 
					<input type="button" class="btn btn-sm btn-secondary" value="Bulanan" onClick="ITRActionButtonClick(\'loanUserYearlyT4B\');" style="width:100px;"> <input type="button" class="btn btn-sm btn-secondary" value="Keseluruhan" onClick="ITRActionButtonClick(\'loanUserYearlyHL\');" style="width:100px;">                   
					</td>
				
				</tr>
				<tr>
				
				</tr>
			</table>
		</td>
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

			//if (get_session("Cookie_groupID") > 0) { //just for staf
			print '
		<tr valign="top" class="textFont">
			<td>
				<table width="100%">
					<tr>
						<td  class="textFont">&nbsp;</td>
						<td align="right" class="textFont">';
			echo papar_ms($pg);
			print '</td>
					</tr>
				</table>
			</td>
		</tr>';
			//}

			print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped">
					<tr class="table-primary">
						<td nowrap rowspan="1" height="20">&nbsp;</td>
						<td nowrap>Nombor - Nama Anggota</td>
						<td nowrap align="center">Kartu Identitas</td>
						<td nowrap colspan="2">Cabang/Zona</td>
						<td nowrap align="right">Sisa Utang Pembiayaan (RM)</td>
					</tr>';
			$totalFees = 0;
			$totalShare = 0;
			while (!$GetMember->EOF && $cnt <= $pg) {
				//$totalFees = number_format($GetMember->fields(DebtBalance),2);
				//sprintf(number_format($GetMember->fields('totalFee'),2,'.',','))
				$BakiHL = number_format(getBalanceHL($GetMember->fields(userID)), 2);

				print ' <tr>
						<td class="Data" align="right">' . $bil . '</td>
						<td class="Data"><input type="checkbox" class="form-check-input" name="pk[]" value="' . tohtml($GetMember->fields(userID)) . ' " ' . $objchk . '>
						<a href="' . $sFileRef . '&ID=' . tohtml($GetMember->fields(userID)) . '">' . $GetMember->fields('memberID') . ' - ' . $GetMember->fields(name) . '</a></td>
						<td class="Data" align="center">' . $GetMember->fields('newIC') . '</td>						
						<td class="Data" colspan="2">' . dlookup("general", "name", "ID=" . tosql($GetMember->fields('departmentID'), "Number")) . '</td>						
						<td class="Data" align="right">' . $BakiHL . '</td>
					</tr>';
				$cnt++;
				$bil++;
				//$totalFee += $GetMember->fields('totalFee');
				//$totalShare += $GetMember->fields('totalShare');				
				$GetMember->MoveNext();
			}
			$GetMember->Close();

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
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Data : <b>' . $GetMember->RowCount() . '</b></td>
		</tr-->';
		} else {
			if ($q == "") {
				print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Tidak Ada Data Untuk ' . $title . '  -</b><hr size=1"></td></tr>';
			} else {
				print '
			<tr><td align="center"><hr size=1"><b class="textFont">- Pencarian data "' . $q . '" tidak ditemukan  -</b><hr size=1"></td></tr>';
			}
		} // end of ($GetMember->RowCount() <> 0)
	} // end of ($q == "" AND $dept == "")

} else {

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesMonthly\')">Penyata Yuran Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesYearly\')">Penyata Yuran Tahunan</a>
		</td>
	</tr>
    ';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'shareMonthly\')">Penyata Syer Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'shareYearly\')">Penyata Syer Tahunan</a>
		</td>
	</tr>
	';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'loanUserYearly\')">Penyata Pembiayaan Tahunan</a>
		</td>
	</tr>
	';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberMonthly\')">Penyata Urusniaga Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberYearly\')">Penyata Urusniaga Tahunan</a>
		</td>
	</tr>
	';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'memberPenyataYearly\')">Penyata Tahunan Anggota</a>
		</td>
	</tr>';
}
print ' 
</table>
</form>';

include("footer.php");

print '
<script language="JavaScript">
	function ITRActionButtonClick(rpt) {
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
				alert(\'Sila pilih satu anggota sahaja \');
			} else {
				if (rpt == "memberMonthly" )  {
					url = "memberMonthly.php?yrmth=' . $yy . $mm . '&id=" + pk;
				} else if (rpt == "loanUserYearlyT4B" )  {
					url = "loanUserYearlyT4B.php?pk="+ pk +"&yr=' . $yy . '";
				} else if (rpt == "memberLoan" )  {
					url = "memberLoan.php?pk=" + pk;
				} else if (rpt == "loanUserYearlyHLthn" )  {
					url = "loanUserYearlyHLthn.php?pk="+ pk +"&yr=' . $yy . '";
				} else if (rpt == "loanUserYearlyHLthnNew" )  {
					url = "loanUserYearlyHLthnNew.php?pk="+ pk +"&yr=' . $yy . '";
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
				}
				
				window.open (url, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
			}
		}
	}

	function selectPenyata(rpt) {
		if (rpt == "feesMonthly" || rpt == "shareMonthly" || rpt == "memberMonthly") {
			url = "selMthYear.php?rpt="+rpt+"&id=' . $ID . '";
		} else if (rpt == "rptG2Dept") {
			url = "selYear.php?rpt="+rpt+"&id=' . $ID . '";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=' . $ID . '";
		}
		
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}

	function doListAll() {
		c = document.forms[\'MyForm\'].pg;
		document.location = "' . $sFileName . '?&StartRec=1&pg=" + c.options[c.selectedIndex].value + "&dept=' . $dept . '";
	}

	function selectPop(rpt) {
		if (rpt == "greImportBul") {
			url = "selMthYear.php?rpt="+rpt+"&id=ALL";
		} else {
			url = "selYear.php?rpt="+rpt+"&id=ALL";
		}
		window.open(url ,"pop","top=100,left=100,width=500,height=100,scrollbars=no,resizable=no,toolbars=no,location=no,menubar=no");		
	}	  

	function ITRActionButtonClick_old(rpt) {
		if (rpt == "BulananU") {
			url = "memberMonthly.php?yrmth=' . sprintf("%04d%02d", $yrS, $mthS) . '&id=' . $pk[0] . '";
		} else if (rpt == "loanUserYearlyT4B") {
			url = "loanUserYearlyT4B.php?yr=' . $yrS . '&id=' . $pk[0] . '";
		} else if (rpt == "SenaraiP") {
			url = "memberLoan.php?pk=' . $pk[0] . '";
		} else if (rpt == "TahunanP") {
			url = "loanUserYearlyHL.php?pk=' . $pk[0] . '&yr=' . $yrS . '";
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

		window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
	}
</script>';
