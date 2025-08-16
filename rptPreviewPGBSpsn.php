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
if (!isset($mth)) $mth	= date("n");
if (!isset($yr)) $yr	= date("Y");


include("common.php");
include("koperasiQry.php");
date_default_timezone_set("Asia/Kuala_Lumpur");
$yrmthNow = sprintf("%04d%02d", $yr, $mth);

$koperasiID = dlookup("setup", "koperasiID", "setupID=" . tosql(1, "Text"));

if (
	get_session("Cookie_groupID") <> 1
	and get_session("Cookie_groupID") <> 2
	or get_session("Cookie_koperasiID") <> $koperasiID
) {
	print '<script>alert("' . $errPage . '");parent.location.href = "index.php";</script>';
}

$sFileName = 'memberPotonganALL.php';
$sFileRef  = 'Edit_memberStmtPotongan.php';
$title     = "Senarai Tunggakkan Potongan Gaji SPSN";

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
			AND   a.status IN  (1) 
			GROUP BY a.departmentID";
$rs = &$conn->Execute($sSQL);

$sSQL2 = "	SELECT * FROM userdetails a, users b
Where a.userID = b.userID AND a.status = 1 group by a.userID order by CAST( a.userID AS SIGNED INTEGER )";
$rs2 = &$conn->Execute($sSQL2);

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
$sWhere = " a.userID = b.userID AND b.status = 1 AND b.departmentID NOT IN (1770,1584,1598,1600,1599,1706)";
if ($dept <> "") {
	$sWhere .= " AND b.departmentID = " . tosql($dept, "Number");
}

