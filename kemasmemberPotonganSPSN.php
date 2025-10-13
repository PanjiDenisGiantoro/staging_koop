<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	KemasmemberPotonganP.php
 *          Date 		: 	15/6/2006
 *********************************************************************************/
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";
if (!isset($mm))	$mm = date("n");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);

include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = 'KemasmemberPotonganP.php';
$sFileRef  = 'Edit_memberStmtPotongan.php';
$title     = "Kemaskini Potongan Gaji (PGB) Pembiayaan / Wajib (NAIK SEKOLAH , UMRAH)";

if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

if ($edit) {

	$updatedDate = date("Y-m-d H:i:s");
	//$UserID = $_POST['ID'];
	$IDtype = $_POST['IDtype'];
	$pymtUntg = $_POST['untung'];
	$FeePokok = $_POST['pokok'];
	//$ID = $_REQUEST['ID'];

	$sSQLUpd	= "UPDATE potbulanlook SET" .
		" pokokE= '" . $FeePokok . "'" .
		" ,untungE= '" . $pymtUntg . "'" .
		" ,status= 2 " .
		" Where ID= '" . $IDtype . "'";
	$rsUpd = &$conn->Execute($sSQLUpd);


	print '<script>alert("Kemaskini Potongan Gaji(PGB) Berjaya !");</script> ';
	//echo $pymtUntg;
	//echo $FeePokok;
	//echo $IDtype;
}
//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT DISTINCT c.loanType, b.code as deptCode, b.name as deptName 
			FROM  general b, potbulan c
			WHERE b.ID = c.loanType AND c.status = 1";
$rs = &$conn->Execute($sSQL);


$sSQL2 = "	SELECT * FROM userdetails a, users b, potbulan c
Where a.userID = b.userID AND a.userID = c.userID AND c.lastyrmthPymt >= '" . $yymm . "' AND a.status = 1 group by a.userID order by CAST( a.userID AS SIGNED INTEGER )";
$rs2 = &$conn->Execute($sSQL2);

if ($rs->RowCount() <> 0) {
	while (!$rs->EOF) {
		//array_push ($deptList, $rs->fields(deptCode).'-'.$rs->fields(deptName));
		array_push($deptList, $rs->fields(deptName));
		array_push($deptVal, $rs->fields(loanType));
		$rs->MoveNext();
	}
}

$yyT = $yy - 1;
if ($mm == 12) {
	$mmT2 = '01';
	$yyT = $yy;
} elseif ($mm == 10 or $mm == 11 or $mm == 9) {
	$mmT2 = $mm + 1;
} else {
	$mmT = $mm + 01;
	$mmT2 = '0' . $mmT; // }
}
$yymmTT = $yyT . $mmT2;


$sSQL = "";
$sWhere = " a.ID = b.potID  AND a.status = 1  AND b.yrmth = '" . $yymm . "' AND b.loanType IN (1616,1540,1620,1630)  And a.userID = c.userID";
if ($dept <> "") {
	$sWhere .= " AND a.loanType = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.userID = '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND a.name like '%" . $q . "%'";
	}
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT b.ID,a.userID, c.name, a.bondNo, b.pokok, b.untung, b.yrmth,a.loanType FROM  `potbulan` a, potbulanlook b, users c";
//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
$sSQL = $sSQL . $sWhere . " order by CAST( b.userID AS SIGNED INTEGER ) desc";
$GetMember = &$conn->Execute($sSQL);
$GetMember->Move($StartRec - 1);

$TotalRec = $GetMember->RowCount();
$TotalPage =  ($TotalRec / $pg);

print '
<form name="MyForm" action=' . $sFileName . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
	<tr>
		<td ><b class="maroonText">' . strtoupper($title) . '</b></td>
	</tr>';


