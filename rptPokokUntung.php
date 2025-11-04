<?php

/*********************************************************************************
 *          Project		:	iKOOP.com.my
 *          Filename		: 	memberStmt.php
 *          Date 		: 	15/6/2006
 *********************************************************************************/
session_start();
if (!isset($StartRec))	$StartRec = 1;
if (!isset($pg))		$pg = 50;
if (!isset($q))			$q = "";
if (!isset($by))		$by = "1";
if (!isset($dept))		$dept = "";
if (!isset($mm))	$mm = date("n"); //date("m");
if (!isset($yy))	$yy = date("Y");
$yymm = sprintf("%04d%02d", $yy, $mm);



include("header.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Jakarta");
//$yrmthNow = sprintf("%04d%02d", $yr, $mth);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = 'rptPokokUntung.php';
$sFileRef  = 'Edit_memberStmtPotongan.php';
$title     = "Kemaskini Potongan Gaji (PGB) Pembiayaan / Yuran";

if (get_session("Cookie_groupID") == 0) {
	$ID = get_session("Cookie_userID");
	$dept = dlookup("userdetails", "departmentID", "userID=" . $ID);
	$pk[0] = $ID;
	$objchk = " checked disabled ";
}

//--- Prepare department list
$deptList = array();
$deptVal  = array();
$sSQL = "	SELECT DISTINCT c.loanType, b.code as deptCode, b.name as deptName 
			FROM  general b, potbulan c
			WHERE b.ID = c.loanType";
$rs = &$conn->Execute($sSQL);

$sSQL2 = "	SELECT * FROM userdetails a, users b, potbulan c
Where a.userID = b.userID AND a.userID = c.userID AND c.lastyrmthPymt >= '" . $yrmthNow . "' AND a.status IN (1) AND c.status = 1 AND c.yrmth < '" . $yrmthNow . "' AND a.departmentID NOT IN (1598,1600,1599,1584,1706,1841,1770) group by a.userID order by CAST( a.userID AS SIGNED INTEGER )";
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
$sWhere = "
	a.userID = c.userID AND 
	a.userID = b.userID AND 
	b.status IN (1) AND 
	c.status IN (1) AND 
	(d.yrmth between '" . $yymmTT . "' AND '" . $yymm . "') AND 
	c.loanType NOT IN (1540) AND 
	b.departmentID NOT IN (1598,1600,1599,1584,1706,1841,1770,1664) AND 
	c.ID = d.potID";

if ($dept <> "") {
	$sWhere .= " AND c.loanType = " . tosql($dept, "Number");
}
if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND d.userID = '" . $q . "'";
	} else if ($by == 2) {
		$sWhere .= " AND c.name like '%" . $q . "%'";
	}
}
if ($ID) {
	$sWhere .= " AND c.userID = " . tosql($ID, "Text");
}

$sWhere = " WHERE (" . $sWhere . ")";
$sSQL = "SELECT	DISTINCT a.*,b.*, c.*,d.* FROM users a, userdetails b ,potbulan c , potbulanlook d";
$sSQL = $sSQL . $sWhere . " order by CAST( d.userID AS SIGNED INTEGER ) DESC";
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
		<td ><b class="maroonText">' . strtoupper($title) . '&nbsp;&nbsp;<input type="button" class="but" value="csv" onClick="ITRActionButtonFinish(\'csv\');"></b></td>	
	</tr>';

print '  <tr valign="top" >
			<td valign="top">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="lineBG">
					<tr class="header">
						<td nowrap rowspan="1" align="center">&nbsp; No</td>
						<td nowrap>Nomor Anggota</td>
						<td nowrap>&nbsp;Nama Anggota</td>
						<td nowrap>&nbsp;Jenis Pembiayaan</td>
						<td nowrap colspan="2">BOND</td>
						<td nowrap colspan="2">Pokok</td>
						<td nowrap colspan="2">Untung';
if ($dept == '1703') {
	print 'PIN1</td>  <td nowrap colspan="2">UNT1</td>';
}
if ($dept == '1710') {
	print 'PIN1C</td>  <td nowrap colspan="2">UNT1</td>';
}
if ($dept == '1828') {
	print 'PIN1D</td>  <td nowrap colspan="2">UNT1</td>';
}

if ($dept == '1769') {
	print 'BPlus</td>  <td nowrap colspan="2">UBPlus</td>';
}
if ($dept == '1829') {
	print 'BPlus2</td>  <td nowrap colspan="2">UBPlus</td>';
}

if ($dept == '1615') {
	print 'BRG1</td>  <td nowrap colspan="2">UBRG2</td>';
}
if ($dept == '1662') {
	print 'BRG2</td>  <td nowrap colspan="2">UBRG2</td>';
}

if ($dept == '1550') {
	print 'PMOTOR</td>  <td nowrap colspan="2">UNTMOT</td>';
}
if ($dept == '1552') {
	print '5107</td>  <td nowrap colspan="2">UNTMOT</td>';
}
if ($dept == '1765') {
	print 'PMOTOR4</td>  <td nowrap colspan="2">UNTMOT</td>';
}

if ($dept == '1546') {
	print '5104</td>  <td nowrap colspan="2">UNTKND</td>';
}
if ($dept == '1548') {
	print '5105</td>  <td nowrap colspan="2">UNTKND</td>';
}


if ($dept == '1737') {
	print 'PIN9</td>  <td nowrap colspan="2">UNT9</td>';
}
if ($dept == '1746') {
	print 'PIN9</td>  <td nowrap colspan="2">UNT9</td>';
}
if ($dept == '1798') {
	print 'PIN11</td>  <td nowrap colspan="2">UNT9</td>';
}
if ($dept == '1803') {
	print 'PIN11B</td>  <td nowrap colspan="2">UNT9</td>';
}
if ($dept == '1806') {
	print 'PIN12B</td>  <td nowrap colspan="2">UNT9</td>';
}
'  

					</tr>';
$totalFee = 0;
$totalShare = 0;
$bil = 1;
while (!$GetMember->EOF) {
	//$totalFees = number_format(getFees($GetMember->fields(userID), $yr),2);
	//sprintf(number_format($GetMember->fields('totalFee'),2,'.',','))
	$totalJumP = getJumlahPGBALL($GetMember->fields(userID), $yymm);
	//$totalJumY = number_format(getJumlahY($GetMember->fields(userID)),2);
	$totalJumY = number_format($GetMember->fields(monthFee), 2);
	$jumALL = number_format($totalJumP + $totalJumY, 2);

	//sprintf(number_format($GetMember->fields('totalShare'),2,'.',','))
	print ' <tr>
			<td class="Data" align="center">' . $bil . '&nbsp;</td>
			<td class="Data">' . tohtml($GetMember->fields(userID)) . '</td>
			<td class="Data">' . $GetMember->fields(name) . '</td>
			<td class="Data">&nbsp;' . dlookup("general", "name", "ID=" . tosql($GetMember->fields('loanType'), "Number")) . '</td>						
			<td class="Data" colspan="2">' . $GetMember->fields('bondNo') . '</td>	
			<td class="Data" colspan="2">' . $GetMember->fields('pokok') . '</td>	
			<td class="Data" colspan="2">' . $GetMember->fields('untung') . '</td>							

					</tr>';
	$cnt++;
	$bil++;
	//$totalFee += $GetMember->fields('totalFee');
	//$totalShare += $GetMember->fields('totalShare');				
	$GetMember->MoveNext();
}
$GetMember->Close();



//include("footer.php");	

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