if ($q <> "") {
	if ($by == 1) {
		$sWhere .= " AND b.memberID = '" . $q . "'";
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
			 FROM 	users a, userdetails b";
//$sSQL = $sSQL . $sWhere . ' ORDER BY applyDate DESC';
$sSQL = $sSQL . $sWhere . " order by CAST( b.userID AS SIGNED INTEGER )";
$GetMember = &$conn->Execute($sSQL);
//$GetMember->Move($StartRec-1);

$TotalRec = $GetMember->RowCount();
//$TotalPage =  ($TotalRec/$pg);
print '
<html>
<head>
	<!--LINK rel="stylesheet" href="images/default.css" -->	
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />        
</head>
<body leftmargin="5" topmargin="5" class="bodyBG">';
print '
<form name="MyForm" action=' . $_SERVER['PHP_SELF'] . ' method="post">
<input type="hidden" name="action">
<input type="hidden" name="StartRec" value="' . $StartRec . '">
<input type="hidden" name="by" value="' . $by . '">
<table border="0" cellspacing="1" cellpadding="3" width="100%" align="center">
<h5 class="card-title mb-4" style="background-color: #A9D7CB; padding: 10px; font-weight: bold;" align="center">' . strtoupper($title) . '</h5>';

if ($generate) {
	$rpath = realpath("PreviewPGBSpsn.php");
	$dpath = dirname($rpath);
	$fname = trim($fname);
	$fname = 'PGBSPSN' . $yrmthNow . '.txt';
	$filename = $dpath . '/SPSNfile/' . $fname;

	//$str = "SELECT * FROM bayar_detail WHERE no_bayar = '". $no_bayar ."' ORDER BY ID";
	//	$rs = &$conn->Execute($str);
	$file = fopen($filename, 'w', 1);
	//$nl="\n";
	//fwrite($file, $nl);
	$bil = 1;
	while (!$GetMember->EOF) {

		$userID = $GetMember->fields(userID);
		$totalJumP = getJumlahPGBALL($GetMember->fields(userID), $yrmthNow);
		//$totalJumY = number_format(getJumlahY($GetMember->fields(userID)),2);
		$totalJumY = number_format($GetMember->fields(monthFee), 2);
		$jumALL = $totalJumP + $totalJumY;
		$jumSPSN = getJumlahPGBALLSPSN($GetMember->fields(userID), $yrmthNow);
		$Baki = number_format($jumALL - $jumSPSN, 2);
		//$strSql = "SELECT a.name, b.newic, b.accTabungan FROM users a, userdetails b WHERE a.userID = b.userID and b.memberID = '". $userID ."'";
		//$rsMember = $conn->Execute($strSql);


		fwrite($file, $bil);
		fwrite($file, " , ");
		fwrite($file, $userID);
		fwrite($file, " , ");
		fwrite($file, $GetMember->fields(name));
		fwrite($file, " , ");
		$Staffno  = $GetMember->fields(staftNo);
		fwrite($file, $Staffno);
		fwrite($file, " , ");
		fwrite($file, $GetMember->fields(amount));
		fwrite($file, " , ");
		fwrite($file, $jumALL);
		fwrite($file, "\r\n");

		$GetMember->MoveNext();
		$bil++;
	}
	fclose($file);


	$link =  '<a href="/KOOPJPAGRO/textfile/' . $fname . '">' . $fname . '</a>';
} else {
	//print 'sila masukkan nama fail.';
}
//--------
if ($link) {
	print '<tr><td>&nbsp;(Right click- save link as to download):&nbsp;' . $link . ' </td></tr>';
}

//if (get_session("Cookie_groupID") > 0) { //just for staf


print '  <tr valign="top" >
			<td valign="top">
	<input type="button" name="generate" value="Print" class="btn btn-secondary waves-light waves-effect" onclick= "Javascript:(window.location.href=\'rptPreviewPGBSpsn.php?action=view&no_bayar=' . $yrmthNow . '&generate=y\')">
				<table border="0" cellspacing="1" cellpadding="2" width="100%" class="table table-sm table-striped" style="font-size: 10pt;">
				<tr><td>&nbsp;</td></tr>	
				<tr class="table-primary">
						<td nowrap colspan="4" height="20">&nbsp;</td>
						<td nowrap height="20" colspan="8" nowrap><div align="center"><b>Jumlah Tunggakkan (RM)</b></div></td>
					</tr>

						<tr class="table-primary">
						<td nowrap height="8" align="center"><b>Bil</b></td>
						<td nowrap height="8" align="center"><b>Nombor Anggota</b></td>
						<td nowrap><b>Nama</b></td>
						<td nowrap><b>Nombor Pekerja</b></td>
						<td nowrap colspan="2" align="right"><b>Yuran (RM)</b></td>
						<td nowrap colspan="2" align="right"><b>Pembiayaan (RM)</b></td>
						<td nowrap colspan="2" align="right"><b>Jumlah Tunggakkan (RM)</b></td>
					    <td nowrap colspan="2" align="right"><b>Yuran Terkini (RM)</b></td>

					</tr>';
$totalFee = 0;
$totalShare = 0;
$bil = 1;



while (!$GetMember->EOF) {
	//$totalFees = number_format(getFees($GetMember->fields(userID), $yr),2);
	//sprintf(number_format($GetMember->fields('totalFee'),2,'.',','))
	$totalJumP = getJumlahPGBALL($GetMember->fields(userID), $yrmthNow);
	//$totalJumY = number_format(getJumlahY($GetMember->fields(userID)),2);
	$totalJumY = number_format($GetMember->fields(monthFee), 2);
	$jumALL = $totalJumP + $totalJumY;
	$jumSPSN = getJumlahPGBALLSPSN($GetMember->fields(userID), $yrmthNow);
	$bakiPem1 = $totalJumP - $jumSPSN;
	$bakiYuran1 = $totalJumY - $jumSPSN;
	$bakiPemb = number_format($totalJumP - $jumSPSN, 2);
	$BakiYuran = number_format($totalJumY - $jumSPSN, 2);
	$jumTgg =  number_format($bakiPem1 + $bakiYuran1, 2);
	$totalShares = number_format(getFeesAwlDiv($GetMember->fields(userID), $yrmthNow), 2);
	//sprintf(number_format($GetMember->fields('totalShare'),2,'.',','))


	print ' <tr>
						<td class="Data" align="center">' . $bil . '</td>
						<td class="Data" align="center">' . $GetMember->fields('memberID') . '</td>
						<td class="Data">' . $GetMember->fields(name) . '</td>
     					<td class="Data">' . $GetMember->fields('staftNo') . '</td>						
						<td class="Data" align="right" colspan="2">' . $BakiYuran . '</td>	
						<td class="Data" align="right" colspan="2">' . $bakiPemb . '</td>
						<td class="Data" align="right" colspan="2">' . $jumTgg . '</td>	
						<td class="Data" align="right" colspan="2">' . $totalShares . '</td>						

					</tr>';
	$cnt++;
	$bil++;
	//$totalFee += $GetMember->fields('totalFee');
	//$totalShare += $GetMember->fields('totalShare');		
	//	$GetMember->MoveNext();

	$GetMember->MoveNext();
}
$GetMember->Close();


print '
			</td>
		</tr>
		<!--tr>
			<td class="textFont">Jumlah Rekod : <b>' . $GetMember->RowCount() . '</b></td>
		</tr-->';

print ' 
</table>
</form>
<tr><td style="font-size: 9pt;" colspan="8" align="center">' . $retooFetis . '</td><tr>';
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