if (get_session("Cookie_groupID") > 0) {
	print '<tr valign="top" class="Header">
	   	<td align="left" >
			Carian melalui 
			<select name="by" class="Data">';
	if ($by == 1)	print '<option value="1" selected>Nombor Anggota</option>';
	else print '<option value="1">Nombor Anggota</option>';
	if ($by == 2)	print '<option value="2" selected>Nama Anggota</option>';
	else print '<option value="2">Nama Anggota</option>';

	print '		</select>
			<input type="text" name="q" value="" maxlength="50" size="30" class="Data">
 			<input type="submit" class="but" value="Cari">&nbsp;&nbsp;&nbsp;	
			Pembiayaan
			<select name="dept" class="Data" onchange="document.MyForm.submit();">
				<option value="">- Semua -';
	for ($i = 0; $i < count($deptList); $i++) {
		print '	<option value="' . $deptVal[$i] . '" ';
		if ($dept == $deptVal[$i]) print ' selected';
		print '>' . $deptList[$i];
	}
	print '		</select>
		Bulan   : 
			<select name="mm" class="data" onchange="document.MyForm.submit();">
				<option value="ALL"';
	if ($mm == "ALL") print 'selected';
	print '>- Semua -';
	for ($j = 1; $j < 13; $j++) {
		print '	<option value="' . $j . '"';
		if ($mm == $j) print 'selected';
		print '>' . $j;
	}
	print '		</select>
			Tahun  : 
			<select name="yy" class="data" onchange="document.MyForm.submit();">';
	for ($j = 1989; $j <= 2079; $j++) {
		print '	<option value="' . $j . '"';
		if ($yy == $j) print 'selected';
		print '>' . $j;
	}
	print '		</select>	</td>
	</tr>';

	print '    <tr valign="top" class="textFont">
	   	<td align="left">
			<table>
				<tr>

				</tr>
			</table>
		</td>
	</tr>';
	if ($q == "" and $dept == "ALL") {
		print '		
	<tr><td	class="Label" align="center" height=50 valign=middle>
		<hr size="1"><b>- Sila masukkan No / Nama Anggota ATAU pilih Pembiayaan  -</b><hr size="1">
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
						<td  class="textFont">&nbsp;Jum Anggota PGB &nbsp' . $TotalRec . '</td>
						<input type="button" class="but" value="Preview PGB" onclick="window.open(\'rptPokokUntung.php?dept=' . $dept . '&yy=' . $yy . '&mm=' . $mm . '\')">
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
			//}

			print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap colspan="10" height="20">&nbsp;</td>
					<tr class="header">
						<td nowrap rowspan="1" height="20">Bil</td>
						<td nowrap>&nbsp;No/Nama Anggota</td>
						<td nowrap>&nbsp;Jenis Pembiayaan</td>
						<td nowrap colspan="2">No Bond</td>
						<td nowrap >Untung</td>
						<td nowrap >Pokok</td>
						<td colspan="3" nowrap><div align="center">Edit</div></td>
					   

					</tr>';
			$totalFee = 0;
			$totalShare = 0;
			while (!$GetMember->EOF && $cnt <= $pg) {
				//$totalFees = number_format(getFees($GetMember->fields(userID), $yr),2);
				//sprintf(number_format($GetMember->fields('totalFee'),2,'.',','))
				$totalJumP = getJumlahPGBALL($GetMember->fields(userID), $yymm);
				//$totalJumY = number_format(getJumlahY($GetMember->fields(userID)),2);
				$totalJumY = number_format($GetMember->fields(monthFee), 2);
				$jumALL = number_format($totalJumP + $totalJumY, 2);

				//sprintf(number_format($GetMember->fields('totalShare'),2,'.',','))
				print ' <tr>
						<td class="Data" align="right">' . $bil . '&nbsp;</td>
						<td class="Data"><a href="' . $sFileRef . '?ID=' . tohtml($GetMember->fields(userID)) . '">' . $GetMember->fields('userID') . ' - ' . $GetMember->fields(name) . '</a></td>
						<td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetMember->fields('loanType'), "Number")) . '</td>						
						<td class="Data" colspan="2">&nbsp;' . $GetMember->fields('bondNo') . '</td>	
 <td class="Data" >&nbsp;';
				if ($IDtype == $GetMember->fields(ID)) {
					print '&nbsp;<input size="7" name="untung" value="' . $GetMember->fields('untung') . '" >';
				} else {
					print '&nbsp;' . $GetMember->fields('untung') . '';
				}
				print ' </td>
	        <td class="Data" align="right" >&nbsp;';
				if ($IDtype == $GetMember->fields(ID)) {
					print '&nbsp;<input size="15" name="pokok" value="' . $GetMember->fields('pokok') . '" >';
				} else {
					print '&nbsp;' . $GetMember->fields('pokok') . '';
				}

				print '</td>
      <td class="Data" align="center" width="5%">&nbsp;<a href="' . $sFileName . '?IDtype=' . $GetMember->fields(ID) . '&ID=' . $ID . '&code=2" title="kemaskini"><img src="b_edit.png"></a> <input size="7" type="hidden" name="IDtype" value="' . $IDtype . '" ><input size="7" type="hidden" name="ID" value="' . $ID . '" ></td>
      <td class="Data" align="center"  width="5%">&nbsp;<a href="' . $sFileNameDel . '?IDtype=' . $GetMember->fields(ID) . '&ID=' . $ID . '&code=1" title="Hapus" onClick="if(!confirm(\'Adakah ada pasti untuk hapus file ini?\')) {return false} else {window.Edittrans.submit();};"><img src="b_drop.png"></td>
	  <td class="Data" align="center" width="5%">';
				if ($IDtype == $GetMember->fields(ID)) {
					print '<input type="submit" size="3" onClick="if(!confirm(\'Adakah ada pasti untuk Kemaskini file ini?\')) {return false} else {window.Edittrans.submit();};" name="edit" value="edit" />       
';
				}

				print '
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
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetMember->RowCount() . '</b></td>
		</tr-->';
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

} else {

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesMonthly\')">Penyata Wajib Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'feesYearly\')">Penyata Wajib Tahunan</a>
		</td>
	</tr>
    ';

	print '
	<tr>
		<td class="Label" valign="top">
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'shareMonthly\')">Penyata Pokok Bulanan</a>
		<li id="print" class="textFont">&nbsp;&nbsp;<a href="#" onclick="selectPenyata(\'shareYearly\')">Penyata Pokok Tahunan</a>
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
				}else if (rpt == "feesYearly_all" )  {
					url = "../kofrim/feesYearly_all.php?yr=' . $yy . ';
				}else if (rpt == "memberPenyataYearly" )  {
					url = "memberPenyataYearly.php?pk="+ pk +"&yr=' . $yy . '&id=" + pk;
				}else if (rpt == "loanUserYearly_all" )  {
					url = "../kofrim/loanUserYearly_all2.php?pk="+ pk +"&yr=' . $yy . '&id=" + pk;
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
		document.location = "' . $sFileName . '?yy=' . $yy . '&mm=' . $mm . '&StartRec=1&pg=" + c.options[c.selectedIndex].value;
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
		} else if (rpt == "TahunanU") {
			url = "membe Yearly.php?yr=' . $yrS . '&id=' . $pk[0] . '";
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

		window.open (rptURL, "mthyear","scrollbars=yes,resizable=yes,toolbars=yes,location=no,menubar=yes");
	}
</script>';
